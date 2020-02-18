<?php

Class Html{
    
    private static $html_page='';
    private static $filters=array();
    private static $orders=array();
    private static $select='';
    private static $table='';
    private static $action='';
    private static $url=URL;
    private static $inc_values=array();
    private static $chk_values=array();
    private static $where='';
    
    public static function setPage( $html_page ){
        self::$html_page = $html_page;
    }
    public static function setFilter( $filters ){
        self::$filters = $filters;
    }
    public static function setOrder( $orders ){
        self::$orders = $orders;
    }
    public static function setSelect( $select ){
        self::$select = $select;
    }
    public static function setTable( $table ){
        self::$table = $table;
    }
    public static function setAction( $action ){
        self::$action = $action;
    }
    public static function setURL( $url ){
        self::$url = $url;
    }
    public static function setIncValues( $valuees ){
        self::$inc_values = $valuees;
    }
    public static function setChkValues( $valuees ){
        self::$chk_values = $valuees;
    }
    public static function setWhere( $where ){
        self::$where = $where;
    }
    public static function generate(){
        $html        = self::load( self::$html_page);
        $html_head   = mb_substr( $html,0,strpos(strtoupper($html),'<TBODY>'));
        $body        = mb_substr( $html,strpos(strtoupper($html),'<TBODY>')+8,(strpos(strtoupper($html),'</TBODY>')-strpos(strtoupper($html),'<TBODY>'))-8);
        $html_body   = '';
        $html_footer = mb_substr( $html,strpos(strtoupper($html),'</TBODY>')+8);
        $where       = '';
        $filtro      = '';
        $flt_name    = 'flt_'.App::$modulo;
        $ordem       = !empty(App::$ordem) ? App::$ordem : self::$filters[0];
        
        //Defining Filters e Orders
        if(isset($_POST['filter'])):
            Session::setValue($flt_name,$_POST['filter']);
        endif;

        if(Session::getValue($flt_name)):
            $filtro   = Session::getValue($flt_name);
            if(self::$where)
                $where = ' where ( '.self::$where.' ) and ';
            else
                $where = ' where '; 
    
            $operador = '';
            foreach( self::$filters as $field ):
                $where .= $operador.$field." like '%".$filtro."%'";
                $operador = ' or ';
            endforeach;
        else:
            if(self::$where)
                $where = ' where '.self::$where;
        endif;
        
        $html_head   = str_replace('#FILTER#',$filtro,$html_head);
        $html_head   = str_replace('#OPTION#',Html::option( self::$orders,$ordem ),$html_head);
        
        $sql = self::$select.$where." order by ".$ordem;

        //Paginação
        $total       = count( R::getAll( $sql ) );
        $pagination   = Html::pagination(URL.App::$modulo.'/show/'.App::$key.'/'.App::$ordem,App::$page,$total,RPP);
        $offset      = (RPP*(App::$page-1));
        $ofsset      = $offset>$total ? 0 : $offset;

        $html_footer = str_replace('#PAGINATION#',$pagination,$html_footer);

        //Registers
        
        $sql .= " LIMIT ".RPP." OFFSET ".$offset;
        
        $regs = R::getAll( $sql ); 

        foreach( $regs as $reg):
            $new_body = $body;
            foreach( $reg as $name => $value ):
                $new_body = str_replace( '#'.strtoupper($name).'#',$value,$new_body);
            endforeach;
            $html_body .= $new_body;
        endforeach;

        //Ending up HTML
        $html = $html_head . '<tbody>' . $html_body . '</tbody>' . $html_footer ;
        
        return $html;
    }
    public static function form_generate(){

        $html = self::load( self::$html_page);
        
        if(self::$action=='update' or App::$action=='update'):
            $html = str_replace( '#ACTION#',self::$url.'/'.App::$key,$html);

            $reg = R::load(self::$table,App::$key);
            foreach( $reg as $name => $value ):
                if(in_array( strtoupper($name), self::$chk_values)):
                    $value = $value == 1 ? 'checked="checked"' : '';
                endif;
                $html = str_replace( '#'.strtoupper($name).'#',$value,$html);
            endforeach;
           
        else:
            if(self::$action=='insert' or App::$action=='insert'):
                $html = str_replace( '#ACTION#',self::$url,$html);
            endif;
            foreach( self::$inc_values as $name => $value ):
                $html = str_replace( '#'.strtoupper($name).'#',$value,$html);
            endforeach;
        endif;
        return $html;
    }
    public static function load( $file_html ){
        $file = PATH_HTML.$file_html;
		if(file_exists($file)):
            return file_get_contents($file);
        else:
            return 'File "'.$file.'" not found!';
        endif; 
    }
    
    public static function option( $options, $option ){
        $html = '';
        foreach($options as $value => $description ):
            $selected = $value==$option ? 'selected="selected"' : '';
            $html .= '<option value="'.$value.'" '.$selected.'>'.$description.'</option>';
        endforeach;
        return $html;
    }

    public static function optionByData( $dados,$indice, $option ){
        $html = '';
        foreach($dados as $reg ):
            $value = $reg[ $indice[0] ];
            $description = $reg[ $indice[1] ];
            $selected = $value==$option ? 'selected="selected"' : '';
            $html .= '<option value="'.$value.'" '.$selected.'>'.$description.'</option>';
        endforeach;
        return $html;
    }

    public static function check( $value ){
        $html = $value==1 ? 'checked="checked"' : '';
        return $html;
    }

    public static function pagination( $url,$page,$total,$rowbypg ){
        
        $html = '<ul>';
        
        if($page==1):
            $html .= '<li class="disabled"><a>&laquo;</a></li>';
        else:
            $html .= '<li><a href="'.$url.'">&laquo;</a></li>';
        endif;
        
        
        for( $count=1;$count <= ceil($total/$rowbypg);$count++ ):
            if($count==$page):
                $html .= '<li class="active"><a class="disabled">'.$count.'</a></li>';
            else:
                $html .= '<li><a href="'.$url.'/'.$count.'">'.$count.'</a></li>';
            endif;
        endfor;

        if($page==ceil($total/$rowbypg)):
            $html .= '<li class="disabled"><a>&raquo;</a></li></ul>';
        else:
            $html .= '<li><a href="'.$url.'">&raquo;</a></li></ul>';
        endif;

        return $html;
    }
    public static function set_markers( $html ){
        $html = str_replace('#URL#'      ,URL            ,$html);
        $html = str_replace('#MODULE#'   ,App::$module   ,$html);
        $html = str_replace('#ACTION#'   ,App::$action   ,$html);
        $html = str_replace('#KEY#'      ,App::$key      ,$html);
        $html = str_replace('#KEY_CHILD#',App::$key_child,$html);
        $html = str_replace('#ORDER#'    ,App::$order    ,$html);
        $html = str_replace('#PAGE#'     ,App::$page     ,$html);
        return $html;
    }
}


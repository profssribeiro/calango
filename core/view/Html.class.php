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
    private static $data=array();
    private static $rpp=10;
    private static $p1=1;
    
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
    public static function setRpp( $rpp ){
        self::$rpp = $rpp;
    }
    public static function setP1( $p1 ){
        self::$p1 = $p1;
    }
    public static function getData(){
        return self::$data;
    }

    public static function generate(){
        $html        = self::load( self::$html_page);
        $html_head   = mb_substr( $html,0,strpos(strtoupper($html),'<TBODY>')-2);
        $sub_body    = mb_substr( $html,strpos(strtoupper($html),'<TBODY>')+6);
        $body        = mb_substr($sub_body,0,strpos(strtoupper($sub_body),'</TBODY>'));
        $html_body   = '';
        $html_footer = mb_substr( $html,strpos(strtoupper($html),'</TBODY>')+7);
        $where       = '';
        $filtro      = '';
        $flt_name    = 'flt_'.App::$module;
        $order       = !empty(App::$order) ? App::$order : self::$filters[0];
        $rpp         = self::$rpp;

        //die(gettype($rpp).'>'.$pagination['RPP']);

        //Defining Filters e Orders
        if(isset($_POST['filter'])):
            Session::setValue($flt_name,$_POST['filter']);
        endif;

        if(Session::getValue($flt_name)):
            $filtro   = Session::getValue($flt_name);
            if(self::$where)
                $where = ' where ( '.self::$where.' ) and (';
            else
                $where = ' where ('; 
    
            $operador = '';
            foreach( self::$filters as $field ):
                $where .= $operador.$field." like '%".$filtro."%'";
                $operador = ' or ';
            endforeach;
            $where.=')';
        else:
            if(self::$where)
                $where = ' where '.self::$where;
        endif;
        
        $html_head   = str_replace('#FILTER#',$filtro,$html_head);
        $html_head   = str_replace('#OPTION#',Html::option( self::$orders,$order ),$html_head);
        
        $sql = self::$select.$where." order by ".$order;

        //Pagination
        $total       = count( R::getAll( $sql ) );
        $pagination  = Html::pagination(URL.App::$module.'/show/'.App::$key.'/'.App::$order,App::$page,$total,$rpp);
        $offset      = ($rpp*(App::$page-1));
        $ofsset      = $offset>$total ? 0 : $offset;

        $paginas = ceil( $total / $rpp );

        $s_pagination = self::setPagData($s_pagination, $paginas);

        $html_footer = self::setPagination($html_footer,App::$page,$paginas);

        //$html_footer = str_replace('#PAGINATION#',$pagination,$html_footer);

        //Registers
        
        $sql .= " LIMIT ".$rpp." OFFSET ".$offset;
        
        $regs = R::getAll( $sql );
        self::$data = $regs; 

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

        $html = self::load(self::$html_page);
        
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
      
        $html = '<select name="selectURL" class="form-select form-select-sm" onchange="window.open(document.nav.selectURL.options[document.nav.selectURL.selectedIndex].value,\'_self\')">';

        for( $count=1;$count <= ceil($total/$rowbypg);$count++ ):
            if($count==$page):
                $html.='<option value="'.$url.'/'.$count.'" selected> Pag '.$count.'</option>';
            else:
                $html.='<option value="'.$url.'/'.$count.'" > Pag '.$count.'</option>';
            endif;
        endfor;

        $html .= '</select>';


        return $html;
    }
    public static function set_markers( $html ){
		$pagination = Session::getValue( 'pagination' );
        $html = str_replace('#URL#'      ,URL            ,$html);
        $html = str_replace('#MODULE#'   ,App::$module   ,$html);
        $html = str_replace('#ACTION#'   ,App::$action   ,$html);
        $html = str_replace('#KEY#'      ,App::$key      ,$html);
        $html = str_replace('#KEY_CHILD#',App::$key_child,$html);
        $html = str_replace('#ORDER#'    ,App::$order    ,$html);
        $html = str_replace('#PAGE#'     ,App::$page     ,$html);
        $html = str_replace('#RPP#'      ,self::$rpp     ,$html);
        return $html;
    }
    public static function setPagination($html_footer,$page,$paginas){
        $pagination = Session::getValue(App::$module);
        $page=intval($page);

        $pg = self::$p1;

        if($pg==1):
            $html_footer = str_replace('#E-STATUS#' ,'disabled',$html_footer);
        else:
            $html_footer = str_replace('#E-STATUS#' ,'',$html_footer);
        endif;
        if($paginas<=10):
            $html_footer = str_replace('#E-HIDDEN#' ,'hidden',$html_footer);
            $html_footer = str_replace('#D-HIDDEN#' ,'hidden',$html_footer);
        else:
            $html_footer = str_replace('#E-HIDDEN#' ,'',$html_footer);
            $html_footer = str_replace('#D-HIDDEN#' ,'',$html_footer);
        endif;

        for( $p=1; $p<=10; $p++ ):
            
            $status = $pg == $page  ? 'active' : '';
            $hidden = $p > $paginas ? 'hidden' : '';

            $html_footer = str_replace('#P'.$p.'-STATUS#',$status,$html_footer);
            $html_footer = str_replace('#P'.$p.'-HIDDEN#',$hidden,$html_footer);
            $html_footer = str_replace('#P'.$p.'#'       ,$pg    ,$html_footer);
            $pg++;

        endfor;

        if($pg>$paginas):
            $html_footer = str_replace('#D-STATUS#' ,'disabled',$html_footer);
        else:
            $html_footer = str_replace('#D-STATUS#' ,'',$html_footer);
            $html_footer = str_replace('#D-HIDDEN#' ,'',$html_footer);
        endif;
        return $html_footer;
    }
    public static function setPagData($pagination, $paginas){
        if($pagination['PGS']<>$paginas):
            $pagination['PGS'] = $paginas;
            $pagination['EA'] = 1;
            $pagination['EB'] = $paginas>1 ? 2 : 0;
            $pagination['M']  = $paginas>2 ? 3 : 0;
            $pagination['DA'] = $paginas>3 ? 4 : 0;
            $pagination['DB'] = $paginas>4 ? 5 : 0;
            $pagination['D']  = $paginas>5 ? 6 : 0;
            Session::setValue( 'pagination',$pagination );
        endif;
        //die(var_dump($pagination).'<br>'.$paginas);
        return $pagination;
    }
}


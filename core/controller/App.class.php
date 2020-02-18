<?php

Class App{
    
    public static $module;
    public static $action;
    public static $key;
    public static $key_child;
    public static $order;
    public static $page;
    
    public function run(){
        
        $site='';
        
        //Database acess
        $geturl = array_filter(explode('/', $_SERVER['REQUEST_URI']));

        self::$module = isset($geturl[POS_MODULE])   ? $geturl[POS_MODULE]   : 'Main'   ;
        self::$action = isset($geturl[POS_MODULE+1]) ? $geturl[POS_MODULE+1] : 'show'     ;
        self::$key    = isset($geturl[POS_MODULE+2]) ? $geturl[POS_MODULE+2] : 'home' ;
        self::$order  = isset($geturl[POS_MODULE+3]) ? $geturl[POS_MODULE+3] : ''         ;
        self::$page   = isset($geturl[POS_MODULE+4]) ? $geturl[POS_MODULE+4] : '1'        ;

        if(strpos(App::$key, '&')!==false):
            $key = explode('&',App::$key);
            App::$key=$key[0];
            App::$key_child=$key[1];
        endif;
        
        if(App::$module=='code'):
            self::$module = isset($geturl[POS_MODULE+1]) ? $geturl[POS_MODULE+1] : '' ;
            self::$action = isset($geturl[POS_MODULE+2]) ? $geturl[POS_MODULE+2] : '' ;
            self::$key    = isset($geturl[POS_MODULE+3]) ? $geturl[POS_MODULE+3] : '' ;
            self::$order  = isset($geturl[POS_MODULE+4]) ? $geturl[POS_MODULE+4] : '' ;
            self::$page   = isset($geturl[POS_MODULE+5]) ? $geturl[POS_MODULE+5] : '1';

            if(strpos(App::$key, '&')!==false):
                $key = explode('&',App::key);
                App::$key=$key[0];
                App::$key_child=$key[1];
            endif;

            if(App::$module):
                if(class_exists(App::$module)):
                    $class_module = App::$module;
                    $obj_module=new $class_module;
                    if(method_exists($obj_module,App::$action)):
                        $action = App::$action;
                        $site = $obj_module->$action();
                    endif;
                endif;
            endif;
        else:
            $template = new Template();
            $site     = $template->run();
        endif;
        
        $site = Html::set_markers( $site );
        
        return $site;
        
    }    
}



<?php

class Template{

    public function run(){
        
        $html         = '';
        $html_content = '';
        
        if(file_exists(TEMPLATE)):
            $html   = file_get_contents(TEMPLATE);
			$layout = Session::getValue('LAYOUT');
			
			if($layout):
				foreach($layout as $classe => $marker):
					if(class_exists($classe)):
						$obj = new $classe;
						$html_marker = $obj->run();
						$html  = str_replace($marker,$html_marker,$html);
					endif;
				endforeach;
			endif;
			
            if(App::$module):
                if(class_exists(App::$module)):
                    $class_module = App::$module;
                    $obj_module=new $class_module;
                    if(method_exists($obj_module,App::$action)):
                        $action = App::$action;
                        $html_content = $obj_module->$action();
                    else:
                        $html_content = "<p>Action [".App::$action."] in module [".App::$module."] Not found !</p>";
                    endif;
                else:
                    $html_content = '<p>module: '.App::$module.' Not found</p>';
                endif;
            endif;
            $html  = str_replace('#CONTENT#',$html_content,$html);
        else:
            $html = '<p>File '.TEMPLATE.' Not found</p>';
        endif;

        return $html;
    }
}

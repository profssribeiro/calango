<?php

/** 
 * MAIN PROGRAM FOR SYSTEM INITIALIZATION
 * 
 * @author Sergio Ribeiro <professor@sergioribeiro.com.br> 
 * @version 1.0 
 * @copyright LGPLv3 � 2012. 
 * @package Calango Framework 
 * @link http://calango.sergioribeiro.com.br
 *
 * @access public
 * @name index.php 
 * @param nenhum
 * @return html
 *
 */ 

/** 
 * Function for system class autoload
 * 
 * @access public
 * @name __autoload 
 * @param String $class Class to be loaded
 * @return null
 *
 */ 
function __autoload($class){
    $folders = array('core/controller','core/model','core/view','core/plugin','controller','model','view','plugin');
    foreach($folders as $folder){
        if(file_exists( "{$folder}/{$class}.class.php" )){
            if(!class_exists($class)):
                include_once "{$folder}/{$class}.class.php";
            endif;
        }
    }
}

//Loading configurations
require_once("config/config.php");

//Running the main class ( Controller )
ob_start();
$app = new App;
$html = $app->run();
ob_end_clean();
echo $html; 
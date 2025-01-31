<?php

/** 
 * MAIN PROGRAM FOR SYSTEM INITIALIZATION
 * 
 * @author Sergio Ribeiro <profssribeiro@gmail.com> 
 * @version 2.0 
 * @copyright LGPLv3 (c) 2012. 
 * @package Calango Framework 
 * @link https://github.com/profssribeiro/calango
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
function autoload($className)
{
    $folders = array('core/controller','core/model','core/view','core/plugin','controller','model','view','plugin');
    foreach($folders as $folder){
        if(file_exists( "{$folder}/{$className}.class.php" )){
            require_once("{$folder}/{$className}.class.php");
        }
    }
}
spl_autoload_register('autoload');

//Loading configurations
require_once("config/config.php");

//Running the main class (Controller)
ob_start();
$app = new App;
$html = $app->run();
ob_end_clean();
echo $html; 
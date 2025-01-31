<?php

/** 
 * SYSTEM PROGRAM CONFIGURATION
 * 
 * @author Sergio Ribeiro <profssribeiro@gmail.com> 
 * @version 2.0 
 * @copyright LGPLv3 (c) 2012. 
 * @package Calango Framework 
 * @link https://github.com/profssribeiro/calango
 *
 * @access public
 * @name config.php 
 * @param nenhum
 * @return null
 *
 */ 

//Session intialization
Session::start(); 
 
// Error message
error_reporting(E_ALL);
ini_set('display_errors',true);

//Path and internal folders 
define('URL','http://'.$_SERVER['HTTP_HOST'].'/calango/');
define('DS', DIRECTORY_SEPARATOR);
define('PATH', getcwd().DS );
define('PATH_IMG',$_SERVER['DOCUMENT_ROOT'].'/calango/public/img/');
define('PATH_HTML',PATH.'view'.DS.'html'.DS);

//Application SMTP and E-Mail 
define('SMTP_SERVER','mail.domain.com.br');
define('SMTP_USER','contact@domain.com.br');
define('SMTP_PASS','pwd');
define('SMTP_PORT',25);
define('EMAIL_SITE','contact@domain.com.br');

//Database 
define('DB_DSN','mysql:host=localhost;dbname=domain_db');
define('DB_USERNAME','root');
define('DB_PWD','');

//Database acess
R::setup(DB_DSN,DB_USERNAME,DB_PWD);

//Encryption
define('SALT',substr(str_replace('+', '.', base64_encode(pack('N4', mt_rand(), mt_rand(), mt_rand(), mt_rand()))), 0, 22));

//General definitions
define('RPP',30);
define('POS_MODULE',2);
define('TEMPLATE', PATH_HTML.'template.html');

//Define Layout Structure
Session::setValue( 'LAYOUT',array( 'Header' => '#HEADER#','Footer' => '#FOOTER#' ) );

<?php
/**
 * Bios Framework
 *
 * 
 * Bios Framework is An open source application development framework for PHP
 *
 * LICENSE	
 * This source file is subject to the  License that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://biosq.com.ar/biosframework/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to pablogcardoso@gmail.com so we can send you a copy immediately.
 *
 * @package    Core
 * @copyright  Copyright (c) 2000-2017 Pablo Gaston Cardoso
 * @version    $Id$
 * @license    GPL License
 */

/* *********************************************************************
 *ESTE ARE DEPRECATED, NEW VERSION FRAMEWORK CHANGE THIS!! 
 *			IS ONLY DEMO INIT FILE
 *                THIS FILE NOT PASS THE STANDARS CODE STYLE
 *********************************************************************/


/*
 * ------------------------------------------------------
 *  DEFINE ENVIRONMENT
 *  
 *  1= production
 *  2= develop and testing
 * ------------------------------------------------------
 */
$_ENVIRONMENT_ = "2";


	
/*
 * ------------------------------------------------------
 *  SET HERE YOUR DEVELOP ENVAIROMENT CONFIG
 * ------------------------------------------------------
 */
$base_url = "http://www.demo.com.ar";

$WEB_DIRECTORY = '/';

$aplication = "";
/*systemPath*/
$systemPath = '../system';
// applicationFolder
$applicationFolder = 'app';
/** works with modules arquitecture**/
$modulesOn = 0;

/*
 * ------------------------------------------------------
 *  SET HERE YOUR PRODUCTION ENVAIROMENT CONFIG
 * ------------------------------------------------------
 */
$p_aplication="appName";
/*systemPath*/
$p_systemPath= '../system';
/*applicationFolder*/
$p_applicationFolder = 'app';
//var_dump(realpath(dirname(__FILE__) . '/../app'));
//die(dirname(__FILE__) . '../app');

/*
 * ------------------------------------------------------
 *  OTHER CONFIGURATIONS
 * ------------------------------------------------------
 */
$systemPath = realpath($systemPath).'/';
$systemPath = rtrim($systemPath, '/').'/';
$basePath = realpath('../app').'/';
$basePath = rtrim($basePath, '/').'/';

$webFolder = realpath('../web').'/';
$webFolder = rtrim($webFolder, '/').'/';
/*
 * ------------------------------------------------------
 *  DEFINE GLOBAL VARS according at CONFIGURATION SYSTEM
 *  todo: cabiar esto esta terriblemente mal!!!!!
 * ------------------------------------------------------
 */
if ($_ENVIRONMENT_ == 1) {	
	define('APP',$aplication);
	define('BASEPATH', str_replace("\\", "/", $systemPath));
    	define('APP_PATH', str_replace("\\", "/", $basePath));
    	define('WEB_PATH', str_replace("\\", "/", $webFolder));
	define('MYSELF', pathinfo(__FILE__, PATHINFO_BASENAME));
	define('WORKPATH', '/t8_dev/');
	define('SYSDIR', trim(strrchr(trim(BASEPATH, '/'), '/'), '/'));
	define('APPPATH', $applicationFolder.'/');
	define("STOP_INITIAL", "0");
	define("DEBUG", "0");
        define("MODULE_ON", $modulesOn);
        define("CACHE_ACTIVE", "0");
        define("BASE_URL",$base_url);
        define("WEB_DIRECTORY", $WEB_DIRECTORY);
} else {
	define('APP',$p_aplication);
	define('BASEPATH', str_replace("\\", "/", $systemPath));
        define('APP_PATH', str_replace("\\", "/", $basePath));
        define('WEB_PATH', str_replace("\\", "/", $webFolder));
	define('MYSELF', pathinfo(__FILE__, PATHINFO_BASENAME));
	define('WORKPATH', str_replace(SELF, '', __FILE__));
	define('SYSDIR', trim(strrchr(trim(BASEPATH, '/'), '/'), '/'));
	define('APPPATH', $applicationFolder.'/');
	define("STOP_INITIAL", "0");
	define("DEBUG", "1");
        define("MODULE_ON", $modulesOn);
        define("CACHE_ACTIVE", "0");
        define("BASE_URL",$base_url);

	//errors display only dev enviroments
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
}
/**
 * This funciton is used in some class for load forms and other clasess
 * @param type $clase
 */
function _loadClass($clase) {
    //implenent here
}
//Doctrine clasess
require_once BASEPATH."lib/vendor/autoload.php";

include_once BASEPATH."core/Application.php";

/****************************************************************/
$app = new Bios\Application($_ENVIRONMENT_, APPPATH . "config/app.xml");
$app->run();


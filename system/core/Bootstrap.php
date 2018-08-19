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
 * https://www.glosdigital.com/biosframework
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to pablogcardoso@gmail.com so we can send you a copy immediately.
 *
 * @package    Core
 * @copyright  Copyright (c) 2000-2014 Pablo Gaston Cardoso
 * @version    $Id$
 * @license    MIT License
 * @link       https://www.glosdigital.com/biosframework
 */
namespace Bios;

/**
 * @see requestHandler
 */
require_once 'Router.php';
/**
 * @see requestHandler
 */
require_once 'requestHandler.php';

require_once 'http/Response.php';

use App\Controller;
/**
 * Bootstrap class, is a critical class, this load controller and run the flow
 *
 * @package	Core
 * @author 	Cardoso Pablo Gaston
 * @copyright  	Copyright (c) 2000-2016 Pablo Gaston Cardoso
 * @version    	Release: @1.2@
 * @link       	http://www.glosdigital.com/biosframework
 * @since      	Class available since Release 1.2
 */
class Bootstrap
{
    /**
     *
     * @var Object 
     */
    private $_application;
    /**
     *
     * @var Object 
     */
    private $_pluginResources;
    /**
     *
     * @var Object 
     */
    private $_loader;
    /**
     *
     * @var Object 
     */
    private $_resources;
    /**
     *
     * @var array 
     */
    private $optionConfig;
    
    /**
     * @var Object
     */
    private $route;
    /**
     * @var Object
     */
    private $_response;
    /**
     * @var String
     */
    protected $_module;
    protected $_controller;
    protected $_action;
    
    /**
     * 
     * @param string $application
     */    
    public function __construct($application)
    {
	  $this->_application = $application;
    /**
    * @see Router class
    */
	  $this->route = new Router();
        $this->_response = new Http\Response();
        $this->route->setRouting();
        //$config = Config::getInstance()->getConfig();
    }
	
    /**
     * This method launch the flow of application
     */
    public function run()
    {
        $this->init();
        $classPath = $this->route->getClassPath();
        $module  = $this->route->getModule();
        $class  = $this->route->getController();
        $action = $this->route->getAction();
        Logger::instance()->log('info','bootstrap 0','module :: ' . $module);
        $className = $class;
        
        if (!file_exists('../'.APPPATH .'module/' . $module .'/'.$classPath .'.php')) {
            print $this->_response->showResponse(404);
            Logger::instance()->log('error','bootstrap 1','no encontro file: ' . '../'.APPPATH .'module/' . $module .'/'.$classPath .'.php');
            die();
        } else {
            require(BASEPATH.'core/Controller.php');
            require('../'.APPPATH .'module/' . $module .'/'.$classPath .'.php');
        }
        $class = 'App\Controller\\'.$class.'Controller';
       
        if (!class_exists($class))
        {
            echo Common::show404('', $class.' not found');
            Logger::instance()->log('error','bootstrap 2','no encontro clase: ' . $class);
            die('Controller\\'.$class.'Controller');
        }
        
        /*
         * ------------------------------------------------------
         *  Instantiate the requested controller and execute
         * ------------------------------------------------------
         */	
        $controller = new $class($this->route->getRequestData());
            if (method_exists($controller, $action)){
                Config::getInstance()->set('currentAction',$action);
                Config::getInstance()->set('currentModule',$module);
                Config::getInstance()->set('currentController',$className);
                $controller->$action();
                exit();
            }else{
                echo Common::show404($className, '');
                Logger::instance()->log('error','bootstrap 3','no encontro action: ' . $action);
                die();	
            }
	}
	/**
   * @todo implement code to load external plugins, see spec 1.2-core customs
            definitions in progress
   */
	private function _loadPlugins()
	{
	}
	/**
   * @todo definition in progress, see spec 1.2-core customs
   */
	protected function _setOptions()
	{
	}
  /**
   * @todo implement code to regist external plugins
   * @param string $name
   */
	protected function _registerPlugin($name)
	{
	}
  /**
   * for custom bootstrap
   */
  public function init()
  {}
	
}	
	
	
	

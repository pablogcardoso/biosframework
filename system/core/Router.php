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
 * http://www.glosdigital.com/biosframework/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to pablogcardoso@gmail.com so we can send you a copy immediately.
 *
 * @package    Core
 * @copyright  Copyright (c) 2000-2017 Pablo Gaston Cardoso
 * @version    $Id$
 * @license    MIT License
 */
namespace Bios;
/**
 *@see BF_Uri class 
 * 
 */
require_once 'Uri.php';

use Bios\Uri;
use Bios\Request\RequestHandler;
/**
 * RequestHandler class
 *
 * @package	Core
 * @author 	Cardoso Pablo Gaston
 * @copyright  	Copyright (c) 2000-2017 Pablo Gaston Cardoso
 * @version    	Release: @1.2@
 * @link       	http://www.glosdigital.com
 * @since      	Class available since Release 1.2
 */
class Router{
    /**
     *
     * @var type 
     */
    private $_routes = Array();
    /**
     *
     * @var type 
     */
    private $uri;
    /**
     *
     * @var type 
     */
    private $uriParams;
    /**
     *
     * @var type 
     */
    private $_module;    
    /**
     *
     * @var type 
     */
    private $_controller;
    /**
     *
     * @var type 
     */
    private $_action;    /**
     *
     * @var type 
     */
    private $method;
    /**
     *
     * @var type 
     */
    private $controllerPath;
    /**
     *
     * @var type 
     */
    private $_segments = Array();
    
    private $_controllerPath = 'controller/';
       
	/**
 	 * @version 2.0.2
 	 * Enter description here ...
 	 */
    public function __construct() 
    {
        $this->uri = new Uri();
        $this->request = new RequestHandler();
        $this->_routes = $this->_loadRoutes();
    }
    
    /**
     * Set basic routes
     * @return type
     */
    function setRouting()
    {
        $this->_segments = $this->uri->processUri();
        $this->_checkLanguage($this->_segments);
        $this->_checkRoutes($this->_segments);

        /**
         * /module/controller/action
         */
        if (Config::getInstance()->get('module_system')) {
            $this->_setComplexRoutes();
            return;
        }
        /**
         * if backend plugins is enabled and not use module_system enabled
         */
        if (Config::getInstance()->get('backend_plugin')) {
           $this->_checkBackendModule($this->_segments);
        }
        /**
         * /controller/action
         */        
        $this->_setStandarRoutes();
        
    }
    protected function _setStandarRoutes()
    {
        /**
         * query string disabled
         */
        if (!Config::getInstance()->get('QUERY_STRING')) {
            $this->_getRoutesByTriggers();
            return;
        }
        /**
         * QUERY STRING ENABLED
         * if No have params by url, Use default values, not validation needed
         */
        if (count($this->_segments) < 1) {
            $this->setModule($this->getModule());
            $this->setController($this->getDefaultController());
            $this->setAction($this->getDefaultAction());
            $this->setClassPath($this->getDefaultController());
            return;
        }
        /**
         * have almost one param
         */
        if (count($this->_segments) == 1) {
            $this->setModule($this->getModule());
            $this->setController($this->_segments[0]);
            $this->setAction($this->getDefaultAction());
            $this->setClassPath($this->_segments[0]);         
        } else if(count($this->_segments) > 1) {
            $this->setModule($this->getModule());
            $this->setController($this->_segments[0]);
            $this->setAction($this->_segments[1]);
            $this->setClassPath($this->_segments[0]);
            
        }   
    }
    /**
     * Set Complex routes
     * @return none
     */    
    protected  function _setComplexRoutes()
    {
        /**
         * query string disabled
         */
        if (!Config::getInstance()->get('QUERY_STRING')) {
            $this->_getRoutesByTriggers();
            return;
        }
        /**
         * if No have params by url, Use default values, not validation needed
         */
        if (count($this->_segments) < 1) {
            $this->setModule(Config::getInstance()->get('defaultModule'));
            $this->setController($this->getDefaultController());
            $this->setAction($this->getDefaultAction());
            $this->setClassPath($this->getDefaultController());
            return;
        }
        /**
         * have almost one param
         */
        if (count($this->_segments) == 1) {
            $this->setModule($this->_segments[0]);
            $this->setController($this->getDefaultController());
            $this->setAction($this->getDefaultAction());
            $this->setClassPath($this->getDefaultController());
            // $this->validateRequest();
            
        } else if(count($this->_segments) > 1) {
            $this->setModule($this->_segments[0]);
            $this->setController($this->_segments[1]);
            $this->setAction($this->_segments[1]);
            $this->setClassPath($this->_segments[0]);
            
        }  else if (count($this->_segments) > 2)  {
            $this->setModule($this->_segments[0]);
            $this->setController($this->_segments[1]);
            $this->setAction($this->_segments[2]);
            $this->setClassPath($this->_segments[1]);
        }
    }
    /**
     * Set Mudule, controller, etc based on triggers, for example:
     * url.php?m=module&c=contronller&a=action.
     * Is a optional use of routing
     * @return none
     */ 
    protected function _getRoutesByTriggers()
    {
        $this->setModule($_GET[Config::getInstance()->get('module_trigger')]);
        $this->setController($_GET[Config::getInstance()->get('controller_trigger')]);
        $this->setAction($_GET[Config::getInstance()->get('action_trigger')]);
        $this->setClassPath($_GET[Config::getInstance()->get('controller_trigger')]);

    }
    /**
     * The system support default backend, and make special routing for that
     * @param  Array $segments
     * @return none
     */ 
    protected function _checkBackendModule($segments)
    {
        $backend = Config::getInstance()->get('backend_module_name');
               
        if (is_array($segments) && count($segments) > 0) {
            if ($backend == $segments[0]) {
                $backend = array_shift($segments);
                $this->setModule($backend);
                $this->_segments = $segments;
                return;    
            }
        }
    }
    /**
     * The system support default backend, and make special routing for that
     * @param  Array $segments
     * @return none
     */
    protected function _checkRoutes($segments)
    {
        $routes = $this->_routes;//Config::getInstance()->get('routes');
        $max = count($segments);
        if ($max === 1){
            foreach ($routes as $key=>$value) {
                if ($key == $segments[0]) {
                    $segments[0] = $value;
                }
            }   
        } else {
            $url = $this->_createUrl($segments);
            foreach ($routes as $key=>$value) {
                if ($key === $url) {
                    $url = $value;
                }
            }
            $segments = $this->uri->explodeUrl($url);
        }
        
        $this->_segments = $segments;
    }

    protected function _createUrl($array)
    {
        $max = count($array);
        $url = '';
        for($i=0; $i<$max; $i++) {
            $url .='/';
            $url .= $array[$i];
        }
        return $url;
    }

    public function _checkLanguage($segments)
    {
        $actualLanguage = Config::getInstance()->get('langs');
        if (is_array($segments) && count($segments) > 0) {
            $max = count($actualLanguage);
            for ($i=0; $i<$max;$i++) {
                
                if ($actualLanguage[$i] == $segments[0]) {
                    $lang = array_shift($segments);
                    Config::getInstance()->set('currentLanguage',$lang);
                    $this->_segments = $segments;
                    return;
                }
            }
        }
        $lang = Config::getInstance()->get('defaultLanguage');
        Config::getInstance()->set('currentLanguage',$lang);
    }
   
    /**
     * @version 2.0.2
     * @return type
     */
    public function getRequestData() {
        return $this->request->getRequest($this->_segments);
    }
    /**
     * 
     */
    private function isQueryStringRequest() {
      if (Config::getInstance()->get('QUERY_STRING') === TRUE && isset($_GET[Config::getInstance()->get('controller_trigger')])) {
            $module_t = Config::getInstance()->get('module_trigger');
            $controller_t = Config::getInstance()->get('controller_trigger');
            $method_t = Config::getInstance()->get('method_trigger');
            if (isset($_GET[$module_t])) { 
                //antes se filtra
                $this->setModule($this->uri->filterUrl($_GET[$module_t]));
                $this->_segments[] = $this->getModule();
            }                
            if (isset($_GET[$controller_t])) { 
               //antes se filtra

                $this->setController($this->uri->filterUrl($_GET[$controller_t]));
                $this->_segments[] = $this->getController();
            }
            if (isset($_GET[$method_t])) { 
                //antes se filtra
                $this->setAction($this->uri->filterUrl($_GET[$method_t]));
                $this->_segments = $this->getAction();
            }
        }
    }
    /**
     * 
     * @param type $value
     */
    private function validateRequest($value){
        $this->validateModulePath();
        $this->vaidateControllerPath();
        $this->validateParams(); 
    }
    /**
     * 
     * @param string $value
     */
    private function setModule($value)
    {
        Config::getInstance()->set('currentModule', $value);
        $this->_module = $value;
    }
    /**
     * 
     * set current class 
     * @param string $value
     */
    private function setController($value){
        $name = $this->_processControllerName($value);
        $this->_controller = $name;
    }
    /**
     * Set a current Action
     * 
     * @param string $value
     */
    public function setAction($value)
    {
        if (isset($value) && !empty($value)){
            $this->_action = $value;
            $value = $this->_processActionName($value) .'Action';
            Config::getInstance()->set("CurrentMethod",$value);
            $this->_action = $value;
        } else {
            $this->_action = $this->_processActionName($this->getDefaultAction()) .'Action';
        }
    }
    /**
     * Set default Action
     */
    private function setDefaultAction() {
        $this->_action = $this->getDefaultAction();    
    }
    /**
     * Normalize action names.
     */
    private function _processActionName($value) 
    {
        $parts = explode("-", $value);
        $max = count($parts); 
        if ($max === 0) return $value;
        for ($i = 0; $i<$max; $i++) {
            if ($i != 0)
            $parts[$i] = ucwords($parts[$i]);
        }
        $name = implode($parts);
        return $name;
        
        return $value;
    }
    /**
     * Set Module Path
     */
    private function validateModulePath() {
        if ($this->getModule()){
            $this->modulePath = MODULE_PATH . strtolower($this->getModule());
            if (!file_exists(APPPATH.$this->modulePath)) {
                $this->modulePath = '';
            }
        }
    }
    /**
     * 
     */
    private function vaidateControllerPath() {
        if ($this->getController()) {
            $this->controllerPath = $this->$_controllerPath . strtolower($this->getController());
            if (!file_exists(APPPATH.$this->controllerPath . '.php')) {
                $this->controllerPath = '';
                //show_error_404($this->getController());
            }
        } else {
            $this->controllerPath = $this->getDefaultController();
        }
    }
    /**
     * 
     * @return type
     */
    private function validateParams() {
        $this->p = $this->uri->getUriParams();
        return;
        if ($this->getModule()) {
            if (count($GET) >3) {
                //PROCESS PARAMS
                return;
            }
        } else { 
            if (count($GET)>2) {
                //PROCESS PARAMS
                return;
            }
        }
        return;
    }
    /**
     * 
     * @param type $value
     * @return type
     */
    private function _processControllerName($value) 
    {
        $parts = explode("-", $value);
        $max = count($parts); 
        if ($max > 1) {
           for ($i = 0; $i<$max; $i++) {
               $parts[$i] = ucwords($parts[$i]);
           }
           $name = implode($parts);
           return $name;
        } 
        return $value;
    }
    /**
     * 
     * @param type $value
     * @return type
     */
    private function _setCurrentViewPath($value)
    {
        $parts = explode("-", $value);
        $max = count($parts); 
        if ($max > 1) {
            return $value;
        } else {
            $name = strtolower(preg_replace('/([A-Z])/', '-$1', $value));
            $name = trim($name,'-');
            return $name;
        }     
    }
    /**
     * 
     * @param type $value
     */
    private function setClassPath($value)
    {
        $name = $this->_processControllerName($value);
        $this->controllerPath = $this->_controllerPath . $name;
        Config::getInstance()->set("CurrentClassPath",$this->controllerPath);
        $nameView = $this->_setCurrentViewPath($value);
        Config::getInstance()->set("CurrentViewPath",$nameView);
    }
    /**
     * Loas if exist routes in route.php file
     * @return array
     */
    protected function _loadRoutes()
    {
        if (file_exists(APP_PATH .'config/routes.php')) {
            return require_once(APP_PATH .'config/routes.php');
        } else {
            return array();
        }
    }
    /**
     * 
     * @return array
     */
    protected function getRoutes()
    {
        return $this->_routes;
    }
    /**
     * 
     * @return string module name
     */
    public function getModule()
    {
        if (null === $this->_module) {
            return $this->getDefaultModule();
        }
        return $this->_module;
    }
    /**
     *
     * return current controller class name
     * @return string controller class name
     */
    public function getController()
    {
        return $this->_controller;
    }
    /**
     * return Controllerr class path
     * @return string path of controller class
     */
    public function getClassPath()
    {
        return $this->controllerPath;
    }	
    /**
     * 
     * get current action invoked
     * @return string action name
     */
    public function getAction()
    {
        return $this->_action;
    }

    /**
     * Return default app path
     * @todo it necesarry check if it is deprecated
     * @return string
     */
    public function getDefaultApp()
    {
        return APPPATH;
    }
    /**
     * return dafault controller, get it from config
     * @return string
     */
    public function getDefaultController()
    {
        return Config::getInstance()->get('defaultController');
    }
    /**
     * Return default Module
     * @return string
     */
    public function getDefaultModule()
    {
        return Config::getInstance()->get('defaultModule');
    }
    /**
     * Return default action
     * @todo this default action must be configurable
     */
    public function getDefaultAction()
    {
        return  "index";
    }
    /*
     * Get filter
     * @todo it necesarry check if it is deprecated
     * @return object 
     */
    private function _filter($value){
        return $value;
    }

    /**
     * __toString standar function
     @return string
     */
    public function __toString()
    {
        echo "Intancia de Router";
    }
        
    /**
     * mock data for test core app
     * @todo it necesarry check if it is deprecated
     */
    private function setMockDataConfig() {

    }
}

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
 * @copyright  Copyright (c) 2000-2018 Pablo Gaston Cardoso
 * @version    $Id$
 * @license    GPL License
 */
namespace Bios;
/**
 * 
 * View class
 *
 * @package	Bios
 * @author 	Cardoso Pablo Gaston
 * @copyright  	Copyright (c) 2000-2018 Pablo Gaston Cardoso
 * @version    	Release: @1.3@
 * @link       	http://www.pablogcardoso.com
 * @since      	Class available since Release 1.2
 */
class View
{
    /**
     *
     * @var string 
     */
    protected $content;
    /**
     *
     * @var array 
     */
    protected $js = array();
    /**
     *
     * @var array 
     */
    protected $css = array();
    /*
     * @var array 
     */
    protected $img = array();
    /**
     *
     * @var array 
     */
    protected $param;
    /**
     *
     * @var int 
     */
    protected $cache_expiration	= 0;
    /**
     * Contructor of class
     */
    function  __construct()
    {
        $this->translate = Translate::getInstance();
    }

    /**
     * Convert string delimited by commas to array, If error exist, return false
     * @param string $data
     * @return false | array
     */
    public function convertToArray($data)
    {
        if(!empty($data) AND !is_array($data)){
                $arrayData = explode(",",$data);
                return $arrayData;
        }else{
                return false;
        }
    }
    /**
     * Return view
     * @param string $view
     * @param array $params
     * @return html
     */
    function displayHtml($view = null, $param = null)
    {
        $this->param = $param;

        if ($view === null) {
            $actualView = 'index';
        } else {
            $actualView =$view;
        }
        $this->check_cache();

        ob_start();

        if (file_exists($this->fullPath($actualView))) {
            require($this->fullPath($actualView));
        } else {
            return Common::visualError($actualView, "Requested view not found:<BR/>".$this->fullPath($actualView));
        }
        $this->content = ob_get_contents();

        //end_cache();
        ob_end_clean();
        return $this->content;
    }
    
    /**
     * 
     * @param string $view
     * @param array $param
     * @return string web view
     */
    function displayLayout($view = null, $param = null)
    {
        $this->param = $param;

        if ($view === null) {
                $actualView = 'index';
        } else {
                $actualView =$view;
        }
        $this->check_cache();

        ob_start();

        if (file_exists($this->fullPathLayout($actualView))) {
                require($this->fullPathLayout($actualView));
        } else {
                return Common::visualError($actualView, "NO SE ENCONTRO LA VISTA ASOCIADA:<BR/>".$this->fullPath($actualView));
        }
        $this->content = ob_get_contents();
        
        //end_cache();
        ob_end_clean();
        return $this->content;
    }
    
    /**
     * It is for print json view
     * @param string $out
     * @return string
     */
    function displayJson($out = null)
    {
        if ($out === null) {
            print('{}');
        } else {
            return $out;
        }
    }
    
    /**
     * Print partial code, used with layout container
     * @param array $params
     */
    public function contentBlock($params = null)
    {
        if ($params === null) {
            $param = $this->param;
        } else {
            $param = $params;
        }

        print($param['content']);
    }
    
    /**
     *
     * Insert fragment code with logic o not logic
     * @param string $name
     * @param array $params
     * example: 
     *        "_head_partials/".$name
     */
    public function partialBlock($name, $params = null)
    {
        if ($params === null) {
                $param = $this->param;
        } else {
                $param = $params;
        }
        $path = $this->fullPath($name);
        include($path);
    }
    /**
     * 
     * @param string $name
     * @param array $params
     */
    public function componentBlock($name, $params = null)
    {
        if ($params === null) {
            $param = $this->param;
        } else {
            $param = $params;
        }
        $path = $this->fullComponentPath($name);
        if ($path) {
            include($path); 
        }
    } 
    /**
     * Return web param, if not exits return clear string
     * @param string $param
     * @param string $second
     * @return string
     */
    public function getParam($param, $second = null)
    {
        if (null == $this->param) return '';
        if (null !== $second) {
            $one_level = array_key_exists($param, $this->param) ? $this->param[$param] : '';
            if ('' !== $one_level) {
                $second_level = array_key_exists($second, $one_level) ? $this->param[$param][$second] : '';
                return $second_level;
            }
            return '';
        }
        return array_key_exists($param, $this->param) ? $this->param[$param] : '';
    }
    /**
     * Create cache from current request
     *
     * @todo it method need to be tested
     *       Also need use execptions whene manage files
     *       For this reason the cache system it not implemented yet
     * @param string $out
     * @return string
     */
    private function _putCache($out)
    {
        $cache_path = Config::getInstance()->get('cache_path');
        $url = Config::getInstance()->get('currentUrl');
        $urlbase = Config::getInstance()->get('currentBaseUrl');
        $url = $urlbase . $url;
        $cache_path .= md5($url);
        if ( ! $fp = @fopen($cache_path, 'w')) {
            return;
        }

        $expire = time() + ($this->cache_expiration * 60);

        if (flock($fp, LOCK_EX))
        {
            fwrite($fp, $out);
            flock($fp, LOCK_UN);
        } else {
            return;
        }
        fclose($fp);
        @chmod($cache_path, FILE_WRITE_MODE);
        
    }
    
    /**
     * Return page cached from cache directory
     * @todo it method need to be tested
     *       Also need use execptions whene manage files
     *       For this reason the cache system it not implemented yet
     * @return string
     */
    private function _getCache()
    {
        $cache_path = Config::getInstance()->get('cache_path');
        $url = Config::getInstance()->get('currentUrl');
        $urlbase = Config::getInstance()->get('currentBaseUrl');
        $url = $urlbase . $url;
        $cache_path .= md5($url);
        
        if ( ! @file_exists($filepath)) {
            return FALSE;
        }

        if ( ! $fp = @fopen($filepath, FOPEN_READ)) {
            return FALSE;
        }

        flock($fp, LOCK_SH);

        $cache = '';
        if (filesize($filepath) > 0) {
            $cache = fread($fp, filesize($filepath));
        }

        flock($fp, LOCK_UN);
        fclose($fp);
        
        return $cache;
    }
    /**
     * @todo it method need to be implemented & tested
     *       Also need use execptions whene manage files
     *       For this reason the cache system it not implemented yet
     */
    private function check_cache()
    {
    }
	
    /**
     * @todo it method need to be implemented & tested
     *       Also need use execptions whene manage files
     *       For this reason the cache system it not implemented yet
     */
    private function end_cache()
    {
    }
    
    /**
     * Return full path of layout
     * @param string $view
     * @return string 
     */
    private function fullPathLayout($view)
    {
        $cf = Config::getInstance()->getConfig();
        if (!isset($view) || empty($view)) {
            $view =  'layout/layout';
        }
        $module = array_key_exists('currentModule',$cf) ? $cf['currentModule'] : 'frontend';
        
        $path = APP_PATH . '/module/'. $module . '/view/' . $view .'.phtml';
       
        return ($path);   
    }
    
    /**
     * FullPath of current view
     * @param string $view
     * @return string (path)
     */
    private function fullPath($view)
    {
        $cf = Config::getInstance()->getConfig();
        if (!isset($view) || empty($view)) {
            
            $view = array_key_exists('currentAction',$cf) ? mb_substr($cf['currentAction'], 0,-6) : 'index';
        }
         
        $folderView = array_key_exists('CurrentViewPath',$cf) ? $cf['CurrentViewPath']: 'index';
        $module = array_key_exists('currentModule',$cf) ? $cf['currentModule'] : 'frontend';
        
        $path = APP_PATH . '/module/'. $module . '/view/' . $folderView. '/'. $view .'.phtml';
       
        return ($path);
    }
    
    /**
     * 
     * @param string $view
     * @return false | string
     */
    protected function fullComponentPath($view = null)
    {
        $cf = Config::getInstance()->getConfig();
        if (null === $view || empty($view)) {
           return false;
        }
        $module = array_key_exists('currentModule',$cf) ? $cf['currentModule'] : 'frontend';
        $path = APP_PATH . '/module/'. $module . '/view/'. $view;
        return ($path);
    }
    /**
     * 
     * @param string $key
     * @return string
     */
    public function translate($key)
    {
        return $this->translate->get($key);
    }
    
    /**
     * @todo check if is deprecated, and functional
     * @param string $view
     * @return string xml formatted
     */
    function displayXml($view=null)
    {
        return false;
    }
    /**
     * 
     * @param array $params
     * @return string url
     */
    public function url($params = array()){
        
        $url =  BASE_URL;
        if (!is_array($params)) {
            return $url . '/'. $params;
        }
        if (array_key_exists('module', $params)) {
            $url .=  '/' . $params['module'];
        }
        if (array_key_exists('controller', $params)) {
            $url .= '/' . $params['controller'];
        }
        if (array_key_exists('controller', $params)) {
            $url .= '/' . $params['action'];
        }
        if (array_key_exists('params', $params)) {
            $url .= '/' . $params['params'];
        }
        return $url;
    }
   
    /**
     * Return base url
     * @todo This method will be moved to viewHelper
     * @return string
     */
    public function baseUrl()
    {
        return BASE_URL . '/';
    }

    /**
     * Return valid hmlt link
     * @todo This method will be moved to viewHelper
     * @param string $action
     * @param string $text
     * @return string <a> link
     */
    public function link($action, $text, $class = '')
    {
        return '<a href="'.BASE_URL . '/'. $action . '" class="'.$class.'">' . $text . '</a>';
    }

    /**
     *
     * @return string
     */
    public function __toString()
    {
        return get_class($this);
    }
    /**
     * Return current section/action  
     * 
     * @return string
     */
    public function getRequestAction()
    {
        $cf = Config::getInstance()->getConfig();
        $view = array_key_exists('currentController',$cf) ? $cf['currentController'] : 'index';
   
        return $view;
    }
}

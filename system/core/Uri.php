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
 * @copyright  Copyright (c) 2000-2014 Pablo Gaston Cardoso
 * @version    $Id$
 * @license    GPL License
 */
namespace Bios;
/**
 * Uri class
 *
 * @package	Core
 * @author 	Cardoso Pablo Gaston
 * @copyright  	Copyright (c) 2000-2014 Pablo Gaston Cardoso
 * @version    	Release: @1.2@
 * @link       	http://www.biosq.com.ar
 * @since      	Class available since Release 1.2
 */

class Uri{

    /**
     *
     * @var string 
     */
    private $queryString = null;
    /**
     *
     * @var string 
     */
    private $prefix_url = null;
    /**
     *
     * @var string 
     */
    private $partUri = null;
    /**
     *
     * @var array 
     */
    private $uriSegments = array();
    /**
     * Contructor
     */
    function __construct()
    {
        //$Security class not implemented yet
        //$this->processUri();
       // Bf_Config::setParameter('protocolUri','AUTO');
    }
    
    /**
     * @todo not migrated completed yet
     */
    function processUri()
    {
        if (php_sapi_name() == 'cli' || defined('STDIN')){
                $this->setUri($this->getUriCli());
                return $this->uriSegments;
            }
        /**
         * llamadas REST/SOAP
         * @TODO validation method
         */

        /**
         * this check url (.htacces)
         */
        $uri = $this->_uriDetect();
        if ($uri) {
             $this->setUri($uri);
             return $this->uriSegments;
        }
    }
    
    /**
     * @todo not migrated yet
     *      che if is necessary netbeans-xdebug
     *@return false | string
     */
    private function _uriDetect() {
        if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] != '') { 
            
            $xdebug = '?XDEBUG_SESSION_START=netbeans-xdebug';
            $url =  $_SERVER['REQUEST_URI'];
            /**
             * if exist xdebug query quit that for process params
             */
            if (strpos($url, $xdebug)) {
                $url = strstr($url, $xdebug, true);
            }
            
            /**
             * if the index.php exists, I remove it
             */
            if (strpos($url, 'index.php')) {
                 $p = explode('index.php', $url );
                 $url = $p[1];

            }

            $url =  preg_split('#\?#i', $url, 2);
            if (count($url) > 1) {
                $this->queryString = $url[1];
            } else {
                $this->queryString = $url[0];
            }
        
            return $this->queryString;
        } else {
            return false;
        }
    }
    /**
     * @param string $uri
     * @todo not migrated yet
     */
    public function setUri($uri) {
        $this->explodeUri();
    }
   /**
     * this maybe fail with conventional url
     * @todo not migrated yet
     * cambiar preg_math
     */
    private function explodeUri(){
        $pParts =  explode("/",$this->queryString);
        $cant = count($pParts);
        
        for($i=0; $i<$cant;$i++){
            $pPart = trim($this->filterUri($pParts[$i]));
            if ($pPart != '') {
                $this->uriSegments[] = $pPart;
            } 
        }
    
    }
    public function explodeUrl($url){
        $pParts =  explode("/",$url);
        $cant = count($pParts);
        $segments = array();
        for($i=0; $i<$cant;$i++){
            $pPart = trim($this->filterUri($pParts[$i]));
            if ($pPart != '') {
                $segments[] = $pPart;
            } 
        }
        return $segments;
    }
    /**
     * 
     * @param type $url
     * @param type $charPermitted
     * @return boolean
     */
    private function checkUriMalisuosus($url,$charPermitted){
        $pattern = "|^[".str_replace(array('\\-', '\-'), '-', preg_quote($charPermitted, '-'))."]+$|i";
        if (!preg_match($pattern, $str)) {
                return TRUE;
        }
        return false;
    }
    /**
     * 
     * @param type $data
     * @return type
     */
    private function filterUri($data){
        $cf = Config::getInstance()->getConfig();
        $charPermitted = array_key_exists('uri', $cf) ? $cf['uri']["permitted_uri_chars"] : '';
        $queryUrlPermitted = array_key_exists('uri', $cf) ? $cf['uri']["enable_query_strings"] : '';
        
        if ($charPermitted != '' && !$queryUrlPermitted != '') {
            if( $this->checkUriMalisuosus($this->uri, $charPermitted)) die("ERROR, MALISUIOSUS CHAR FOUNDED"); 
        }
        //Convert special chars
        $urlBad	= array('$', '(', ')', '%28', '%29');
        $urlPass	= array('&#36;', '&#40;', '&#41;', '&#40;', '&#41;');
        return str_replace($urlBad, $urlPass, $data);
    }

    /**
     * 
     * @return type
     */
    private function getUriCli()
    {
        $args = array_slice($_SERVER['argv'], 1);
        return $args ? '/' . implode('/', $args) : '';
    }
    /**
     * if you configure apache for rewrite url this is the correct option
     * But if not, you use this with the conventional form.
     *
     * @return string
     */
    private function getUriQueryString(){
        $uri =  (isset($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : @getenv('QUERY_STRING');
        if (trim($uri, '/') != '')
        {
            return $uri;
        }
    }

    /**
     * 
     * @todo not migrated yet
     * @param unknown_type $param
     * @param unknown_type $value
     */
    function setUriParams($param, $value)
    {
        return $this->uriData["params"];
    }
    /**
     * 
     */
    function setPostParameters()
    {
        if (is_array($_POST) && count($_POST) >= 1) {
            //if($Security->checkPost($_POST)===true){
            $this->post_vars = array_merge((array)$this->post_vars, (array)$_POST);
            //}else{
                    //$this->post_vars ==null;
            //}
        } else {
                $this->post_vars == null;
        }
    }
    /**
     * 
     * 
     * @param string $param
     * @return false | string
     */
    public function getPostParam($param)
    {
        if (array_key_exists($param, $this->post_vars)) {
            return $this->post_vars[$param];
        } else {
            return false;
        }
    }
    /**
     * 
     * return array prams
     */
    public function getPostParams()
    {
        return $this->post_vars;
    }
    /**
     * 
     * 
     * @param string $param
     * @param string $value
     * @param boolean $rewrite
     */
    public function setPostParam($param, $value,$rewrite=true)
    {
        if ($rewrite === true) {
            $this->post_vars[$param] = $value;
            return true;
        } else {
            if (!array_key_exists($param, $this->post_vars)) {
                $this->post_vars[$param] = $value;
            } else {
               return false;
            }
        }
    }
    /**
     * 
     * @todo this method have some error, is called from controller class
     *        in controller class was commented
     *        check release notes & issues for more details
     */
    function getUriParams()
    {
//            if ($this->uriData["params"]) {
//                    return false;
//            } else {
//                    return $this->uriData["params"];		
//            }
    } 	
  
}


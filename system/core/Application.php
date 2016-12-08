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

require_once 'Logger.php';
/**
 * @see Biosq_Common
 */
require_once 'Common.php';
        
/**
 * @see Biosq_loader
 */
require_once 'Loader.php';

/**
 * @see Config class
 */
require_once 'Configs.php';

/**
 * @see Translate class
 */
require_once 'Translate.php';

/**
 * This class represent de app in the system
 *
 * @package    Core
 * @author Cardoso Pablo Gaston
 * @copyright  Copyright (c) 2000-2014 Pablo Gaston Cardoso
 * @license GPL License
 * @version    Release: @1.2.0@
 * @link       http://www.glosdigital.com/biosframework
 * @since      Class available since Release 1.2.0
 */	
class Application
{
    /**
     *
     * @var Object 
     */                                
    protected $_loader;
    /**
     * $_bootstrap contain instance of current boostrap
     * 
     * @var Object 
     */
    protected $_bootstrap;
    /**
     *
     * @var array 
     */	
    protected $_configOptions;
    /**
     * $cfig contain config instance
     * 
     * @var Object 
     */	
    protected $cfig;//cambiar poner _ en el nombre
	
    /**
     * 
     * @param type $environment
     * @param type $config
     */
    public function __construct($environment = null, $config = null)
    {
        $this->_setBootstrap('core/Bootstrap.php', null);
    }
    
    /**
     * 
     * @param type $configs
     * @deprecated since version 1.1.2
     */
    protected function _setConfig()
    {
    }
    
    /**
     * Set default bootstrap 
     * @param type $path
     * @param string $class
     */
    protected function _setBootstrap($path, $class)
    {
        if ($class === NULL) {
            $class = 'Bios\Bootstrap';
        }
        if (!class_exists($class, false)) {
            if(file_exists(BASEPATH.$path)){
                require_once(BASEPATH.$path);
            } else {
                throw new \Exception('Bootstrap error - file not found'); 
            }	
            if (!class_exists($class, false)) {
                throw new \Exception('Bootstrap error - class not found'); 
            }
		}
		$this->_bootstrap = new $class(APP);
    }

    /**
     *
     * @deprecated since version 1.1
     * @return type
     */
    protected function _getLoader()
    {
        return $this->_loader;
    }
    
    /**
     * @deprecated since version 1.0
     * @return type
     */
    public function getBootstrap()
    {
        return $this->_bootstrap;
    }
	
    /**
     * This start the flow of Application
     */
    public function run()
    {
        $this->_bootstrap->run();
    }
}

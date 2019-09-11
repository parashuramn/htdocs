<?php
/**
* GrassBlade Learning Record Store: GrassBlade LRS v2.2.1
* This product is under Commercial License as described in LICENSE.txt. 
* However, the CakePHP core library used in the product is under MIT License. 
* @copyright     Copyright (c) Next Software Solutions. (http://www.nextsoftwaresolutions.com/)
* @link          https://www.nextsoftwaresolutions.com/ Next Software Solutions
* @since         GrassBlade LRS v1.0
* @license       Commercial
*/

define('APP_DIR', 'app');
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__));
define('WEBROOT_DIR', 'webroot');
define('WWW_ROOT', ROOT . DS . APP_DIR . DS . WEBROOT_DIR . DS);

if (!defined('CAKE_CORE_INCLUDE_PATH')) {
	define('CAKE_CORE_INCLUDE_PATH', ROOT . DS . 'lib');
}

require APP_DIR . DS . WEBROOT_DIR . DS . 'index.php';

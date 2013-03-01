<?php
/*
 *	This is MyCMS. Flex. By Michael Niles. Blindmikey.com
 */
  
// State Definitions
	if ( !defined('ABSPATH') ) {
		define('ABSPATH', dirname(__FILE__) . '/');
	}
	if ( !defined('COREPATH') ) {
		define('COREPATH', ABSPATH . 'core/');
	}
	if ( !defined('CONTROLLERPATH') ) {
		define('CONTROLLERPATH', COREPATH . 'controller/');
	}
	if ( !defined('CLASSPATH') ) {
		define('CLASSPATH', COREPATH . 'classes/');
	}
	if ( !defined('VIEWPATH') ) {
		define('VIEWPATH', COREPATH . 'views/');
	}
	if ( !defined('THEMEPATH') ) {
		define('THEMEPATH', ABSPATH . 'content/themes/');
	}
	
// Determine Error Reporting
	error_reporting(-1); // default
	function errors($arg = null) { // user switch
		if ($arg) {
			error_reporting(-1);
			return true;
		}
		else {
			error_reporting(0);
			return false;
		}
	}
	
// Instantiate Flex
	require(CONTROLLERPATH .'Flex.php');
	$Flex = new Flex();
	
	if (!$Flex->is_installed()) {
		$Flex->install();
	}
	elseif (isset($in_admin)) {
		$Flex->displayAdmin();
	}
	else {
		require(COREPATH . 'userfunc.php');
		$Flex->display();
	}
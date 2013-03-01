<?php
/*
 *	User-End Simple Functions. 
 */
	
	/**
	 * Includes the Header
	 *
	 * @access 	public
	 * @param	string $arg Optional. Will get header-{$arg}.php instead
	 * @return 	null
	 */
	function the_header($arg = null) {
		if ($arg) {
			include(CURRENTTHEMEPATH .'header-'.$arg.'.php'); 
		}
		else {
			include(CURRENTTHEMEPATH .'header.php'); 
		}
	}

	/**
	 * Includes the Foter
	 *
	 * @access 	public
	 * @param	string $arg Optional. Will get footer-{$arg}.php instead
	 * @return 	null
	 */
	function the_footer($arg = null) {
		if ($arg) {
			include(CURRENTTHEMEPATH .'footer-'.$arg.'.php'); 
		}
		else {
			include(CURRENTTHEMEPATH .'footer.php'); 
		}
	}

	/**
	 * Includes the stylesheet
	 *
	 * @access 	public
	 * @param	string $arg Optional. Will get style-{$arg}.css instead
	 * @return 	null
	 */
	function the_style($arg = null) {
		if ($arg) {
			echo ('<link rel="stylesheet" href="'. THISTHEMEPATH .'style-'.$arg.'.css" />'); 
		}
		else {
			echo ('<link rel="stylesheet" href="'. THISTHEMEPATH .'style.css" />'); 
		}
	}

	/**
	 * Fetches an array of Posts by criteria
	 *
	 * @access 	public
	 * @param	array $arg Optional. This is the criteria that will filter the posts fetched.
	 * @return 	array
	 */
	function getPosts($args = null) {
		global $Flex;
		if($args) {
			$posts = $Flex->fetch($args);
		}
		else {
			$posts = $Flex->fetch();
		}
		return $posts;
	}

	/**
	 * Fetches an array of general Site Info by criteria
	 *
	 * @access 	public
	 * @param	array $arg Optional. This is the criteria that will filter the site information fetched.
	 * @return 	array
	 */
	function get_siteinfo($arg = null) {
		global $Flex;
		if (!$arg) {
			$info = $Flex->siteinfo();
		}
		else {
			$info = $Flex->siteinfo($arg);
		}
		return $info;
	}

	/**
	 * Displays a single piece of Site Info
	 *
	 * @access 	public
	 * @param	string $arg The specific site information to be fetched.
	 * @return 	null
	 */
	function siteinfo($arg) {
		global $Flex;
		$info = $Flex->siteinfo($arg);
		echo $info;
	}

	/**
	 * Displays a single piece of User Info
	 *
	 * @access 	public
	 * @param	string $arg The specific user information to be fetched.
	 * @return 	null
	 */
	function userinfo($arg) {
		global $Flex;
		$info = $Flex->userinfo($arg);
		echo $info;
	}

	/**
	 * Adds an Action to an existing Hook. If Hook not found, it creates Hook.
	 *
	 * @access 	public
	 * @param	string $hook The name of the Hook to attach to
	 * @param	function $function The function to attach
	 * @return 	null
	 */
	function addAction($hook, $function){
		global $Flex;
		$Flex->addAction($hook, $function);
	}

	/**
	 * Runs all actions attached to Hook
	 *
	 * @access 	public
	 * @param	string $name The name of the Hook
	 * @return 	null
	 */
	function hook($name){
		global $Flex;
		$Flex->hook($name);
	}

	/**
	 * Echos or Returns a single piece of Current Post's Data, or returns boolean on failure.
	 *
	 * @access 	public
	 * @param	string $data The name of the data to be returned
	 * @return 	mixed
	 */
	function post($data, $echo = true, $post = null){
		if(!$post){
			global $post;
		}
		if(isset($post[$data])) {
			if($echo){
				echo $post[$data];
			}
			else {
				return $post[$data];
			}
		}
		else {
			return false;
		}
	}
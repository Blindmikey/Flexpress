<?php
/**
 * Flex CMS master class
 * 
 * @version 1.0
 * @author Michael Niles (michael@blindmikey.com)
 * @copyright Blindmikey 2012
 */
class Flex {
	
	/********************* PROPERTY ********************/
	
	private $DB;
	private $Explorer;
	private $Hooks;
	
	public $submit;
	public $dberror;
	public $passcheck;
	public $loginInvalid;
	
	/******************** CONSTRUCT ********************/
	
	/**
	 * Constructor
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct(){
		require_once(CLASSPATH .'MysqlDB.php');
		require_once(CLASSPATH .'Explorer.php');
		if (file_exists(COREPATH .'config.php')) {
			require_once(COREPATH .'config.php');
			$this->DB = new MysqlDB(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		}
		$this->Explorer = new Explorer();
		$this->Hook = array();
		
		if(!isset($this->submit)) { $this->submit = false; }
		if(!isset($this->dberror)) { $this->dberror = false; }
		if(!isset($this->passcheck)) { $this->passcheck = true; }
		if(!isset($this->loginInvalid)) { $this->loginInvalid = false; }
	}
	
	/********************* PRIVATE *********************/
	
// Should we create some example PostTypes that can be toggled on
// - Might get people kick-started on what this CMS can easily do
	
//!	Replace DB class with PHP 5.3 built in PDO database functions

	/**
	 * Creates Database Structure
	 *
	 * @access 	private
	 * @return 	null
	 */
	private function createDB() {
		$queries = array (
			'CREATE TABLE '.DBPREFIX.'options ( id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), option_name VARCHAR(100), option_value LONGTEXT )',
			//'CREATE TABLE '.DBPREFIX.'users ( id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), user_name VARCHAR(100), user_email VARCHAR(100), user_login VARCHAR(60), user_pass VARCHAR(64), user_level INT )',
			'CREATE TABLE '.DBPREFIX.'data_types ( id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), data_type_name VARCHAR(100), data_type_form_before LONGTEXT, data_type_form_after LONGTEXT )',
			'CREATE TABLE '.DBPREFIX.'data_templates ( id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), data_template_name VARCHAR(100), data_type VARCHAR(100), data_template_default LONGTEXT )',
			'CREATE TABLE '.DBPREFIX.'post_types ( id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), post_type_name VARCHAR(100), post_type_uri VARCHAR(100), data_templates TEXT, taxonomies TEXT )',
			'CREATE TABLE '.DBPREFIX.'post_data ( id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), data_for_post INT(11), data_template TEXT, data_value LONGTEXT, data_status TEXT, data_date DATETIME )',
			'CREATE TABLE '.DBPREFIX.'posts ( id INT NOT NULL AUTO_INCREMENT, PRIMARY KEY(id), post_type VARCHAR(100), post_name TEXT, post_slug TEXT, post_date DATETIME )',
		);
		$this->DB->multi_query($queries);
	}
	
	/**
	 * Populates Database
	 *
	 * @access 	private
	 * @param	string $sitename The name of the website
	 * @param	string $sitedesc The description of the website
	 * @param	string $adminname The name of the administrator
	 * @param	string $adminemail The email address of the administrator
	 * @param	string $adminlogin The username of the administrator
	 * @param	string $adminpass The password (encrypted) of the administrator
	 * @return 	null
	 */
	private function configureDB($sitename, $sitedesc, $adminname, $adminemail, $adminlogin, $adminpass) {
		$mysqldate = date( 'Y-m-d H:i:s' ); //$phpdate = strtotime( $mysqldate );//
		$pass = crypt($adminpass, MYSALT); 
		$this->DB->insert( DBPREFIX .'options', array('option_name','option_value'), array('Site Name', $this->DB->escape($sitename)));
		$protocol = $_SERVER['HTTPS'] ? "https" : "http";
		$locpath = $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$hello = 'Welcome to Flex. The no-nonsense customizable CMS. <br />Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';
		
		$this->DB->insert( DBPREFIX .'options', array('option_name','option_value'), array('Site Url', $this->DB->escape($locpath)));
		$this->DB->insert( DBPREFIX .'options', array('option_name','option_value'), array('Site Description', $this->DB->escape($sitedesc)));
		$this->DB->insert( DBPREFIX .'options', array('option_name','option_value'), array('Site Admin Email', $this->DB->escape($adminemail)));
		$this->DB->insert( DBPREFIX .'options', array('option_name','option_value'), array('Current Theme', 'default'));
		
		//$this->DB->insert( DBPREFIX .'users', array('user_name','user_email','user_login','user_pass','user_level'), array($this->DB->escape($adminname), $this->DB->escape($adminemail), $this->DB->escape($adminlogin), $pass, 10));
		
		$this->DB->insert( DBPREFIX .'data_types', array('data_type_name', 'data_type_form_before', 'data_type_form_after'), array('editor', '<textarea class="editor" cols="30" rows="10">', '</textarea>'));
		$this->DB->insert( DBPREFIX .'data_types', array('data_type_name', 'data_type_form_before', 'data_type_form_after'), array('checkboxes', '<ul class="checkboxes">', '</ul>'));
		$this->DB->insert( DBPREFIX .'data_types', array('data_type_name', 'data_type_form_before', 'data_type_form_after'), array('select', '<select class="select">', '</select>'));
		$this->DB->insert( DBPREFIX .'data_types', array('data_type_name', 'data_type_form_before', 'data_type_form_after'), array('single line text', '<input type="text" class="single-line-text" value="', '" />'));
		
		$this->DB->insert( DBPREFIX .'data_templates', array('data_template_name', 'data_type', 'data_template_default'), array('main content', 'editor', ''));
		$this->DB->insert( DBPREFIX .'data_templates', array('data_template_name', 'data_type', 'data_template_default'), array('taxonomy terms', 'checkboxes', ''));
		$this->DB->insert( DBPREFIX .'data_templates', array('data_template_name', 'data_type', 'data_template_default'), array('primary taxonomy term', 'select', ''));
		$this->DB->insert( DBPREFIX .'data_templates', array('data_template_name', 'data_type', 'data_template_default'), array('user_email', 'single line text', ''));
		$this->DB->insert( DBPREFIX .'data_templates', array('data_template_name', 'data_type', 'data_template_default'), array('user_login', 'single line text', ''));
		$this->DB->insert( DBPREFIX .'data_templates', array('data_template_name', 'data_type', 'data_template_default'), array('user_pass', 'single line text', ''));
		$this->DB->insert( DBPREFIX .'data_templates', array('data_template_name', 'data_type', 'data_template_default'), array('user_level', 'single line text', ''));
		
		$this->DB->insert( DBPREFIX .'post_types', array('post_type_name', 'post_type_uri', 'data_templates', 'taxonomies'), array('page', 'pages', 1, '')); // taxonomies structure: serialize::array('taxonomy'=>array('list', 'all', 'terms', 'evar!'), 'food'=>array('chinese', 'american', 'italian', 'japanese'))
		$this->DB->insert( DBPREFIX .'post_types', array('post_type_name', 'post_type_uri', 'data_templates', 'taxonomies'), array('user', 'users', '4,5,6,7', ''));
		
		$this->DB->insert( DBPREFIX .'posts', array('post_type', 'post_name', 'post_slug', 'post_date'), array('user', $this->DB->escape($adminname), $this->slugThis($this->DB->escape($adminname)), $mysqldate));
		$this->DB->insert( DBPREFIX .'posts', array('post_type', 'post_name', 'post_slug', 'post_date'), array('page', 'Welcome to Flex', 'welcome-to-flex', $mysqldate));
		
		$this->DB->insert( DBPREFIX .'post_data', array('data_for_post', 'data_template', 'data_value', 'data_status', 'data_date'), array(1, 'user_email', $this->DB->escape($adminemail), 'published', $mysqldate));
		$this->DB->insert( DBPREFIX .'post_data', array('data_for_post', 'data_template', 'data_value', 'data_status', 'data_date'), array(1, 'user_login', $this->DB->escape($adminlogin), 'published', $mysqldate));
		$this->DB->insert( DBPREFIX .'post_data', array('data_for_post', 'data_template', 'data_value', 'data_status', 'data_date'), array(1, 'user_pass', $pass, 'published', $mysqldate));
		$this->DB->insert( DBPREFIX .'post_data', array('data_for_post', 'data_template', 'data_value', 'data_status', 'data_date'), array(1, 'user_level', 10, 'published', $mysqldate));
		
		$this->DB->insert( DBPREFIX .'post_data', array('data_for_post', 'data_template', 'data_value', 'data_status', 'data_date'), array(2, 'main content', $hello, 'published', $mysqldate));
		
		//POST-DATA: PRIMARY TAX TERM: $this->DB->insert( DBPREFIX .'post_data', array('data_template', 'data_value'), array('primary_taxonomy_term', $serialize::array('taxonomy', 'term'));
		//POST-DATA: TAX TERMS: $this->DB->insert( DBPREFIX .'post_data', array('data_template', 'data_value'), array('taxonomy_terms', $serialize::array('taxonomy' => array('tax', 'term', 'list'), 'food' => array('american','italian'))));
	}
	
	/********************* PUBLIC **********************/
	
	/**
	 * Display Site with correct Template or Specified View
	 *
	 * @access 	public
	 * @param	string $view The name of the view. If empty display the site. (optional)
	 * @param	mixed $args Any additional arguments to pass onto the view. (optional)
	 * @return 	null
	 */
	public function display($in_admin = false, $view = null, $args = null) {
		if ($view == null) {
			if ( !defined('CURRENTTHEMEPATH') ) { // has roots - for includes
				define('CURRENTTHEMEPATH', THEMEPATH . $this->DB->get_var("SELECT DISTINCT option_value FROM ". DBPREFIX ."options WHERE option_name = 'Current Theme'") .'/');
			}
			if ( !defined('THISTHEMEPATH') ) { // no roots - for styles, scripts
				define('THISTHEMEPATH', $this->siteinfo('url') . 'content/themes/' . $this->DB->get_var("SELECT DISTINCT option_value FROM ". DBPREFIX ."options WHERE option_name = 'Current Theme'") .'/');
			}
			$uri = $this->parseUrl(null, $in_admin);
			
			if(!$in_admin) {
				
				// IF taxonomy_term IS in uri	
					
				if (isset($uri['taxonomy_term'])) {

					// PRIORITY CHAIN
					// ** IF uri has specific post **
					// {postType}-single.php
					// single.php
					// index.php
					// ** ELSE **
					// {postType}-{taxTerm}-term.php
					// {postType}-{tax}-terms.php
					// {postType}-{tax}-taxonomy.php
					// {postType}-taxonomies.php
					// taxonomies.php
					// {postType}-index.php
					// index.php
					
					if(isset($uri['post'])) {
						if(file_exists(CURRENTTHEMEPATH . $uri['post_type'] . '-single.php')) {
							$template = $uri['post_type'] . '-single.php';
						}
						elseif(file_exists(CURRENTTHEMEPATH . 'single.php')) {
							$template = 'single.php';
						}
						else {
							$template = 'index.php';
						}
					}
					else {
						if(file_exists(CURRENTTHEMEPATH . $uri['post_type'] . '-' . $uri['taxonomy_term'] . '-term.php')) {
							$template = $uri['post_type'] . '-' . $uri['taxonomy_term'] . '-term.php';
						}
						elseif(file_exists(CURRENTTHEMEPATH . $uri['post_type'] . '-' . $uri['taxonomy'] . '-terms.php')) {
							$template = $uri['post_type'] . '-' . $uri['taxonomy'] . '-terms.php';
						}	
						elseif(file_exists(CURRENTTHEMEPATH . $uri['post_type'] . '-' . $uri['taxonomy'] . '-taxonomy.php')) {
							$template = $uri['post_type'] . '-' . $uri['taxonomy'] . '-taxonomy.php';
						}	
						elseif(file_exists(CURRENTTHEMEPATH . $uri['post_type'] . '-taxonomies.php')) {
							$template = $uri['post_type'] . '-taxonomies.php';
						}
						elseif(file_exists(CURRENTTHEMEPATH . 'taxonomies.php')) {
							$template = 'taxonomies.php';
						}
						elseif(file_exists(CURRENTTHEMEPATH . $uri['post_type'] . '-index.php')) {
							$template = $uri['post_type'] . '-index.php';
						}
						else {
							$template = 'index.php';
						}
					}
				}

				// IF taxonomy_term IS NOT in uri but taxonomy IS
				
				elseif (isset($uri['taxonomy'])) {

					// PRIORITY CHAIN
					// ** IF uri has specific post **
					// {postType}-single.php
					// single.php
					// index.php
					// ** ELSE **
					// {postType}-{tax}-taxonomy.php
					// {postType}-taxonomies.php
					// taxonomies.php
					// {postType}-index.php
					// index.php	
					
					if(isset($uri['post'])) {
						if(file_exists(CURRENTTHEMEPATH . $uri['post_type'] . '-single.php')) {
							$template = $uri['post_type'] . '-single.php';
						}
						elseif(file_exists(CURRENTTHEMEPATH . 'single.php')) {
							$template = 'single.php';
						}
						else {
							$template = 'index.php';
						}
					}
					else {
						if(file_exists(CURRENTTHEMEPATH . $uri['post_type'] . '-' . $uri['taxonomy'] . '-taxonomy.php')) {
							$template = $uri['post_type'] . '-' . $uri['taxonomy'] . '-taxonomy.php';
						}
						elseif(file_exists(CURRENTTHEMEPATH . $uri['post_type'] . '-taxonomies.php')) {
							$template = $uri['post_type'] . '-taxonomies.php';
						}
						elseif(file_exists(CURRENTTHEMEPATH . 'taxonomies.php')) {
							$template = 'taxonomies.php';
						}
						elseif(file_exists(CURRENTTHEMEPATH . $uri['post_type'] . '-index.php')) {
							$template = $uri['post_type'] . '-index.php';
						}
						else {
							$template = 'index.php';
						}
					}
				}

				// IF taxonomy IS NOT in uri BUT post_type IS
				
				elseif(isset($uri['post_type'])) {

					// PRIORITY CHAIN
					// ** IF uri has specific post **
					// {postType}-single.php
					// single.php
					// index.php
					// ** ELSE **
					// {postType}-index.php
					// index.php	
					
					if(isset($uri['post'])) {
						if(file_exists(CURRENTTHEMEPATH . $uri['post_type'] . '-single.php')) {
							$template = $uri['post_type'] . '-single.php';
						}
						elseif(file_exists(CURRENTTHEMEPATH . 'single.php')) {
							$template = 'single.php';
						}
						else {
							$template = 'index.php';
						}
					}
					else {
						if(file_exists(CURRENTTHEMEPATH . $uri['post_type'] . '-index.php')) {
							$template = $uri['post_type'] . '-index.php';
						}
						else {
							$template = 'index.php';
						}
					}
				}

				// IF post_type IS NOT in uri BUT post IS
				
				elseif(isset($uri['post'])) {

					// PRIORITY CHAIN
					// page-single.php
					// single.php
					// index.php
					
					if(file_exists(CURRENTTHEMEPATH . 'page-single.php')) {
						$template = 'page-single.php';
					}
					elseif(file_exists(CURRENTTHEMEPATH . 'single.php')) {
						$template = 'single.php';
					}
					else {
						$template = 'index.php';
					}
				}

				// IF nothing is in uri
				
				// PRIORITY CHAIN
				// page-index.php
				// index.php
				
				elseif(file_exists(CURRENTTHEMEPATH . 'page-index.php')) {
					$template = 'page-index.php';
				}
				else {
					$template = 'index.php';
				}

				global $post;
				if(file_exists(CURRENTTHEMEPATH . 'functions.php')){
					include(CURRENTTHEMEPATH . 'functions.php');
				}
				include(CURRENTTHEMEPATH . $template);
			}

			// IF in admin

			else {
				if(isset($uri['id']) && isset($uri['type']) && isset($uri['action']) && isset($uri['view'])) {
					$this->display(true, $uri['view'], array('id'=>$uri['id'], 'type'=>$uri['type'], 'action'=>$uri['action']));
				}
				elseif(isset($uri['type']) && isset($uri['action']) && isset($uri['view'])) {
					$this->display(true, $uri['view'], array('type'=>$uri['type'], 'action'=>$uri['action']));
				}
				elseif(isset($uri['id']) && isset($uri['view'])) {
					$this->display(true, $uri['view'], array('id'=>$uri['id'], 'action'=>'default'));
				}
				elseif(isset($uri['type']) && isset($uri['view'])) {
					$this->display(true, $uri['view'], array('type'=>$uri['type'], 'action'=>'default'));
				}
				elseif(isset($uri['view'])) {
					$this->display(true, $uri['view'], array('action'=>'default'));
				}
			}
		}
		else {
			$data = $args;
			require_once(COREPATH . 'userfunc.php'); // Include User Functions
			if(file_exists(CURRENTTHEMEPATH . 'functions.php')){
				include(CURRENTTHEMEPATH . 'functions.php');
			}
			include(VIEWPATH . $view . '.php');
		}
	}

	/**
	 * Adds an Action to an existing Hook. If Hook not found, it creates Hook.
	 *
	 * @access 	public
	 * @param	string $hook The name of the Hook to attach to
	 * @param	function $function The function to attach
	 * @return 	null
	 */
	public function addAction($hook, $function) {
		$this->Hook[$hook][] = $function;
	}

	/**
	 * Runs all actions attached to Hook. If Hook not found, return boolean
	 *
	 * @access 	public
	 * @param	string $name The name of the Hook
	 * @return 	boolean
	 */
	public function hook($name) {
		if(isset($this->Hook[$name])) {
			$actions = $this->Hook[$name];
			foreach( $actions as $action ) {
				if(is_array($action)){
					$function = $action[0];
					unset($action[0]);
					call_user_func_array($function, $action);
				}
				else {
					call_user_func($action);
				}
			}
		}
		else {
			return false;
		}

	}
	
	/**
	 * Converts a string into a slug-friendly string
	 *
	 * @access 	public
	 * @param	string $title The string to be turned into a slug
	 * @return 	string
	 */
	public function slugThis($title) {
		$slug = strtolower(str_replace(' ', '-', $title));
		return $slug;
	}
	
	/**
	 * Get all registered Post Types
	 *
	 * @access 	public
	 * @return 	array
	 */
	public function get_postTypes() {
		$results = $this->DB->get_rows("SELECT * FROM ". DBPREFIX ."post_types");
		return $results;
	}
	
	/**
	 * Get all registered Data Types
	 *
	 * @access 	public
	 * @return 	array
	 */
	public function get_dataTypes() {
		$results = $this->DB->get_rows("SELECT * FROM ". DBPREFIX ."data_types");
		return $results;
	}
	
	/**
	 * Gets the Post Type Name from it's URI
	 *
	 * @access 	public
	 * @param	string $uri The post-type URI
	 * @return 	string
	 */
	public function postTypeNameFromURI($uri) {
		$post_type = $this->DB->get_rows("SELECT post_type_name FROM ". DBPREFIX ."post_types WHERE post_type_uri = '". $uri ."' LIMIT 1");
		return $post_type[0]->post_type_name;
	}
	
	/**
	 * Checks if a PostType is registered
	 *
	 * @access 	public
	 * @param	string $name The post-type name to check
	 * @return 	boolean
	 */
	public function postType_exists($name) {
		$post_types = $this->get_postTypes();
		if($this->in_object($name, $post_types) !== false) {
			return true;
		}
		return false;
	}
	
	/**
	 * Fetches all crucial site information into an array
	 *
	 * @access 	public
	 * @return 	array
	 */
	public function siteinfo($arg = null) {
		if (!$arg) {
			$info = $this->DB->get_rows("SELECT option_value FROM ". DBPREFIX ."options");
			return $info;
		}
		else {
			if($arg == 'name') { $arg = 'Site Name'; }
			elseif($arg == 'url') { $arg = 'Site Url'; }
			elseif($arg == 'description') { $arg = 'Site Description'; }
			elseif($arg == 'admin') { $arg = 'Site Admin Email'; }
			elseif($arg == 'theme') { $arg = 'Current Theme'; }
			else { return "siteinfo( $arg ) is not an available option."; }
			$info = $this->DB->get_var("SELECT option_value FROM ". DBPREFIX ."options WHERE option_name = '". $arg ."' LIMIT 1");
			return $info;
		}
	}
	
	/**
	 * Fetches all information on user
	 *
	 * @access 	public
	 * @param	string	$arg 		Specific detail to fetch (optional)
	 * @param	int		$userID		ID of user to fetch (optional)
	 * @return 	array
	 */
	public function userinfo($arg = null, $userID = null) {
		if (!$userID) {
			if (isset($_COOKIE['username'])) {
				$userID = $this->DB->get_var("SELECT data_for_post FROM ". DBPREFIX ."post_data WHERE data_template = 'user_login' AND data_value = '". $_COOKIE['username'] ."'");
			}
			else {
				return 'No User';
			}
		}
		if (!$arg) {
		
			$info = array();
			
			
			$info['name'] = $this->DB->get_var("SELECT post_name FROM ". DBPREFIX ."posts WHERE id = '". $userID ."' LIMIT 1");
			$info['id'] = $userID;
			
			$info_datas = $this->DB->get_rows("SELECT * FROM ". DBPREFIX ."post_data WHERE data_for_post = '". $userID ."'");
			if(is_array($info_datas)){
				foreach ( $info_datas as $info_data ) {
					//prevent password from being fetched
					if($info_data->data_template != 'user_pass') {
						$info[$info_data->data_template] = $info_data->data_value;
					}
				}
			}
			return $info;
		}
		else {
			if($arg == 'name') { $arg = 'user_name'; }
			elseif($arg == 'email') { $arg = 'user_email'; }
			elseif($arg == 'id') { $arg = 'id'; }
			elseif($arg == 'user level') { $arg = 'user_level'; }
			else {
				return 'userinfo('.$arg.') is not a valid option';
			}
			
			if($arg == 'user_name') {
				$info = $this->DB->get_var("SELECT post_name FROM ". DBPREFIX ."posts WHERE id = '". $userID ."' LIMIT 1");
			}
			elseif($arg == 'id') {
				$info = $userID;
			}
			else {
				$info = $this->DB->get_var("SELECT data_value FROM ". DBPREFIX ."post_data WHERE data_template = '". $arg . "' AND data_for_post = '". $userID ."' LIMIT 1");
			}
			return $info;
		}
	}
	
	/**
	 * URL Helper
	 *
	 * Parses and disects a given url segment
	 *
	 * @access 	public
	 * @param	string $url The url to be parsed
	 * @return	array
	 */
	public function parseUrl($url = null, $in_admin = false) {
		if(!$url){
			if(isset($_SERVER['HTTPS'])) {$protocol = "https";} else {$protocol = "http";}
			$url = $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		}
		$url = str_replace($this->siteinfo('url'), '', $url);
		$pieces = explode('/', $url);
		$data = array();
		$urldata = array();
		foreach ($pieces as $piece) {
			if ($piece) {
				$urldata[] = $piece;
			}
		}
		if(!$in_admin) {
			if(isset($urldata[3])) {
				$data['post'] = $urldata[3];
				$data['taxonomy_term'] = $urldata[2];
				$data['taxonomy'] = $urldata[1];
				$data['post_type'] = $urldata[0];
			}
			elseif(isset($urldata[2])) {				
				if ($this->taxonomy_exists($urldata[1], $urldata[0])) {
					$data['taxonomy_term'] = $urldata[2];
					$data['taxonomy'] = $urldata[1];
				}
				else {
					$data['post'] = $urldata[2];
					$data['taxonomy_term'] = $urldata[1];
				}
				
				$data['post_type'] = $urldata[0];
			}
			elseif(isset($urldata[1])) {
				if($parent = $this->term_exists($urldata[1], $urldata[0])) {
					$data['taxonomy_term'] = $urldata[1];
					$data['taxonomy'] = $parent;
				}
				elseif($parent = $this->taxonomy_exists($urldata[1], $urldata[0])) {
					$data['taxonomy'] = $urldata[1];
				}
				else {
					$data['post'] = $urldata[1];
				}
				$data['post_type'] = $urldata[0];
			}
			elseif(isset($urldata[0])) {
				$postTypes = $this->DB->get_rows("SELECT * FROM ". DBPREFIX ."post_types WHERE post_type_uri = '". $urldata[0] ."' LIMIT 1");
				if ($postTypes) {
					$data['post_type'] = $urldata[0];
				}
				else {
					$data['post'] = $urldata[0];
				}
			}
		}
		else {
			if(isset($urldata[3])) {
				$data['id'] = $urldata[3];
				$data['type'] = $urldata[2];
				$data['action'] = $urldata[1];
				$data['view'] = $urldata[0];
			}
			if(isset($urldata[2])) {
				if(is_numeric($urldata[2])){
					$data['id'] = $urldata[2];
				}
				else {
					$data['type'] = $urldata[2];
				}
				$data['action'] = $urldata[1];
				$data['view'] = $urldata[0];
			}
			elseif(isset($urldata[1])) {
				$data['action'] = $urldata[1];
				$data['view'] = $urldata[0];
			}
			elseif(isset($urldata[0])) {
				$data['view'] = $urldata[0];
			}
		}
		return $data;
	}
	
	/**
	 * Displays the backend Administration
	 *
	 * @access 	public
	 * @return 	null
	 */
	public function displayAdmin() {		
		
		// check if user has session
		// if yes:
		if (isset($_COOKIE['username'])) {
			
			$userID = $this->DB->get_var("SELECT data_for_post FROM ". DBPREFIX ."post_data WHERE data_template = 'user_login' AND data_value = '". $_COOKIE['username'] ."' LIMIT 1");
			$realPW = $this->DB->get_var("SELECT data_value FROM ". DBPREFIX ."post_data WHERE data_template = 'user_pass' AND data_for_post = '". $userID ."' LIMIT 1");
			
			// check if user session is valid
			// if yes:
			// reveal admin view
			if ($_COOKIE['password'] == md5($realPW)) {
				$this->display(true);
			}
		
			// else:
			// begin login procedure
			else{
				$this->login();
			}
		}
		
		// else:
		// begin login procedure
		else{
			$this->login();
		}
	}
	
	/**
	 * Handles Login form and procedure
	 *
	 * @access 	public
	 * @return 	null
	 */
	public function login() {
		// check if login form has been submitted
		
		// if yes:
		// check form for errors
		if (isset($_POST['loginSubmit'])) {
			// gather POST data
			$this->submit = true;
			$username = $_POST['username'];	
			$userpass = $_POST['userpass'];	

			//get real password for username given
			$userID = $this->DB->get_var("SELECT data_for_post FROM ". DBPREFIX ."post_data WHERE data_template = 'user_login' AND data_value = '". $username ."' LIMIT 1");
			$realPW = $this->DB->get_var("SELECT data_value FROM ". DBPREFIX ."post_data WHERE data_template = 'user_pass' AND data_for_post = '". $userID ."' LIMIT 1");
			
			// if errors:
			// show form with errors
			if (!$username || !$userpass) {
				require_once(VIEWPATH .'login.php');
			}
			elseif ((!isset($realPW)) || (crypt($userpass, MYSALT) != $realPW)) {
				$this->loginInvalid = true;
				require_once(VIEWPATH .'login.php');
			}
			
			// else:
			// set user session, reveal admin view '$this->displayAdmin()'
			
			else { // TODO: add longer-lived cookie if user checks 'remember me' box.
				
				//$uri = $_SERVER['HTTP_HOST'];		
				$uri = false; //for localhost dev only
				
				setcookie('username', $username, false, '/', $uri);
				setcookie('password', md5(crypt($userpass, MYSALT)), false, '/', $uri);
				
				$this->display(true);
			}
		
		}
		// else:
		// show form:
		else {
			require_once(VIEWPATH .'login.php');
		}
	}
	
	/**
	 * Handles Logout procedure.
	 *
	 * @access 	public
	 * @return 	null
	 */
	public function logout() {
		// destroy user session
		
		// reveal login view '$this->login()'
	}
	
	/**
	 * In Object
	 *
	 * Finds a parameter in an object or array
	 *
	 * @access 	public
	 * @param	string $val The string to search for
	 * @param	mixed $array The object or array to search in
	 * @return	mixed The key containing the searched string, or false if none is found. 
	 */	
	public function in_object($val, $array) {
		if($val == ""){
			trigger_error("in_object expects parameter 1 must not empty", E_USER_WARNING);
			return false;
		}
		if(!is_array($array)){
			$array = (array)$array;
		}
		
		foreach($array as $key => $value){
			if(!is_array($value) && !is_object($value)) {
				if($value == $val){
					return $key;
				}
			}
			elseif((is_array($value) || is_object($value)) && $this->in_object($val, $value)!==false) {
				return $key;
			}
		}
		return false;
	}
	
	/**
	 * Get taxonomy terms by taxonomy and postID
	 *
	 * @access 	public
	 * @param	string	taxonomy
	 * @param	int		postID
	 * @return	array
	 */
	public function get_terms($taxonomy, $postID) {
		
	}
	
	/**
	 * Find if Taxonomy exists in PostType
	 *
	 * @access 	public
	 * @param	string $tax The taxonomy to search for
	 * @param	string $postTypeURI The post type uri that should house the term
	 * @return	boolean
	 */	
	public function taxonomy_exists($tax, $postTypeURI) {
		$taxonomy_terms = $this->DB->get_rows("SELECT taxonomies FROM ". DBPREFIX ."post_types WHERE post_type_uri = '". $postTypeURI ."'");
		
		// sift through posts taxonomy listings
		if(is_array($taxonomy_terms)){
			foreach ($taxonomy_terms as $taxonomy_term) {
				$term_array = unserialize($taxonomy_term->taxonomies);
				if(is_array($term_array)){
					foreach($term_array as $key => $value) {
						if($key == $tax) {
							return true;
						}
					}
				}
			}
		}
		return false;
	}
	
	/**
	 * Find if Term exists in PostType
	 *
	 * @access 	public
	 * @param	string $term The term to search for
	 * @param	string $postTypeURI The post type uri that should house the term
	 * @return	string The taxonomy of the term verified
	 */	
	public function term_exists($term, $postTypeURI) {
		$taxonomy_terms = $this->DB->get_rows("SELECT taxonomies FROM ". DBPREFIX ."post_types WHERE post_type_uri = '". $postTypeURI ."'");
		
		// sift through posts taxonomy terms
		foreach ($taxonomy_terms as $taxonomy_term) {
			$term_array = unserialize($taxonomy_term->taxonomies);
			if($parent = $this->in_object($term, $term_array)) {
				return $parent;
			}
		}
		return false;
	}
	
	/**
	 * Get all Data Templates used by a PostType
	 *
	 * @access 	public
	 * @param	string $postType The postype name whos dataTemplates were collecting
	 * @return	array
	 */	
	public function get_dataTemplates($postType) {
		$templates_string = $this->DB->get_rows("SELECT data_templates FROM ". DBPREFIX ."post_types WHERE post_type_name = '". $postType ."'");
		$templates_arr = explode(',', $templates_string[0]->data_templates);
		$templates = array();
		foreach($templates_arr as $key => $template) {
			$temp = $this->DB->get_rows("SELECT data_template_name FROM ". DBPREFIX ."data_templates WHERE id = '". $template ."'");
			$templates[] = $temp[0]->data_template_name;
		}
		return $templates;
	}

	/**
	 * Get Post by ID
	 *
	 * @access 	public
	 * @param	string $id The id of the post we're fetching
	 * @return	array
	 */	
	public function get_post($id) {
		$post = $this->fetch($id);
		if (is_array($post)) {
			return $post[0];
		}
		return false;
	}
	
	/**
	 * Fetches all posts by given criteria
	 *
	 * @access 	public
	 * @param	mixed $args The Criteria
	 * $return	array
	 */
	public function fetch($args = null) {
//!		// add option to omit 'users' postType from showing in results.

		$uri = $this->parseUrl();
		// support for usecase: $Flex->fetch(); - will depend on URI structure.
		if (!$args) {
			// support for uri: site.com/?p=# (1st priority - returns 1 post)
			if(isset($_GET['p'])) {
				$results = $this->DB->get_rows("SELECT * FROM ". DBPREFIX ."posts WHERE id = '". $_GET['p'] ."' LIMIT 1");
			}
			// support for uri: site.com/post_type/taxonomy_term/ (returns index of posts of taxonomy_terms associated to the given post_type)
			elseif(isset($uri['taxonomy_term'])) {
				$post_type = $this->postTypeNameFromURI($uri['post_type']);
				$posts = $this->DB->get_rows("SELECT * FROM ". DBPREFIX ."post_data WHERE data_template = 'taxonomy terms'");
				
				$ids = '';
				$count = 0;
				foreach($posts as $post){
				//echo'<pre>';print_r($post);echo'</pre>';
					if(strstr($post->data_value, '"'. $uri['taxonomy_term'] .'"')) {
						if($count == 0) {
							$ids .= " AND id = '". $post->data_for_post ."'";
						}
						else {
							$ids .= " OR id = '". $post->data_for_post ."'";
						}
						$count = $count + 1;
					}
				}
				//echo "SELECT * FROM ". DBPREFIX ."posts WHERE post_type = '". $post_type ."'". $ids;
				$results = $this->DB->get_rows("SELECT * FROM ". DBPREFIX ."posts WHERE post_type = '". $post_type ."'". $ids);
			}
			// support for uri: site.com/post_type/taxonomy/ (returns index of posts of taxonomy associated to the given post_type)
			elseif(isset($uri['taxonomy'])) {
				$post_type = $this->postTypeNameFromURI($uri['post_type']);
				$posts = $this->DB->get_rows("SELECT * FROM ". DBPREFIX ."post_data WHERE data_template = 'taxonomy terms'");
				
				$ids = '';
				$count = 0;
				foreach($posts as $post){
				//echo'<pre>';print_r($post);echo'</pre>';
					if(strstr($post->data_value, '"'. $uri['taxonomy'] .'"')) {
						if($count == 0) {
							$ids .= " AND id = '". $post->data_for_post ."'";
						}
						else {
							$ids .= " OR id = '". $post->data_for_post ."'";
						}
						$count = $count + 1;
					}
				}
				//echo "SELECT * FROM ". DBPREFIX ."posts WHERE post_type = '". $post_type ."'". $ids;
				$results = $this->DB->get_rows("SELECT * FROM ". DBPREFIX ."posts WHERE post_type = '". $post_type ."'". $ids);
			}
			// support for uri: site.com/post_type/post/ && site.com/post/ (latter case assumed 'page' post_type, ie: site.com/pages/post/ - returns 1 post)
			elseif(isset($uri['post'])) {
				if (isset($uri['post_type'])){
					$post_type = $this->postTypeNameFromURI($uri['post_type']);
					$results = $this->DB->get_rows("SELECT * FROM ". DBPREFIX ."posts WHERE post_slug = '". $uri['post'] ."' AND post_type = '". $post_type ."' LIMIT 1");
				}
				else {
					$results = $this->DB->get_rows("SELECT * FROM ". DBPREFIX ."posts WHERE post_slug = '". $uri['post'] ."' AND post_type = 'page' LIMIT 1");
				}
			}
			// support for uri: site.com/post_type/ (returns index of posts of post_type)
			elseif(isset($uri['post_type'])) {
				$post_type = $this->postTypeNameFromURI($uri['post_type']);
				$results = $this->DB->get_rows("SELECT * FROM ". DBPREFIX ."posts WHERE post_type = '". $post_type ."'");
			}
//!			// support for uri: site.com/ (returns index of pages ||| in the future look for homepage setting and display that if exists)
			else {
				$results = $this->DB->get_rows("SELECT * FROM ". DBPREFIX ."posts WHERE post_type = 'page'");
			}
		}
		else {
			// support for usecase: $Flex->fetch('post_type_uri');
			// eg) $Flex->fetch('pages');
			if (!is_array($args) && !is_object($args) && $args) {
				if(!is_numeric($args)) {
					$posttype = $this->postTypeNameFromURI($args);
					$args = array();
					$args['post_type'] = $posttype;
				}
				// support for usecase: $Flex->fetch(12);
				else {
					$id = $args;
					$args = array();
					$args['post_type'] = '*';
					$args['id'] = $id;
				}
			}
			else {
				$args['post_type'] = $this->postTypeNameFromURI($args['post_type']);
			}
			// support for usecase without amount specification
			if (!isset($args['amount'])) {
				$args['amount'] = ' ';
			}
			else {
				$args['amount'] = ' LIMIT '.$args['amount'];
			}
			// support for usecase without exclude specification
			if (!isset($args['exclude'])) {
//!			// need to add multiple-exclude usecase	
//!			// need to add exclude by name usecase in addition to the exclude by id usecase
				$args['exclude'] = ' ';
			}
			else {
				$args['exclude'] = ' AND id != '.$args['exclude'];
			}
			if(isset($args['id'])) {
				$results = $this->DB->get_rows("SELECT * FROM ". DBPREFIX ."posts WHERE id = '". $args['id'] . "'");
			}
			else {
				$results = $this->DB->get_rows("SELECT * FROM ". DBPREFIX ."posts WHERE post_type = '". $args['post_type'] ."'" . $args['exclude'] . $args['amount']);
			}
		}

		$posts = array();
		
		if (is_array($results)) {
			foreach ($results as $result) {
				$post = array();
				$post['id'] = $result->id;
				$post['type'] = $result->post_type;
				$post['name'] = $result->post_name;
				$post['slug'] = $result->post_slug;
				$post['date'] = $result->post_date;
				
				$post_datas = $this->DB->get_rows("SELECT * FROM ". DBPREFIX ."post_data WHERE data_for_post = '". $result->id ."'");
				
				if($result->post_type == 'user') {
					if(is_array($post_datas)){
						foreach ( $post_datas as $post_data ) { if($post_data->data_template != 'user_pass' && $post_data->data_template != 'user_login') {
							$post[$post_data->data_template] = $post_data->data_value;
						}}
					}
				}
				else {
					if(is_array($post_datas)){
						foreach ( $post_datas as $post_data ) { 
							$post[$post_data->data_template] = $post_data->data_value;
						}
					}
				}
				
				$posts[] = $post;
			}
			return $posts;
		}
		else {
			echo 'No posts found.';
			return false;
		}
	}
	
	/**
	 * Checks if Flex is installed
	 *
	 * @access 	public
	 * @return 	boolean
	 */
	public function is_installed() {
		if (file_exists(COREPATH .'config.php')) {
			if ($this->DB->count("SELECT * FROM ". DBPREFIX ."options")) {
				return true;
			}
		}
		else {
			return false;
		}
	}
	
	/**
	 * Handles installation forms and procedures
	 *
	 * @access 	public
	 * @return 	null
	 */
	public function install() {
	
		// Check if file 'core/config.php' exists 	
		if (file_exists(COREPATH .'config.php')) {
			
		// if exists:
		// check if tables have been added
			if ($this->DB->descrip( DBPREFIX.'options' )) {
			
			// if tables have been added:
			// check if they've been filled
				if ($this->DB->count("SELECT * FROM ". DBPREFIX ."options")) {
					// if so, display site
					require_once(COREPATH . 'userfunc.php'); // Include User Functions
					$this->display();
				}		
			// else check if 'core/options.php' has been run & check for errors
				elseif (isset( $_POST['optionsSubmit'] )) {
					// gather POST data
						$this->submit = true;
						$sitename = $_POST['sitename'];
						$sitedesc = $_POST['sitedesc'];
						$adminname = $_POST['adminname'];
						$adminemail = $_POST['adminemail'];
						$adminlogin = $_POST['adminlogin'];
						$adminpass = $_POST['adminpass'];
						$adminpasscheck = $_POST['adminpasscheck'];
					
				// if form errors:
				// get 'core/options.php' form.
					if (!$sitename || !$sitedesc || !$adminname || !$adminlogin || !$adminpass || !$adminpasscheck) {
						require_once(VIEWPATH .'options.php');
					}
					elseif ($adminpass != $adminpasscheck) {
						$this->passcheck = false;
						require_once(VIEWPATH .'options.php');
					}
				// else
				// deliver table insert queries.
					else {
						$this->configureDB($sitename, $sitedesc, $adminname, $adminemail, $adminlogin, $adminpass);
						// check if they've been filled
						if ($this->DB->count("SELECT * FROM ". DBPREFIX ."options")) {
							// if so, display site
							require_once(COREPATH . 'userfunc.php'); // Include User Functions
							$this->display();
						}
						else {
							echo 'Tables weren\'t filled. A Terrible Error. line: ' . __LINE__;
						}
					}
				}
			// else
			// call options form
				else {
					$this->submit = false; // turn switch off
					require_once(VIEWPATH .'options.php');	
				}
			}
			else {
				echo 'tables have not been added, and failsafe loop wasn\'t very safe... line: ' . __LINE__;
			}
		}
		
	// else:
	// see if 'core/install.php' has been run & check for errors
		else {
			if (isset( $_POST['setupSubmit'] )) {
				// gather POST data
					$this->submit = true;
					$dbname = $_POST['dbname'];
					$dbprfx = $_POST['dbprfx'];
					$dbuser = $_POST['dbuser'];
					$dbpass = $_POST['dbpass'];
					$dbhost = $_POST['dbhost'];
					$dbchar = $_POST['dbchar'];
					$dbcoll = $_POST['dbcoll'];
					
					//Generate custom salt
					$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#%^&*()_+';
					$mysalt = '';
					for ($i = 0; $i < 27; $i++) {
						$mysalt .= $characters[rand(0, strlen($characters) - 1)];
					}
				
			// if form errors:
			// get 'core/install.php' form.
				if (!$dbname || !$dbuser || !$dbhost || !$dbchar) {
					require_once(VIEWPATH .'install.php');
					}
			// else
			// create 'core/config.php'.
				else {
				
					$newConfig = <<<HTML
<?php
/*
 *	Config File for Flex.
 */

// Your MySQL database information
// Database Name for Flex
define("DB_NAME", "$dbname");

// Database Prefix
define('DBPREFIX', '$dbprfx');

// Database UserName
define("DB_USER", "$dbuser");

// Database UserPassword
define("DB_PASSWORD", "$dbpass");

// Database HostName
define("DB_HOST", "$dbhost");

// Database Charset
define("DB_CHARSET", "$dbchar");

// Database Collate type
define("DB_COLLATE", "$dbcoll");

// My Custom Salt
define("MYSALT", "$mysalt");

// Error Reporting
errors(false);
HTML;
					$this->Explorer->SetPath(COREPATH .'config.php');
				// if file already exists from failed attempt:
				// delete file.
					if (file_exists(COREPATH .'config.php')) {
						$this->Explorer->Delete();
					}
					$this->Explorer->Create();
					$this->Explorer->Write($newConfig);
					require_once(COREPATH .'config.php');
					$this->DB = new MysqlDB(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
					$this->createDB();
					// check if dbinstall was succesfull
					if ($this->DB->descrip( DBPREFIX.'options' )) {
					// if yes
						$this->submit = false; // turn switch off
						require_once(VIEWPATH .'options.php');
					}
					else {
					// else
						$this->submit = true;
						$this->dberror = true;
						$this->Explorer->Delete();
						require_once(VIEWPATH .'install.php');
					}
				}
			}		
		// else: 
		// run 'core/install.php'.
			else {
				$this->submit = false; // turn switch off
				require_once(VIEWPATH .'install.php');
			}
		}
	}
}
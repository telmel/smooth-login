<?php
/*
Plugin Name: Smooth Login
Plugin URI: http://gudron.net
Description: A comprehensive and user-friendly login plugin for your site.
Author: Sait Sharifullin
Version: 1.0
Author URI: http://gudron.net
*/

class SmoothLogin {

	// If logged in upon instantiation, it is a user object.
	public static  $current_user;
	// URL for the AJAX Login procedure in template (including callback parameter)
	public static $url_login;
	// URL for the AJAX Remember Password procedure in template (including callback parameter)
	public static $url_remember;
	// URL for the AJAX Registration procedure in template (including callback parameter)
	public static $url_register;

	// Actions to take upon initial action hook
	public static function init(){
		
		//Remember the current user, in case there is a logout
		self::$current_user = wp_get_current_user();

		//Generate URLs for login, remember, and register
		self::$url_login =    site_url('wp-login.php', 'login_post');
        self::$url_register = site_url('wp-login.php?action=register', 'login');
		self::$url_remember = site_url('wp-login.php?action=lostpassword', 'login_post');

		//Make decision on what to display
		if ( !empty($_REQUEST["sl"]) ) { //AJAX Request
		    self::ajax();
		}elseif ( isset($_REQUEST["smooth-login-widget"]) ) { //Widget Request via AJAX
			self::widget( $instance );
			exit();
		}else{
			//Enqueue scripts - Only one script and one css enqueued here.... 
			if( !is_admin() ) {
			    $js_url = trailingslashit(plugin_dir_url(__FILE__))."widget/smooth-login.source.js";
                wp_enqueue_script( "smooth-login", $js_url, array( 'jquery' ), null );
                $css_url = trailingslashit(plugin_dir_url(__FILE__))."widget/widget.css";
                wp_enqueue_style( "smooth-login", $css_url, array(), null );
        		$js_vars = array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) );
        		wp_localize_script( 'smooth-login', 'SL', apply_filters ( 'sl_js_vars', $js_vars ) );
			}
					
		}
	}
	
	//Include file with widget and register widget
	public static function widgets_init(){
		include_once('smooth-login-widget.php');
		register_widget("SmoothLoginWidget");
	}

	/*
	 * LOGIN OPERATIONS
	 */

	// Decide what action to take from the ajax request
	public static function ajax(){
		$return = array('result'=>false, 'error'=>'Unknown command requested');
		switch ( $_REQUEST["smooth-login"] ) {
			case 'login': // A login has been requested
			    $return = self::login();
				break;
			case 'logout': // Logout has been requested
			    $return = self::logout();
				break;
			case 'remember': // Remember the password
				$return = self::remember();
				break;
			case 'register': // Remember the password
			default:
			    $return = self::register();
			    break;
		}
		//add this for HTTP -> HTTPS requests which assume it's a cross-site request
		@header( 'Content-Type: application/javascript; charset=UTF-8', true );
		echo self::json_encode(apply_filters('sl_ajax_'.$_REQUEST["smooth-login"], $return));
		exit();
	}

	// Reads ajax login creds via POSt, calls the login script and interprets the result
	public static function login(){
		$return = array(); //What we send back
		if( !empty($_REQUEST['log']) && !empty($_REQUEST['pwd']) && trim($_REQUEST['log']) != '' && trim($_REQUEST['pwd'] != '') ){
			$credentials = array('user_login' => $_REQUEST['log'], 'user_password'=> $_REQUEST['pwd'], 'remember' => !empty($_REQUEST['rememberme']));
			$loginResult = wp_signon($credentials);
			$user_role = 'null';
			if ( strtolower(get_class($loginResult)) == 'wp_user' ) {
				//User login successful
				self::$current_user = $loginResult;
				$return['result'] = true;
				$return['widget'] = get_bloginfo('wpurl')."?smooth-login-widget=1";
				$return['message'] = 'Login successful, updating...';
			} elseif ( strtolower(get_class($loginResult)) == 'wp_error' ) {
				//User login failed
				$return['result'] = false;
				$return['error'] = $loginResult->get_error_message();
			} else {
				//Undefined Error
				$return['result'] = false;
				$return['error'] = 'An undefined error';
			}
		}else{
			$return['result'] = false;
			$return['error'] = 'Username and password required';
		}
		$return['action'] = 'login';
		//Return the result array with errors etc.
		return $return;
	}
	
	public static function logout(){
		$return = array(); //What we send back
		wp_logout();
		//User logout successful		
		$return['result'] = true;
		$return['widget'] = get_bloginfo('wpurl')."?smooth-login-widget=1";
		$return['message'] = 'You are logging out...';
		$return['action'] = 'logout';
		//Return the result array with link and message.
		return $return;
	}

	/**
	 * Checks post data and registers user, then exits
	 * @return string
	 */
	public static function register(){
	    $return = array();
	    if( get_option('users_can_register') ){
			$errors = register_new_user($_REQUEST['user_login'], $_REQUEST['user_email']);
			if ( !is_wp_error($errors) ) {
				//Success
				$return['result'] = true;
				$return['message'] = 'Registration complete. Please check your e-mail.';
				//add user to blog if multisite
				if( is_multisite() ){
				    add_user_to_blog(get_current_blog_id(), $errors, get_option('default_role'));
				}
			}else{
				//Something's wrong
				$return['result'] = false;
				$return['error'] = $errors->get_error_message();
			}
			$return['action'] = 'register';
	    }else{
	    	$return['result'] = false;
			$return['error'] = 'Registration has been disabled';
	    }
		return $return;
	}

	// Reads ajax login creds via POST, calls the login script and interprets the result
	public static function remember(){
		$return = array(); //What we send back
		//if we're not on wp-login.php, we need to load it since retrieve_password() is located there
		if( !function_exists('retrieve_password') ){
		    ob_start();
		    include_once(ABSPATH.'wp-login.php');
		    ob_clean();
		}
		$result = retrieve_password();
		if ( $result === true ) {
			//Password correctly remembered
			$return['result'] = true;
			$return['message'] = "We have sent you an email";
		} elseif ( strtolower(get_class($result)) == 'wp_error' ) {
			//Something went wrong
			/* @var $result WP_Error */
			$return['result'] = false;
			$return['error'] = $result->get_error_message();
		} else {
			//Undefined Error
			$return['result'] = false;
			$return['error'] = 'Undefined error';
		}
		$return['action'] = 'remember';
		//Return the result array with errors etc.
		return $return;
	}

	
	// WIDGET OPERATIONS
	public static function widget($instance = array() ){
		if(is_user_logged_in())
		{		
			include 'widget/default/widget_in.php';			
		}
		else
		{    
			include 'widget/default/widget_out.php';			
		}
	}
	
	
	// Returns a sanitized JSONP response from an array. Takes array and returns string
	public static function json_encode($array){
		$return = json_encode($array);
		if( isset($_REQUEST['callback']) && preg_match("/^jQuery[_a-zA-Z0-9]+$/", $_REQUEST['callback']) ){
			$return = $_REQUEST['callback']."($return)";
		}
		return $return;
	}
	
}

//Set when to init this class
add_action( 'init', 'SmoothLogin::init' );
add_action( 'widgets_init', 'SmoothLogin::widgets_init' );

?>
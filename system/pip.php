<?php

function pip() 
{
	global $config;
    
	
	// Set default timezone;
	date_default_timezone_set($config['timezone']);
	
    // Set our defaults
    $controller = $config['default_controller'];
    $action = 'index';
    $url = '';
	
	// Get request url and script url
	$request_url = (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '';
	$script_url  = (isset($_SERVER['PHP_SELF'])) ? $_SERVER['PHP_SELF'] : '';
    	
	// Get our url path and trim the / of the left and the right
	if($request_url != $script_url) $url = trim(preg_replace('/'. str_replace('/', '\/', str_replace('index.php', '', $script_url)) .'/', '', $request_url, 1), '/');

	// Split the url into segments
	$segments = explode('/', $url);
	
	// Do our default checks
	if(isset($segments[0]) && $segments[0] != '') $controller = $segments[0];
	if(isset($segments[1]) && $segments[1] != '') $action = $segments[1];
	
	define('CURRENT_URL', BASE_URL.$controller);
	define('CURRENT_PAGE', $controller);

	// Get our controller file
    $path = APP_DIR . 'controllers/' . $controller . '.php';
	if(file_exists($path)){
		/* Set start segments param
			example.com/class/function/param
		*/
		$offset_param = 2;
		
        require_once($path);
	} else {
	//Use action in implicit default controller
		
		/* 
			Set start segments param on implicit default controller
			example.com/function/param
		*/
		$offset_param = 1;
		
		$action		= $controller; 
        $controller = $config['default_controller'];
        require_once(APP_DIR . 'controllers/' . $controller . '.php');
	}
	   
    // Check the action exists
    if(!method_exists($controller, $action)){
        $controller = $config['error_controller'];
        require_once(APP_DIR . 'controllers/' . $controller . '.php');
        $action = 'index';
    }
	
	// Create object and call method
	$obj = new $controller;
    die(call_user_func_array(array($obj, $action), array_slice($segments, $offset_param)));
}

?>

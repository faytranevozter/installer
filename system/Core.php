<?php 

/**
* Core Class Installer
* @author 		Fahrur Rifai <faimaknyus@gmail.com>
* @copyright 	Copyright (c) 2016, Elfay.id
*/
class Core
{
	protected $_ob_level;
	protected $_cached_vars = array();

	private $request_uri;

	private $class_segment = 1;
	private $route_class_segment = 1;
	private $method_segment = 2;
	private $route_method_segment = 2;
	private $param_segment_start = 3;
	private $route_param_segment_start = 3;
	private $last_directory_segment = 0;
	private $route_last_directory_segment = 0;

	public $directory_name = '';
	public $route_class_name;
	public $route_method_name;
	public $route_params;

	public $route_directory_name = '';
	public $class_name;
	public $method_name;
	public $params;

	public $_is_exist;
	public $_file_path;
	public $_file;

	private $overrided = FALSE;
	
	private static $instance;

	private $config;

	public $uri;
	public $uri_routes;
	private $count_segment = 0;
	private $route_count_segment = 0;

	protected $step_dir = STEP_PATH;

	function __construct()
	{
		$this->config =& getConfig();

		$this->_ob_level = ob_get_level();

		$this->routes = $this->config['routes'];
		$this->default_route = $this->routes['default'];
		$this->request_uri = $_SERVER['REQUEST_URI'];

		self::$instance =& $this;

		$this->_init_uri();
	}

	protected function load($_file, $_data=array(), $_return=FALSE, $_def_ext='.php')
	{
		// is file exists
		$ext = pathinfo(INS_PATH . $_file, PATHINFO_EXTENSION);
		$file = ($ext === '') ? $_file.$_def_ext : $_file;
		$file_path = INS_PATH . '' . $file;
		if ( ! file_exists($file_path))
		{
			die('Unable to load the requested file: '.$_file);
		}
		else
		{
			include($file_path);
		}

		/*
		 * Extract and cache variables
		 *
		 * You can either set variables using the dedicated $this->load->vars()
		 * function or via the second parameter of this function. We'll merge
		 * the two types and cache them so that views that are embedded within
		 * other views can have access to these variables.
		 */
		if (is_array($_data))
		{
			$this->_cached_vars = array_merge($this->_cached_vars, $_data);
		}

		extract($this->_cached_vars);

		/*
		 * Buffer the output
		 *
		 * We buffer the output for two reasons:
		 * 1. Speed. You get a significant speed boost.
		 * 2. So that the final rendered template can be post-processed by
		 *	the output class. Why do we need post processing? For one thing,
		 *	in order to show the elapsed page load time. Unless we can
		 *	intercept the content right before it's sent to the browser and
		 *	then stop the timer it won't be accurate.
		 */
		ob_start();

		// Return the file data if requested
		if ($_return === TRUE)
		{
			$buffer = ob_get_contents();
			@ob_end_clean();
			return $buffer;
		}

		/*
		 * Flush the buffer... or buff the flusher?
		 *
		 * In order to permit views to be nested within
		 * other views, we need to flush the content back out whenever
		 * we are beyond the first level of output buffering so that
		 * it can be seen and included properly by the first included
		 * template and any subsequent ones. Oy!
		 */
		if (ob_get_level() > $this->_ob_level + 1)
		{
			ob_end_flush();
		}
		else
		{
			echo ob_get_contents();
			@ob_end_clean();
		}

		return $this;
	}

	protected function _init_uri()
	{
		$this->fetch_links();
		$this->count_segment();
		$this->count_segment(TRUE);
		$this->fetch_class();
		$this->fetch_method();
		$this->fetch_params();
		$this->fetch_control();
	}

	function show($route_url='')
	{
		$this->uri_routes = $route_url;
		$segments = explode('/', trim($route_url, '/'));
		foreach ($segments as $segment) {
			$is_class_name = $this->is_class_name($segment, TRUE);
			if ($is_class_name !== FALSE) {
				$this->route_directory_name = $is_class_name['dirname'];
				$this->route_last_directory_segment = $is_class_name['last_dir_segment'];
				$this->route_class_name = $this->segment($this->route_last_directory_segment+1, FALSE, TRUE);
				$this->route_method_name = $this->segment($this->route_last_directory_segment+2, 'index', TRUE);
				$this->route_params = $this->segment($this->route_last_directory_segment+3, array(), TRUE);
				break;
			}
		}
		// join
		array_pop($segments);
		$class_name = ucwords($segment);
		$dirname = implode('/', $segments);
		$path_to_file = $this->step_dir . $this->route_directory_name . $class_name . '.php';
		include ($path_to_file);
		$GO = new $class_name();
		if ( ! method_exists($GO, $this->route_method_name)) {
			die("Method {$this->route_method_name} not exists.");
		}

		call_user_func_array(array(&$GO, $this->route_method_name), $this->route_params);
	}

	function segment($n=1, $default=FALSE, $routes=FALSE)
	{
		$_url = $routes ? $this->uri_routes : $this->uri;
		if (strpos($this->uri,'?') !== FALSE) {
			$_url = get_index('?', $_url, 0);
		}
		if ( ! empty($_url)) {

			$_url = trim($_url, '/');
			$slash = explode('/', $_url);
			
			$return = isset($slash[$n-1]) ? $slash[$n-1] : $default;
			return $return;

		} else {
			return $default;
		}
	}

	function count_segment($route=FALSE)
	{
		$_url = $route ? $this->uri_routes : $this->uri;
		if (strpos($_url, '?') !== FALSE) {
			$_url = get_index('?', $_url, 0);
		}
		if ( ! empty($_url)) {

			$_url = trim($_url, '/');
			if (strpos($_url, '/') !== FALSE) {
				$slash = explode('/', $_url);
				
				$n = count($slash);
				if ($route) {
					$this->route_count_segment = $n;
				} else {
					$this->count_segment = $n;
				}
			}
		}
	}

	function is_class_name($_class_name, $external=FALSE)
	{
		$_class_name = ucfirst($_class_name);
		$dir = read_dir_tree($this->step_dir);

		foreach ($dir as $folder) {
			if (file_exists($folder.'/'.$_class_name.'.php')) {
				if ( ! $external) {
					$this->directory_name = str_replace($this->step_dir, '', rtrim($folder, '/').'/');
					if (empty($this->directory_name)) {
						$this->directory_name = '/';
						$this->last_directory_segment = 0;
					} else {
						$this->last_directory_segment = count(explode('/', trim($this->directory_name, '/')));
					}
				} else {
					$return_ex['dirname'] = str_replace($this->step_dir, '', rtrim($folder, '/').'/');
					if (empty($return_ex['dirname'])) {
						$return_ex['dirname'] = '/';
						$return_ex['last_dir_segment'] = 0;
					} else {
						$return_ex['last_dir_segment'] = count(explode('/', trim($return_ex['dirname'], '/')));
					}
					return $return_ex;
				}
				return true;
				break;
			}
		}
		return false;
	}

	function fetch_class()
	{
		// default landing page
		if (empty($this->uri)) {
			$this->uri = $this->default_route;
			$class_name = $this->fetch_class();
		} else {
			$this->overrided = FALSE;
			// check uri routing
			foreach ($this->routes as $real_url => $execute_as) {
				if ($this->uri != 'default' AND $this->uri != '404') {
					if (trim($real_url, '/') == trim($this->uri, '/')) {
						$this->overrided = TRUE;
						$this->uri_routes = $execute_as;
						break;
					}
				}
			}
			
			foreach (explode('/', $this->uri) as $n => $segment) {
				if ($this->is_class_name($segment)) {
					$this->class_segment += $n;
					$this->method_segment += $n;
					$this->param_segment_start += $n;
					break;
				}
			}
			
			foreach (explode('/', $this->uri_routes) as $n => $segment) {
				if ($this->is_class_name($segment)) {
					$this->route_class_segment += $n;
					$this->route_method_segment += $n;
					$this->route_param_segment_start += $n;
					break;
				}
			}

			if ($this->overrided) {
				$class_name = $this->segment($this->route_class_segment, FALSE, TRUE);
			} else {
				$class_name = $this->segment($this->class_segment);
			}
		}
		$this->route_class_name = $class_name;
		$this->class_name = $this->segment($this->class_segment);
		return $this->route_class_name;
	}

	function fetch_method()
	{
		$this->method_name = $this->segment($this->method_segment, 'index');
		$this->route_method_name = $this->segment($this->route_method_segment, 'index', TRUE);
		if ( ! $this->overrided) {
			$this->route_method_name = $this->method_name;
		}
		return $this->route_method_name;
	}

	function fetch_params()
	{
		$params = array();
		// if parameter is set
		if ($this->route_count_segment >= $this->route_param_segment_start) {
			for ($i=0; $i <= $this->route_count_segment; $i++) { 
				if ($i >= $this->route_param_segment_start) {
					$params[] = $this->segment($i, FALSE, TRUE);
				}
			}
			$this->route_params = $params;
		} else {
			$this->route_params = $params;
		}

		// if parameter is set
		if ($this->count_segment >= $this->param_segment_start) {
			for ($i=0; $i <= $this->count_segment; $i++) { 
				if ($i >= $this->param_segment_start) {
					$params[] = $this->segment($i);
				}
			}
			$this->params = $params;
		} else {
			$this->params = $params;
		}

		if ( ! $this->overrided) {
			$this->route_params = $this->params;
		}

		return $this->route_params;
	}

	function fetch_links()
	{
		if (strpos($this->request_uri, $_SERVER['SCRIPT_NAME']) === 0)
		{
			$this->request_uri = substr($this->request_uri, strlen($_SERVER['SCRIPT_NAME']));
		}
		elseif (strpos($this->request_uri, dirname($_SERVER['SCRIPT_NAME'])) === 0)
		{
			$this->request_uri = substr($this->request_uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
		}

		$this->uri = str_replace(array('//', '../'), '/', trim($this->request_uri, '/'));
		return $this->uri;
	}

	function fetch_control()
	{
		$_class_name = ucfirst($this->route_class_name);
		$dir = read_dir_tree($this->step_dir);
		// find file
		$exist = $path = $file = FALSE;
		foreach ($dir as $folder) {
			if (file_exists($folder.'/'.$_class_name.'.php')) {
				$exist = TRUE;
				$path = $folder;
				$file = $path.'/'.$_class_name.'.php';

				break;
			}
		}

		// if url is without folder but class name
		if ($this->route_last_directory_segment >= $this->route_class_segment) {
			$exist = $path = $file = FALSE;
		}

		$this->_is_exist = $exist;
		$this->_file_path = $path;
		$this->_file = $file;
		return $this;
	}
}
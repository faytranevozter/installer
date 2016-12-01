<?php 
/**
 * Common File Installer
 * @author Elfay <faimaknyus@gmail.com>
 * @copyright 	Copyright (c) 2016, Elfay.id
 */

function &loadClass($className,$dir)
{
	// add trailing slash
	$dir = rtrim($dir,'/').'/';

	// capitalize
	$className = ucfirst($className);

	static $classes = array();

	if (class_exists($className)) {
		return $classes[$className];
	}

	if (is_dir(INS_PATH.$dir)) {
		
		// check the file is exists
		if (file_exists(INS_PATH.$dir.$className.'.php')) {
			
			// load the file
			require INS_PATH.$dir.$className.'.php';

			// check if class is available
			if (class_exists($className)) {

				is_loaded_class($className);

				$classes[$className] = new $className();
				return $classes[$className];
			} else {
				die('Class not exists.');
			}

		} else {
			// file not found
			die('File not found : '.INS_PATH.$dir.$className.'.php');
		}

	} else {
		// dir not found
		die('Directory not found');
	}
}

function &is_loaded_class($class='')
{
	static $loaded_class_ = array();

	if ($class != '') {
		$loaded_class_[strtolower($class)] = $class;
	}

	return $loaded_class_;
}

/**
 * Get the config from the config.php
 * @param  string  $_cfg_name get specific config
 * @param  array   $replace   replace with new config
 * @return array              the config in array
 */
function &getConfig($_cfg_name=FALSE, $replace=array())
{
	include(INS_PATH . 'config.php');
	if ( ! isset($config)) {
		die('Config variable not found');
	}

	// replace config
	foreach ($replace as $key => $value) {
		if (isset($config[$key])) {
			$config[$key] = $value;
		}
	}

	// done
	$_config[0] =& $config;
	if ($_cfg_name && isset($_config[0][$_cfg_name])) {
		return $_config[0][$_cfg_name];
	} else {
		return $_config[0];
	}
}

/**
 * get index (n) from exploded string with (x)
 * @param  string  $delimiter splitter
 * @param  string  $string    string to split
 * @param  integer $on        index of array
 * @return string             array index(n)
 */
function get_index($delimiter='', $string='', $on=0)
{
	$arrs = explode($delimiter, $string);
	if ($on < 0) {
		$on = count($arrs)+$on;
	}
	
	$return = isset($arrs[$on]) ? $arrs[$on] : FALSE;
	return $return;
}

/**
 * to get the base url
 * @param  string 	$url 
 * @return string 	the base with additional url
 */
function base_url($url='')
{
	$http = 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 's' : '') . '://';
	$additional = str_replace("index.php", "", $_SERVER['SCRIPT_NAME']);
	$new_url = $http . $_SERVER['SERVER_NAME'] . $additional;
	$new_url = rtrim($new_url, '/') . '/' . trim($url, '/');
	return $new_url;
}

/**
 * get list directory include subdirectory
 * @param  string $path path directory to fetch
 * @return array       list directory
 */
function read_dir_tree($path)
{
	// Make sure we have a trailing slash
	$path = rtrim($path, '/') . '/';
	$directories = array();
	$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
	foreach($objects as $name => $object){
		if (is_dir($name) === true AND !isset($directories[str_replace(array('/..','/.'), '', $name)]))
		{
			$directories[str_replace(array('/..','/.'), '', $name)] = realpath(str_replace(array('/..','/.'), '', $name).'/');
		}
	}
	return $directories;
}

/**
 * Element
 *
 * Lets you determine whether an array index is set and whether it has a value.
 * If the element is empty it returns NULL (or whatever you specify as the default value.)
 *
 * @param	string
 * @param	array
 * @param	mixed
 * @return	mixed	depends on what the array contains
 */
function element($item, array $array, $default = NULL)
{
	return array_key_exists($item, $array) ? $array[$item] : $default;
}

/**
 * Elements
 *
 * Returns only the array items specified. Will return a default value if
 * it is not set.
 *
 * @param	array
 * @param	array
 * @param	mixed
 * @return	mixed	depends on what the array contains
 */
function elements($items, array $array, $default = NULL)
{
	$return = array();

	is_array($items) OR $items = array($items);

	foreach ($items as $item)
	{
		$return[$item] = array_key_exists($item, $array) ? $array[$item] : $default;
	}

	return $return;
}

/**
 * Header Redirect
 *
 * Header redirect in two flavors
 * For very fine grained control over headers, you could use the Output
 * Library's set_header() function.
 *
 * @param	string	$uri	URL
 * @param	string	$method	Redirect method
 *			'auto', 'location' or 'refresh'
 * @param	int	$code	HTTP Response status code
 * @return	void
 */
function redirect($uri = '', $method = 'auto', $code = NULL)
{
	if ( ! preg_match('#^(\w+:)?//#i', $uri))
	{
		$uri = base_url($uri);
	}

	// IIS environment likely? Use 'refresh' for better compatibility
	if ($method === 'auto' && isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== FALSE)
	{
		$method = 'refresh';
	}
	elseif ($method !== 'refresh' && (empty($code) OR ! is_numeric($code)))
	{
		if (isset($_SERVER['SERVER_PROTOCOL'], $_SERVER['REQUEST_METHOD']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.1')
		{
			$code = ($_SERVER['REQUEST_METHOD'] !== 'GET')
				? 303	// reference: http://en.wikipedia.org/wiki/Post/Redirect/Get
				: 307;
		}
		else
		{
			$code = 302;
		}
	}

	switch ($method)
	{
		case 'refresh':
			header('Refresh:0;url='.$uri);
			break;
		default:
			header('Location: '.$uri, TRUE, $code);
			break;
	}
	exit;
}
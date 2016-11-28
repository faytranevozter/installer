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

	// check directory exists
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
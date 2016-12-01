<?php 
/**
* Session Class
*/
class Session
{
	
	function __construct()
	{
		if (!isset($_SESSION['_installer'])) {
			$_SESSION['_installer'] = array();
		}
	}

	function get($key='', $default=FALSE)
	{
		if (empty($key)) {
			return $_SESSION['_installer'];
		} else {
			if(isset($_SESSION['_installer'][$key])) {
				return $_SESSION['_installer'][$key];
			} else {
				return $default;
			}
		}
	}

	function set($data='', $value='')
	{
		if (is_array($data)) {
			foreach ($data as $key => $v) {
				$_SESSION['_installer'][$key] = $v;
			}
			return true;
		}

		if (is_string($data)) {
			$_SESSION['_installer'][$data] = $value;
			return true;
		}

		return false;
	}

	function remove($key='')
	{
		if(isset($_SESSION['_installer'][$key])) {
			unset($_SESSION['_installer'][$key]);
		}
	}

	function destroy()
	{
		unset($_SESSION['_installer']);
	}
}
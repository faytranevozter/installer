<?php 
/**
 * system path (parent of parent this folder)
 * @var string
 */

$installer_path = realpath('.');
$app_path = dirname($installer_path);
$step_dir = 'step';

define('INS_PATH', $installer_path.'/');
define('APP_PATH', $app_path.'/');
define('STEP_PATH', INS_PATH.$step_dir.'/');

require_once(INS_PATH.'system/Common.php');

// var_dump(getConfig('session'));
$sess_key = getConfig('session');
if ($sess_key !== FALSE) {
	session_start($sess_key);
}

$control =& loadClass('Core','system');

function &get_instance()
{
	return Core::get_instance();
}

if ( ! $control->_is_exist) {
	header('Status: 404 Not Found', TRUE);
	$control->show($control->routes['404']);
	exit();
}

require_once($control->_file);

$GO = new $control->route_class_name();
if ( ! method_exists($GO, $control->route_method_name)) {
	die("Method {$control->route_method_name} not exists.");
}


call_user_func_array(array(&$GO, $control->route_method_name), $control->route_params);
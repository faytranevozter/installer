<?php 

/**
* Template Class
*/
class Template 
{
	protected $template_file_path = 'assets/themes/default/template.php';
	function __construct()
	{
		$this->ins =& get_instance();
	}

	function display($file, $data)
	{
		$_dt['_content'] = $this->ins->load($file, $data, TRUE);
		$_dt['_title'] = $data['title'];
		$this->ins->load($this->template_file_path, $_dt);
	}
}
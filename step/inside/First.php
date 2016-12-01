<?php 

/**
* First Step
*/
class First extends Core
{
	
	function __construct()
	{
		parent::__construct();
		$this->load_class('system/form');
		$this->load_class('assets/themes/default/lib/template');
		$this->load_class('system/session');

		$this->form->form_group_template = "<div class=\"group\">\n{label}{form}{extra}</div>";
		$this->file_config = getConfig('file');
	}

	function index()
	{
		$this->first();
	}

	function first()
	{
		$data['title'] = 'Database configuration';

		$form['host'] = array('label'=>'Hostname', 'type'=>'text', 'class'=>'text-form', 'extra'=>'<span class="help">@hostname</span>', 'id'=>'host', 'value'=>$this->session->get('host','localhost'), 'required'=>'required');
		$form['user'] = array('label'=>'Username', 'type'=>'text', 'class'=>'text-form', 'extra'=>'<span class="help">@username</span>', 'id'=>'user', 'value'=>$this->session->get('user','root'), 'required'=>'required');
		$form['pass'] = array('label'=>'Password', 'type'=>'password', 'class'=>'text-form', 'extra'=>'<span class="help">@password</span>', 'id'=>'pass', 'value'=>$this->session->get('pass',''));
		$form['database'] = array('label'=>'Database Name', 'type'=>'text', 'class'=>'text-form', 'id'=>'database', 'value'=>$this->session->get('database',''), 'required'=>'required');
		$form['create'] = array('checkbox_label'=>'Create Database if not exist', 'extra_label'=>'class="checkbox"', 'type'=>'checkbox', 'checked'=>FALSE, 'class'=>'checkbox-form', 'id'=>'create', 'value'=>'yes');
		$form['prefix'] = array('label'=>'Table Prefix', 'type'=>'text', 'class'=>'text-form', 'id'=>'prefix', 'value'=>$this->session->get('prefix','in_'));

		$data['nextUrl'] = base_url('second/account');
		$data['checkUrl'] = base_url('first/check_database');
		$data['form'] = $this->form->multiple_group($form);
		$this->template->display('step/ui/db_step_ui', $data);
	}

	function check_database()
	{
		header("Content-type: text/json");
		$response = array();

		// validation
		if (!isset($_POST['host']) || empty($_POST['host'])) {
			$response['error']['msg'] = 'Host must be filled!';
			echo json_encode($response);
			exit();
		}
		if (!isset($_POST['user']) || empty($_POST['user'])) {
			$response['error']['msg'] = 'Username must be filled!';
			echo json_encode($response);
			exit();
		}
		if (!isset($_POST['database']) || empty($_POST['database'])) {
			$response['error']['msg'] = 'Database must be filled!';
			echo json_encode($response);
			exit();
		}

		if (isset($_POST['host'])) {
			$a = @new Mysqli($_POST['host'], $_POST['user'], $_POST['pass'], $_POST['database']);
			// jika ada error
			if ($a->connect_errno != 0) {
				$err_code = $a->connect_errno;
				// create db
				if ($a->connect_errno == 1049 && isset($_POST['create']) && $_POST['create']=='yes') {
					$b = @new Mysqli($_POST['host'], $_POST['user'], $_POST['pass']);
					$q = $b->query("CREATE DATABASE IF NOT EXISTS {$_POST['database']}");
					if ($q) {
						$response['success'] = true;
					} else {
						$response['error']['msg'] = 'Error while creating database.';
					}
				} else {
					if($err_code==1045) {
						$response['error']['msg'] = 'Wrong Username or Password';
					} else if($err_code==2002) {
						$response['error']['msg'] = 'Name or service host not known';
					} else {
						$response['error']['msg'] = $a->connect_error;
					}
				}
			} else {
				$response['success'] = true;
			}
		} else {
			$response['error'] = "Something went wrong :(";
		}
		if (isset($response['success'])) {
			$this->session->set(elements(array('host','user','pass','database','prefix'), $_POST, ''));
		}
		sleep(2);
		echo json_encode($response);
	}

	function execute()
	{
		if ($this->session->get('host')) {
			// select from config
			foreach ($this->file_config as $name => $file) {
				$file_content = file(APP_PATH . $file['path']);
				$fopen = fopen(APP_PATH . $file['path'], 'w');
				$new_file_content = '';
				foreach($file_content as $line => $content)
				{
					foreach ($file['replace'] as $replace_key => $find) {
						$content = str_replace($find, $this->session->get($replace_key), $content);
					}
					$new_file_content .= $content;
				}
				fwrite($fopen, $new_file_content);
				fclose($fopen);
			}
		}
	}

}
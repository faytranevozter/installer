<?php 
/**
* 
*/
class Second extends Core
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
		$this->account();
	}

	function account()
	{
		$data['title'] = 'Account Information';

		$form['ac_name'] = array('label'=>'Account Name', 'type'=>'text', 'class'=>'text-form', 'id'=>'ac_name', 'placeholder'=>'Display Name', 'extra'=>'<span class="help">ex: Elfay</span>');
		$form['ac_email'] = array('label'=>'Account Email', 'type'=>'email', 'class'=>'text-form', 'id'=>'ac_email', 'placeholder'=>'your@email.com', 'extra'=>'<span class="help">ex: elfay12@mail.com</span>');
		$form['ac_password'] = array('label'=>'Account Password', 'type'=>'password', 'class'=>'text-form', 'id'=>'ac_password', 'placeholder'=>'Your Password');

		$data['nextUrl'] = base_url('third/install');
		$data['backUrl'] = base_url('first-step');
		$data['checkUrl'] = base_url('first/check_database');
		$data['form'] = $this->form->multiple_group($form);
		$this->template->display('step/ui/account_ui', $data);
	}
}
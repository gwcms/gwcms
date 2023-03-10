<?php

class Module_Config extends GW_Common_Module
{	
	public $default_view = 'default';	
	
	function init()
	{
		$this->model = new GW_Config($this->module_path[0].'/');
		
		parent::init();
	}

	
	function viewDefault()
	{
		return ['item'=>$this->model];
	}
	
	
	

	
	function doSave()
	{
		$vals = $_REQUEST['item'];
		
		$this->model->setValues($vals);
		
		//jeigu saugome tai reiskia kad validacija praejo
		$this->setPlainMessage('/g/SAVE_SUCCESS');
		
		if($_POST['submit_type']=='testemail')
		{
			$this->doTestPhpMailer();
		}
		
		///$this->__afterSave($vals);
		
		
		$this->jump();
	}

	
	function doTestPhpMailer()
	{			
			
		////--------------2nd test----------------------------------
		$opts['subject']="This is test message ".date('Y-m-d H:i:s');
		$opts['body']="You asked to send test message from emails/config";
		
		$opts['to'] = $this->app->user->email;
		//$opts['debug'] = 1;
		
		
		$this->setMessage('<pre>'.json_encode($opts, JSON_PRETTY_PRINT).'</pre>');
		
		$status = GW_Mail_Helper::sendMail($opts);
		$opts['to']=implode(',', $opts['to']);
		
		$details = '';
		
		
		
		
		if(isset($opts['status'])){
			$details=' ('.$opts['status'].')';
		}

		
		$this->setMessage([
			"text"=>"Mail send from ".htmlspecialchars(GW_Mail_Helper::$last_from)." to {$opts['to']} ".($status ? 'succeed':'failed'.$details),
			'type'=>$status ? GW_MSG_SUCC : GW_MSG_ERR,
			'footer'=>$opts['error'] ?? false,
			'float'=>1
		]);	
	}
		
	
}


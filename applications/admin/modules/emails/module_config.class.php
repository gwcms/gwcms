<?php

class Module_Config extends GW_Module_Config_Common
{	
	function init()
	{
		$this->model = new GW_Config($this->module_path[0].'/');
		
		parent::init();
	}

	
	protected function notifyConfigSaveSuccess()
	{
		$this->setPlainMessage('/g/SAVE_SUCCESS');
	}
	
	
	

	
	function doSave()
	{
		$vals = $_REQUEST['item'];
		
		$this->normalizeConfigValues($vals);
		$this->persistConfigValues($vals);
		$this->notifyConfigSaveSuccess();
		
		if($_POST['submit_type']=='testemail')
		{
			$this->jump(false, ['act'=>'doTestPhpMailer']);
		}
		
		///$this->__afterSave($vals);
		
		
		$this->jump();
	}

	
	function doTestPhpMailer()
	{			
			
		$form = ['fields'=>[
			//'from'=>$sel, 
			'to'=>['type'=>'email', 'default'=>$this->app->user->email, 'required'=>1] 
		    ],'cols'=>4];
		
		
		if(!($answers=$this->prompt($form, "Input receivers address")))
			return false;	
		
				
		
		////--------------2nd test----------------------------------
		$opts['subject']="This is test message ".date('Y-m-d H:i:s');
		$opts['body']="You asked to send test message from emails/config";
		
		$opts['to'] = $answers['to'];
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


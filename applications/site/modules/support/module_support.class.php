<?php

class Module_Support  extends GW_Public_Module {

	function init() {		
		$this->config = new GW_Config('support/');
	}

	function viewDefault() {
		//$this->tpl_name = 'support';
	}

	
	function encodeTextMessage($vals)
	{
		$str = '';
		foreach($vals as $key => $val)
		{
			$str .="{$key}\n------------\n{$val}\n";
		}
		return $str;
	}

	function doMessage() 
	{
		$vals = $_POST['item'];
		$msg = GW_Support_Message::singleton()->createNewObject();
		
		$msg->setValues($vals);
		$msg->user_id = $this->app->user ? $this->app->user->id : 0;
		$msg->ip = $_SERVER['REMOTE_ADDR'];
	
		if ($msg->validate()) {
			$msg->insert();
			
			//mail(, 'New support request', $this->encodeTextMessage($vals));
			
			$opts['subject']="Gauta nauja zinute i menuturas.lt (".$vals['subject'].")";

			$str = "Nuo: <b>".$vals['name'].'</b><br />';

			if(isset($vals['phone']))
				$str .= "Telnr: <b>".$vals['phone'].'</b><br />';
			
			if(isset($vals['email']))
				$str .= "El pašto adresas: <b>".$vals['email'].'</b><br />';

			if(isset($vals['message']))
				$str .= "Žinutė: <b><br /><br />".$vals['message'].'</b><br />';
			
			$str .= "<br><small style='color:silver'>Visas gautas žinutes galima matyti admin/support modulyje</small>";

			$opts['body']=$str;

			$opts['to'] = $this->config->notify_mail;
			//$opts['debug'] = 1;

			$status = GW_Mail_Helper::sendMail($opts);		
			
			echo json_encode(['status'=>'1']);
		}else{
			echo json_encode(['status'=>'0']);
		}

		exit;
	}
	
	function doDiscount()
	{
		$_POST['item']['subject']="DISCOUNT 10%";
		$this->doMessage();
	}
	
	function viewIndex()
	{
		
	}
	
	function viewIndexDiscount()
	{
		
	}

}

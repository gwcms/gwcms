<?php

class Module_Did_Contacts extends GW_Public_Module
{
	
	function init()
	{
		
	}
	
	function viewDefault()
	{
		if(GW::$user){
			$this->smarty->assign('user',GW::$user);
		}
		$this->smarty->assign('subject', $_REQUEST['subject']);
	}
	
	function doSend()
	{
		$s = $_REQUEST['subject'];
		$new_subject = $_REQUEST['new_subject'];
		$message = $_REQUEST['message'];
		$name = $_REQUEST['name'];
		$email = $_REQUEST['email'];
		$message = str_replace("\n.", "\n..", $message);
		if ($s == '' || $message == '' || $name == '' || $email == ''){
			GW::$request->setErrors(Array('/GENERAL/MISSING_ARGUMENTS'));
			return;
		}
		switch ($s) {
			case 'Teknisk':
				$subject = '[Teknisk]' . $new_subject;
				break;
			case 'Bestilling':
				$subject = '[Bestilling]' . $new_subject;
				break;
			case 'Faktura':
				$subject = '[Faktura]' . $new_subject;
				break;
			case 'Annet':
				$subject = "[Annet]: " . $new_subject;
				break;
			default:
				$subject = '[Unknown]:' . $new_subject;
			break;
		}
		
		$subject = $subject . " " . date("d.m.y G:i:s");
		$headers = "From: " . $email . "<" . $email . ">\r\n";
		$headers .= "Reply-To: " . $email . "<" . $email . ">";
		$to='dropindesign@gmail.com';
		$message = "User named: " . $name . " with email: " . $email . " sent following message:\r\n\r\n" . $message;
		//$success = mail($to, $subject, $message, $headers);
		if( mail($to, $subject, $message, $headers) )
		{
			GW::$request->setMessage('Success', 0);
		}
		else
		{
			GW::$request->setErrors(Array('error'));
		}
		GW::$request->jump();
		
	}
}
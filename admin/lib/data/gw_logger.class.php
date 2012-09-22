<?php
class GW_Logger
{
	var $file;
	var $STDOUT=0;
	var $date_format='ymd H:i:s';
	var $pre='';

	function replacern($str)
	{
		return str_replace(Array("\r","\n"),Array('\r','\n'),$str);
	}

	function __construct($file=-1)
	{
		if($file===-1)
		{
			$this->STDOUT=1;
		}
		else
		{
			$this->file=$file;
			$this->file=str_replace('\\','/',$this->file);
	
			if(strpos($this->file,'/') === false)
				$this->file=GW::$dir['LOGS'].$this->file;
		}
	}

	function msg($msg)
	{
		$logstr=date($this->date_format).($this->pre ? $this->pre : ' ').$msg;
		if($this->STDOUT)
		{
			echo $logstr."\n";
			flush();
			ob_flush();
		}

		if($this->file)
			file_put_contents($this->file,$logstr."\r\n",FILE_APPEND);
		
	}
	
	function fmsg()
	{
		$args=func_get_args();
		$this->msg(call_user_func_array('sprintf',$args));
	}

	function critical_msg($msg,$inform='mail')
	{
		die('not implemented');
		
		switch($inform)
		{
			case 'sms':
				send_sms(SYS_USR_UID,SYS_ADMIN_TELNR,$msg,1);
			break;

			default:

			case 'mail':
				
				include_once GW_LIB_DIR.'mail.func.php';

				utf_mail(
					Array(
						'to'=>SYS_ADMIN_EMAIL,
						'from'=>'important_msg@gw.lt',
						'subject'=>mb_strlen($msg)>100 ? mb_substr($msg, 0, 100).' ...' : $msg,
						'content'=>str_replace("\n", "<br>",$msg)
					)
				);
			break;

		}
		
		if($this)
			$this->msg($msg,'CRITICAL MSG');
	}
}
<?php


class GW_NL_Message extends GW_Data_Object
{
	var $table = 'gw_nl_messages';

	function parseRecipients($text, $lang, &$list)
	{
		$text = str_replace("\t",'', $text);
		
		$recipients = explode("\n", $text);
		
		foreach($recipients as $recipient)
		{
			$tmp = explode(';', $recipient);;
			if(count($tmp)==2)
				$list[] = ['name'=>$tmp[0], 'email'=>$tmp[1], 'lang'=>$lang];
		}
	}
	
	function beforeSaveParseRecipients()
	{
		$recipients=[];
		$this->parseRecipients($this->recipients_lt, 'lt', $recipients);
		$this->parseRecipients($this->recipients_en, 'en', $recipients);
		$this->parseRecipients($this->recipients_ru, 'ru', $recipients);

		//d::dumpas($recipients);

		$this->recipients_count = count($recipients);
		$this->recipients_data = json_encode($recipients);
	}
	
}			
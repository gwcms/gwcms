<?php


class GW_Mail_Template extends GW_i18n_Data_Object
{
	public $table = 'gw_mail_templates';
	
	public $calculate_fields = ['title'=>1];
	
	public $validators = [
	    'admin_title'=>['gw_string', ['required'=>1]],
	];		
	
	public $default_order="owner_type ASC, owner_field ASC, admin_title ASC";	
	public $i18n_fields = [
	    "subject"=>1,
	    "sender"=>1,
	    "body"=>1,
	    "ln_enabled"=>1
	];
	
	
	public $ownerkey = 'emails/email_templates';	
	public $extensions = ['changetrack'=>1];
	
	public $change_track2 = ["body"=>1];	
	
	
	function validate()
	{
		parent::validate();
		
		$config=json_decode($this->config);
		
		if(!isset($config->no_idname)){
			if($this->count(Array('idname=? AND id!=?', $this->idname, $this->id)))
				$this->errors['idname']='/G/VALIDATION/UNIQUE';			
			
			if(!$this->idname)
				$this->errors['idname']='/G/VALIDATION/REQUIRED';
		}
				
		if($this->count(Array('admin_title=? AND id!=?', $this->admin_title, $this->id)))
			$this->errors['admin_title']='/G/VALIDATION/UNIQUE';		
		
		
		
		return $this->errors ? false : true;	
	}		
	
	
	function calculateField($name) {
		
		switch($name){
			case 'title':
				return $this->admin_title;
			break;
		}
		
		
		parent::calculateField($name);
	}
	

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
	
	
	function getOptions($cond=false)
	{
		return $this->getAssoc(['idname','admin_title'], $cond);
	}
	
	function getOptionsByID($cond=false)
	{
		return $this->getAssoc(['id','admin_title'], $cond);
	}
	
	
		
	function eventHandler($event, &$context_data = array()) {
		
		switch($event){
			case 'BEFORE_SAVE':
								

				//ckeditoriaus fix, kad smarty tage nereplacintu
				foreach($this->changed_fields as $field  => $x){
					if(strpos($field, 'body_')===false)
						continue;
				
					$this->content_base[$field] = 
						preg_replace_callback('/\{.*?\}/is', 
						function($match){ return str_replace('&gt;','>', $match[0]); }, $this->content_base[$field]);
				}
 	
			break;
			

		}
		
		parent::eventHandler($event, $context_data);
	}
		
	
	function findByIdName($idname)
	{
		return $this->find(['idname=?', $idname]);
	}
	
}			
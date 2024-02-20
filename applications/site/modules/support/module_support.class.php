<?php

class Module_Support  extends GW_Public_Module {

	function init() {
		parent::init();
		$this->config = new GW_Config('support/');
		$this->cfg = $this->config;
	}

	function viewDefault() {
		$this->tpl_name = 'support';
		
		$this->tpl_vars['page_title'] = GW::ln("/m/SUPPORT_FORM");
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
		if(!$this->app->user && $this->cfg->recapPublicKey){
			$recaprez = $this->verifyRecaptchaV2();

				//d::dumpas($recaprez);

			if(!$recaprez['pass']){
				$this->setError(GW::ln('/G/validation/RECAPTCHA_FAILED'));
				$this->app->jump();
			}			
		}		
		
		$vals = $_POST['item'];
		$msg = GW_Support_Message::singleton()->createNewObject();
		
		$msg->setValues($vals);
		$msg->user_id = $this->app->user ? $this->app->user->id : 0;
		$msg->ip = $_SERVER['REMOTE_ADDR'];
		
		if($this->app->user)
			$vals['name'] = "Vartotojas id: ".$this->app->user->id.", ".$this->app->user->title;
	
		if ($msg->validate()) {
			$msg->insert();
			
			//mail(, 'New support request', $this->encodeTextMessage($vals));
			
			if(isset($vals['date'])){
				$opts['subject']="Gauta nauja zinute i ".$_SERVER['HTTP_HOST']." (".($vals['subject'] ?? 'betemos').")";
			}else{
				$opts['subject']="Užregistruota specialisto konsultacija! ".$_SERVER['HTTP_HOST']." (".($vals['subject'] ?? 'betemos').")";
			}
			

			$str = "Nuo: <b>".$vals['name'].'</b><br />';

			if(isset($vals['date']))
				$str .= "Data: <b>".$vals['date'].'</b><br />';			
			
			if(isset($vals['time']))
				$str .= "Laikas: <b>".$vals['time'].'</b><br />';		
			
			if(isset($vals['subject']))
				$str .= "Tema: <b>".$vals['subject'].'</b><br />';				
			
			if(isset($vals['phone']))
				$str .= "Telnr: <b>".$vals['phone'].'</b><br />';
			
			if(isset($vals['email'])){
				$str .= "El pašto adresas: <b>".$vals['email'].'</b><br />';
				$opts['replyto'] = $vals['email'];
			}
			
			if(isset($this->app->user->email)){
				$str .= "El pašto adresas: <b>".$this->app->user->email.'</b><br />';
				$opts['replyto'] = $this->app->user->email;
			}
			if(isset($this->app->user->phone)){
				$str .= "Telnr: <b>".$this->app->user->phone.'</b><br />';
			}			
			
			

			if(isset($vals['message']))
				$str .= "Žinutė: <b><br /><br />".$vals['message'].'</b><br />';
			
			$str .= "<br><small style='color:silver'>Visas gautas žinutes galima matyti admin/support modulyje</small>";

			$opts['body']=$str;

			$opts['to'] = $this->config->notify_mail;
			//$opts['debug'] = 1;

			$status = GW_Mail_Helper::sendMail($opts);		
			
			if($_GET['json'])
				die(json_encode(['status'=>'1']));
			
			if(isset($vals['date'])){
				$this->setMessage(GW::ln('/m/APPOINTMENT_REGISTERED'));
			}else{
				$this->setMessage(GW::ln('/m/MESSAGE_SENT'));

			}
			
			
			$this->jump('/');
		}else{
			if($_GET['json'])
				die(json_encode(['status'=>'0']));
			
			
			$this->jump();
		}

		
	}
	
	function doDiscount()
	{
		$_POST['item']['subject']=" Naujas prenumeratorius";
		$this->doMessage();
	}
	
	function doNewsLetter()
	{
		$_POST['item']['subject']=" Naujas prenumeratorius";
		$this->doMessage();
	}	
	
	
	function viewIndex()
	{
		
	}
	
	function viewSubscribe()
	{
		
	}
	
	function verifyRecaptchaV2()
	{
		//https://www.google.com/u/3/recaptcha/admin/site/437873903
		
		if(isset($_POST['g-recaptcha-response'])){
			$captcha=$_POST['g-recaptcha-response'];
		}
		
		if(!$captcha){
			return [];
		}
		$secretKey = $this->cfg->recapPrivateKey;
		$ip = $_SERVER['REMOTE_ADDR'];
		// post request to server
		$url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secretKey) .  '&response=' . urlencode($captcha);
		$response = file_get_contents($url);
		$arrResponse = json_decode($response,true);
	      // should return JSON with success as true
		if($arrResponse["success"]) {
			$arrResponse['pass']=1;
		} else {
			$arrResponse['pass']=1;
		}
		
		return $arrResponse;
	}	
	function  viewAppointment()
	{
		$ids = json_decode($this->config->appointment_topic_ids, true);
		$this->tpl_vars['subject_opt'] = GW_Classificators::singleton()->getKeyTitleOptions(GW_DB::inCondition('id', $ids), $this->app->ln);
		
		
		
		$this->tpl_vars['item'] = GW_Support_Message::singleton()->createNewObject();
	}

}

	


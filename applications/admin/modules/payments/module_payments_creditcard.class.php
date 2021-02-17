<?php


class Module_Payments_CreditCard extends GW_Common_Module
{	
	
	function init()
	{	
		parent::init();
		
		$this->list_params['paging_enabled']=1;	
		

		
	}
	
	function eventHandler($event, &$context) {
		
		switch($event){
			case 'BEFORE_SAVE_CONFIG':
				
				$vals =& $context;
				
				$enc_key = GW::s('CC_ENC_STR');
					
				if($vals['new_storage_pass']){
					
					$currentpass = GW_Config::singleton()->get('datasources__payments_creditcard/storage_pass');
					
					if($currentpass)
						$currentpass = GW::db()->aesCrypt($vals['storage_pass'], $enc_key, true);
					
					if($vals['storage_pass'] && $vals['old_storage_pass']!=$currentpass){
						$this->dialog_iframe_errors = true;
						$this->setError('Cant set storage pass, old pass is wrong');
					}else{
						$vals['storage_pass'] = GW::db()->aesCrypt($vals['new_storage_pass'], $enc_key);
					}
					
					
					
					$vals['old_storage_pass']='';
					$vals['new_storage_pass']='';
					
				}
				
				
				
			break;
			
		}
		
		parent::eventHandler($event, $context);
	}
	
	function doEncrypt()
	{
		
		$item = $this->getDataObjectById();

		if($item->encrypted==0)
		{
			$item->crypt();
			$this->setMessage('Encrypt done');
		}
	}
	
	function doDecrypt()
	{
		$item = $this->getDataObjectById();

		if($item->encrypted==1){
			$item->crypt(true);
		
			$this->setMessage('Decrypt done');
		}
	}	
	
	
	function viewShowDecrypted()
	{
		$item = $this->getDataObjectById();
		
		$this->tpl_vars['item'] = $item;
		
			
		if($key=$this->getTempPw()){
			
			
			$decoded = GW::db()->aesCrypt($item->num_cvc_exp, $key, true);
			if(!$decoded){
				$this->setError("Bad password");
			}else{
				$item->num_cvc_exp = $decoded;	
				$this->tpl_vars['decoded']=true;
				//dont save item!!!
			}
		}
	}
	function doSetPw()
	{
		$vals = $_POST['item'];
				
			// GW::db()->aesCrypt($item->num_cvc_exp, $key, true);
		$this->app->sess['credcard_decr_temp_pw_expires'] = strtotime('+'.$vals['valid'].'seconds');
		$this->app->sess['credcard_decr_temp_pw'] = GW::db()->aesCrypt($vals['pw'], GW::s('CC_ENC_STR'));
		
		$this->jump();
	}
	
	function getTempPw()
	{
		$timediff = ($this->app->sess['credcard_decr_temp_pw_expires'] ?? 0)-time();
		if($timediff > 0){
			return GW::db()->aesCrypt($this->app->sess['credcard_decr_temp_pw'], GW::s('CC_ENC_STR'), true);
		}
		//time()-($this->app->sess->credcard_decr_temp_pw_expires?? 0)<0
	}
	
	
	function viewShow()
	{
		
		$item = $this->getDataObjectById();
		$passenc = GW_Config::singleton()->get('datasources__payments_creditcard/storage_pass');
		$pass = GW::db()->aesCrypt($passenc, GW::s('CC_ENC_STR'), true);
	}
/*	
	function __eventAfterList(&$list)
	{
		
	}

	function init()
	{
		parent::init();
	}
 
 */	
}

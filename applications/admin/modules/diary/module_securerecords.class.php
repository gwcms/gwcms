<?php


class Module_SecureRecords extends GW_Common_Module
{	
	use Module_Import_Export_Trait;
	
	function init()
	{	
		$this->model = GW_Secure_Record::singleton();
		
		parent::init();
		
		$this->list_params['paging_enabled']=false;	
		

		
	}
	


	
	function getListConfig()
	{
		
		$cfg = array('fields' => [
			'id' => 'Lof', 
			'user_id' => 'Lof',
			'title'=> 'Lof',
			'username'=>'Lof',
			'pass'=>'Lof',		    
			'comments'=>'Lof',
			'encrypted'=>'Lof',
			'insert_time'=>'lof',
			'update_time'=>'lof',		    
			]
		);
		
		//$cfg['filters']['project_id'] = ['type'=>'select','options'=>$this->options['project_id']];
					
		return $cfg;
	}
	
	/*
	function __eventAfterList(&$list)
	{
		$this->attachFieldOptions($list, 'composer_id', 'IPMC_Composer');
		
		
		$pieces0 = IPMC_Competition_Pieces::singleton();
	
		foreach($list as $item)
			$item->rel_pieces = $pieces0->count(['composition_part_id=?', $item->id]);			
	}
*/

	
	function __eventBeforeSave($item)
	{
		
		//d::dumpas($item);
		$item->user_id = $this->app->user->id;
	}
		
	function viewLockUnlock()
	{
		
	}
		
	function doEncrypt()
	{
	
		
		$enc_key = GW_DB::escape($_POST['item']['encryptkey']);
		$encrypt = $_POST['encrypt_1_decrypt_0'];
		
		$f = $encrypt ? 'AES_ENCRYPT' : 'AES_DECRYPT';
		$e = $encrypt ? 1 : 0;
		$note = $encrypt ? 0 : 1;
		
		$uid = (int)$this->app->user->id;
		
		$extra_set ="";
		$extra_cond ="";
		
		if($encrypt){
			$extra_set = "test=AES_ENCRYPT('testcheck', '$enc_key'),";
		}else{
			$extra_cond="AES_DECRYPT(test)='testcheck'";
		}
		
		$this->model->getDB()->query($q="
			UPDATE 
				`gw_secure_records` 
			SET 
				username=$f(username, '$enc_key'), 
				$extra_set
			encrypted=$e 
			WHERE user_id=$uid AND encrypted=$note $extra_cond
				
		");
		
		//d::dumpas($q);
		
		$this->jumpAfterSave();
	}

	

}

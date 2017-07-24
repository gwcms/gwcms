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
		
		$this->model->getDB()->query("UPDATE `gw_secure_records` SET username=$f(username, UNHEX(SHA2('$enc_key',512))));");
		
		$this->jumpAfterSave();
	}

	

}

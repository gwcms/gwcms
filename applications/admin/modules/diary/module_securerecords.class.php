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
		
	function viewUnlock()
	{
		
	}
	function viewLock()
	{
		
	}	
	
	private $enc_fields = ['username','pass','comments'];
	
	
	function doEncrypt()
	{
	
		$vals = $_POST['item'];
		$enc_key = GW_DB::escape($vals['encryptkey']);
		$encrypt = $_POST['encrypt_1_decrypt_0'];
		
		$id_cond=isset($_POST['id']) ? " AND id=".(int)$_POST['id'] : "";
		
		if($encrypt && $vals['encryptkey']!=$vals['encryptkey_repeat']){
			$this->setError("Encrypt keys don't match");
			$this->jumpAfterSave();
		}
		
		
		$f = $encrypt ? 'AES_ENCRYPT' : 'AES_DECRYPT';
		$e = $encrypt ? 1 : 0;
		$note = $encrypt ? 0 : 1;
		$action = $encrypt ? 'encrypted':'decrypted';
		
		
		$uid = (int)$this->app->user->id;
		
		$extra_set ="";
		$extra_cond ="";
		$randstr = "1fas2d1gt5hs2e41tq652f1d56m5f1";
		
		if($encrypt){
			$extra_set = "test=AES_ENCRYPT(md5('$randstr'), '$enc_key'),";
		}else{
			$extra_cond="AND AES_DECRYPT(test,'$enc_key')=md5('$randstr')";
		}
		
		
		$set ="";
		
		foreach($this->enc_fields as $field)
			$set .= "$field=$f($field, SHA2('$enc_key',512)), ";
		
		$this->model->getDB()->query($q="
			UPDATE 
				`gw_secure_records` 
			SET 
				$set
				$extra_set
			encrypted=$e 
			WHERE user_id=$uid AND encrypted=$note $extra_cond $id_cond
				
		");
		
		$affected = $this->model->getDB()->affected();
		
		if($affected)
			$this->setMessage("Records $action: $affected");
		else
			$this->setError("Bad news");
		
		
		//d::dumpas($q);
		
		$this->jumpAfterSave();
	}
	
	function viewShow()
	{
		//$id = $_GET['id'];
		//$this->find())
	}
	
	function doShowOne()
	{
		$id = $_GET['id'];
		$enc_key =  $_POST['item']['encryptkey'];
		$f = 'AES_DECRYPT';
		$select = [];
			
		foreach($this->enc_fields as $field)
			$select[]= "$f($field, SHA2('$enc_key',512)) AS $field ";
		
		$res = $this->model->find(['id=?', $id],['select'=>implode(',', $select)]);
		echo "<div style='color:#eee'>";
		//print_r($res->toArray());
		
		foreach($res->toArray() as $key => $val)
		{
			echo "<i>{$key}</i>: {$val}<br/>";
		}
		
		exit;
	}
	

	

}

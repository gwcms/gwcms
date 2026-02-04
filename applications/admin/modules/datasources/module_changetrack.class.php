<?php


class Module_ChangeTrack extends GW_Common_Module
{	
	
	function init()
	{	
		parent::init();
		
		$this->list_params['paging_enabled']=1;	
		
		$this->app->carry_params['owner_type']=1;
		$this->app->carry_params['owner_id']=1;
		$this->app->carry_params['clean']=1;
		
		$this->filters['owner_type']=$_GET['owner_type'];
		$this->filters['owner_id']=$_GET['owner_id'];
		
		$this->paging_enabled = false;
		
		
		
		$this->checkOwnerPermission($this->filters['owner_type']);
		
	}
	
	
	
	function checkOwnerPermission($owner_type, $error = true)
	{
		if(!($res = GW_Permissions::canAccess($owner_type, $this->app->user->group_ids)))
		{
			$this->setError(GW::l('/G/GENERAL/ACTION_RESTRICTED').' ("'.$owner_type.'"; "'.$res.'")');
		}
		
		return $res;
	}
	
	
	function canBeAccessed($item, $opts=[]) 
	{
		if($item){
			$item->load_if_not_loaded();
	
			$result = $this->checkOwnerPermission($item->owner_type);
		}else{
			$result = $this->app->user->isRoot();
		}

		if (!isset($opts['die']) || $result)
			return $result;

		$this->jump();
	}	
	
	
	function getListConfig()
	{

		$cfg = parent::getListConfig();
		
		if(isset($this->filters['owner_type'])){
			unset($cfg['fields']['owner_type']);
		}
		
		if(isset($this->filters['owner_id'])){
			unset($cfg['fields']['owner_id']);
		}
		
		$cfg['fields']['username'] = 'Lo';
		$cfg['fields']['note'] = 'Lo';
		
		unset($cfg['fields']['update_time']);

		return $cfg;
	}
		
	function __eventBeforeListParams(&$params)
	{		
		
		$params['key_field']='id';
		
		
		$params['select']='a.*, usr.username';

			
		$params['joins']=[
		    ['left','gw_users AS usr','a.user_id = usr.id'],
		];	
								
		
		
	}	
	

}

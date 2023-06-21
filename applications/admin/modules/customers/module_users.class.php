<?php

class Module_Users extends GW_Common_Module 
{

	
	function initAdmin()
	{
		$this->rootadmin = $this->app->user->isRoot();
		
		$this->isSuperAdmin = in_array($this->cfg->superadmin_group, $this->app->user->group_ids);
	}	
	
	function init() 
	{
		//$this->filters['id'] = isset(GW::$app->path_arr[1]['data_object_id']) ? GW::$app->path_arr[1]['data_object_id'] : false;

		parent::init();
		$this->config = $this->cfg = new GW_Config($this->module_path[0].'/');
		
		
		$this->rootadmin = $this->app->user->isRoot();
		
		$this->initAdmin();
		
		if(!$this->rootadmin && !$this->isSuperAdmin){
			$this->filters['parent_user_id'] = $this->app->user->id;
		}
			
		
		//d::dumpas($this->options['languages']);
		
		$this->list_params['paging_enabled']=1;
		
		
		$this->fields_required =  array_flip((array)json_decode($this->cfg->registration_fields_required));
		$this->fields_optional =  array_flip((array)json_decode($this->cfg->registration_fields_optional));		
		$this->enabled_fields = $this->fields_required+$this->fields_optional;
		
		///part of permission system, allow create if dont have write permission
		$this->canCreateNew = 1;
	
		$this->initFeatures();
		
	}
	
	function __eventAfterForm($item)
	{
		
	}
	
	function __eventAfterList($list)
	{
	
	
		
	}
	
	function getListConfig()
	{
		$cfg = parent::getListConfig();
		

		
		return $cfg;
	}	
	
	
	function viewDefault()
	{
		$this->viewList();
	}
	
	function doLoginAs()
	{
		//$_SESSION[PUBLIC_AUTH_SESSION_KEY] = ['user_id'=>$_GET['user_id'], 'ip_address'=>$_SERVER['REMOTE_ADDR']];
		//Header('Location: '.Navigator::getBase().$_GET['redirect_url']);
		
		if(!$item = $this->getDataObjectById())
			return;
		
		$this->canBeAccessed($item, true);	
		
		// jei ne root tai neleisti pasikeisti i root
		
		// jei admin grupej iseiti
		
		

		GW_Auth::adminLoginToSite($_GET['id'], $this->app->user->id);
		
		
		
		
		
		$redir_url="/{$this->app->ln}/direct/users/users?act=doAfterLogin";
		$redir_url.='&redirect_url='. (($_GET['redirect_url'] ?? false) ? urlencode($_GET['redirect_url']):'/');
		$url = Navigator::getBase(). ltrim($redir_url,'/');

		Header('Location: '.$redir_url);	

			
		
	}
	
	function eventHandler($event, &$context) 
	{
		switch($event)
		{
			case "BEFORE_SAVE_0":
				
				$item = $context;
				
				if($item->id){
					$item->setValidators('update');
				}else{
					if(isset($_GET['quickinsert'])){
						
						if($item->country!='LT')
							$item->setValidators('quick_insert_foreigner');
						else
							$item->setValidators('quick_insert');
					}else{
						$item->setValidators('insert');
					}
						
					
					
					$item->group_ids = [$this->cfg->customer_group];
					
					$item->parent_user_id = $this->app->user->id;
				}
				
				
				
			break;
		}
		
		parent::eventHandler($event, $context);
	}

	public $options_search_field = "concat(name,' ','surname', ' ',club)";
	
	function getOptionsCfg()
	{
		$opts = [
		    'title_func'=>function($o){ 
			$ms = $o->getActiveMembership();
			$single = !$ms ? $o->getUnusedSingleUseLic() : false;
			return 
			$o->id." ".$o->title. (isset($_GET['birthdate'])?' ('.explode('-',$o->birthdate)[0].')':'').($ms ? ' '.date('Y').'-'.$ms->id: ($single?" SINGLE-LIC":' NO-LIC') );
		    },
		    'search_fields'=>['name','surname','email'],
		];	
		$opts['condition_add'] = 'removed=0';
		    
		    
		if(isset($_GET['gender']))
			$opts['condition_add'].= ' AND '. GW_DB::prepare_query(['gender=?',$_GET['gender']]);
		
		if(isset($_GET['club']))
			$opts['condition_add'].= ' AND '. GW_DB::inCondition('club',explode(',',$_GET['club']));		
		
		if(isset($_GET['active']))
			$opts['condition_add'] .= ' AND  active=1';
		    
		
		return $opts;	
	}	

	function doSetLicId()
	{
		$item = $this->getDataObjectById();
		$item->setLicId();
		$this->setMessage("doSetLicId: $item->lic_id");
		$this->jump();
	}
	
	
	//dadeti info kada pasiupdeitino
	
	
	
	
	
	
	function doRecover()
	{
		$item = $this->getDataObjectById();
		$item->fireEvent('BEFORE_CHANGES');
		$item->removed = 0;
		$item->updateChanged();
		$this->setMessage("Recovered #$item->id");
	}
	
	
	
	function doRealRemove()
	{
		$ids = $this->acceptIds(__FUNCTION__);

		
		$list = $this->model->findAll(GW_DB::inCondition('id', $ids));
		$succ = 0;
		
		
		$this->confirm('!!! Real Remove '.count($list).' items ???');
		
		foreach($list as $item){
			$item->realDelete();
			$succ++;
		}

		$this->setMessage("Action performed on $succ items");
		$this->jump();
	}	
	

	

}


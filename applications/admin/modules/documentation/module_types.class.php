<?php


class Module_Types extends GW_Common_Module
{	
	
	function init(){
		parent::init();
		
		
		$this->app->carry_params=['clean'=>1];
	}
	
	
	function viewOptions()
	{
		
		$cond = isset($_GET['q']) ? GW_DB::prepare_query(['title LIKE ?', '%'.$_GET['q'].'%']) : false;
		
		$opts = $this->model->getOptions($cond);
		
		$list = [];
		
		foreach($opts as $id => $text)
			$list['items'][]=["id"=>$id, "title"=>$text];
		
		echo json_encode($list);
		exit;
	}
	
	
	//disable filtering feature
	function prepareListConfig($item=false)
	{
		parent::prepareListConfig($item);
		
		$this->list_config['dl_filters'] = [];
	}


	//dont show some fields if it isnt asked
	function getListConfig()
	{
		$cfg = parent::getListConfig();
		

		//dont show at first time
		foreach(['id','insert_time','update_time'] as $field)
			$cfg['fields'][$field] = str_replace('L', 'l', $cfg['fields'][$field]);
		
		return $cfg;
	}
	
	
	function doGetIcons()
	{
		$webdir = 'applications/admin/static/img/icons/';
		$base = GW::s('DIR/ROOT').$webdir;
		$icons = GW_File_Helper::rglob($base.'*');

		$formated = [
				["id"=>"browse", 'text'=>'Browse..', 'parent'=>'#'],
				["id"=>"root", 'text'=>'Root', 'parent'=>'browse']
		];
		
		foreach($icons as $icopath){
			
			$rel = str_replace($base, '', $icopath);
			$parent = dirname($rel);
			
			if(is_file($icopath)){				
				$formated[] = ["icon"=>'/'.$webdir.$rel, 'id'=>$rel,'text'=>$rel, "parent"=> $parent=='.' ? 'root': $parent];
			}else{
				$formated[] = ['id'=>$rel, 'text'=>$rel, "parent"=>  $parent=='.' ? 'browse': $parent];
			}	
			
		}
		
		echo json_encode($formated);
		exit;
	}
	
	function eventHandler($event, &$context) {
		
		
		
		switch ($event){
			case 'BEFORE_SAVE_0':
				$item = $context;
								
				if($item->iconfromrepository){
					$item->icon = $item->iconfromrepository;
					
					//d::dumpas($item->icon);
					$item->ignore_fields['iconfromrepository']=1;
					
				}
			break;
			case 'AFTER_SAVE':
				$item = $context;
				//d::dumpas($item->icon);
			break;
		}
		
		
		parent::eventHandler($event, $context);
		
		
	}
	
	
	
}
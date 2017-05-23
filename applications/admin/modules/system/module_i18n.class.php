<?php


class Module_i18n extends GW_Common_Module
{
	function init()
	{
		$this->model = new GW_ADM_Page();//nebutinas uzloadina per lang faila jei nurodyta
		parent::init();
	}
	
	
	function viewList()
	{
		$list = $this->model->getChilds(Array('menu'=>true));
		
		$list1=Array();
		
		foreach($list as $item)
		{
			$list1[]=$item;
			$childs=$item->findAll("path LIKE '$item->path/%'");
			
			$list1=array_merge($list1, $childs);
			
			
		}
		
		
		foreach($list1 as $i => $item)
		{
			if(!isset($item->info['model']) || !$item->info['model']){
				unset($list1[$i]);
				continue;
			}
			
			$classname = $item->info['model'];
			
			
			$obj = new $classname;
			
			if(!is_subclass_of($obj, 'GW_i18n_Data_Object'))
				unset($list1[$i]);
		}
		
		return ['list'=>$list1];	
	}
	
	function doMove($params=false)
	{
		if(! ($item = $this->getDataObjectById()))
			return $this->jump();
		
		$item->move($_REQUEST['where'], "parent_id=".(int)$item->get('parent_id'));
		
		$this->jump(false, ['id'=>$item->get('id')]);
	}
	
	function doGetNotes()
	{
		
		$item = $this->model->getByPath($_REQUEST['path']);
		
		$this->canBeAccessed($item, true);
		
		echo $item->notes;
		exit;
	}
	
	
	function doAddLang()
	{
		if(!$this->app->user->isRoot())
			die('cant access');
		
		$model_class=$_GET['model'];
		$model = new $model_class;
		
			
		$sqls = $model->addLang(GW::$settings['LANGS'][0], $_GET['modlang']);
		
		
		echo implode(";\n ",$sqls);
	}
	
	function doDropLang()
	{
		if(!$this->app->user->isRoot())
			die('cant access');
		
		$model_class=$_GET['model'];
		$model = new $model_class;
		
		$sqls = $model->dropLang($_GET['modlang']);
		
		echo implode(";\n ",$sqls);
	}	
	
}

?>

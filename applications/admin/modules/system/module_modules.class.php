<?php


class Module_Modules extends GW_Common_Module
{
	function init()
	{
		$this->model = new GW_ADM_Page();//nebutinas uzloadina per lang faila jei nurodyta
		parent::init();
	}
	
	
	function viewList()
	{
		$list = $this->model->getChilds(Array('menu'=>false));
		
		$list1=Array();
		
		foreach($list as $item)
		{
			$list1[]=$item;
			$childs=$item->findAll("path LIKE '$item->path/%'");
			
			$list1=array_merge($list1, $childs);
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
	
	
	function viewRearange()
	{
		$list = $this->model->getChilds(Array('menu'=>false));
		
		return ['list'=>$list];		
	}
	
	function doSavePositions()
	{
		$positions = json_decode($_POST['positions'], true);
		
		$items = $this->model->findAll('parent_id=0', ['key_field'=>'id']);
				
		
		$debug=[];
		
		$idx=0;
		$updated=0;
		foreach($positions as $row)
		{
			if(isset($row['id']) && $row['id']!='0')
			{
				
				$itm = $items[$row['id']];
				
				if($itm->priority != $idx){
					$itm->saveValues(['priority'=>$idx]);
					$updated++;
				}
				$idx++;
				
				$debug[]=[$itm->title, $idx, $itm->errors];
				
			}
			
		}
		
		
		echo "Updated: $updated";
		exit;
	}
	
	function doAddSeparator()
	{
		$itm = $this->model->createNewObject();

		
		foreach(GW::$settings['LANGS'] as $lncode){
			$itm->set("title", $_GET['title'], $lncode);
		}		
		
		$itm->path = 'separator';
		$itm->parent_id = 0;
		$itm->priority = -1;
		$itm->insert();
		
		$this->setPlainMessage("/g/SAVE_SUCCESS");
		

		
		$this->jump();
	}
	
	
	function doSyncFromXmls()
	{
		$t = new GW_Timer;
		$msgs = GW_ADM_Sitemap_Helper::updateSitemap();
		
		if($msgs)
			foreach($msgs as $msg)
				$this->setMessage(['type' => GW_MSG_INFO, 'text'=>$msg, 'float'=>1, 'footer'=>$t->stop().'s']);		
	}
	
}

?>

<?php


class GW_Common_Module_Tree_Data extends GW_Common_Module
{
	function init()
	{	
		parent::init();

		//pernesti $_GET[pid] per jump'us ir per FH::gw_link generuojamus linkus
		$this->app->carry_params['pid']=1;
		
		$this->filters['parent_id']=isset($_GET['pid']) && (int)$_GET['pid']?(int)$_GET['pid']:-1;	
		
		
		//jeigu vykdomas filtravimas rodyti visus elementus
		if(isset($this->list_params['filters']) && $this->list_params['filters'])
			unset($this->filters['parent_id']);		
		
		
		
		//uzloadinti tevini irasa
		$this->parent=$this->model->createNewObject(isset($this->filters['parent_id']) ?  $this->filters['parent_id'] : false);
		$this->parent->load();
				
		
		//jeigu veiksmo pakvietimas - nevykdyti
		if(!isset($_GET['act']))
			$this->breadcrumbsAttach();
		
	}
	
	function getMoveCondition($item)
	{
		$tmp = $this->filters;
		$tmp['type']=$item->get('type');
		
		return GW_SQL_Helper::condition_str($tmp);
	}	
	
	
	//rodyti kelia breadcrumbs juostoje
	//pvz ieiname i paveikslu galerijos kataloga Menas ir dar i vidini kataloga Rytu menas
	//tuomet rodys Galerija > irasai > Menas > Rytu menas
	function breadcrumbsAttach()
	{
		if(! $this->parent->title)
			return;
		
		$breadcrumbs_attach=Array();
		
		foreach($this->parent->getParents() as $item)
			$breadcrumbs_attach[]=Array
			(
				'path'=>$this->app->fh()->gw_path(Array('params' => Array('pid'=>$item->id) )),
				'title'=>$item->title
			);
		
		$breadcrumbs_attach[]=Array('title'=>$this->parent->title, 'path'=>$this->app->fh()->gw_path(Array('params' => Array('pid'=>$this->parent->id) )));
		
		
		$this->smarty->assign('breadcrumbs_attach', $breadcrumbs_attach);
	}	

}
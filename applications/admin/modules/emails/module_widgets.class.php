<?php

class Module_Widgets extends GW_Module
{

	public $is_widget_module = true;
	
	function init()
	{
		//$this->__processViewSolveViewName();
		//$this->db =& $this->app->db;
		//$this->tpl_dir="{$this->module_dir}tpl/".$this->module_name."/";

		//$this->smarty = $this->app->smarty;		
		
		
		parent::init();
	}
	
	
	
	
	
	function viewProgress()
	{
		$list = GW_NL_Message::singleton()->findAll('status=10',['select'=>'recipients_total,sent_count,id,title']);
				
		$this->tpl_vars['messages_in_progres'] = $list;
	}
}

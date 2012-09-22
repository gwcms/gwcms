<?

class Module_Config extends GW_Module
{	
	function init()
	{
		parent::init();
	}
	
	function viewDefault()
	{
		GW::$request->jumpToFirstChild();
	}
}


<?php

class Module_Config extends GW_Module_Config_Common
{
	function init()
	{
		parent::init();
		$this->model = new GW_Config('expenses/');
	}
}

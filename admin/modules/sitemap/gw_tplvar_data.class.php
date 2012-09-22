<?php



class GW_TplVar_Data extends GW_Data_Object
{
	var $table = 'gw_sitemap_data';
	
	function getByPage($page_id)
	{
		return $this->findAll(Array('page_id=?',$page_id));
	}
}

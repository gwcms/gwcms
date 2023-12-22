<?php


class GW_Article extends GW_Composite_Data_Object
{
	var $table = 'gw_articles';
	public $default_order = 'priority DESC, insert_time DESC';
	
	var $composite_map = Array
	(
		'image' => Array('gw_image', Array('dimensions_resize'=>'800x600', 'dimensions_min'=> '100x100')),
	);
	
	public $ownerkey = 'articles/articles';
	public $extensions = ['attachments'=>1];



	function getAttachments($ln)
	{
		return $this->extensions['attachments']->findAll(["field=?", "attachments_{$ln}"]);		
	}	
}
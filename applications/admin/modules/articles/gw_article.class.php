<?php


class GW_Article extends GW_i18n_Data_Object
{
	var $table = 'gw_articles';
	public $default_order = 'priority DESC, insert_time DESC';
	
	var $composite_map = Array
	(
		'image' => Array('gw_image', Array('dimensions_resize'=>'800x600', 'dimensions_min'=> '100x100')),
	);
	
	public $ownerkey = 'articles/articles';
	public $extensions = ['attachments'=>1];
	public $i18n_fields = [
	    'title'=>1,
	    'short'=>1,
	    'text'=>1,
	];	


	function getAttachments($ln=false)
	{
		if($ln)
			$ln="_$ln";
		
		return $this->extensions['attachments']->findAll(["field=?", "attachments{$ln}"]);		
	}	
	
	function getAttachmentByLtTitle($title){
		if($attachment = $this->extensions['attachments']->getByTitle($title,'lt')){
			return $attachment->image;
		}
	}
}
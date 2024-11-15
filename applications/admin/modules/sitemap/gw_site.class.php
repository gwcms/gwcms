<?php

class GW_Site extends GW_i18n_Data_Object
{
	public $table = 'gw_sites';
	
	public $i18n_fields = ['title' => 1];	
	
	public $calculate_fields = [
	    'count_sitemap'=>1,
	    'relations'=>1,
	];
	public $encode_fields = ['langs'=>'comma'];
	

		
	public $composite_map = Array
	(
		
		'favico' => Array('gw_image', Array('dimensions_resize'=>'500x500', 'dimensions_min'=> '32x32')),
	);
	


	function calculateField($key)
	{
		switch ($key) {
			case 'count_sitemap':
				return GW_Page::singleton()->count("site_id=".(int)$this->id);
			break;
			case 'relations':
				
				return [
				    'sitemap'=> $this->count_sitemap,
				    'blocks'=>GW_Site_Block::singleton()->count("site_id=".(int)$this->id)
				    ];
			break;
			//case 'ext':
			//	return new IPMC_Competition_Extended($this->id);
		}
	}	
	
	function eventHandler($event, &$context_data = [])
	{
		switch ($event) {
			case 'BEFORE_SOMETHING':
			break;
		}
		
		parent::eventHandler($event);
	}

	function getOptions($lang='lt')
	{
		return $this->getAssoc(['id','title_'.$lang],'', ['order'=>'title_'.$lang.' ASC']);
	}	
	
	function getOptionsKey()
	{
		return $this->getAssoc(['id','key']);
	}		
	

}
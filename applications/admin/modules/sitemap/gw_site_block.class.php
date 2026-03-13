<?php
/**
 *
 * @author vidmantas
 *
 */




class GW_Site_Block extends GW_Data_Object
{
	public $table = 'gw_site_blocks';
	public $default_order = 'priority ASC';


	public $calculate_fields = [
	    'title'=>1,
	];

	public $keyval_use_generic_table=1;
	public $ownerkey = 'sitemap/blocks';
	
	public $extensions = [
	    //'keyval'=>1,
	    'changetrack'=>1
	];

	function calculateField($key)
	{
		switch ($key) {
			case 'title':
				return $this->name;
			break;

		}
	}	
	
	function __toString()
	{
		return $this->contents ?? '';
	}
}

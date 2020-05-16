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

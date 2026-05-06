<?php

class GW_Change_Transaction extends GW_Data_Object
{
	public $table = 'gw_change_transactions';
	
	public $calculate_fields = [
		'changetrack_count' => 1,
	];
	
	public $encode_fields = [
		'meta' => 'jsono',
	];
	
	function calculateField($name)
	{
		switch($name){
			case 'changetrack_count':
				return GW_Change_Track::singleton()->count(['transaction_id=?', $this->id]);
		}
		
		return parent::calculateField($name);
	}
}

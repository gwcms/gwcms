<?php

class GW_Config_Change_Track extends GW_Data_Object
{
	public $table = 'gw_config_change_track';

	public $encode_fields = [
		'new' => 'jsono',
		'old' => 'jsono',
		'diff' => 'jsono',
	];
}

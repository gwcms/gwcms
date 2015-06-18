<?php

interface GW_Composite_Slave
{
	public function setParams($params);
	public function setOwnerObject($master, $fieldname);
	public function getByOwnerObject($master, $fieldname);
	public function save();
	public function delete();
	public function getValue();
    
}
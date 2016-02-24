<?php

interface GW_Composite_Slave {

	//paruosimas slave domenu operacijos
	public function setParams($params);

	public function setOwnerObject($master, $fieldname);

	public function getByOwnerObject($master, $fieldname);

	//saugojimas
	public function save();

	//pasalinimas
	public function deleteComposite();

	//duomenu paemimas
	public function getValue();
}

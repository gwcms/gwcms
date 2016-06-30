<?php


class gw_phonecountry_code_test extends gw_testclass
{
	
	function testGetCountryByPhone()
	{
		
		
		
		$this->assertEquals($this->testobj->getCountryByPhone('37060089089'), 'Lithuania');
		
		$this->assertEquals($this->testobj->getCountryByPhone('42037052077886'), 'Czech Republic');
		$this->assertEquals($this->testobj->getCountryByPhone('42037052077886'), 'Kaunas');
		
	}
}
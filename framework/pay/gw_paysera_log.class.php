<?php

class GW_Paysera_Log extends GW_Data_Object
{

	public $calculate_fields = ['title'=>1];
	public $table = 'gw_paysera_log';
	
	public function calculateField($name) 
	{
				
		switch($name){
			case 'title':
				return $this->orderid.' - '.($this->amount/100).' '.$this->currency. ($this->test ? ' (!TEST)':'');
			break;
		}
		
		
	}

}

/*
CREATE TABLE IF NOT EXISTS `gw_paysera_log` (
  `id` int(11) NOT NULL,
  `orderid` int(11) NOT NULL,
  `handler` varchar(15) NOT NULL,
  `action` varchar(15) NOT NULL,
  `paytext` varchar(255) NOT NULL,
  `p_firstname` varchar(100) NOT NULL,
  `p_lastname` varchar(100) NOT NULL,
  `p_email` varchar(255) NOT NULL,
  `amount` int(11) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `country` varchar(10) NOT NULL,
  `test` tinyint(4) NOT NULL,
  `payment` varchar(10) NOT NULL,
  `m_pay_restored` varchar(15) NOT NULL,
  `status` int(11) NOT NULL,
  `requestid` varchar(15) NOT NULL,
  `payamount` int(11) NOT NULL,
  `paycurrency` varchar(10) NOT NULL,
  `type` varchar(10) NOT NULL,
  `handler_state` int(11) NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gw_paysera_log`
--
ALTER TABLE `gw_paysera_log`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gw_paysera_log`
--
ALTER TABLE `gw_paysera_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=18;
 */
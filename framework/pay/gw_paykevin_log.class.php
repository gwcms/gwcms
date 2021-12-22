<?php

class GW_PayKevin_Log extends GW_Composite_Data_Object
{	
	public $composite_map = [
		'order' => ['gw_composite_linked', ['object'=>'GW_Order_Group','relation_field'=>'order_id']],
	];	
	
	public $calculate_fields = ['title'=>1];
	public $table = 'gw_paykevin_log';
	
	public function calculateField($name) 
	{
				
		switch($name){
			case 'title':
				return  $this->id ? $this->order_id.' - '.$this->amount.' '.$this->statusGroup.' '.$this->pm_debtorAccount_iban.($this->test ? ' (!TEST)':'') : '';
			break;
		}
		
		
	}
}

/*

CREATE TABLE `gw_paykevin_log` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `kevin_id` char(40) NOT NULL,
  `bankStatus` varchar(10) NOT NULL,
  `statusGroup` varchar(20) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `currencyCode` char(3) NOT NULL,
  `description` varchar(100) NOT NULL,
  `pm_creditorName` varchar(50) NOT NULL,
  `pm_endToEndId` int NOT NULL,
  `pm_creditorAccount_iban` char(20) NOT NULL,
  `pm_creditorAccount_currencyCode` char(3) NOT NULL,
  `pm_debtorAccount_iban` char(20) NOT NULL,
  `pm_debtorAccount_currencyCode` char(3) NOT NULL,
  `pm_bankId` varchar(15) NOT NULL,
  `pm_paymentProduct` varchar(20) NOT NULL,
  `pm_requestedExecutionDate` datetime NOT NULL,
  `test` tinyint NOT NULL,
  `wait` smallint NOT NULL,
  `info` varchar(255) NOT NULL,
  `processed` tinyint NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gw_paykevin_log`
--
ALTER TABLE `gw_paykevin_log`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gw_paykevin_log`
--
ALTER TABLE `gw_paykevin_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

 */
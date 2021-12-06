<?php

class gw_payuniversal_log extends GW_Composite_Data_Object
{	
	
	public $table = 'gw_payuniversal_log';
	
	public $composite_map = [
		'order' => ['gw_composite_linked', ['object'=>'GW_Order_Group','relation_field'=>'order_id']],
	];	


	public $calculate_fields = ['title'=>1];
	
	
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

DROP TABLE IF EXISTS `gw_payrevolut_log`;
CREATE TABLE `gw_payrevolut_log` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` char(3) NOT NULL,
  `test` tinyint NOT NULL,
  `remote_id` varchar(40) NOT NULL,
  `public_id` varchar(50) NOT NULL,
  `state` varchar(15) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `completed_at` datetime NOT NULL,
  `customer_id` varchar(50) NOT NULL,
  `email` int NOT NULL,
  `phone` int NOT NULL,
  `payment_id` varchar(40) NOT NULL,
  `payment_method` varchar(10) NOT NULL,
  `card_bin` int NOT NULL,
  `card_country` char(2) NOT NULL,
  `card_last_four` char(4) NOT NULL,
  `card_expiry` varchar(7) NOT NULL,
  `cardholder_name` varchar(200) NOT NULL,
  `card_brand` varchar(10) NOT NULL,
  `checks` varchar(20) NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;



--
-- Indexes for table `gw_payrevolut_log`
--
ALTER TABLE `gw_payrevolut_log`
  ADD PRIMARY KEY (`id`);

 */
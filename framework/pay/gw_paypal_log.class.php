<?php

class GW_PayPal_Log extends GW_Data_Object
{
	public $table = 'gw_paypal_log';
	
	public $encode_fields = ['extra'=>'json'];	
	
	
	//https://www.sandbox.paypal.com/us/cgi-bin/webscr?cmd=_profile-language-encoding
	//more - options - both utf8
	//http://stackoverflow.com/questions/12284341/paypal-ipn-override-charset
}


/*

CREATE TABLE IF NOT EXISTS `gw_paypal_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orderid` int(11) NOT NULL,
  `handler` varchar(15) NOT NULL,
  `action` varchar(15) NOT NULL,
  `payment_status` varchar(25) NOT NULL,
  `payment_type` varchar(25) NOT NULL,
  `ipn_track_id` varchar(25) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `custom` varchar(255) NOT NULL,
  `mc_gross` float NOT NULL,
  `mc_fee` float NOT NULL,
  `tax` float NOT NULL,
  `payment_fee` float NOT NULL,
  `handling_amount` float NOT NULL,
  `shipping` float NOT NULL,
  `payment_gross` varchar(10) NOT NULL,
  `quantity` int(11) NOT NULL,
  `item_number` int(11) NOT NULL,
  `mc_currency` varchar(5) NOT NULL,
  `txn_id` varchar(255) NOT NULL,
  `txn_type` varchar(20) NOT NULL,
  `transaction_subject` varchar(255) NOT NULL,
  `receiver_id` varchar(25) NOT NULL,
  `business` varchar(255) NOT NULL,
  `receiver_email` varchar(255) NOT NULL,
  `payer_email` varchar(255) NOT NULL,
  `payer_id` varchar(25) NOT NULL,
  `payer_status` varchar(25) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `address_name` varchar(255) NOT NULL,
  `address_street` varchar(255) NOT NULL,
  `address_zip` varchar(10) NOT NULL,
  `address_country_code` varchar(3) NOT NULL,
  `residence_country` varchar(3) NOT NULL,
  `address_country` varchar(255) NOT NULL,
  `address_state` varchar(25) NOT NULL,
  `address_city` varchar(255) NOT NULL,
  `address_status` varchar(25) NOT NULL,
  `payment_date` varchar(30) NOT NULL,
  `protection_eligibility` varchar(30) NOT NULL,
  `test_ipn` tinyint(4) NOT NULL,
  `extra` text NOT NULL,
  `handler_state` int(11) NOT NULL,
  `insert_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

 */


  
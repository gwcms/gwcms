<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Shop_Subscription_Period extends GW_Composite_Data_Object
{
	public $composite_map = [
		'groupObj' => ['gw_composite_linked', ['object'=>'Shop_SubscriptionGroups','relation_field'=>'group_id']],
	];	
	
}
<?php

if($this->user){
	$unpaidorders = GW_Order_Group::singleton()->count(['payment_status!=7 AND user_id=?', $this->user->id]);

	GW::s('UNPAID_ORDERS', $unpaidorders);

}
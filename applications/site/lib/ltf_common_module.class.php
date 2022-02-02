<?php

class LTF_Common_Module extends GW_Module_Extension 
{
	function ltf_SaveCoach($vals)
	{
		if(isset($vals['coach']) && $vals['coach']==-1 && $vals['coach_other'])
		{
			$sc  = LTF_Coaches::singleton()->createNewObject();
			$sc->title = $vals['coach_other'];
			$sc->user_id = $this->app->user->id;
			$sc->insert();
			
			$mail=[
			    'subject'=>'Sukurtas naujas treneris - "'.$sc->title.'" reikia patvirtinimo',
			    'body'=>"Kuo greičiau treneris bus patvirtintas tuo mažesnė tikimybė kad atsiras dublikatai.\n Pataisymą / patvirtinimą galima atlikti.:".
					" https://events.ltf.lt/admin/lt/events/coaches/".$sc->id."/form",
			];
			GW_Mail_Helper::sendMailAdmin($mail);			
			
			$vals['coach'] = $sc->id;
		}
		unset($vals['coach_other']);	
		return $vals;
	}
	
	function ltf_SaveClub($vals)
	{
		if(isset($vals['club']) && $vals['club']==-1 && $vals['club_other'])
		{
			$sc  = LTF_Clubs::singleton()->createNewObject();
			$sc->title = $vals['club_other'];
			$sc->user_id = $this->app->user->id;
			$sc->insert();
			
			$mail=[
			    'subject'=>'Sukurtas naujas klubas - "'.$sc->title.'" reikia patvirtinimo',
			    'body'=>"Kol nepatvirtintas negalės užregistruoti komandos.\n Pataisymą / patvirtinimą galima atlikti.:".
					" https://events.ltf.lt/admin/lt/events/clubs/".$sc->id."/form",
			];
			GW_Mail_Helper::sendMailAdmin($mail);			
			
			$vals['club'] = $sc->id;
			
		}
		unset($vals['club_other']);
		return $vals;
	}
	
	function ltf_initCoachOptions()
	{
		
		$cond = $this->app->user ? ["approved=1 OR user_id=?", $this->app->user->id] : 'approved=1';
		
		
		$this->options['coach'] = LTF_Coaches::singleton()->getOptions($cond) +
			['-1'=>GW::ln('/g/CANT_FIND_IN_LIST')];		
	}

	
	function ltf_addMembership2Cart($userid, $opts=[])
	{
		$this->userRequired();
		
		$opts['category'] =  $opts['category'] ?? 'beach';
		
		$cart = $this->app->user->getCart(true);
		
		$user = GW_Customer::singleton()->find(['id=?',$userid]);
		
		$age = $user->getAge();
		$payprice = GW_Membership::calcPriceScheme($age,$opts['category']);
		
		$currenttime = date('Y-m-d H:i:s');

		$cartitem = false;
		
		//nebepridet pakartotinai
		if($cart->items)
			foreach($cart->items as $citem)
				if($citem->obj_type=='gw_membership' && $citem->context_obj_id==$user->id){
					$cartitem = $citem;
				}
					
		$ms = GW_Membership::singleton()->createNewObject();
		$ms->user_id = $user->id;

		$start = date('Y-m-d H:i:s');	
		
		$ms->validfrom = $start;
		
		$last = $opts['category'] == 'beach' ? '+1 year' : '10 month';
		
		$ms->expires = date('Y-m-d H:i:s', strtotime($start.' '.$last));

		$ms->category = $opts['category'];
		$ms->payment_amount  = $payprice;
		$ms->active = 0;
		$ms->insert();				
		
		if(!$cartitem)
			$cartitem = new GW_Order_Item;
		
		
		$cartItmVals=[
			'obj_type'=>'gw_membership',
			'obj_id'=>$ms->id,
			'qty'=>1,
			'unit_price'=>$payprice,
			'context_obj_id'=>$user->id,
			'context_obj_type'=>'gw_customer',
			'can_remove'=> $opts['can_remove'] ?? 0
		];
		
		if(isset($opts['vals']))
			$cartItmVals = array_merge($cartItmVals, $opts['vals']);
		
		$cartitem->setValues($cartItmVals);
		
		$cart->addItem($cartitem);
	}	
	
}




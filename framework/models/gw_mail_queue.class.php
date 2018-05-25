<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of gw_mail_queue
 *
 * @author wdm
 */
class GW_Mail_Queue extends GW_Data_Object
{

	public $table = 'gw_mail_queue';
	
	
	
	function eventHandler($event, &$context_data = array()) {
		
		
		switch($event)
		{
			case 'BEFORE_SAVE':
				if(is_array($this->content_base['to']))
					$this->content_base['to'] = GW_Mail_Helper::implodeMultipleEmails($this->content_base['to']);
				
			break;
		}
		
		parent::eventHandler($event, $context_data);
	}

}

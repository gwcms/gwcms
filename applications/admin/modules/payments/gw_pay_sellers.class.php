<?php


class GW_Pay_Sellers extends GW_Composite_Data_Object
{
	public $table = "gw_pay_sellers";
	/*
	public $i18n_fields=[
	    'title'=>1
	];
	 * 
	 */
	
	public $ownerkey = 'events/organisers';
	public $extensions = ['keyval'=>1];	
	public $keyval_use_generic_table = 1;	
	
	public $validators = [
		'email' => ['gw_email', ['required' => 1]],

	];	
	

	public $default_order = "title ASC";	
	
	function getOptions($cond=false)
	{		
		//if($lang==false)
		//	$lang = $this->getDefaultLn();
			
		return $this->getAssoc(['id','title'], $cond);
	}



	function titleInOptions($item)
	{
		return ($item->short? '('.$item->short.') ' : '').$item->title;
	}


	
	
}
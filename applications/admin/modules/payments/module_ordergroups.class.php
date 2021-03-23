<?php


class Module_OrderGroups extends GW_Common_Module
{	
	use Module_Import_Export_Trait;
	
	function init()
	{	
		$this->model = GW_Order_Group::singleton();
		
		parent::init();
		
		$this->list_params['paging_enabled']=1;	
		

		$this->app->carry_params['search']=1;
		$this->app->carry_params['composer_id']=1;
		$this->app->carry_params['clean']=1;
		
		if(isset($_GET['composer_id'])){
			$this->filters['composer_id'] = (int)$_GET['composer_id'];
			$this->list_params['paging_enabled']=0;	
		}
		
		
	}
	

	
	
	
	
	function getListConfig()
	{
		$cfg = parent::getListConfig();
		
		$cfg['fields']['user_title'] = 'Lf';		
		//$cfg['filters']['catalog_type'] = ['type'=>'select','options'=>$this->options['catalog_type']];
		//$cfg['filters']['tonality'] = ['type'=>'select','options'=>$this->options['tonality']];
		//$cfg['filters']['instruments'] = ['type'=>'select','options'=>$this->options['instrument_id'], 'ct'=>['LIKE%,,%'=>'==']];
		
		//d::dumpas($cfg);
					
		return $cfg;
	}
	
	function __eventAfterList(&$list)
	{		
		$this->attachFieldOptions($list, 'user_id', 'GW_User');

		foreach($list as $item)
		{
			$item->items_count = GW_Order_Item::singleton()->count(['group_id=?', $item->id]);
		}	
	}
	

	
	function overrideFilterUser_title($value, $compare_type)
	{	
		$x=$this->__overrideFilterExObject("GW_User", "user_id", ["name","surname",'email'], $value, $compare_type);
		
		
		return $x;
	}
	
	/*
	function overrideFilterInstruments($value, $compare_type)
	{	
		d::Dumpas([$compare_type, $value]);
		
		$compare_type = "LIKE%,,%";
		
		return $x;
	}	*/
	
	
	
/*	
	function __eventAfterList(&$list)
	{
		
	}

	function init()
	{
		parent::init();
	}
 
 */	
	
	function initInvoiceVars($item)
	{
		
		$user =  $item->user;
		
		$payconfirm = $item->pay_confirm;
		if(!$payconfirm)
		{
			$this->setError("/m/NO_PAY_CONFIRM");
			$this->jump();
		}
		
		
		//d::dumpas(count($list));
		
		if($_GET['offset'] ?? false)
			$list = [$list[$_GET['offset']]];
		
		$tpl = GW_Mail_Template::singleton()->find(['idname=?', $this->modconfig->invoice_template]);
		
		if(!$tpl){
			$this->setError("Nenurodytas sąskaitos šablonas, modulio žiūrėti nustatymuose");
			$this->jump();
		}
		
		$tpl_code = $tpl->get("body_lt");
		
		
		$v =& $this->tpl_vars;
		
		
		
		
		$attachuservars = function(&$v, $user){
			$v['FULLNAME'] = $user->title;
			$v['CITY'] = mb_convert_case($user->city, MB_CASE_TITLE, 'UTF-8');
			$v['COUNTRY'] = GW_Country::singleton()->getCountryByCode($user->country, 'lt');
			$v['PHONE'] = $user->phone;
		};
		

			
		$build = false;
		$v = [];
		$v['PRICE'] = $item->amount_total;
		$v['PRICE_TEXT'] = GW_Sum_To_Text_Helper::sum2text($v['PRICE'], 'lt');

		$v['INVOICE_NUM'] = $payconfirm->orderid;
		$v['DATE'] = explode(' ',$item->insert_time)[0];
		$v['EMAIL'] = $payconfirm->p_email;
		$v['ITEMS'] = [];
			//$pdf=GW_html2pdf_Helper::convert($html, false);			
			
		
		
			
		foreach($item->items as $oitem){
			
			$v['ITEMS'][] = [
			    'title'=> $oitem->obj->invoice_line ?: $oitem->obj->title, 
			    'type'=>GW::ln('/g/CART_ITM_'.$oitem->obj_type), 
			    'qty'=>$oitem->qty, 
			    'unit_price'=>$oitem->unit_price, 
			    'total'=>$oitem->total
			];
		}
		
		$attachuservars($v, $user);
		
		return [$tpl_code, $v];
	}
	
	function viewInvoice()
	{
		$item = $this->getDataObjectById();
		list($tpl_code, $v) = $this->initInvoiceVars($item);
			
			
			
		$html = GW_Mail_Helper::prepareSmartyCode($tpl_code, $v);
		
		
		
		$tmp = $this->mute_errors; $this->mute_errors = true;
		
		if(isset($_GET['html']))
			die($html);
		
		$pdf=GW_html2pdf_Helper::convert($html, false);
		$this->mute_errors=$tmp;

		header('Content-type: application/pdf');
		echo $pdf;
		exit;		
	}


	function doSaveInvoice($item=false)
	{
		if(!$item){
			$item = $this->getDataObjectById();
			$die=1;
		}else{
			$die=0;
		}
		
		list($tpl_code, $v) = $this->initInvoiceVars($item);
		
		$item->invoicevars = json_encode($v);
		$item->updateChanged();
		
		$dir = GW::s('DIR/REPOSITORY') . ($this->modconfig->invoice_directory_name ?: 'invoices').'/';
		
		@mkdir($dir);
		
		$html = GW_Mail_Helper::prepareSmartyCode($tpl_code, $v);
		$fname="invoice-{$item->id}";
		file_put_contents($dir.$fname.'.html', $html);
		
		shell_exec($cmd="cd '$dir' && unlink '$fname.zip' ; zip -Z bzip2 '$fname.zip' '$fname.html' && unlink '$fname.html'");
		
		
		if($die)
			exit;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   
	}
	
	function doUpdateInvoices()
	{
		$list = $this->model->findAll();
		
		foreach($list as $item)
			$this->doSaveInvoice($item);
		
	}

}

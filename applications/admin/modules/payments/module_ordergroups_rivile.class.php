<?php

class Module_OrderGroups_Rivile extends GW_Module_Extension
{

	
	function createExportData($orders,$oitems)
	{
		$clients="";
		$income = "";
		foreach($orders as $order){
			//d::dumpas([$item->toArray(), $item->user->toArray()]);
			//if(!$item->company)continue; //testuot tik kompanijas
				
			$user=$order->user;
			$ccode = ($order->company_code ? $order->company_code : $user->id);
$clients.="			
     <N08>
         <N08_KODAS_KS>".$ccode."</N08_KODAS_KS>
          <N08_RUSIS>3</N08_RUSIS>
          <N08_PVM_KODAS>".($order->vat_code ? $order->vat_code : 'N/D')."</N08_PVM_KODAS>
          <N08_IM_KODAS>".($order->company_code ? $order->company_code : 'N/D')."</N08_IM_KODAS>
          <N08_PAV>".($order->company ? $order->company : $user->title)."</N08_PAV>
          <N08_ADR>".($order->company ?  $order->company_addr : $user->city.' '.$user->country)."</N08_ADR>
          <N08_TEL>".($order->phone)."</N08_TEL>
          <N08_MOB_TEL>".($user->phone)."</N08_MOB_TEL>
          <N08_E_MAIL>".($user->email)."</N08_E_MAIL>
          <N08_TIPAS_TIEK>1</N08_TIPAS_TIEK>
          <N08_TIPAS_PIRK>1</N08_TIPAS_PIRK>
          <N08_KODAS_DS>PT001</N08_KODAS_DS>
          <N08_KODAS_XS_T>PVM</N08_KODAS_XS_T>
          <N08_KODAS_XS_P>PVM</N08_KODAS_XS_P>
     </N08>
";

		$income.="
<I06>
	<I06_OP_TIP>51</I06_OP_TIP>
	<I06_VAL_POZ>0</I06_VAL_POZ>
	<I06_DOK_NR>".GW::ln("/G/application/PAYMENT_BANKTRANSFER_DETAILS_PREFIX").($order->id)."</I06_DOK_NR>
	<I06_OP_DATA>".($order->insert_time)."</I06_OP_DATA>
	<I06_KODAS_KS>{$ccode}</I06_KODAS_KS>
	<I06_KODAS_SS>2714</I06_KODAS_SS>
	<I06_MOK_SUMA>".($order->amount_total)."</I06_MOK_SUMA>
	<I06_MOK_DOK>".($order->pay_confirm_id)."</I06_MOK_DOK>";
	
		$ois = $oitems[$order->id];
		foreach($ois as $oi)
		{
			//5303 - nario mokestis,5310 - Starto mokestis,5312 -bilietų pardavimas
			switch($oi->obj_type){
				case 'gw_membership':
					$code = 5303;
				break;
				case 'ltf_participants':
					$code = 5310;
				break;
				default:
					$code = 5312;
				break;
			}
		
			$income.="
	<I07>
	     <I07_TIPAS>3</I07_TIPAS>
	     <I07_KODAS>{$code}</I07_KODAS>
	     <I07_KAINA_BE>{$oi->unit_price}</I07_KAINA_BE>
	     <I07_MOKESTIS>0</I07_MOKESTIS>
	     <I07_MOKESTIS_P>0.00</I07_MOKESTIS_P>
	     <T_KIEKIS>{$oi->qty}</T_KIEKIS>
	</I07>
	";

		}
		
			$income.="\n</I06>";			

		}
		
		return [$clients, $income];
	}
	
	

	
	function doRivileExport()
	{
		
		$defaults = json_decode($this->config->last_rivile_export, true);
		
		
		$form = ['fields'=>[
		    'from'=>['type'=>'date', 'default'=>$defaults['from'],'required'=>1],
		    'to'=>['type'=>'date', 'default'=>$defaults['to'],'required'=>1]
		    ],'cols'=>5];
		
		
		

		
		if(!($answers=$this->prompt($form,'Nurodykite užsakymo sukūrimo intervalą')))
			return false;
		
		
		$this->config->last_rivile_export = json_encode($answers);;
		
		
		$conds = ['payment_status=7 AND pay_test=0'];
				
		$conds[] = GW_DB::prepare_query(['pay_time >= ?', $answers['from']]);
		$conds[] = GW_DB::prepare_query(['pay_time <= ?', $answers['to']." 23:59"]);		
	
		
		
		$list = $this->model->findAll(implode(' AND ', $conds),['order'=>'id DESC','key_field'=>'id']);
		$order_ids = array_keys($list);
		

		
		
		
		$orderitems = GW_Order_Item::singleton()->findAll(GW_DB::inCondition('group_id', $order_ids));
		
	
		
		$this->tpl_vars['list'] = $list;
		$this->tpl_vars['orderitems'] = $ois = GW_Array_Helper::groupObjects($orderitems,'group_id');	
		
		
		
		list($clients,$income) = $this->createExportData($list,$ois);
		 
		$range = $answers['from'].'_'.$answers['to'];
		
		$zip = new ZipArchive();
		$zip_fname = GW::s('DIR/TEMP')."rivile_export.zip"; // Zip name
		$zip->open($zip_fname,  ZipArchive::CREATE);
		
		$zip->addFromString("clients_".$range.'.eis', $clients);  
		$zip->addFromString("income_".$range.'.eis', $income);  
		$zip->close();
		
		header("Content-Type: application/x-download");	
		header("Content-Disposition: attachment; filename=rivile_export_{$range}.zip");
		header("Accept-Ranges: bytes");
		header("Content-Length: ".filesize($zip_fname));
		echo file_get_contents($zip_fname);
		unlink($zip_fname);

		exit;
		
	}
}
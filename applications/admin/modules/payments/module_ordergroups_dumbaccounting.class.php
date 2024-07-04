<?php

class Module_OrderGroups_DumbAccounting extends GW_Module_Extension
{

	
	function createExportData($orders,$oitems)
	{
		$clients="";
		$income = "";
		$rows = [[ 'SF išrašymo data', 'Serija', 'SF Nr.', 'Kliento vardas pav.', 'Paslauga', 'Kiekis', 'Kaina su PVM', 'Kaina be PVM']];
		
		foreach($orders as $order){
			$user=$order->user;
			$rows[]=[
			    $order->pay_time,
			    'SSA',
			    $order->id,
			    $order->company ? $order->company : $user->title,
			    'Aikštelės nuoma',
			    '1',
			    $order->amount_total,
			    round($order->amount_total*0.79, 2)
			];
			//d::dumpas([$item->toArray(), $item->user->toArray()]);
			//if(!$item->company)continue; //testuot tik kompanijas
		}
		//d::dumpas($rows);

		return $rows;
	}
	
	

	function ancientRivileString(&$csv)
	{
		setlocale(LC_CTYPE, "lt_LT.UTF-8"); //set your own locale
		$csv = iconv("UTF-8", "WINDOWS-1257//TRANSLIT//IGNORE", $csv);	
	}
	
	function doDumbAccountingExport()
	{
		$defaults = json_decode($this->config->last_buhalt_exportas_data, true);
		
		$form = ['fields'=>[
		    'range'=>['type'=>'daterange','required'=>1],
		    'mail'=>['type'=>'text','hidden_note'=>'Nurodžius bus siunčiama el paštu']
		    ],'cols'=>5];
		
		if(!($answers=$this->prompt($form,'Nurodykite užsakymo sukūrimo intervalą', ['rememberlast'=>1])))
			return false;
		
		$range = explode(' - ', $answers['range']);
		$answers['from'] = $range[0];
		$answers['to'] = $range[1];
		
		
		
		
		$conds = ['payment_status=7 AND pay_test=0'];
				
		$conds[] = GW_DB::prepare_query(['pay_time >= ?', $answers['from']]);
		$conds[] = GW_DB::prepare_query(['pay_time <= ?', $answers['to']." 23:59"]);		
	
		
		
		$list = $this->model->findAll(implode(' AND ', $conds),['order'=>'pay_time ASC','key_field'=>'id']);
		$order_ids = array_keys($list);
		

		
		
		
		$orderitems = GW_Order_Item::singleton()->findAll(GW_DB::inCondition('group_id', $order_ids));
		
	
		
		$this->tpl_vars['list'] = $list;
		$this->tpl_vars['orderitems'] = $ois = GW_Array_Helper::groupObjects($orderitems,'group_id');	
		
		
		
		$rows = $this->createExportData($list,$ois);
		 
		$range = $answers['from'].'_'.$answers['to'];
		
		$filename = "dumb_accounting_export_{$range}.xlsx";
				
		if($answers['mail'] ?? false){
			$filecontents = Others\Shuchkin\SimpleXLSXGen::fromArray($rows)->__toString($filename);
			
			$opts = [
			    'subject'=>GW::s('PROJECT_NAME').' Mėnesinis įplaukų eksportas '.$range, 
			    'body'=>'Informacija prisegta rinkmenoje:  '.$filename.' ('.GW_File_Helper::cFileSize(strlen($filecontents)).')',
			    'to'=>explode(';',$answers['mail'])
			];
		
			$opts['attachments'] = [$filename => $filecontents];
			$rez =GW_Mail_Helper::sendMail($opts);
			

			//d::dumpas($rez);
			
			//d::dumpas($opts);
			$this->setMessage('Sent mail to '.implode(', ',$opts['to']). ' status: '.($rez?'ok':'fail'));
			$this->jump();
		}else{
			Others\Shuchkin\SimpleXLSXGen::fromArray($rows)->downloadAs($filename);
		}
	}
	
	
	function doDumbAccountingExportMail()
	{
		if(date('d')=='01')
		{
			$start = date('Y-m-01',strtotime('-1 MONTH'));
			$end = date('Y-m-d',  strtotime("last day of {$start}")) ;
			$_GET['item']['from'] = $start;
			$_GET['item']['to'] = $end;
			
			$_GET['item']['mail'] = 'xxxbugalterija@mailinator.com';
			
			
			
			$file = $this->doRivileExport();
			$this->config->last_dumbaccounting_export_monthly_exec = date('Y-m-d H:i:s');
		}
	}
}
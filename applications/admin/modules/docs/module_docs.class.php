<?php


class Module_Docs extends GW_Common_Module
{	

	use Module_Import_Export_Trait;		
	
	
	function init()
	{	
		parent::init();
		
		$this->model = GW_Doc::singleton();
		
		$this->list_params['paging_enabled']=1;	
		$this->app->carry_params['owner_type']=1;
		$this->app->carry_params['clean']=1;
		
		
		if(isset($_GET['owner_type']))
		{
			$this->filters['owner_type'] = $_GET['owner_type'];
		}
		
		if(isset($_GET['owner_field']))
		{
			$this->filters['owner_field'] = $_GET['owner_field'];
		}
	}

	
	function viewDefault()
	{
		$this->viewList();
	}

	
	
	function getListConfig()
	{
		
		$cfg = parent::getListConfig();
		
		$cfg["fields"]['insert_time'] = 'lof';
		$cfg["fields"]['update_time'] = 'lof';
		//$cfg["fields"]['priority'] = 'lof';
		
		return $cfg;
	}	


		
	
	
	
	
	
	
	function __eventBeforeDelete($item)
	{
		if($item->protected)
		{
			$this->setError("Cant delete protected item");
		}
		
	}
	
	//function __eventAfterForm()
	//{
	//	d::dumpas('test');
		
	//}
	
	function doTest()
	{
		d::dumpas('test');
		
	}
	
	
	
	function doOpenInSite()
	{
		
		$item = $this->getDataObjectById();
		
		Header('Location: '.Navigator::getBase().$this->app->ln.'/direct/docs/docs/item?id='.$item->key);
	}
	
	
	
	function viewTestPdfGen()
	{
		$item = $this->getDataObjectById();
		
		
		
		if(isset($_POST['item'])){
			$item->set('body', $_POST['item']['htmlcontents'], $_GET['lang']??'');
			$item->updateChanged();
		}
		
		
		$this->tpl_vars['item'] = $item;
		$this->tpl_vars['filecontents'] = $item->get('body', $_GET['lang']??'');
	}		
	
	function doGenPdf()
	{
		$item = $this->getDataObjectById();
		$body = $item->get('body', $_GET['lang']??'');

		$body = " <style>*{ font-family: DejaVu Sans !important;}</style>".$body;
		
		$dpi = $item->get('config/dpi') ? : 150;
		
		
		$pdf = @GW_html2pdf_Helper::convert($body, false, ['params'=>['dpi'=>$dpi]]);
		header("Content-type:application/pdf");
		header("Content-Disposition:inline;filename=test.pdf");
		die($pdf);		
	}

	
	function normaliseAct($fname)
	{
		$item = $this->getDataObjectById();
		$body = $item->get('body', $_GET['lang']??'');
		

		switch ($fname){
			case 'pt2px':
				$body = preg_replace_callback(
				       '/(:) ?([0-9.]+)(pt)/',
				       function ($m) {
					 //d::dumpas($m);
					   return $m[1].round($m[2]*$_GET['ratio'], 4).'px';
				       },
				       $body
				   );	
			break;
			case 'cm2px':
				$body = preg_replace_callback(
				       '/(:) ?([0-9.]+)(cm)/',
				       function ($m) {
					 //d::dumpas($m);
					   return $m[1].round($m[2]*$_GET['ratio'], 4).'px';
				       },
				       $body
				   );	
		       							
			break;
			case 'adjfontsz':
				$body = preg_replace_callback(
				       '/(font-size:) ?([0-9.]+)(px)/',
				       function ($m) {
					 //d::dumpas($m);
					   return $m[1].(round($m[2]*$_GET['ratio'], 4)).$m[3];
				       },
				       $body
				   );	
		       				
			break;
		}
		
		$item->set('body', $body, $_GET['lang']??'');
		$this->setMessage('Conversion done');
		$item->updateChanged();
		$this->jumpOutOfAct();
		
			
	}
	
	function doProcess()
	{
		$this->normaliseAct($_GET['fname']);
	}
	
	function doConvertpt2px()
	{
		$this->normaliseAct('pt2px');
		
	}
	
	function doAdjustFontsize()
	{
		$this->normaliseAct('adjfontsz');
	}	
	

	function doSavePdfParams()
	{
		$item = $this->getDataObjectById();
		
		$vals = $_POST['item'];
		$item->setValues($vals);
		$item->updateChanged();
		
		$this->setMessage(GW::ln('/g/SAVE_SUCCESS'));
		$this->jumpOutOfAct();
		
	}
	
	function jumpOutOfAct()
	{
		$prm = $_GET;unset($prm['act']);
		$this->app->jump(false, $prm);			
	}
	
	
	
	function dofixIsue20200929()
	{
		$answers = GW_Form_Answers::singleton()->findAll(['doc_id=0']);
		
		$answersvals = [];
		
		foreach($answers as $answ){
			$data = $answ->toArray();
			unset($data['id']);
			
			$data['keyval'] = $answ->extensions['keyval']->getAll();
			$answersvals[] = $data;
			$answ->delete();
		}
		
		$cnt =0;
		
		foreach(GW_Doc::singleton()->findAll() as $doc){
			foreach($answersvals as $vals){
				$vals['doc_id'] = $doc->id;
				$keyval = $vals['keyval'];
				//d::dumpas($vals);
				
				$a = GW_Form_Answers::singleton()->createNewObject($vals);
				
				
				
				$a->save();
				
				foreach($keyval as $key => $val)
					$a->set("keyval/$key", $val);
				
				$a->update();
				
				$cnt++;
			}
		}
		
		$this->setMEssage("Answer $cnt clones done");
		$this->jump();		
	}
	
	
}

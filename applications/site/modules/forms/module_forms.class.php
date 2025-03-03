<?php


class Module_Forms extends GW_Public_Module
{
	
	function init()
	{		
		
		parent::init();
		
		//$this->tpl_dir .= $this->module_name."/";
		
		$this->tpl_vars['page_title'] = GW::ln("/m/TITLE");
		

		
	}
	
	//function viewDefault()
	//{
		
	//}	
	
	function viewDefault()
	{
				
		$forms = GW_Forms::singleton()->findAll();
		
		$this->tpl_vars['forms'] = $forms;
		
		
		
		Navigator::jump($this->buildDirectUri("start"));
	}	
	function viewTest()
	{
		
	}
	
	function initForm()
	{
		$form = GW_Forms::singleton()->find(['admin_title=?', $_GET['id']]);
		

		$this->tpl_vars['form'] = $form;	
		
		$elements = $form->elements;
		
		$steps = [];
		
		
		$step_curr = $_GET['step'] ?? 0;
		
		
		
		foreach($elements as $e){
			$steps[$e->fieldset][]=$e;
		}
		
		$this->tpl_vars['steps'] = $steps;


		if($step_curr){

			$idx = array_keys($steps);


			$currstep = $idx[$step_curr-1];

			if(!isset($idx[$step_curr])){
				$this->tpl_vars['laststep']=1;
				
			}else{
				$this->tpl_vars['nextstep'] = $idx[$step_curr];
			}
			
			$this->tpl_vars['stepname'] = $currstep;
			$this->tpl_vars['elements'] = $steps[$currstep];
		}		
	}
	
	
	function viewForm()
	{
		$this->initForm();
		
		$form = $this->tpl_vars['form'];
		
		if(!isset($_GET['step']) && !$form->description){
			Navigator::jump($this->buildDirectUri("form",['id'=>$form->admin_title,'step'=>1]));
		}
		
		$this->tpl_vars['answer'] = $this->getAnswer($this->tpl_vars['form']);
		

		
		//d::dumpas([$idx, $step_curr, $currstep, $steps[$currstep]]);
		$_GET['clean']=1;
	}
	
	
	function getAnswer($form, $create=false)
	{		
		$initial = [
		    'owner_id'=>$form->id,
		];
		
		
		if(!$this->app->user){
			$anonymous = $this->app->initAnonymousUser();	
			$initial['auid'] = $anonymous->id;;
		}else{
			$initial['user_id'] = $this->app->user->id;
		}
		
		//d::dumpas($initial);

		if(isset($_SESSION['answerid'])){
			$initial['id'] = $_SESSION['answerid'];
		}
	
		$answer = false;
	
		if(isset($initial['id']))
			$answer = GW_Form_Answers::singleton()->find(GW_DB::buidConditions($initial));
		
		
					
		
		if(!$answer){
			$answer = GW_Form_Answers::singleton()->createNewObject($initial);
			
			
			
			
			if($create && !$answer->id){
				$answer->insert();
				
				$_SESSION['answerid'] = $answer->id;
			}
			
			
			
			//pvz po pirkimo sugeneruojama nuoroda kuria paspaudus iskart uzpildoma pvz kokia preke / paslauga pirko
			if(isset($_GET['prefill'])){
				
				$this->initForm();
				d::dumpas([
				    $_GET['prefill'],
				    $this->tpl_vars['elements']
					]);
				
				
				
				foreach($_GET['prefill'] as $key => $val)
				{
					if(isset($this->tpl_vars['elements'][$key])){
						$answer->set("keyval/{$key}", $val);
					}else{
						$this->setError("Prefill problem <b>$key</b> field was not found");
					}
				}
				
				
			}
		}
		
		return $answer;
	}	
	
	function doRestart()
	{
		unset($_SESSION['answerid']);
		
		
		
		Navigator::jump($this->buildDirectUri('start'));;
	}
	

	
	
	function doSaveForm()
	{
		$this->initForm();
		$answer = $this->getAnswer($this->tpl_vars['form'], true);
		
		
		//if($_SERVER['REMOTE_ADDR']=="84.15.236.87")
		//	d::dumpas($answer);
		
		
		
		foreach($this->tpl_vars['elements'] as $elm){
			$val = $_POST['item']['keyval/'.$elm->fieldname];
			
			if($elm->type=='checkbox')
				$val = json_encode($val);
						
			$answer->set('keyval/'.$elm->fieldname, $val);
			
		}
		
		
		if(isset($this->tpl_vars['laststep'])){
			Navigator::jump($this->buildDirectUri("answer",['id'=>$this->tpl_vars['form']->admin_title]));
		}else{
			Navigator::jump($this->buildDirectUri("form",[
			    'id'=>$this->tpl_vars['form']->admin_title,
			    'step'=>($_GET['step']??0)+1]));			
		}		
		

	}
	
	
	function viewAnswer()
	{
		$this->initForm();
		$this->tpl_vars['answer'] = $this->getAnswer($this->tpl_vars['form']);
		
		$_GET['clean']=1;
	}
	
	function viewPdfAnswer()
	{
		$this->tpl_vars['pdf'] = 1;
		$this->viewAnswer();
		$html = $this->processTemplate('answer', true);
		
		$body = " <style>*{ font-family: DejaVu Sans !important;}</style>".$html;
		
		$dpi = 150;
		
		file_put_contents(GW::s('DIR/LOGS').'test1','JOA');
		
		
		$pdf = @GW_html2pdf_Helper::convert($body, false, ['params'=>['dpi'=>$dpi]]);
		header("Content-type:application/pdf");
		header("Content-Disposition:inline;filename=healthy_boost_training_youranswer.pdf");
		die($pdf);
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
	
	
	function viewStart()
	{
		unset($_SESSION['answerid']);
		$_GET['clean']=1;
		
	}
}
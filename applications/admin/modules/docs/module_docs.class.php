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
		
		
		//$this->itax = new Itax(GW_Config::singleton()->get('itax/itax_apikey'));		
		$this->addRedirRule('/^doItax|^viewItax/i','itax');

		//if(GW::s('PROJECT_ENVIRONMENT') != GW_ENV_DEV){
		//	GW::db()->query('SET GLOBAL sort_buffer_size = 512000');
		//}
		

		$this->initFeatures(true);
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
		
		$this->setMessage(GW::l('/g/SAVE_SUCCESS'));
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


	/**
	 * dont translate disabled languages
	 */
	
	function getTranslation($opts)
	{
		if(!$opts['item']->get('ln_enabled', $opts['dest']))
			return false;
		
		return parent::getTranslation($opts);
	}
	
	
	function __sendInvitations($users, $answers, $args=[])
	{
		
		$temmplateid = $answers['tpl'];
		
		
		$item = $this->getDataObjectById();
				
		$link1=Navigator::__getAbsBase().'/';
		
		
		
		$link2='/direct/docs/docs/item?id='.$item->key;
		
		$link3='';
		
		if(!isset($_GET['confirm'])){
			echo "<style>.bodytest{ max-height:300px;overflow:scroll;background-color:white; padding: 10px;color:black }</style>";
			echo "</pre>";
		}
		
		foreach($users as $user){
			
			$opts=[];
			$opts['tpl']=$temmplateid;
			$opts['to'] = $user->email;
			$opts['ln'] = $user->use_lang ? $user->use_lang : 'lt';
			
			
			if(isset($args['authkey']))
			{
				$link3='&authkey='.$user->get('keyval/authkey').'&cid='.$user->id;
			}
			
			
			$contracturl = $link1.$opts['ln'].$link2.$link3;
			
			$contractlink = '<a href="'.$contracturl.'">'.GW::ln("/M/docs/CONTRACT_LINK_TEXT").'</a>';
			
			$opts['vars']=[
			    'user'=>$user,
			    'CONTRACT_LINK'=>$contractlink,
			    'CONTRACT_URL'=>$contracturl,
			    'PASS_RESET_LINK'=>$link1."/direct/users/users/passchange?email=".$user->email
			];
			
			//d::dumpas($contractlink);
			
			if(!isset($_GET['confirm'])){
				$opts['dryrun']=1;
			}
			
			GW_Mail_Helper::sendMail($opts);
			
			
			if(!isset($_GET['confirm'])){
				echo '<div class="bodytest">';
				echo implode(' ',$opts['to']);
				echo "<hr>";
				echo $opts['subject'];
				echo "<hr>";
				echo $opts['body'];
				echo '</div>';
			}
			
		}

		if(isset($_GET['confirm'])){
			$this->setMessage('SENT: '.count($users));
			$this->jump();
		}
		
		$this->askConfirm("Are you sure you want to send");
		
	}
	
	function doSendInvitations()
	{
		$users = [
		    'type'=>'multiselect_ajax', 
		    'modpath'=>"customers/users", 
		    'empty_option'=>1, 
		    'options'=>[], 
		    'preload'=>1, 'required'=>1, 'width'=>"500px",
		    'after_input_f'=>"editadd",
		    'import_url'=>$this->app->buildUri('emails/subscribers/importsimple'),
		    'export_url'=>$this->app->buildUri('emails/subscribers/exportsimple'),
			'btngroup_width'=>"100%"	
		];
		
		$emailtpl = [
		    'type'=>'select_ajax', 
		    'modpath'=>"emails/email_templates", 
		    'empty_option'=>1, 
		    'options'=>[], 
		    'source_args'=>['byid'=>1,'owner_type'=>'customers/users'] , 
		    'preload'=>1, 
		    'required'=>1, 
		    'width'=>"250px",
		    
		];

		$form = ['fields'=>['users'=>$users,'tpl'=>$emailtpl],'cols'=>2];
		
		
		if(!($answers=$this->prompt($form, GW::l('/m/SELECT_USERS_AND_TEMPLATE'))))
			return false;		
		
		$users = GW_Customer::singleton()->findAll(GW_DB::inCondition('id', $answers['users']));
		$this->__sendInvitations($users, $answers);
		

	}

	
	function doSendInvitationsCreateUsers()
	{
		$users = [
			'type'=>'textarea', 
			'width'=>'500px',
			'height'=>'500px',
		    'hidden_note'=>"Pavizdys (kopijuojama iš el. puslapio lentelės): <pre>SPORTININKAS	NUMERIS	POZICIJA	EL. PAŠTAS	TELEFONAS	
Jonas Jonaitis	123	libero	jonas.jonaitis@gmail.com	860012345
Petras Petraitis	124	outside	petras.petraitis@gmail.com	860054321</pre>
"
		];
		
		$map = ['type'=>'text', 'default'=>"SPORTININKAS:namesurname;EL. PAŠTAS:email;TELEFONAS:phone"];

		
		
		$emailtpl = [
		    'type'=>'select_ajax', 
		    'modpath'=>"emails/email_templates", 
		    'empty_option'=>1, 
		    'options'=>[], 
		    'source_args'=>['byid'=>1,'owner_type'=>'customers/users'] , 
		    'preload'=>1, 
		    'required'=>1, 
		    'width'=>"250px",
		    
		];

		$form = ['fields'=>['users'=>$users, 'map'=>$map,'tpl'=>$emailtpl],'cols'=>1];
		
		
		if(!($answers=$this->prompt($form, GW::l('/m/SELECT_USERS_AND_TEMPLATE'),['method'=>'post'])))
			return false;	

		$lines = explode("\n", trim($answers['users']));
		
		$maptxt = $answers['map'];
		$map=[];
		foreach(explode(';',$maptxt) as $mapentry){
			list($key, $trans) = explode(':',$mapentry);
			$map[$key] = $trans;
		}
		//d::dumpas($map);
		
		$head = trim(array_shift($lines));
		$head= explode("\t", $head);
		
		$rows = [];
		
		foreach($lines as $line){
			
			$line= explode("\t", trim($line));
			$row = [];
			foreach($head as $idx => $col){
				if(isset($map[$col]))
					$row[ $map[$col] ] = $line[$idx];
			}
			
			$rows[] = $row;
		}
		
		$users = [];
		
		foreach($rows as $row){
			
			
			if(!($row['email'] ?? false)){
				$this->setError("Skipping ".json_encode($row). " (no email)");
				continue;
			}
			
			$c = GW_Customer::singleton()->find(["a.removed=0 AND email=?", $row['email']]);
			$insert = false;
			if(!$c){
				$c = GW_Customer::singleton()->createNewObject();
				$insert=true;
			}
			
			if($row['namesurname']){
				$name =  explode(' ', $row['namesurname'], 2);
				$c->name = $name[0];
				$c->surname = $name[1];
			}
			
			$c->email = $row['email'];
			$c->active = 1;
			
			if($row['phone']){
				$c->phone = $row['phone'];
			}
			
			if($insert)
			{
				$c->insert();
								
				
				$this->setMessage("New user ". json_encode($row));
			}elseif($c->changed_fields){
				$this->setMessage("User updated ". json_encode($row));
				$c->updateChanged();
				
			}
			
			$c->set('keyval/authkey', GW_String_Helper::getRandString(35));
			
			$users[$c->id] = $c;
		}
		
		
				
		$this->__sendInvitations($users, $answers, ['authkey'=>1]);
		
		
	}
	
	
	function viewDocument()
	{
		d::dumpas($_GET);
	}
	
	
}

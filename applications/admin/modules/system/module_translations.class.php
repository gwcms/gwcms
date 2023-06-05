<?php



class Module_Translations extends GW_Common_Module
{	
	
	//to remove integer validation
	var $data_object_id_type=0;

	function init()
	{
		parent::init();
		
		$this->model = new GW_Lang_File;
	}
	
	
	function getFile()
	{
		if(! $file=$this->model->getFilename($fn=$_REQUEST['id']))
			die("File '$fn' Not Exists!");
			
		return $file;
	}

	
	function doGetUpdates()
	{
		$default_lines_count=1000;
		
		$file=$this->getFile();
			
		$timer = new GW_Timer;
			
		$lines =  isset($_REQUEST['lines']) ? $_REQUEST['lines'] : $default_lines_count;
		
		$data=($offset=(int)$_REQUEST['offset']) ? GW_Log_Read::offsetRead($file, $offset) : GW_Log_Read::LinesRead($file, $lines, $offset);
			
		echo json_encode(Array('data'=>htmlspecialchars($data),'time'=>$timer->stop(5),'offset'=>$offset));
		exit;
	}
	
	function doClean()
	{
		$lw = new GW_Log_Watch($_REQUEST['id']);
		$lw->clean();
		$this->jump();
	}
	

	
	function viewTree()
	{
		header("content-type: text/plain");
		
		$file = $this->getDataObjectById();
		$filename = $file->getFilename($file->id);
		
		$data = GW_Lang_XML::parseXML($filename);
		
		d::dumpas($data);
		
	}
	
	function doCreateTemp()
	{
		$item = $this->getDataObjectById();
		$filename = $item->filename;
		
		$data = GW_Lang_XML::parseXML($filename);
				
		$new = GW_Lang_XML::struct2Xml($data);
		
		
		$item->writeTemp($new);
	}
	
	function doPushTemp($item = false)
	{
		if(!$item){
			$item = $this->getDataObjectById();
			$jump = 1;
		}
		
		
		$path = GW::s('DIR/ROOT')."applications/cli/sudogate.php";
		
		$sudouser = GW::s('PROJECT_ENVIRONMENT') == GW_ENV_DEV ? 'wdm' : 'root';	
		
		
		$user = $this->app->user->username;
		
		$res=shell_exec($cmd="sudo -S -u $sudouser /usr/bin/php $path writelang '$item->id' '$user'  2>&1");
		$this->setMessage("$cmd\n\n<pre>$res</pre>");
		
		$item->removeTemp();
		
		if($jump ?? false)
			$this->jump();
	}
	
	function doResetTemp()
	{
		$item = $this->getDataObjectById();
		$item->removeTemp();
		$this->jump();
	}
	
	

	
	
	static function recursiveSplit(&$data, $path, $mainln, $destln, &$collect)
	{
		
		
		foreach($data as &$el){			
			if($el['tag']!='I')
				break;
			
			$key = $el['attributes']['ID'];
			
		
			
			if(isset($el['childs'])){
				
				if($el['childs'][0]['tag']=='I'){
					self::recursiveSplit ($el['childs'], $path.'/'.$key,  $mainln, $destln, $collect);
				}else{
					$src =& GW_Lang_XML::structLangNodeSeek($el['childs'], $mainln);
					$dst =& GW_Lang_XML::structLangNodeSeek($el['childs'], $destln, true);
					
					
					if(($src['value']??false) && !($dst['value']??false)){
						$dst['value'] = $src['value']; 
						$collect["$path/$key"] =& $dst['value'];	
					}
				}
			}else{
				$val = trim($el['value']);
				$el['value'] = $val;
				
				if((strpos($path,'/MAP')===0 && $key!='title') || strpos($path, '/FIELDS_SHORT')===0)
					continue;
				
				if(substr_count($val, "\n") > 3 || substr_count($val, "\{$") > 2 || substr_count($val, "&lt;") > 2 || substr_count($val, '<br') > 1)
					continue;
				
				if(strip_tags($val)=='')
					continue;;
					
				if(preg_match('/\#[A-F0-9]/i', $val))
					continue;
				
				if(is_numeric($val))
					continue;
							
				$x =&  $el['value'];
				
				$collect["$path/$key"] =& $x;
				
				$el['childs'] = [
				    ['tag'=>$mainln, 'value'=>$val],
				    ['tag'=>$destln, 'value' => &$x],
				];
			}
		}
	}
	
	
	
	
	
	
	//uses gw/tools/Skriptai/google_translate/test2.php
	//placed on web servers services/translate/test2.php
	
	function autoTranslateColl(&$collect, &$data, $mainln, $destln) {
		
				
		foreach($collect as $item)
			$title_array[] = $item;
				
		$opts = http_build_query(['from'=>$mainln,'to'=>$destln]);
		
		$serviceurl = GW_Config::singleton()->get('system__translations/main_service_url');
								
		$resp_raw = GW_Http_Agent::singleton()->postRequest($serviceurl.'?'.$opts, ['queries'=>json_encode($title_array)]);
				
		$resp = json_decode($resp_raw);
		
		//D::dumpas([$serviceurl, $resp_raw]);
			
		$failed = [];
		
		if($_GET['item']['interupt'] ?? false)
		{
			d::ldump(['$serviceurl'=>$serviceurl,'$opts'=>$opts,'queries'=>$title_array], ['hidden'=>'request_details']);
			d::ldump([$resp_raw], ['hidden'=>'response raw']);
			d::ldump([$resp], ['hidden'=>'response']);
		}
		
		$idxmap = array_keys($collect);
		
		if(!isset($resp->result))
		{
			$errinfo = json_encode(['$serviceurl'=>$serviceurl,'$opts'=>$opts,'queries'=>$title_array], JSON_PRETTY_PRINT);
			$this->setError("Translation request failed <pre>". htmlspecialchars($resp_raw)."</pre>");
			$this->setError("<pre>$errinfo</pre>");
			$failed = $idxmap;
			return $failed;
		}
		
		
		
		foreach($resp->failed_idxs as $idx)
			$failed[ $idxmap[$idx] ] = $collect[ $idxmap[$idx] ];
		
		
		foreach($resp->result as $idx => $res)			
			$collect[ $idxmap[$idx] ] = "[A] ".$res->res;
		
		return $failed;
	}
	
	
	function autoTranslateSingle($str, $from, $to)
	{
		$title_array = [$str];
				
		$opts = http_build_query(['from'=>$from,'to'=>$to]);
		
		$serviceurl = GW_Config::singleton()->get('system__translations/main_service_url');
								
		$resp_raw = GW_Http_Agent::singleton()->postRequest($serviceurl.'?'.$opts, ['queries'=>json_encode($title_array)]);
				
		$resp = json_decode($resp_raw);
		
		
		if($resp->failed_idxs){
			$this->setError("Auto translate '$str' from $from to $to FAILED ");
			return '';
		}
		
		
		
		foreach($resp->result as $idx => $res)			
			return $res->res;	
	}
		
	
	function viewAutoTranslate()
	{
		$item = $this->getDataObjectById();
		
		$sel=['type'=>'select','options'=>array_merge(GW::s("LANGS"),GW::s('i18nExt')), 'empty_option'=>1, 'options_fix'=>1, 'required'=>1];
		$form = ['fields'=>['mainlang'=>$sel, 'addlang'=>$sel],'cols'=>4];
		if($this->app->user->isRoot())
			$form['fields']['interupt'] = ['type'=>'bool','note'=>'(Debug mode)'];
		
		if(!($answers=$this->prompt($form, GW::l('/m/SPECIFY_MAIN_AND_ADDING'))))
			return false;
		
				
		$mainln = $answers['mainlang'];
		$destln = $answers['addlang'];
		
		
		
		$filename = $item->filename;
		$data = $item->getDataStruct();
		
		if($answers['interupt']){
			d::ldump($data,['hidden'=>'initial data']);
		}		
		
		//d::ldump($data);
		$collect = [];
		
		self::recursiveSplit($data, '', strtoupper($mainln), strtoupper($destln), $collect);
				
		$this->setMessage("TRANS Count: ".count($collect));
		
		//d::dumpas($collect);
		//foreach($collect as &$el){
		//	$el = "TEST $el";
		//}
		
		//d::dumpas($data);
		
		
		if($answers['interupt']){
			d::ldump($data,['hidden'=>'data after split']);
		}
		
		$t = new GW_Timer;
		
		
		
		//d::dumpas(implode("\n\n",array_values($collect)));
		
		$translate_in_chuncks=false;
		
		if($translate_in_chuncks){
		
			$chuncs = [];
			$i = 0;
			foreach($collect as &$elm){
				$chuncs[$i][] =& $elm;
				$i++;
			}


			//d::ldump($chuncs);
			$i = 0;		
			foreach($chuncs as &$chuncs)
			{
				$i++;
				if($failed = $this->autoTranslateColl($chuncs, $data,$mainln,$destln)){
					$this->setMessage(["FAILED"=>$failed]);
				}
				//sleep(2);

				//if($i > 10)
				//	d::dumpas($data);

			}
		}else{
			
			if($failed = $this->autoTranslateColl($collect, $data,$mainln,$destln)){
				$this->setMessage(["FAILED"=>$failed]);
			}
		}
		
		if($answers['interupt']){
			d::dumpas($data,['hidden'=>'final data']);
		}
		

		$this->setMessage("speed {$t->stop()} secs");
	
		
		
		
		$new = GW_Lang_XML::struct2Xml($data);
		$item->writeTemp($new);
		
		
		$this->jump('system/translations/flatedit',['id'=>$item->id]);
	
	}

	
	
	
	function viewTranslateTest()
	{
		$this->tpl_vars['item']	= (object)json_decode($this->modconfig->lastopt);
		$this->tpl_vars['item']->result = $this->translatetestresult ?? '';
		$this->tpl_vars['item']->text =  $this->translatetestrequest ?? '';
	}
	
	
	function doTranslateTest()
	{
		
		$vals = $_POST['item'];
		$valsstore = $vals;
		unset($valsstore['text']);
		unset($valsstore['result']);
		
		$this->modconfig->lastopt = json_encode($valsstore);

		$request_array = [$vals['text']];	
		$opts = http_build_query(['from'=>$vals['fromln'],'to'=>$vals['toln']]);
				
		$resp_raw = GW_Http_Agent::singleton()->postRequest($vals['service_url'].'?'.$opts, ['queries'=>json_encode($request_array)]);
				
		$resp = json_decode($resp_raw);
		
		if(!isset($resp[0])){
			$this->setError("Translation failed response received: <pre>$resp_raw</pre>");
		}
		
		$this->translatetestresult = $resp[0] ?? '';
		$this->translatetestrequest = $vals['text'];
	}
	

	function viewXMLmodifications()
	{
		$item = $this->getDataObjectById();
		
		$this->tpl_vars['item'] = $item;
	}
	
	function dotempmodify()
	{
		$item = $this->getDataObjectById();
		
		$vals = $_POST['item'];
		$item->writeTemp($_POST);
	}
	
	
	
	function initLines()
	{
		$lf = $this->getDataObjectById();
		
		$data = $lf->getLines();
		$list = $data['list'];
		$langs = $data['lns'];
		
		$orig = $lf->getLines(true);
		
		foreach($list as $key => &$item){
			$item['id'] = $key;
			
			$item = (object)$item;
		}
		
		$this->tpl_vars['langfileobj'] = $lf;
		$this->tpl_vars['list'] = $list;
		$this->tpl_vars['orig'] = $orig['list'];		
		$this->tpl_vars['langsfound'] = $langs;
		$this->app->carry_params['parent'] = 1;		
	}
	
	function viewFlatedit()
	{
		$this->initLines();
			
		if( $this->tpl_vars['langfileobj']->newexists ){
			//https://artistdb.eu/admin/lt/system/translations?id=M%2Fcompetitions&act=doPushTemp
			$link1 = $this->buildUri('flatedit', ['id'=>$this->tpl_vars['langfileobj']->id, 'act'=>'doPushTemp']);
			$link2 = $this->buildUri('flatedit', ['id'=>$this->tpl_vars['langfileobj']->id, 'act'=>'doResetTemp']);
			$link1 = "<a href='$link1' class='btn btn-default'>Push</a>";
			$link2 = "<a href='$link2' class='btn btn-primary float-right' "
				. "style='margin-right:50px;' onclick='if(!confirm(\"Are you sure? You will lose changes\"))return false;'><i class='fa fa-trash-o'></i> Reset</a>";
			
			$this->setMessage("There is changes waiting to be pushed $link1 $link2");
		}
	}
	
	function viewEditLine()
	{
		
		$key = $_GET['key'];
		list($group, $key) = GW_Lang::transKeyAnalise("/".$key);
		
				
		$_REQUEST['id'] =  $group;;
		

		
		//d::dumpas($group);
		
		$this->initLines();
		
		if(isset($this->tpl_vars['list'][$key])){
			$item = (object)$this->tpl_vars['list'][$key];
			
		}else{
			$item = (object)['isnew'=>1];
			$item->id = $key;
		}
		
		$item->key = $_GET['key'];
		
		$item->lfid = $group;
		
		$this->tpl_vars['item'] = $item;
		
	}
	
	function doSaveLine()
	{
		$vals = $_POST['item'];
		$lns = explode(',', $vals['lns']);
		
		$item = $this->model->createNewObject($vals['lfid'], true);
		$data = $item->getDataStruct();
		
		
		if($_POST['submit_type']==3){
			$avail = []; $missing = [];

			foreach($lns as $ln){
				if($vals[$ln]){
					$avail[]=$ln;
				}else{
					$missing[] = $ln;
				}
			}

			$infostr = [];
			
			if($missing && !isset($avail[0])){
				$this->setError("Cant auto translate. No source");
			}else{
				foreach($missing as $ln){
					$vals[$ln] = '[A] '.$this->autoTranslateSingle($vals[ $avail[0] ], $avail[0], $ln);
					$infostr[]=$ln.': '.$vals[$ln];
				}
			}



			if($infostr)
				$this->setMessage("Auto translated ".implode('; ', $infostr)." SOURCE: ".$avail[0].': '.$vals[ $avail[0] ]);
		
		}
		
		if($vals['ANY']){
			if($vals['ANY'] == 'debug')
				d::ldump($data, ['hidden'=>"before"]);
			
			GW_Lang_XML::structMod($data, $vals['id'], $vals['ANY']);
			
			if($vals['ANY'] == 'debug')
				d::dumpas($data, ['hidden'=>"after"]);
		}else{
			foreach($lns as $ln){
				if($vals[$ln])
					GW_Lang_XML::structMod($data, $vals['id'], $vals[$ln], $ln);
			}
		}		
				
		$xml = GW_Lang_XML::struct2Xml($data);
		$item->writeTemp($xml);
		
		
		if($_POST['submit_type']==7)
		{
			$_REQUEST['id'] = $vals['lfid'];
			$this->doPushTemp($item);
		}		
		
		if($_POST['submit_type']==3){
			unset($_GET['act']);
			$this->app->jump(false, $_GET);			
		}
		
		
		
		if(isset($_GET['clean'])){
			$r = $vals[strtoupper($this->app->ln)] ?? ($vals['ANY'] ?? first($vals));
			$result = addslashes($r);
			echo "<script>window.parent.updateTranslation('$result'); window.parent.gwcms.close_dialog2();</script>";
			exit;
		}
	}
	
	
	function canBeAccessed($item, $opts=[])
	{
		return true;
	}
	
	
	
	function doSaveLines()
	{
		$item = $this->model->createNewObject($_GET['id'], true);
		$rows = json_decode($_POST['rows'], true);
		
		$data = $item->getDataStruct();
			
		foreach($rows as $row){
			$ln = $row['field']=="ANY" ? false : $row['field'];	
			GW_Lang_XML::structMod($data, $row['id'], $row['value'], $ln);
		}
		
		$xml = GW_Lang_XML::struct2Xml($data);
		$item->writeTemp($xml);
		
		unset($_GET['act']);
		
		
		if(isset($_GET['ajax'])){
			die("SAVEOK");
		}
		
		$this->app->jump(false, $_GET);
	}
	
	function viewFlatedit1()
	{
		$loadlist = [];
		foreach(GW_Lang::$developLnResList as $key => $x){
			list($group, $key) = GW_Lang::transKeyAnalise("/".$key);
			$loadlist[$group][]=$key;
		}
		
		$grouped = [];
		$langfiles = [];
		$changed = [];
		$fields = array_merge(['ANY'], GW::s('LANGS'));
		
		foreach($loadlist as $group => $keys){
			$lf = new GW_Lang_File($group, 1);
			
			$data = $lf->getLines();
			$orig = $lf->getLines(true);
			
			foreach($keys as $key){
				$grouped[$group][$key] = $data['list'][$key] ?? [];
				
				foreach($fields as $field)
					if(($data['list'][$key][$field] ?? false) != ($orig['list'][$key][$field] ?? false))
						$changed[$group][$key][$field]=1;
			}
			
			$langfiles[$group] = $lf;
		}
		
		$app = $grouped['G/application'];
		unset($grouped['G/application']);
		$grouped['G/application'] = $app;
		
		
		$this->tpl_vars['groupedlist']=$grouped;
		$this->tpl_vars['langfiles'] = $langfiles;
		$this->tpl_vars['changedlines'] = $changed;
		
		//d::ldump($changed);
		//exit;
	}

}


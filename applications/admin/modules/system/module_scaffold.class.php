<?php


class Module_Scaffold extends GW_Common_Module
{	

	
	
	public $default_view = 'default';	
	
	function viewDefault()
	{
		
		
	}
	
	
	function doScaffold()
	{
		GW_File_Helper::unlinkOldTempFiles(GW::s('DIR/TEMP'));
		
		$config = [
		    'module'=>'competitions',
		    
		    'submodules'=>[
			[
			    'name'=>'mparticpassive1',
			    'title'=>'Pasyvūs dalyviai',
			    'model'=>'ADB_Passive_Participants',
			    'structure'=>
				[
				    'admin_name'=>['type'=>'varchar255', 'title'=>"Ident. vardas"],
				    'description'=>['type'=>'varchar255','i18n'=>1],
				    'price'=>['type'=>'float']
				],
			    'actions'=>['inline_edit','remove'],
			    'list_actions'=>['inline_form'],
			    'in_menu'=>1,
			    'iconclass'=>'fa fa-info',
			    
			    'list'=>['checklist'=>1]
			]
		    ],
		    'overwrite_tables'=>0,
		    'installid'=>'test'
		];	
		
		
		$module = $config['module'];
		
		$scaffid = ($config['installid'] ?? date('YmdHis'));
		$installdir = GW::s('DIR/TEMP').'scaff_'.$scaffid.'/';
		@mkdir($installdir);
		$moddir = $installdir.'modules/';
		@mkdir($moddir);
		
		
		$module_dir=$moddir.$module.'/';
		$tpl_dir = $module_dir.'/tpl/';
		
		$config['tpl_dir'] = $tpl_dir;
		$config['mod_dir'] = $module_dir;
		
		@mkdir($module_dir);
		@mkdir($tpl_dir);
		
		
		header('content-type: text/plain');
		
		
		//1
		$xml = $this->submoduleAdd_lang($config);
		file_put_contents($module_dir.'lang.xml', $xml);
		
		
		//2
		$sqls = $this->submoduleAdd_db($config);
		$this->execSqls($sqls, true);
		//changes sql
		file_put_contents($installdir.'update.sql', $sqls);
		
		
		//3
		$this->submoduleAdd_expandFiles($config);
		
		GW_Install_Helper::recursiveChmod($installdir);
		
		echo "Run\n";
		echo('php '.GW::s('DIR/ROOT').'applications/cli/scaffold.php '.$scaffid);
		//die(print_r($config));
	}
	
	function submoduleAdd_lang(&$config)
	{
		$file = GW::s('DIR/ADMIN/MODULES').$config['module'].'/lang.xml';
	
		
		$data=[];
		/*
		$data = ['particpassive'=>[
			'title'=>'Pasyvūs dalyviai' , 
			'info'=>['iconclass'=>"fa faa", 'model'=>'adb_competitions_particpassive'],
			'in_menu'=>0,
		    ],
		    
		];
		*/
		
		$existsfields = GW::l('/M/'.$config['module']."/FIELDS");
		$newfields = [];
		

		
		foreach($config['submodules'] as $submodule)
		{
			$data[$submodule['name']]=[
			    'title'=>$submodule['title'],
			];
			
			if(isset($submodule['iconclass']))
				$data[$submodule['name']]['info']['iconclass'] = $submodule['iconclass'];
			
			if(isset($submodule['model']))
				$data[$submodule['name']]['info']['model'] = $submodule['model'];
			
			if(isset($submodule['in_menu']))
				$data[$submodule['name']]['in_menu'] = $submodule['in_menu'];
			
			
			foreach($submodule['structure'] as $key => $x)
				if(!isset($existsfields[$key]))
					$newfields[$key]=$submodule['structure'][$key];
			
		}
			
		
		$xml = file_get_contents($file);
		$xml = GW_Lang_XML::modify($xml, 'MAP/childs', $data);	
		
		
		$fieldslang = [];
		foreach($newfields as $key => $opts)
			if(isset($opts['title']))
				$fieldslang[$key] = $opts['title'];
		
		$xml = GW_Lang_XML::modify($xml, 'FIELDS', $fieldslang);
		
		
		return $xml;
		
	}
	
	function submoduleAdd_db(&$config)
	{
		$type = function($name){
			switch($name){
				case 'varchar255';
					return "varchar(255)";
				default:
					return $name;
			}
		};
		$ctbl_row = function($name, $type){
			return "  `{$name}` {$type} NOT NULL,\n";
		};
		
		$sqls=[];
		
		
		
		foreach($config['submodules'] as &$submodule)
		{
			$tbl = $submodule['table'] = strtolower($submodule['model']);
			
			$fieldssql = "";
				
			foreach($submodule['structure'] as $name => $field)
			{
				
				if(isset($field['i18n']) && $field['i18n']){
					foreach(GW::s('LANGS') as $ln)
						$fieldssql .= $ctbl_row("{$name}_{$ln}", $type($field['type']));
					
					$submodule['i18n']=1;
				}else{
					$fieldssql .= $ctbl_row("{$name}", $type($field['type']));
				}
				
				
			}
			
			
			
$sqls[]= (isset($config['overwrite_tables']) && $config['overwrite_tables'] ? "DROP TABLE IF EXISTS `$tbl`;":''). "
CREATE TABLE IF NOT EXISTS `$tbl` (
  `id` int(11) NOT NULL,
$fieldssql
  `insert_time` datetime NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `$tbl`  ADD PRIMARY KEY (`id`);
ALTER TABLE `$tbl`  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;			
			";


			
		}
			
		return implode(';', $sqls);
	}
	
	
	function execSqls($sqls, $show_exec_res=true)
	{
		$sqls = explode(';', $sqls);
		
		$db =& $this->app->db;
		
		$results;
		
		foreach($sqls as $sql)
		{
			$sql = trim($sql);
			if(!$sql)continue;
			
			$result = ['sql'=>htmlspecialchars($sql)];
			
			
			$res = $db->fetch_rows($sql, true, true);
			$result['res']= json_encode($res, JSON_PRETTY_PRINT);
			$result['affected'] = $db->affected();
			$result['error'] = $db->error; 
			$results[] = $result;
		}	
		
		if($show_exec_res)
			echo  GW_Data_to_Html_Table_Helper::doTable($results);
		
		return $results;
	}
	
	
	function var_export($arr)
	{
		$out=[];
		foreach($arr as $key => $val){
			$out[] = var_export($key, true) ." => " .var_export($val, true);
		}
		return '['.implode(', ', $out).']';
			
	}
	
	function submoduleAdd_expandFiles(&$config)
	{
		$inputtype = function($name){
			switch($name){
				case 'varchar255';
					return "text";
				case 'float';
					return "number";
				default:
					return $name;
			}
		};
		
		
		$tpldir = __DIR__.'/tpl/scaffold_tpls/';
		@mkdir($tpldir);
		
		$modeltpl = file_get_contents("{$tpldir}model.class.php");
		$controllertpl = file_get_contents("{$tpldir}submodule.class.php");
		$listtpl =  file_get_contents("{$tpldir}list.tpl");
		$elementstpl =  file_get_contents("{$tpldir}elements.tpl");
		
		$classfile = function($classname){ return strtolower($classname).'.class.php'; };
		
		$smarty_opts = function($opts){ 
			$out=[];
			foreach($opts as $key => $val){
				$out[] = $key."=".(preg_match('/^[a-z0-9_]*$/i', $val)? $val : var_export($val, true));
			}
			return implode(' ', $out);
		};
		
		
		
		foreach($config['submodules'] as &$submodule)
		{
			//init
			$submod = $submodule['name'];
			
			//model
			$model_tpl = str_replace('GW_Model_Template', $submodule['model'], $modeltpl);
			$model_tpl = str_replace('gw_model_table', $submodule['table'], $model_tpl);
			
			if(isset($submodule['i18n'])){
				$model_tpl = str_replace('GW_Data_Object', 'GW_i18n_Data_Object', $model_tpl);
				
				$i18n_fields=[];
				
				foreach($submodule['structure'] as $fieldid => $opts)
					if(isset($opts['i18n']))
						$i18n_fields[$fieldid] = 1;
					
				
				$model_tpl = str_replace('public $i18n_fields = [];', 'public $i18n_fields = '. self::var_export($i18n_fields, true).';', $model_tpl);
			}
			
			$desttpldir = $config['tpl_dir'].$submod.'/';
			@mkdir($desttpldir);
			
			
			file_put_contents($config['mod_dir'].$classfile($submodule['model']), $model_tpl);
			
			
			//controller
			
			//TODO: prideti visa struktura be titlu tik
			//getDisplayConfig uzsiloadintu fieldus
			
			$submodclass = "Module_".ucfirst($submod);
			$controller_tpl = str_replace('Module_Submodule', $submodclass, $controllertpl);
			
			
			file_put_contents($config['mod_dir'].$classfile($submodclass), $controller_tpl);
			
			
			
			//list 
			
			file_put_contents($desttpldir.'list.tpl', $listtpl);
			
			
			$inputssmarty = "";
			//form
			foreach($submodule['structure'] as $name => $field)
			{
				$opts=[];
				
				$inptpl="\t".'{elseif $field=="$field$"}'."\n\t\t".'{include file=$i $dropopts$}'."\n";
				

				$opts['name'] = $name;
				$opts['type'] = $inputtype($field['type']);
				
				if(isset($field['i18n']))
					$opts['i18n']=4;				
				
				$inp_tpl = str_replace('$dropopts$', $smarty_opts($opts), $inptpl);;
				$inp_tpl = str_replace('$field$', $name, $inp_tpl);;
				
				$inputssmarty .= $inp_tpl;
			}			
			
			
			$elements_tpl = str_replace('{*inputsdrop*}', $inputssmarty, $elementstpl);
			
							
			
			file_put_contents($desttpldir.'elements.tpl', $elements_tpl);
		}
		

	}
	
	
	function doScaffoldProceed()
	{
		$scaffid = $_GET['scaffid'];
		$scaffdir = GW::s('DIR/TEMP').'scaff_'.$scaffid.'/';

		$src = $scaffdir.'modules/';
		$dest = dirname(GW::s('DIR/ADMIN/MODULES'));

		//print_r([$src, $dest]);

		if(is_dir($scaffdir)){

			$out=shell_exec($cmd="cp -r $src $dest");
			echo "$cmd:\n";
			echo $out."\n";
		}else{
			echo "no such dir";
		}
		
		copy($scaffdir.'update.sql', GW::s('DIR/ROOT').'sql/'.date('Y-m-d-H-i-s').' scaff '.$scaffid.'.sql');

		exit;
	}	
	
	

	
}

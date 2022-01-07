<?php


class Module_Config  extends GW_Common_Module
{	
	use Module_Import_Export_Trait;	
		
	function init()
	{
		parent::init();
				
		$this->app->carry_params['clean']=1;
		$this->app->carry_params['type']=1;
		
	
	}
	



	function exportAll()
	{
		$list = $this->model->findAll();
		
		$rows=[];
		foreach($list as $item){
			$row = $item->toArray();
			unset($row['id']);
			$rows[] = $row;
		}
		return [json_encode($rows, JSON_PRETTY_PRINT), count($rows)];
	}
	
	function doImportAll()
	{
		if(!$this->app->user->isRoot())
			return $this->setError("No permission");
		
		if(!isset($_POST['answers']['codejson']))
			die("<script>location.href='{$_GET['camefrom']}'</script>");
		
		$array = json_decode($_POST['answers']['codejson'], true);
		
		if(!$array)
			d::dumpas($array);
			
		$t = new GW_Timer;
		$cfg = new GW_Config;
		
		
		foreach($array as $row)
			$cfg->set($row['key'], $row['value']);
		
		if($_POST['answers']['count']!=count($array)){
			$this->setError('Wrong rows count!!! expected: '.$_POST['answers']['count'].'; real:'.count($array));
		}
			
		$this->setMessage("Import done, cnt: ".count($array).", speed: {$t->stop()}");
		$this->jump();
	}
	
	function doSendToDev()
	{
		initEnviroment(GW_ENV_DEV);
		list($rows,$cnt) = $this->exportAll();
		$formaction=GW::s("SITE_URL").'admin/lt/datasources/config?act=doImportAll&camefrom='. urlencode($_SERVER['REQUEST_URI']);
		echo "<form id='jsoncodeform' action='$formaction' method='post'>";
		echo "<textarea name='answers[codejson]'>".$rows.'</textarea>';
		echo "<input name='answers[count]' value='".$cnt."'>";
		echo "</form>";
		echo "<script>require(['gwcms'], function(){ $('#jsoncodeform').submit(); })</script>";
		
	}	

}

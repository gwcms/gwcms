<?php




class GW_Public_Module
{
	var $module_file;
	var $tpl_dir;
	var $module_dir;
	var $lang;
	var $smarty;

	/*
	 if path is "www.site.com/en/firstlevel/products/item" and "item" is view namem,
	 for correct working set view_path_index=2,
	 another example "www.site.com/en/first/second/third/products/item" - view_path_index=4
	 */
	var $view_path_index=2;


	function __construct($variables=Array())
	{
		foreach($variables as $key => $val)
		$this->$key = $val;
			
			
		$this->module_dir = dirname($this->module_file).'/';
		$this->tpl_dir = $this->module_dir.'tpl/';

		$this->smarty =& GW::$smarty;

		if(file_exists($langf="{$this->module_dir}lang.xml"))
		$this->lang = GW_Lang_XML::load($langf, GW::$request->ln);
	}



	function processTemplate($name)
	{
		$this->smarty->assignByRef('messages', $this->messages);
		$this->smarty->assign('m', $this);

		$file=$this->tpl_dir.$name;

		if(!file_exists($tmp = $file.'.tpl'))
		die("Template $tmp not found");
			
		$this->smarty->display($tmp);
			
	}

	function processView($name)
	{
		$methodname="view".$name;
		$this->$methodname();

		$this->processTemplate($name);

	}


	function doJson()
	{
		$params=$_REQUEST['params'];
		$func='json'.$_REQUEST['function'];

		$result = call_user_func_array(Array($this, $func), $params);

		echo json_encode($result);
		exit;
	}

	function processAction($name)
	{
		if(substr($name,0,2)!='do')
		die('Invalid action name');


		$methodname=$name;
		$this->$methodname();
	}


	/**
	 * Validate view,action name
	 * @param $str
	 * @return unknown_type
	 */
	function __funcVN($str)
	{
		if(!$str)
		return; // jei tuscias stringas pasidarys 1 simbolio stringas
			
		//valid method name
		$str=preg_replace('/[^a-z0-9]/i', '_', $str);


		$str[0]=preg_replace('/[^a-z]/i', '_', $str[0]);

		return strtolower(str_replace('_','',$str));
	}



	function process()
	{
		$this->init();


		$act_name = self::__funcVN($_REQUEST['act']);



		if (isset(GW::$request->path_arr[$this->view_path_index]) ){
			$view_name = self::__funcVN(GW::$request->path_arr[$this->view_path_index]['name']);
		}
		if(!method_exists($this, 'view'.$view_name)){
			$view_name = self::__funcVN('default'); //perspektyvoj kad padaryti kitus viewsus
		}


		if($act_name)
		$this->processAction($act_name);

			

		$this->processView($view_name);
	}
	
	function setUpPaging($last_query_info)
	{	
		$current=(int)$this->list_params['page'] ? (int)$this->list_params['page'] : 1;
		$length=ceil($last_query_info['item_count'] / $this->list_params['page_by']);
	
		$this->smarty->assign('paging_tpl_page_count', $length);
	
		if($length<2)
			return;
			
			
		$this->smarty->assign('paging', $paging=Array
		(
			'current'=>$current,
			'length'=>$length,
			'first'=> $current < 2 ? 0 : 1,
			'prev'=>  $current <= 2 ? 0 : $current-1,
			'next'=>  $current >= $length-1 ? 0 : $current+1,
			'last'=>  $current >= $length   ? 0 : $length,
		));	

	}	

}
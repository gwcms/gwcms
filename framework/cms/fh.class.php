<?php

$GLOBALS['smarty_vars_store']=Array();
/**
 * 
 * Frontend Helper for presentation (Smarty)
 * @author wdm
 *
 */
class FH
{
	public $app;
	
	static function &getTplVars(&$template_obj, $vars_arr)
	{
		$res=Array();
		
		foreach($vars_arr as $key)
			$res[$key] =& $template_obj->getVariable($key)->value;
		
		return $res;
	}
	
	static function out($var)
	{
		if(is_array($var) || is_object($var))
			return dump($var);
			
		echo $var;
	}
	

	
	//alternative lang files use
	//if no value in module lang file, take it from main lang file
	function altLang($key_arr)
	{
		return GW_Array_Helper::altValue($key_arr, $this->app->module->lang, $this->app->lang);
	}
	
	//to use in javascript
	//get lang strings
	//var lang={FH::printLangStrings('FIELDS/title','BUTTONS/OK','BUTTONS/CANCEL')}
	function printLangStrings()
	{
		$arr=Array();

		foreach(func_get_args() as $arrkey)
			$arr[$arrkey]=$this->altLang($arrkey);
			
		echo json_encode($arr);
	}
	
	function fieldTitle($key)
	{	
		$title = $this->altLang(Array('FIELDS',$key));
		
		return $title ? $title : $key;
	}
	
	function shortFieldTitle($key)
	{
		$title = $this->fieldTitle($key);
		
		if($tmp=$this->app->module->lang['FIELDS_SHORT'][$key])
			return "<span title='$title'>$tmp</span>";
		
		return $title;
	}
	
	function viewTitle($key)
	{	
		$title=$this->altLang(Array('VIEWS',$key));
		
		//magic stuff for "form" view. switch create or view
		//if($key=='form')
		//{
		//	$create_edit=explode('/', $title);
		//	$title=$create_edit[(bool)(int)$_GET['id']];
		//}	
			
		return $title;		
	}
	
	
	/**
	 * should be called in smarty only
	 */
	function smarty_vars_push($keys)
	{
		$vars = explode(',',$keys);
		foreach($vars as $key)
			$GLOBALS['smarty_vars_store'][$key]=$GLOBALS['smarty']->_tpl_vars[$key];
	}
	
	/**
	 * should be called in smarty only
	 */
	function smarty_vars_pull($keys)
	{
		$vars = explode(',',$keys);
		foreach($vars as $key)
		{
			$GLOBALS['smarty']->_tpl_vars[$key]=$GLOBALS['smarty_vars_store'][$key];
			unset($GLOBALS['smarty_vars_store'][$key]);
		}
	}
	
	function maxUploadSize()
	{
		$cache;if($cache)return $cache;
		
		return $cache=sprintf($this->app->lang['MAX_UPLOAD_SIZE'], ini_get('upload_max_filesize'));
	}
	

	
	function gw_path($params)
	{
		$this->gw_link_po($params);
		
		return isset($params['path']) ? $params['path'] : false;
	}
	
	//path only
	function gw_link_po(&$params)
	{		
		$params['params'] = (isset($params['params']) ? $params['params'] : [])   + $this->app->carryParams();		
		$params['params'] = http_build_query($params['params']);	
			
		if(isset($params['do']))
			$params['params']= 'act=do:'.$params['do'].($params['params']?'&':'').$params['params'];
			

		$path_start = $this->app->ln.'/' ;
		$path = $this->app->path;
			
		if(isset($params['levelup'])){ 
			// back to upper level
			$params['path'] = $path_start. dirname($path);
		}elseif(isset($params['relative_path'])){ 
			
			//pvz jeigu path = sitemap/pages/15 o relative_path = 10/form
			//padaryt sitemap/pages/10/form
			$tmp=is_numeric(pathinfo($path,PATHINFO_FILENAME)) ? dirname($path) : $path;
			
			// extend path
			$params['path']= $path_start .$tmp . '/' . $params['relative_path'];
		}elseif(!isset($params['path'])){ 
			// refresh 
			$params['path'] = $path_start . $path;
		}
		
		$params['path']=$params['path'].($params['params']?'?':'').$params['params'];		
	}	
	
	
	function gw_link($params)
	{		
		if(!isset($params['show_title']))
			$params['show_title']=1;

		if(!$params['title'])
			$params['title']=$this->app->lang['ICON_TITLES'][$params['icon']];

		if(!isset($params['html']))
			$params['html']=1;
			

		$this->gw_link_po($params);

		if(isset($params['path_only']))
			return $params['path'];
			

		$params['img']=$this->app->app_root.'img/icons/'.$params['icon'].'.png';
			
		if(!$params['html'])
			return Array('link'=>$params['path'], 'img'=>$params['img'], 'title'=>$params['title']);

		$img = ($params['icon']?'<img align="absmiddle" src="'.$params['img'].'" title="'.$params['title'].'" />':'');


		if($params['confirm'])
		{
			$params['tag_params']['onclick']="return confirm('".$this->app->lang['ACTION_CONFIRM_REQUIRED']."\\n".$this->app->lang['ACTION'].": '+this.title)";
		}


		$tag_params="";

		if($params['tag_params'])
			foreach($params['tag_params'] as $param_name => $value)
				$tag_params.=' '.$param_name.'="'.$value.'"';
			


		$a =
			'<a href="'.$params['path'].'" '.$tag_params.' title="'.$params['title'].'">';
			
		$a_end='</a>';

		$contents .=
		($img ? $a.$img.$a_end : '').
		($img && $params['show_title'] ? ' ':'') .
		($params['show_title']? $a.$params['title'].$a_end : '');
			
		return $contents;
	}
	
	/*
	 * time: only Y-m-d H:i:s (mysql) time format
	 * 
	 * Display time like in gmail
	 * pass time string as argument for exmpl: "2010-11-15 16:22:12"
	 * 
	 * "16:15" if today
	 * "Oct 15" if this year
	 * "2005 Oct 10" if else 
	 */
	
	function shortTime($time)
	{
		if($time=='0000-00-00 00:00:00')
			return '';
			
		list($y,$m,$d,$h,$i,$s)=preg_split("/[-\s:]+/", $time);
		
		
		if(date('Y-m-d') == "$y-$m-$d")
			return "$h:$i";
		
		$lang = $this->app->lang;

		return (date('Y') != $y ? $y.' ':'').$lang['MONTHS_SHORT'][$m-1].' '.$d;
	}

	function dateFormate($date, $format)
	{
		return date($format, strtotime($date));
	}
	
	
}


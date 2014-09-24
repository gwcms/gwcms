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
	static function &getTplVars(&$template_obj, $vars_arr)
	{
		$res=Array();
		
		foreach($vars_arr as $key)
			$res[$key] =& $template_obj->getVariable($key)->value;
		
		return $res;
	}
	
	function out($var)
	{
		if(is_array($var) || is_object($var))
			return dump($var);
			
		echo $var;
	}
	

	
	//alternative lang files use
	//if no value in module lang file, take it from main lang file
	static function altLang($key_arr)
	{
		return GW_Array_Helper::altValue($key_arr, GW::$request->module->lang, GW::$lang);
	}
	
	//to use in javascript
	//get lang strings
	//var lang={FH::printLangStrings('FIELDS/title','BUTTONS/OK','BUTTONS/CANCEL')}
	static function printLangStrings()
	{
		$arr=Array();

		foreach(func_get_args() as $arrkey)
			$arr[$arrkey]=self::altLang($arrkey);
			
		echo json_encode($arr);
	}
	
	static function fieldTitle($key)
	{	
		$title = self::altLang(Array('FIELDS',$key));
		
		return $title ? $title : $key;
	}
	
	static function shortFieldTitle($key)
	{
		$title = self::fieldTitle($key);
		
		if($tmp=GW::$request->module->lang['FIELDS_SHORT'][$key])
			return "<span title='$title'>$tmp</span>";
		
		return $title;
	}
	
	static function viewTitle($key)
	{	
		$title=self::altLang(Array('VIEWS',$key));
		
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
	static function smarty_vars_push($keys)
	{
		$vars = explode(',',$keys);
		foreach($vars as $key)
			$GLOBALS['smarty_vars_store'][$key]=$GLOBALS['smarty']->_tpl_vars[$key];
	}
	
	/**
	 * should be called in smarty only
	 */
	static function smarty_vars_pull($keys)
	{
		$vars = explode(',',$keys);
		foreach($vars as $key)
		{
			$GLOBALS['smarty']->_tpl_vars[$key]=$GLOBALS['smarty_vars_store'][$key];
			unset($GLOBALS['smarty_vars_store'][$key]);
		}
	}
	
	static function maxUploadSize()
	{
		static $cache;if($cache)return $cache;
		
		return $cache=sprintf(GW::$lang['MAX_UPLOAD_SIZE'], ini_get('upload_max_filesize'));
	}
	

	
	static function gw_path($params)
	{
		self::gw_link_po($params);
		
		return $params['path'];
	}
	
	//path only
	static function gw_link_po(&$params)
	{
		
		$params['params'] = (array)$params['params'] + GW::$request->carryParams();		
		$params['params']=http_build_query($params['params']);	
			
		if(isset($params['do']))
			$params['params']= 'act=do:'.$params['do'].($params['params']?'&':'').$params['params'];
			

		$path_start = GW::$request->ln.'/' ;
		$path = GW::$request->path;
			
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
	
	
	static function gw_link($params)
	{
		if(!isset($params['show_title']))
			$params['show_title']=1;

		if(!$params['title'])
			$params['title']=GW::$lang['ICON_TITLES'][$params['icon']];

		if(!isset($params['html']))
			$params['html']=1;
			

		self::gw_link_po($params);

		if(isset($params['path_only']))
			return $params['path'];
			

		$params['img']='img/icons/'.$params['icon'].'.png';
			
		if(!$params['html'])
			return Array('link'=>$params['path'], 'img'=>$params['img'], 'title'=>$params['title']);

		$img = ($params['icon']?'<img align="absmiddle" src="'.$params['img'].'" title="'.$params['title'].'" />':'');


		if($params['confirm'])
		{
			$params['tag_params']['onclick']="return confirm('".GW::$lang['ACTION_CONFIRM_REQUIRED']."')";
		}


		$tag_params="";

		if($params['tag_params'])
			foreach($params['tag_params'] as $param_name => $value)
				$tag_params.=' '.$param_name.'="'.$value.'"';
			


		$a =
			'<a href="'.$params['path'].'" '.$tag_params.'>';
			
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
	
	static function shortTime($time)
	{
		if($time=='0000-00-00 00:00:00')
			return '';
			
		list($y,$m,$d,$h,$i,$s)=preg_split("/[-\s:]+/", $time);
		
		
		if(date('Y-m-d') == "$y-$m-$d")
			return "$h:$i";
		
		$lang = GW::$smarty->getTemplateVars('lang');

		return (date('Y') != $y ? $y.' ':'').$lang['MONTHS_SHORT'][$m-1].' '.$d;
	}

	static function dateFormate($date, $format)
	{
		return date($format, strtotime($date));
	}
	
	
}


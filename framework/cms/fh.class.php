<?php
$GLOBALS['smarty_vars_store'] = Array();

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
		$res = Array();

		if(GW::s('SMARTY_VERSION')>=4){
			return $smarty->getTemplateVars();
		}
		
		foreach ($vars_arr as $key)
			$res[$key] = & $template_obj->getVariable($key)->value;

		return $res;
	}

	static function out($var)
	{
		if (is_array($var) || is_object($var))
			return dump($var);

		echo $var;
	}

	//alternative lang files use
	//if no value in module lang file, take it from main lang file
	function altLang($key_arr)
	{
		return GW_Array_Helper::altValue($key_arr, $this->app->module->lang, $this->app->lang);
	}

	function fieldTitle($key)
	{
		$title = GW::l($fkey = '/A/FIELDS/' . $key, ['noedit'=>1]);

		return $title != $fkey ? $title : $key;
	}

	function shortFieldTitle($key)
	{
		$title = self::fieldTitle($key);

		if (($tmp = GW::l($fkey="/m/FIELDS_SHORT/$key", ['noedit'=>1])) && ($fkey!=$tmp))
			return "<span title='".strip_tags ($title)."'>$tmp</span>";

		return $title;
	}

	/**
	 * should be called in smarty only
	 */
	function smarty_vars_push($keys)
	{
		$vars = explode(',', $keys);
		foreach ($vars as $key)
			$GLOBALS['smarty_vars_store'][$key] = $GLOBALS['smarty']->_tpl_vars[$key];
	}

	/**
	 * should be called in smarty only
	 */
	function smarty_vars_pull($keys)
	{
		$vars = explode(',', $keys);
		foreach ($vars as $key) {
			$GLOBALS['smarty']->_tpl_vars[$key] = $GLOBALS['smarty_vars_store'][$key];
			unset($GLOBALS['smarty_vars_store'][$key]);
		}
	}

	function maxUploadSize()
	{
		$cache;
		if ($cache)
			return $cache;

		return $cache = sprintf($this->app->lang['MAX_UPLOAD_SIZE'], ini_get('upload_max_filesize'));
	}

	function gw_path($params)
	{
		$this->gw_link_po($params);

		return isset($params['path']) ? $params['path'] : false;
	}

	//path only
	function gw_link_po(&$params)
	{
		$params['params'] = (isset($params['params']) ? $params['params'] : []) + $this->app->carryParams();
		$params['params'] = http_build_query($params['params']);

		if (isset($params['do']))
			$params['params'] = 'act=do:' . $params['do'] . ($params['params'] ? '&' : '') . $params['params'];


		$path_start = '';
		$path = $this->app->path;

		if (isset($params['levelup'])) {
			// back to upper level
			$params['path'] = $path_start . dirname($path);
		} elseif (isset($params['relative_path'])) {

			//pvz jeigu path = sitemap/pages/15 o relative_path = 10/form
			//padaryt sitemap/pages/10/form
			$tmp = is_numeric(pathinfo($path, PATHINFO_FILENAME)) ? dirname($path) : $path;

			// extend path
			$params['path'] = $path_start . $tmp . '/' . $params['relative_path'];
		} elseif (!isset($params['path'])) {
			// refresh 
			$params['path'] = $path_start . $path;
		}

		$params['path'] = isset($params['fullpath']) ? $params['fullpath'] : $this->app->buildUri($params['path']);
		$params['path'] = $params['path'] . ($params['params'] ? '?' : '') . $params['params'];
	}

	function gw_link_confirm($include_onclick = true)
	{
		$title_or_inner_txt = "(this.title ? this.title : this.textContent)";
		$str = "return confirm($(this).data('confirm_text') ? $(this).data('confirm_text') : '" . $this->app->lang['ACTION_CONFIRM_REQUIRED'] . "\\n" . $this->app->lang['ACTION'] . ": '+$title_or_inner_txt)";

		return $include_onclick ? 'onclick="' . $str . '"' : $str;
	}

	function gw_link_prompt($question, $url, $include_onclick = true)
	{
		$str = "var ss=window.prompt('{$question}');if(ss)location.href='" . $url . "'+ss;return false;";

		return $include_onclick ? 'href="#" onclick="' . $str . '"' : $str;
	}

	function gw_link($params)
	{
		if (!isset($params['show_title']))
			$params['show_title'] = 1;

		if (!$params['title'])
			$params['title'] = $this->app->lang['ICON_TITLES'][$params['icon']];

		if (!isset($params['html']))
			$params['html'] = 1;


		$this->gw_link_po($params);

		if (isset($params['path_only']))
			return $params['path'];


		$params['img'] = $this->app->icon_root . $params['icon'] . '.png';

		if (!$params['html'])
			return Array('link' => $params['path'], 'img' => $params['img'], 'title' => $params['title']);

		$img = ($params['icon'] ? '<img align="absmiddle" src="' . $params['img'] . '" title="' . $params['title'] . '" />' : '');


		if ($params['confirm'])
			$params['tag_params']['onclick'] = $this->gw_link_confirm(false);


		if ($params['shift_button']) {
			$params['tag_params']['onclick'] = "if(event.shiftKey){location.href=gw_navigator.url(this.href,{'shift_key':1});return false}";
		}

		$tag_params = "";

		if ($params['tag_params'])
			foreach ($params['tag_params'] as $param_name => $value)
				$tag_params.=' ' . $param_name . '="' . $value . '"';



		$a = '<a href="' . $params['path'] . '" ' . $tag_params . ' title="' . $params['title'] . '">';

		$a_end = '</a>';

		$contents .=
		    ($img ? $a . $img . $a_end : '') .
		    ($img && $params['show_title'] ? ' ' : '') .
		    ($params['show_title'] ? $a . $params['title'] . $a_end : '');

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
		if ($time == '0000-00-00 00:00:00')
			return '';

		list($y, $m, $d, $h, $i, $s) = preg_split("/[-\s:]+/", $time);


		if (date('Y-m-d') == "$y-$m-$d")
			return "$h:$i";

		$lang = $this->app->lang;
		
		
		if($m > 0){
			$month = $this->app->app_name=="ADMIN" ? GW::l('/G/date/MONTHS_SHORT/'. ($m-1)) : GW::ln('/G/date/MONTHS_SHORT/'. ($m-1));
		}else{
			$month ="";
		}

		return (date('Y') != $y ? $y . ' ' : '') . $month . ' ' . $d;
	}

	function dateFormate($date, $format)
	{
		return date($format, strtotime($date));
	}
	
	static function dateHuman($date, $opts=[])
	{				
		if(!preg_match('/(\d{4})-(\d{2})-(\d{2})/', $date, $m)) 
			return $date;
		
		$replace="";
		if($opts['year'] ?? false)
			$replace.=$m[1].' ';
		
		$mdw=date('N',strtotime($m[0]));
		
		
		$trln = "";
		if($ln=$opts['ln'] ?? false)
			$trln=$ln ? "/LN/$ln":"";
		
		$replace .= GW::l("$trln/G/DATE/MONTH_KILMININKAS/".(int)$m[2]).' ';
		//$replace .= (int)$m[3].' '. GW::ln("/G/DATE/DAYSHORT");
		$replace .= (int)$m[3];
		
		if(isset($opts['weekday']))
			$replace .=', '. mb_strtolower(GW::ln('/G/DATE/WEEKDAYS/'.$mdw));;
		
		if($opts['format']){
			switch($opts['format']){
				case 'adb_en':
					//17th January 2021
					$nf = new NumberFormatter('en_US', NumberFormatter::ORDINAL);
					$letters = str_replace([0,1,2,3,4,5,6,7,8,9],'',$nf->format($m[3]));
					$letters = '<sup>'.$letters.'</sup>';
					
					return  $m[3].$letters.' '. GW::ln("$trln/G/DATE/MONTHS/".(int)$m[2]).' '.$m[1];
				break;
			}
		}
		
		
		
		

		
		return str_replace($m[0], $replace, $date);
	}

	static function dateHumanRange($start, $end, $opts=[])
	{
		$start = date('Y-m-d-N', strtotime($start));
		$end = date('Y-m-d-N', strtotime($end));
		
		list($sy,$sm, $sd, $sWd) = explode('-', $start);
		list($ey,$em, $ed, $eWd) = explode('-', $end);
		
		$trln = isset($opts['ln'])  ? "/LN/$ln":"";
		
		$sM = GW::l("$trln/G/DATE/MONTH_KILMININKAS/".(int)$sm);
		$eM = GW::l("$trln/G/DATE/MONTH_KILMININKAS/".(int)$em);
		
		
		if($opts['weekday'] ?? false){
			$sWd = mb_strtolower(GW::l('/G/DATE/WEEKDAYS/'.$sWd));;
			$eWd = mb_strtolower(GW::l('/G/DATE/WEEKDAYS/'.$eWd));;
			$sWdstr=", $sWd";
			$eWdstr=", $eWd";
		}else{
			$sWdstr="";
			$eWdstr="";
		}
		
		
		if($sy != $ey && $sm==$em && $sd ==$ed){
			return "$sy $sM $sd d.$sWdstr — $ey $eM $ed d.$eWdstr";
		}elseif($sy == $ey && $sm!=$em && $sd ==$ed){
			return "$sy $sM $sd d.$sWdstr — $eM $ed d.$eWdstr";
		}elseif($sy == $ey && $sm==$em && $sd!=$ed){
			return "$sy $sM $sd d.$sWdstr — $ed d.$eWdstr";
		}else{
			return "$sy $sM $sd d.$sWdstr";
		}
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
	
	static function urlStr($s)
	{
		return preg_replace('/[^a-z0-9]/i', '-', strtolower(gw_url_crypt_helper::convertAccentsAndSpecialToNormal($s)));
	}
	
	
	
	static function calcInputNamePattern($input_name_pattern, $type='')
	{
		if(!$input_name_pattern)
			$input_name_pattern="item[%s]";
		
		if(strpos($type, 'multiselect')!==false)
			$input_name_pattern="{$input_name_pattern}[]";

		return $input_name_pattern;
	}	
}

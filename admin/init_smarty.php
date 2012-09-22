<?


require GW::$dir['LIB'].'smarty/Smarty.class.php';
$s =& GW::$smarty;

$s = new Smarty;
$s->compile_check = true;
$s->allow_php_tag=true;

if(GW::$public){
	$s->compile_dir =GW::$dir['PUB_TEMPLATES_C'];
	$s->template_dir = GW::$dir['PUB_TEMPLATES'];
	$s->trusted_dir = GW::$dir['PUB'];		
}else{
	$s->compile_dir = GW::$dir['TEMPLATES_C'];
	$s->template_dir = GW::$dir['TEMPLATES'];
	$s->trusted_dir = GW::$dir['ADMIN'];	
}

$s->_file_perms = 0666;
$s->_dir_perms = 0777;

$s->assignByRef('GLOBALS', $GLOBALS);
$s->assign('request', GW::$request);
$s->assignByRef('ln', GW::$request->ln);
$s->assignByRef('lang', GW::$lang);
$s->assignByRef('page', GW::$request->page);



class Smarty_Internal_Template_Custom extends Smarty_Internal_Template
{
	function getRenderedTemplate ()
	{
		$str = parent::getRenderedTemplate();
		
		if(isset($_SESSION['debug']))
			return "\n<!--$this->template_resource-->\n".$str."\n<!--$this->template_resource END-->\n";
		
		return $str;
	}
} 


$s->template_class = 'Smarty_Internal_Template_Custom';



?>
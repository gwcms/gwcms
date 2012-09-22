<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
{php}
	GW::$smarty->assign('session_exp', GW::$user ? GW::$user->remainingSessionTime() : -1);
{/php}


<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<base href="{Navigator::getBase(1)}" />
	<title>{$title|default:$request->page->get(title,$ln)} - {$lang.SITE_TITLE}</title>
	<meta name="description" content="{$lang.GW_CMS_DESCRIPTION}" />
	<link rel="icon" href="img/favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" type="text/css" href="css/main.css" />
    <!--[if lte IE 1]><link rel="stylesheet" type="text/css" href="css/main_ie.css" /><![endif]-->
	
	<link type="text/css" href="css/jquery_ui/jquery-ui-1.8rc3.custom.css" rel="stylesheet" />
	
	<script type="text/javascript" src="js/jquery.min-latest.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.8rc3.custom.min.js"></script>
	
	<script type="text/javascript" src="js/jquery.selectboxes.min.js"></script>	

	<script type="text/javascript" src="js/main.js"></script>
		
	
	<script type="text/javascript">
		$.extend(GW, { base:'{$request->base}', ln:'{$request->ln}', path:'{$request->path}', session_exp:{$session_exp}, server_time:'{"F d, Y H:i:s"|date}'});
		gw_adm_sys.init();
	</script>
</head>
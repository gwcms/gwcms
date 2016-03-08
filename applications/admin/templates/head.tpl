<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"><html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

{if $app->user}
	{assign var="session_exp" value=$app->user->remainingSessionTime() scope=parent}
{/if}

{assign var="breadcrumbs" value=$app->getBreadcrumbs($breadcrumbs_attach) scope=parent}
{$translations[]='CLOSE'}


<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<base href="{$sys_base}" />
	<title>
		{include "title_breadcrumbs.tpl"}
		{*$title|default:$app->page->get(title,$ln)*} - {GW::s(SITE_TITLE)}</title>
	<meta name="description" content="{$lang.GW_CMS_DESCRIPTION}" />
	<link rel="icon" href="{$app_root}img/favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="{$app_root}img/favicon.ico" type="image/x-icon" />
	<link rel="manifest" href="{$app->buildUri('default/public/manifest/manifest.json')}">
	<link rel="stylesheet" type="text/css" href="{$app_root}css/main.css" />
    <!--[if lte IE 1]><link rel="stylesheet" type="text/css" href="{$app_root}css/main_ie.css" /><![endif]-->
	
	<link type="text/css" href="{$app_root}css/jquery_ui/jquery-ui-1.8rc3.custom.css" rel="stylesheet" />
	
	<script type="text/javascript" src="{$app_root}js/jquery.min-latest.js"></script>
	<script type="text/javascript" src="{$app_root}js/jquery-ui-1.8rc3.custom.min.js"></script>
	
	<script type="text/javascript" src="{$app_root}js/jquery.selectboxes.min.js"></script>	

	<script type="text/javascript" src="{$app_root}js/main.js"></script>
	<script type="text/javascript" src="{$app_root}js/set_sw_notifications.js"></script>
	
		
	
	<script type="text/javascript">
		$.extend(GW, { app_name: '{$app->app_name|strtolower}', app_root: '{$app_root}', app_base:'{$app_base}', base:'{$sys_base}', ln:'{$app->ln}', path:'{$app->path}', session_exp:{$session_exp}, server_time:'{"F d, Y H:i:s"|date}'});
		gw_adm_sys.init();
		
		translations = {};
		{foreach $translations as $key}
			translations['{$key}']='{GW::l("/A/$key")}';
		{/foreach}
	</script>
	
	<link rel="stylesheet" href="{$app->sys_base}vendor/font-awesome/css/font-awesome.min.css">	
	
</head>

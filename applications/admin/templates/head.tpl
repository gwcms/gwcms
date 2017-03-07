<!DOCTYPE html>

{include "common.tpl"}

<html lang="en">
{if $app->user}
	{assign var="session_exp" value=$app->user->remainingSessionTime() scope=global}
{/if}

{assign var="breadcrumbs" value=$app->getBreadcrumbs($breadcrumbs_attach) scope=parent}
{$translations[]='CLOSE'}	
	
<head>
    <meta charset="utf-8">
		
	<base href="{$sys_base}" />
	
	<link rel="shortcut icon" href="{$app->sys_base}tools/favico?{GW::s('PROJECT_FAVICO_ARGS')}" type="image/x-icon" />
	
    {*<meta name="viewport" content="width=device-width, initial-scale=1.0">*}
	<meta name="viewport" content="width=1400">
	
	<title>{include "title_breadcrumbs.tpl"}
		{*$title|default:$app->page->get(title,$ln)*} - {GW::s(SITE_TITLE)}</title>
	<meta name="description" content="{$lang.GW_CMS_DESCRIPTION}" />
	<link rel="manifest" href="{$app->buildUri('default/public/manifest/manifest.json')}">	
		

		

    <!--STYLESHEET-->
    <!--=================================================-->

    <!--Open Sans Font [ OPTIONAL ] -->
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700&amp;subset=latin" rel="stylesheet">


    <!--Bootstrap Stylesheet [ REQUIRED ]-->
    <link href="{$app_root}static/css/bootstrap.min.css" rel="stylesheet">


       
    <link href="{$app_root}static/css/theme-demo.css" rel="stylesheet">
	


    <!--Themify Icons [ OPTIONAL ]-->
    <link href="{$app_root}static/vendor/themify-icons/themify-icons.min.css" rel="stylesheet">
	<link rel="stylesheet" href="{$app_root}static/fonts/gwcms/style.css">
		


	{*

	<script type="text/javascript" src="{$app_root}js/jquery.selectboxes.min.js"></script>	
	
	<script type="text/javascript" src="{$app_root}js/set_sw_notifications.js"></script>	
	*}

        
    <!--JAVASCRIPT-->
    <!--=================================================-->

    <!--Page Load Progress Bar [ OPTIONAL ]-->
    <link href="{$app_root}static/css/pace.min.css" rel="stylesheet">
    <script src="{$app_root}static/js/pace.min.js"></script>
	
	<link href="{$app_root}static/css/theme.css" rel="stylesheet">
	<link href="{$app_root}static/css/project.css" rel="stylesheet">

    <!--jQuery [ REQUIRED ]-->{*
    <script src="{$app_root}static/js/jquery-2.2.4.min.js"></script>
	*}
	
    <!--BootstrapJS [ RECOMMENDED ]-->
   {* <script src="{$app_root}static/js/bootstrap.min.js"></script>*}
	
	{*load after bootstrap*}
	<link href="{$app_root}static/vendor/jqueryui/jquery-ui.min.css" rel="stylesheet">
	{*<script  src="{$app_root}static/vendor/jqueryui/jquery-ui.min.js" type="text/javascript"></script>		*}

    
    <!--Nifty Admin [ RECOMMENDED ]-->
	{*
    <script src="{$app_root}static/js/nifty.min.js"></script>
    <script src="{$app_root}static/js/gwcms.js"></script>
	*}
	
	
	<link rel="stylesheet" href="{$app->sys_base}vendor/font-awesome/css/font-awesome.min.css">	
	<script src="{$app->sys_base}vendor/jslibs/require.js"></script>
	

 	<script type="text/javascript">
		require.config({ 
			urlArgs: 'version={$GLOBALS.version_short}',			
			baseUrl: '{$app_root}static',
			shim : {
				bootstrap : { "deps" :['jquery'] },
				jqueryui: { "deps" : ['jquery'] },
				nifty: { "deps" : ['jquery','bootstrap'] },
				gwcms: { "deps" : ['jquery','bootstrap','nifty','jqueryui'] },
				sortable: { deps: ['gwcms'] }
			},			
			paths: {
                jquery: 'js/jquery-2.2.4.min',
				bootstrap :  "js/bootstrap.min",
				jqueryui: "vendor/jqueryui/jquery-ui.min",
				nifty: "js/nifty.min",
				gwcms: "js/gwcms",
				sortable: "js/jq/jquery-sortable"
			}
		});	
		
		require(['gwcms'], function(){
			$.extend(GW, { app_name: '{$app->app_name|strtolower}', app_root: '{$app_root}', app_base:'{$app_base}', base:'{$sys_base}', ln:'{$app->ln}', path:'{$app->path}', session_exp:{$session_exp|intval}, server_time:'{"F d, Y H:i:s"|date}'});
			gw_adm_sys.init();	
		});
		
		
		
		
		
		translations = {};
		{foreach $translations as $key}
			translations['{$key}']='{GW::l("/A/$key")}';
		{/foreach}		
	</script>
	
		
    
</head>
	
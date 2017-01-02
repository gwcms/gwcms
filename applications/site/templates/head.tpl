<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">

    
    
	<script src="{$app_root}assets/js/vendors.js"></script>

	<!-- Syntaxhighlighter -->
	<script src="{$app_root}assets/js/syntaxhighlighter/shCore.js"></script>
	<script src="{$app_root}assets/js/syntaxhighlighter/shBrushXml.js"></script>
	<script src="{$app_root}assets/js/syntaxhighlighter/shBrushJScript.js"></script>

	<script src="{$app_root}assets/js/app.js"></script>
	<script src="{$app_root}assets/js/index.js"></script> 
	<script src="{$app_root}assets/js/jquery-ui.min.js"></script>
	

    
  
 
 
 

    
    <title>{$app->page->title} - {GW::s('SITE/TITLE')}</title>
    <base href="{$app->app_base}" />

    <link rel="shortcut icon" href="{$app_root}assets/img/favicon4.png" />

    <meta name="description" content="">

    <!-- CSS -->
	{if !GW::$devel_debug}
		<link href="{$app_root}assets/css/preload.css" rel="stylesheet">
	{/if}    
    <!-- Compiled in vendors.js -->
    <!--
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/bootstrap-switch.min.css" rel="stylesheet">
    <link href="assets/css/font-awesome.min.css" rel="stylesheet">
    <link href="assets/css/animate.min.css" rel="stylesheet">
    <link href="assets/css/slidebars.min.css" rel="stylesheet">
    <link href="assets/css/lightbox.css" rel="stylesheet">
    <link href="assets/css/jquery.bxslider.css" rel="stylesheet" />
    <link href="assets/css/buttons.css" rel="stylesheet">
    -->

	<link rel="stylesheet" href="{$app->sys_base}vendor/font-awesome/css/font-awesome.min.css">	
	
    <link href="{$app_root}assets/css/vendors.css" rel="stylesheet">
    <link href="{$app_root}assets/css/syntaxhighlighter/shCore.css" rel="stylesheet" >

    <link href="{$app_root}assets/css/style-orange3.css" rel="stylesheet" title="default">
    <link href="{$app_root}assets/css/width-full.css" rel="stylesheet" title="default">

    

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
        <script src="assets/js/html5shiv.min.js"></script>
        <script src="assets/js/respond.min.js"></script>
    <![endif]-->
</head>
{include file="head.tpl"}
<body>

{if !$smarty.get.clean}

<div id="wrap">
    <div id="header">
        <div class="space1">
            <a href="#" title="">{$lang.SITE_TITLE}{*<img src="img/logo.png">*}</a>
        </div>
        
	{include file="submenu.tpl"}
	
        <div id="login-info">
        	<b>{$lang.LOGGED_AS}:</b> <a href="{$request->ln}/users/profile">{GW::$user->get('name')|default:GW::$user->get('username')}</a> 
        	{if $smarty.session.cms_auth.switchUser}
        	<a href="{$request->ln}/users?act=do:switch_user_return"  style="font-weight:normal;color:orange">
        		{$sw_usr_return=GW::$user->find(['id=?',$smarty.session.cms_auth.switchUser])}
        		({$lang.SWITCH_USER_RETURN|sprintf:$sw_usr_return->name})
        	</a>
        	{/if}
        	| 
            	<a href="{$request->ln}/users/login/logout" id="logout">{$lang.LOGOUT}</a>
		</div>
    </div>

    <div id="sidebar">
		{include file="menu.tpl"}
		
		{gw_display_plugins id="after_menu"}
		
    </div>

	{include file="breadcrumbs.tpl"}
	{if $toolbar}{include file="toolbar.tpl"}{/if}
    
    <div id="content">

{/if}

{include file="messages.tpl"}


{*<h2 class="top">Page title</h2>*}

{* SHOW OUTPUT FROM MODULE *}
{foreach $log as $item}
	{if $item}
		{dump($item)}
	{/if}
{/foreach}



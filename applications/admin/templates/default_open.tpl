{include file="head.tpl"}

<body>
    
{if !isset($smarty.get.clean) && !$no_standart_cms_frame && !$smarty.get.print_view}

<div id="wrap">
    <div id="header">
        <div class="space1">
            <a href="#" title="{GW::s('SITE_TITLE_DETAIL')}">{GW::s('SITE_TITLE')}{*<img src="{$app_root}img/logo.png">*}</a>
        </div>
        
	{include file="submenu.tpl"}
	
        <div id="login-info">
        	<i class="fa fa-user"></i>
        	<a href="{$app->app_base}{$app->ln}/users/profile">{$app->user->title|default:$app->user->get('username')} 
        	 
        	</a> 
        	{if $smarty.session.cms_auth.switchUser}
        	<a href="{$app->app_base}{$app->ln}/users/profile?act=do:switch_user_return"  style="font-weight:normal;color:orange">
        		{$sw_usr_return=$app->user->find(['id=?',$smarty.session.cms_auth.switchUser])}
        		({$lang.SWITCH_USER_RETURN|sprintf:$sw_usr_return->name})
        	</a>
        	{/if}
        	| 
            	<a href="{$app->app_base}{$app->ln}/users/login/logout" id="logout"><i class="fa fa-sign-out"></i> {$lang.LOGOUT}</a>
            	
			{$new_messages=$app->user->countNewMessages()}
			<div id="new_messages_block" {if !$new_messages}style="display:none"{/if}>
	            				
			<a href="#show_msg" onclick="open_iframe({ url:'{$ln}/users/messages/new', title:'{$lang.NEW_MESSAGES}' }); return false">
				{$lang.NEW_MESSAGES} (<font color="#ffff99" id="drop_new_messages_count">{$new_messages}</font>)
			</a>
			</div>
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

{if $smarty.get.print_view}
	<h3>
	{include file="breadcrumbs.tpl" nobreadcrumbscontainer=1}
	</h3>
{/if}


{include file="messages.tpl"}


{*<h2 class="top">Page title</h2>*}

{* SHOW OUTPUT FROM MODULE *}
{foreach $log as $item}
	{if $item}
		{d::ldump($item)}
	{/if}
{/foreach}



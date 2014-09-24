{include file="head.tpl"}


<body>
	
{if GW::$user}
<div style="  float: right; font-family: verdana; font-size: 13px; margin-left: 15px; margin-top: 3px;">
Jūs prisijungę kaip: <b>{GW::$user->name}</b>. <a class="button2" href="{$ln}/{GW::$static_conf.GW_SITE_PATH_LOGOUT}"><b>Atsijungti</b></a>
</div>
{/if}


{if GW::$user}
{include file="user_menu.tpl"}
{/if}

{include "messages.tpl"}
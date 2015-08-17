{include file="head.tpl"}


<body>
	

{if $app->user}
<div style="  float: right; font-family: verdana; font-size: 13px; margin-left: 15px; margin-top: 3px;">
Jūs prisijungę kaip: <b>{$app->user->name}</b>. <a class="button2" href="{$ln}/{GW::$settings.SITE.PATH_LOGOUT}"><b>Atsijungti</b></a>
<br />
Sąskaitos likutis: <b>{$app->user->funds|round:2}</b>
</div>
{/if}


{if $app->user}
	{include file="user_menu.tpl"}
{/if}

{include "messages.tpl"}
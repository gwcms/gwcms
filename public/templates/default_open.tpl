{include file="head.tpl"}


<body>
<div id="header">
<div id="header_nex">
<div id="header_middle">
<a href="http://www.stile.lt"><div id="logo"></div></a>


{if GW::$user}
<div style="  float: right; font-family: verdana; font-size: 13px; margin-left: 15px; margin-top: 3px;">
Jūs prisijungę kaip: <b>{GW::$user->name}</b>. <a class="button2" href="{$ln}/{GW::$static_conf.users_path}/logout"><b>Atsijungti</b></a>
</div>
{/if}








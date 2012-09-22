{include file="header.tpl"}
<body>
{include file="top_meny.tpl"}
{include file="categori_meny.tpl"}

<div class="warp">
	<div class="contentbg_top"></div>

	<div class="contentbg_mid">
	<div class="overskrift">{$m->lang.must_login_to_view}</div>
	{include file="messages.tpl"}
		<div id="shopsign">
		{include file="login_form.tpl"}
		</div>
	</div>
	<div class="contentbg_bot"></div>
</div>

{include file="footer.tpl"}
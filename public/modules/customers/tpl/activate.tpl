{include file="header.tpl"}
<body>
{include file="top_meny.tpl"}
{include file="categori_meny.tpl"}



<div class="warp">

<!--bilde hovedside-->
	<div class="contentbg_top"></div>

	<div class="contentbg_mid">

	<div class="content_registrering">
		{if $answer == 0}
			{$m->lang.account_is_already_active}
		{elseif $answer == 1}
			{$m->lang.account_activated}
		{else}
			{$m->lang.failed}
		{/if}
	</div>

	</div>
	
	<div class="contentbg_bot"></div>
	
</div>
{include file="footer.tpl"}
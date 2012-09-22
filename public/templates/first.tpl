{include file="header.tpl"}
<body>
{include file="top_meny.tpl"}
{include file="categori_meny.tpl"}

<div class="warp">

<!--bilde hovedside-->
	<div class="contentbg_top"></div>

	<div class="contentbg_mid">
		<div class="advertisementfrontpage">
			<div class="frontBildeHtml">{$page->getContent("frontbildehtml")}</div>
			<img height="300" width="960" title="{$page->getContent("startbildedescription")}" alt="{$page->getContent("startbildedescription")}" src="tools/img.php?id={$page->getContent("startbildeindex")}">
		</div>
	</div>
	<div class="contentbg_bot"></div>


<!--Slutt bilde hovedside-->

<!--start 4 produkt bilder-->
	<div style="margin-left:-5px;">
		<table width="1000" border="0" cellspacing="0">
  		<tr>
    		{$page->getContent("4bilderhtml")}
		</tr>
		</table>
	</div>
</div>

{include file="footer.tpl"}
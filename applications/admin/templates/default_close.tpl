{if !isset($smarty.get.clean) && !$smarty.get.clean && !$no_standart_cms_frame && !$smarty.get.print_view}
<br /><br />

        <span class="cleaner"></span>
    </div>
    <div id="push"></div>
</div>

<div id="footer">
    {include file="footer.tpl"}
</div>

{/if}


{foreach $footer_hidden as $block}
	{$block}
{/foreach}
{if $m->includes}
	{foreach $m->includes as $include}
		{if $include.0=='js'}
			<script type="text/javascript" src="{$include.1}"></script>
		{elseif $include.0=='css'}
			<link rel="stylesheet" type="text/css" href="{$include.1}" />
		{elseif $include.0=='jsstring'}			
			<script type="text/javascript">{$include.1}</script>
		{/if}
	{/foreach}
{/if}

</body>
</html>
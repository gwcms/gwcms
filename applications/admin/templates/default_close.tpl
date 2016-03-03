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

</body>
</html>
<div class="gw_toolbar">
	<table cellpadding=2px cellspacing=0>
	<tr>
	
	{foreach $toolbar.items as $item}
		<td>
		<a href="{$item.link}" {if $item.onclick}onclick="{$item.onclick}"{/if} class="gw_button">
			{if $item.img}
				<img src="{$item.img}" align="absmiddle" />
			{/if}
			
			{if $item.title}<span class="gw_button_label">{$item.title}</span>{/if}
		</a>
		</td>
	{/foreach}
	
	<td style="border-right:1px solid silver;border-left:1px solid silver;padding:1px">
	
	</td>
	
	</tr></table>
</div>
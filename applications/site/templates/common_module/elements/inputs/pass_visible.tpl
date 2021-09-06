

{if $item->id}
	<a href="#" onclick="$(this).next().toggle();$(this).toggle();return false">{GW::ln('/g/CHANGE_PASS')}</a>
	<div style="display:none">
{/if}

{include file="elements/inputs/text.tpl"}	

{if $item->id}
	</div>
{/if}
<table class="gwTable" style="width:auto">
	<tr>
	<th>{$lang.VIEWS_LABEL}:</th> 		
	<td>
	{foreach $views as $view}
		<a href="{$ln}/{GW::$request->path}?act=do:setView&name={$view.name}"
			{if $view.active} style="font-weight:bold"{/if}
			title="{$view.conditions|escape:'html'}"
		>{$view.name}{if $view.calculate} ({$view.count}){/if}</a>
	{/foreach}
	
	{$pgid=GW::$request->page->id}
	</td>
	<td>
	{if GW::$user->isRoot()}
		{gw_link path="`$ln`/config/modules/`$pgid`/form" icon="action_edit" title="Edit views" show_title=0 params=[return_to=>$page->path]}
	{/if}
	</td>
	</tr>
</table>
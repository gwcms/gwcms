


<table class="gwListViews gwTable" style="width:auto">
	<tr>
	<th><i class="fa fa-filter" aria-hidden="true" title="{$lang.VIEWS_LABEL}"></i></th> 		
	<td>
	{foreach $views as $view}
		<a href="{$app->buildUri(false,[act=>dosetView,name=>$view.name],[carry_params=>1])}"
			{if $view.active} style="font-weight:bold"{/if}
			title="{if $view.hint}{$view.hint|escape}{else}{$view.conditions|escape:'html'}{/if}"
		>{$view.name}{if $view.calculate} ({$view.count}){/if}</a>
	</td>
	<td>		
	{/foreach}
	
	{$pgid=$app->page->id}
	{if $app->user->isRoot()}	
		<a class="fontsz5" href="{$app->buildUri("system/modules/`$pgid`/form",[return_to=>$page->path])}" title="Edit views"><i class="fa fa-pencil-square-o"></i></a>
	{/if}
	</td>
	</tr>
</table>

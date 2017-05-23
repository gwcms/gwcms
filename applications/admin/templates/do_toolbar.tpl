

{if !$smarty.get.print_view}

	{include file="tools/toolbar_buttons.tpl"}

	{capture assign="tmp"}
		{call do_display_toolbar_buttons}


		{if $do_display_toolbar_pull_right}
			<div class="pull-right">
			{foreach $do_display_toolbar_pull_right as $include}
				{call gwinclude}
			{/foreach}		
			</div>
			{assign var=gw_toolbar_show value=1 scope=global}
		{/if}
	{/capture}

	{if $gw_toolbar_show}
	<div class="row gwtoolbarcont">{$tmp}</div>
	{/if}
	


	{*if $m->list_params.paging_enabled && count($list)}
	<td	align="right" width="1%">
	{include file="list/page_by.tpl"}
	</td>
	{/if*}

{/if}


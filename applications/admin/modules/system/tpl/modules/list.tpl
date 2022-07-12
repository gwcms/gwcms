{extends file="default_list.tpl"}


{block name="init"}

	{capture append="footer_hidden"}
	<style>
		.gwroot_node{ color: silver}
		.gwroot_node td:first-child{
			padding-left: 15px !important;
		}
		
		.gw_listicon { margin: 0px 0px 0px 10px;}
		.gw_listicon > i{ margin-right: .3em; width: 1.28571429em;text-align: center; }
		.gw_separator { background-color: #ffc; }		
		{if !$smarty.get.expand}.gwroot_node{ display:none; }{else}{$expanded="expanded"}{/if}
		.expanded{ color:orange !important; }
	</style>
	{/capture}
	
	{function name=do_toolbar_buttons_rearrange} 
		{toolbar_button title="Rearrange structure" iconclass='gwico-Sorting-Arrows-Filled' href=$m->buildUri(rearrange)}
	{/function}		
	
	{function name=do_toolbar_buttons_synchronizefromxml} 
		{toolbar_button title=GW::l('/A/VIEWS/doSyncFromXmls') iconclass='gwico-Refresh' href=$m->buildUri(false,[act=>doSyncFromXmls])}	
	{/function}		
	
	
	

	
	{function name=do_toolbar_buttons_expand} 
		{if $smarty.get.expand}
			{toolbar_button title=Shrink iconclass='gwico-CircledUp2' href=$m->buildUri(false,[expand=>0]+$smarty.get)}
		{else}
			{toolbar_button title=Expand iconclass='gwico-CircledDown2' href=$m->buildUri(false,[expand=>1]+$smarty.get)}
		{/if}
	{/function}		
	
	{function name=dl_prepare_item}
	
		{if $item->path=='separator'}
			{$item->set('row_class', 'gw_separator')}
		{elseif $item->parent_id}
			
			{$item->set('row_class', "gwroot_node childs{$item->parent_id}")}
			
		{/if}
	{/function}	
	
	
	

	
	{function dl_cell_title}
		{if $item->path!='separator'}
			<span class="gw_listicon">{if $item->getIcon()}{$item->getIcon()}{else}<i class="fa fa-caret-right" style="opacity: 0.3"></i>{/if}</span> {$item->title}
		{else}
			<center><i>{$item->title}</i></center>
		{/if}
		
	{/function}	
	{function dl_cell_path}
		{if $item->path!='separator'}
			{$item->path}
		{/if}
	{/function}	
	
		
	{$dl_fields=[title,path]}
	{$dl_smart_fields=[title,path]}
	{$do_toolbar_buttons=[info,rearrange,synchronizefromxml,expand]}
	{$do_toolbar_buttons[] = search}	
	{$dl_actions=[invert_active,ext_actions,expand]}
	

	
	{function name=dl_actions_expand}
		{if $item->__child_count}
			{list_item_action_m onclick="$(this).toggleClass('expanded');$('.childs{$item->id}').toggle(400);return false"  
				iconclass='fa fa-chevron-down' title="sub {$item->__child_count}" action_class="{$expanded}"}	
		{/if}
	{/function}
	
{/block}
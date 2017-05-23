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
	</style>
	{/capture}
	
	{function name=do_toolbar_buttons_rearange} 
		{toolbar_button title="Rearange structure" iconclass='gwico-Sorting-Arrows-Filled' href=$m->buildUri(rearange)}
	{/function}		
	
	
	
	{function name=dl_prepare_item}
	
		{if $item->path=='separator'}
			{$item->set('row_class', 'gw_separator')}
		{elseif $item->parent_id}
			{$item->set('row_class', 'gwroot_node')}
		{/if}
	{/function}	
	
	
	

	
	{function dl_cell_title}
		{if $item->path!='separator'}
			<span class="gw_listicon">{if $item->info.icon}{$item->info.icon}{else}<i class="fa fa-caret-right" style="opacity: 0.3"></i>{/if}</span> {$item->title}
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
	{$do_toolbar_buttons=[info,rearange]}	
	{$dl_actions=[edit,invert_active,delete]}
	
	
	
{/block}
{extends file="default_list.tpl"}

	{*
	{block name="before_list"}
			<table class="table-condensed table-hover table-vcenter table-bordered gwListTable">
				
			
				<tr><th data-field="{$field}">{GW::l('/m/GROUP_NAME')}</th><td>{$m->group->title}</td></tr>
			
				
			</table>
			<br />
			
		
	{/block}
	*}
	
{block name="init"}

	
	
	{$dl_inline_edit=1}

	{function name=do_toolbar_buttons_addmulti} 
		{toolbar_button title="Pridėti seriją" href=$m->buildUri(addmulti)}
	{/function}	

	{function name=do_toolbar_buttons_types} 
		{toolbar_button title="Tipai" href=$m->buildUri(types,[clean=>2,type=>'slot'],[level=>1]) btnclass="iframeopen" iconclass="fa fa-chevron-circle-down" tag_params=['data-dialog-width'=>"1200px"]}
	{/function}	
	
	
	
	{$do_toolbar_buttons[] = addmulti}
	{$do_toolbar_buttons[] = types}	
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons_hidden=[dialogconf,print]}	

	
	
	
	
	{function dl_actions_addafter}
		{if $item->can_add_after}
			

			{$tmp=[id=>0,clean=>2,smallform=>1,item=>[start_time=>$item->end_time, date=>$item->date, group_id=>$m->group_id], 'RETURN_TO'=>$m->buildUri(false,[iframeclose=>1])]}
			
			{if $item->next_item && $item->next_item->start_time > $item->end_time}
				{$tmp.item.end_time=$item->next_item->start_time}
			{/if}
			
			{list_item_action_m 
				iconclass="fa fa-plus-circle text-success" url=["form", $tmp] 
				action_addclass="iframe-under-tr" 
				title="Įterpti nuo {$item->end_time}" 
				tag_params=["data-iframe-after-close"=>"location.reload()"]}		
		{/if}
	{/function}
	
	
	{capture append=footer_hidden}
	<style>
		.canAddAfter{ border-bottom: 3px solid #997643 }
		
		{foreach $options['type_id'] as $id => $type}
			.rowCustomType_{$id}{ background-color: {$type->color} !important }
		{/foreach}
	</style>
	{/capture}

	{function name="dl_prepare_item"}
		
		{$rowclass=""}
		
		{if $item->can_add_after}
			{$rowclass="canAddAfter"}
		{/if}
		
		{if $item->type_id}
			{$rowclass="`$rowclass` rowCustomType_`$item->type_id`"}
		{/if}
			
		{if $rowclass}
			{$item->set('row_class',$rowclass)}	
		{/if}
	{/function}


	{$dl_smart_fields=[type_id]}	

	
	{function name=dl_cell_type_id}
		{if $item->type_id}
			{$options['type_id'][$item->type_id]->title}
		{/if}
	{/function}		
	
	
	{$dl_actions=[edit,delete,ext_actions,addafter]}
	

	
{/block}


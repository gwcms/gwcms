{extends file="default_list.tpl"}


{block name="init"}

	{function name=dl_cell_actions}
		
		{if $item->parent_id}
			{$dl_actions=[edit,invert_active]}
			{call dl_display_actions}
			
			<style>#list_row_{$item->id}{ color:silver; }</style>
		{else}
			{$dl_actions=[edit,invert_active,move,delete]}
			{call dl_display_actions}
		{/if}	
		
	{/function}	
		
	{$dl_fields=[path,title,actions]}
	{$dl_smart_fields=[actions]}
	{$dl_toolbar_buttons=[info]}	
	{$dl_actions=[]}
	
{/block}
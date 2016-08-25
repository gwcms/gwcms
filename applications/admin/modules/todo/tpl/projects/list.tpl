{extends file="default_list.tpl"}

{block name="init"}
		
	
	{$dl_inline_edit=1}
	
	{$do_toolbar_buttons = [addinlist]}
	{$dl_actions=[invert_active,edit,delete]}
	
	{$dl_smart_fields=[title]}
	
	{function dl_cell_title}
		<span style="background-color:{$item->color};padding: 0 5px 0 5px;color:{$item->fcolor};border-radius: 3px;">{$item->title}</span>
	{/function}
{/block}

{extends file="default_list.tpl"}

{block name="init"}
	{$dl_fields=[title,insert_time,update_time]}
	{$dl_actions=[invert_active,edit,delete]}
	
	{$dl_smart_fields=[title]}
	
	{function dl_cell_title}
		<span style="background-color:{$item->color};padding: 0 5px 0 5px;color:{$item->fcolor};border-radius: 3px;">{$item->title}</span>
	{/function}
{/block}

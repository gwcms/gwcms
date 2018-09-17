{extends file="default_list.tpl"}

{block name="init"}
	{$dl_fields=[title,insert_time]}
	{$dl_output_filters=[insert_time=>short_time, update_time=>short_time]}	
	{$dl_actions=[invert_active,edit,delete]}
	{$dl_filters=[]}
	
{/block}

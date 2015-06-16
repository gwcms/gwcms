{extends file="default_list.tpl"}

{block name="init"}
	{$dl_fields=[title,subscribers_count,insert_time,update_time]}
	{$dl_actions=[invert_active,edit,delete]}
{/block}

{extends file="default_list.tpl"}

{block name="init"}
	{$dl_fields=[title,insert_time,update_time]}
	{$dl_actions=[invert_active,edit,delete]}
{/block}

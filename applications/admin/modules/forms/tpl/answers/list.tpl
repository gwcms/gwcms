{extends file="default_list.tpl"}



{block name="init"}



	
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons_hidden=[exportdata,importdata,dialogconf]}	
	{$do_toolbar_buttons[] = search}
	
	{$dl_actions=[]}
	{$dl_filters=[]}
	{$dl_smart_fields=[value]}
	
	
	{$dl_actions=[invert_active_ajax,edit,delete]}
	{$dl_output_filters=[
		signature=>expand_truncate
	]}	
	

	{function dl_cell_value}
		{$item->value} {$item->fieldtype}
		{if $item->fieldtype==file}
			{$files = $item->extensions['attachments']->findAll()}
			{foreach $files as $itemfile}
				{$file=$itemfile->attachment}
				<a href="{$app->sys_base}tools/download/{$file->key}?view=1">{$file->original_filename}</a>
			{/foreach}
		{/if}
	{/function}


{/block}


{extends file="default_list.tpl"}


{block name="init"}


	{$display_fields=[
		user_id=>1, 
		name=>1, 
		email=>1, 
		message=>1, 
		ip=>1, 
		insert_time=>1
	]}

	{$dl_fields=$m->getDisplayFields($display_fields)}

	{$dl_toolbar_buttons[] = hidden}
	{$dl_toolbar_buttons_hidden=[exportdata,importdata,dialogconf,print]}		

	{$dl_actions=[edit,delete]}

	{$dl_filters=$display_fields}
	{$dl_smart_fields=[insert_time]}
	
	{function dl_cell_insert_time}
		{$x=explode(' ',$item->insert_time)}
		<span title="{$x.1}">{$x.0}</span>
	{/function}	




{*
	{function dl_actions_imagesactions}

		{gw_link do="rotate" params=[id=>$item->id] tag_params=[title=>GW::l('/m/ROTATE_CLOCKWISE')] title="<i class='fa fa-rotate-right'></i>"}		
	{/function}			
*}

{/block}
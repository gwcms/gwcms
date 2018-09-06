{extends file="default_list.tpl"}


{block name="init"}


	{$display_fields=[image=>1,owner=>1,filename=>1,width=>1,height=>1,size=>1,original_filename=>1,v=>1,insert_time=>1]}

	{$dl_fields=$m->getDisplayFields($display_fields)}

	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons_hidden=[exportdata,importdata,dialogconf,print]}		

	{$dl_actions=[imagesactions,edit,delete]}

	{$dl_filters=[owner=>1,original_filename=>1,insert_time=>1,v=>1,height=>1,width=>1,size=>1]}
	{$dl_smart_fields=[image,insert_time,original_filename,size]}
	
	{function dl_cell_insert_time}
		{$x=explode(' ',$item->insert_time)}
		<span title="{$x.1}">{$x.0}</span>
	{/function}	
	{function dl_cell_original_filename}
		<span title="{$item->original_filename}">{$item->original_filename|truncate:30}</span>
	{/function}		
	{function dl_cell_size}

		<span title="{$item->size}">{GW_Math_Helper::cFileSize($item->size)}</span>
	{/function}		


	{$dl_order_enabled_fields=array_keys($display_fields)}

	{function name=dl_cell_image}


		<a href="{$app->sys_base}tools/imga/{$item->id}v={$item->v}">
			<img src="{$app->sys_base}tools/imga/{$item->id}?size=32x32&v={$item->v}" align="absmiddle" vspace="2"  />
		</a>

	{/function}		





	{function dl_actions_imagesactions}
		{*
		{gw_link relative_path="`$item->id`/balancelog"  params=[id=>$item->id] title="BL"}
		*}
		{gw_link do="rotate" params=[id=>$item->id] tag_params=[title=>GW::l('/m/ROTATE_CLOCKWISE')] title="<i class='fa fa-rotate-right'></i>"}		
	{/function}	
        
        
        
	{$dl_checklist_enabled=1}
	{$dl_cl_actions=[invertactive,dialogremove]}

{/block}
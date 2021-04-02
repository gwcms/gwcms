{extends file="default_list.tpl"} 


{block name="init"} 

	
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons[] = search}
	
	{function name=do_toolbar_buttons_config} 

	
		
		
	{/function}	
	
	{$do_toolbar_buttons_hidden=[exportdata,importdata,dialogconf,print,config,rtlog]}		
		
	


{function name=dl_output_filters_optionsobj_title}
		{$tmp=$options[$field][$item->$field]}
		{$mod=$m->fieldInfo[$field].mod}
		<a class="iframeopen" href='{$app->buildUri("shop/`$mod`/`$tmp->id`/form",[clean=>2,dialog=>1])}'>
				{$tmp->title}
		</a>
{/function}	

{function name=dl_output_filters_dynfieldoptions}
	
		{$class=$dynfieldopts[$field]}
		{$obj=$options[$class][$item->$field]}
		<span title="id: {$obj->id}">{$obj->title}</span>
		
{/function}	


{function dl_cell_mod}
	{$url=$m->buildUri(false,[parent_id=>$item->id,clean=>2])}
	{*iconclass="fa fa-globe"*}
	{if $item->mod_count}
		{list_item_action_m href=$url action_addclass="iframe-under-tr" title="Modifications" caption="Mod({$item->mod_count})"}
	{/if}
{/function}

{function dl_cell_image}
	{$image=$item->image}
	{if $image}
		<img src="{$app->sys_base}tools/imga/{$image->id}?size=16x16" align="absmiddle" vspace="2" />
	{/if}
{/function}	
	
{function dl_cell_type}
	<span title="id: {$item->type}">{$item->typeObj->title}</span>
{/function}

{$dl_smart_fields=[mod,image,type]} 


{$dl_toolbar_buttons[] = hidden}
{$dl_toolbar_buttons_hidden=[import,export,dialogconf,fail_img]}

{$dl_actions=[edit,invert_active_ajax,ext_actions]} 

{$dl_output_filters=[
	insert_time=>short_time, 
	update_time=>short_time]}	
		
{foreach $dynfieldopts as $field => $class}
	{$dl_output_filters[$field]=dynfieldoptions}
{/foreach}

		
	{$dl_checklist_enabled=1}
	{$dl_cl_actions=[dialogremove,invertactive]}	

	
	
	{if $smarty.get.parent_id}
		{$dl_filters=[]}
		{$do_toolbar_buttons=[]}
	{/if}
{/block}





{*
{block name="after_list"}
	<br />
	<small style="color:silver">Last products update: {$m->config->last_update_info}</small>
{/block}
*}

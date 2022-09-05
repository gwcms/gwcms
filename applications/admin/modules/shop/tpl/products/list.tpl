{extends file="default_list.tpl"} 


{block name="init"} 
	
	

	{if $smarty.get.mods}
		{$dl_fields=[modif_title]+$dl_fields}
	{/if}
	
	
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons[] = search}
	
	{function name=do_toolbar_buttons_config} 

	
		
		
	{/function}	
	

	
	{function name=do_toolbar_buttons_modules} 
		{if $m->features.doublef}
			</li><li class="divider"></li><li>
			{toolbar_button title="Sukurti užsakymą pagal kainą" iconclass='gwico-Upload-SVG' href=$m->buildUri(false,[act=>doCreateOrderByPrice])}	
			{toolbar_button title="Importuoti užsakymus iš swedbank xml" iconclass='gwico-Upload-SVG' href=$m->buildUri(false,[act=>doOrdersImportSwedXml])}	
		{/if}
	{/function}		
	
	{$do_toolbar_buttons_hidden=[exportdata,importdata,dialogconf,print,config,rtlog,modules]}	

	
	{*
	{d::ldump($m->config)}
	*}	
	


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
	{$url=$m->buildUri(false,[parent_id=>$item->id,mods=>1,clean=>2])}
	{*iconclass="fa fa-globe"*}
	{if $item->mod_count}
		{list_item_action_m href=$url action_addclass="iframe-under-tr" title="Modifications" caption="Mod({$item->mod_count})"}
	{/if}
{/function}
{function dl_cell_orders}

	{*iconclass="fa fa-globe"*}
	
	{if $item->mod_count==0}
		{$extra=""}
		{if $m->features.ttlock}{$extra="{$extra},door_code"}{/if}
		{if $m->parent->contracts}{$extra="{$extra},contracts"}{/if}
		
		{$url=$app->buildUri("payments/orderitems",[
			processed=>0,
			obj_type=>$m->model->table,
			obj_id=>$item->id,orderflds=>1,
			flds=>"group_id,user_title,user_email,pay_time,payment_status,pay_test,qty,unit_price{$extra}",ord=>'payment_status DESC',clean=>2])}		
	{else}
		{$url=$app->buildUri("payments/orderitems",[
			processed=>0,
			context_obj_type=>$m->model->table,
			context_obj_id=>$item->id,orderflds=>1,
			flds=>"group_id,user_title,user_email,pay_time,payment_status,pay_test,qty,unit_price",groupby=>title,ord=>'obj_id,payment_status DESC',clean=>2])}		
	{/if}

	{if isset($counts.orders[$item->id])}
		{list_item_action_m href=$url action_addclass="iframe-under-tr" title="Modifications" caption="Orders({$counts.orders[$item->id]})"}
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

{$dl_smart_fields=[mod,orders,image,type]} 


{$dl_toolbar_buttons[] = hidden}
{$dl_toolbar_buttons_hidden=[import,export,dialogconf,fail_img]}

{$dl_actions=[edit,invert_active_ajax,ext_actions]} 

{$dl_output_filters=[
	insert_time=>short_time, 
	update_time=>short_time]}	
	
{$dl_output_filters.changetrack=changetrack}
		
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

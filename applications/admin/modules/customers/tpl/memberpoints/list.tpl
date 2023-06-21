{extends file="default_list.tpl"}

{block name="init"}

	{$dl_actions=[invert_active_ajax,edit,ext_actions]}	
	
	{*
	{function "dl_actions_dashb"}
		{list_item_action_m url=["`$item->id`/dashboard"] iconclass="fa fa-th-large"}
	{/function}
	*}

	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons_hidden=[dialogconf,print]}	
	{$dlgCfg2MWdth=300}
	{*$do_toolbar_buttons[] = dialogconf2*}	
	
	{$dl_inline_edit=1}	
	
	{function name=dl_prepare_item}
		{*
		{if !($item->isValid())}
			{$item->set('row_class', 'gw_notactive')}
		{/if}
		*}
	{/function}	
	
	{function dl_cell_logo}
		{$image=$item->logo}
		{if $image}
			<img src="{$app->sys_base}tools/imga/{$image->id}?size=16x16" align="absmiddle" vspace="2" />
		{/if}
	{/function}
	{function dl_cell_image}
		{$image=$item->image}
		{if $image}
			<img src="{$app->sys_base}tools/imga/{$image->id}?size=16x16" align="absmiddle" vspace="2" />
		{/if}
	{/function}	
	
 
	
	{$dl_smart_fields=[logo,image]}	
	
	{*
	{$dl_output_filters=[
		group_id=>options
	]}
	*}
	
	
{/block}


{*
{block name="after_list"}
	<br />
	<small style="color:silver" >Last sums update info: {$m->modconfig->last_update_sums}</small>
{/block}
*}
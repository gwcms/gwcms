{include file="default_form_open.tpl" form_width="100%"}

{$width_title=100px}


	{foreach GW::$settings.LANGS as $lncode}
		{call e field="title_$lncode"}
	{/foreach}	

{if $item->path==separator}


{else}
	{call e field=path}
	{call e field=pathname}

	{call e field=views type=code_json height=200px nopading=1}  
	{call e field=orders type=code_json height=200px nopading=1}  


	{$ck_options=[toolbarStartupExpanded=>false]}
	{call e field=notes type=htmlarea width="100%"}
	{call e field=active type="bool"}

{/if}


{$item->set('fields_str',str_replace('"','',json_encode($item->fields)))}

{$extra_fields=[in_menu,fields_str,id,insert_time,update_time,sync_time]}

{include file="default_form_close.tpl"}
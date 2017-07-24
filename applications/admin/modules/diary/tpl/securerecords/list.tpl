{extends file="default_list.tpl"}




{block name="init"}

{$dl_inline_edit=1}

	{$do_toolbar_buttons[]='encrypt'}
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons_hidden=[exportdata,importdata,dialogconf]}	
	{$dl_output_filters=[
		insert_time=>short_time,
		update_time=>short_time,
		username=>encrypteddisp,
		pass=>encrypteddisp,
		comments=>encrypteddisp
	]}
	
	
	{$dl_smart_fields=[encrypted]}	
	
	{function name=do_toolbar_buttons_encrypt}
		{*{toolbar_button title=Encrypt iconclass='fa fa-lock' href=$m->buildUri(false,[act=>doEncrypt,pw=>'']) query_param="Enter encryption key"}*}
		{toolbar_button title="Encrypt" iconclass='fa fa-lock' href=$m->buildUri(lock)}
		{toolbar_button title="Decrypt" iconclass='fa fa-unlock' href=$m->buildUri(unlock)}
		
		{*{list_item_action_m url=[false,[id=>$item->id,act=>doSwitchSim,simid=>'']] query_param="Enter sim id 0-`$tmp`" caption="Sw" title="Switch sim"}*}
	{/function}	
	
	{function dl_cell_encrypted}
			<i class="fa {if $item->encrypted}fa-lock text-success{else}fa-unlock text-danger{/if}"></i>
	{/function}


	
	{function name=dl_output_filters_encrypteddisp}
		{if $item->encrypted}
			{base64_encode($item->get($field))}
		{else}
			{$item->get($field)}
		{/if}
	{/function}		
	
	
{/block}


<i class="fa fa-" aria-hidden="true"></i>
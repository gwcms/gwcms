{extends file="default_list.tpl"}




{block name="init"}

{$dl_inline_edit=1}

	{$do_toolbar_buttons[]='encrypt'}
	{$dl_smart_fields=[encrypted,username]}	
	
	{function name=do_toolbar_buttons_encrypt}
		{*{toolbar_button title=Encrypt iconclass='fa fa-lock' href=$m->buildUri(false,[act=>doEncrypt,pw=>'']) query_param="Enter encryption key"}*}
		{toolbar_button title="Encrypt / Decrypt" iconclass='fa fa-lock' href=$m->buildUri(lockunlock)}
		
		{*{list_item_action_m url=[false,[id=>$item->id,act=>doSwitchSim,simid=>'']] query_param="Enter sim id 0-`$tmp`" caption="Sw" title="Switch sim"}*}
	{/function}	
	
	{function dl_cell_encrypted}
		<a href="{gw_path do=invertField params=[id=>$item->id,field=>encryped]}">
			<i class="fa {if $item->encrypted}fa-lock text-success{else}fa-unlock text-danger{/if}"></i>
		</a>
	{/function}

	{function dl_cell_username}
		{if $item->encrypted}
			{base64_encode($item->username)}
		{else}
			{$item->username}
		{/if}
	{/function}
	
	
{/block}


<i class="fa fa-" aria-hidden="true"></i>
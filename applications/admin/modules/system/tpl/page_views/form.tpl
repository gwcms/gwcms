{include file="default_form_open.tpl"}


{call e field=title}
{call e field=title_short}


{if $item->id}
	
	{call e field=path type=read}
	{call e field=type type=read value_options=GW::l("/m/OPTIONS/page_view_types")}
	
	
	{if ($app->user->isRoot() && $smarty.get.shift_key==1) || $smarty.get.update}
		
		{call e field=condition type=textarea autoresize=1 height=25}
		{call e field=order type=text autoresize=1 height=25}
		{call e field=fields type=textarea autoresize=1 height=25}
		{call e field=page_by type=number autoresize=1 height=25}
		{call e field=group_by type=text}
		{call e field=select type=text}
		
		
		{call e field=access type=select options=['1'=>"Read", "3"=>"Read + Write"]}
		
	{else}
		
		{if $options.path}
			{call e field=path type=select options=$options.path}
		{else}
			{call e field=path type=text}
		{/if}
		
		{if $item->condition}
			{call e field=condition type=read}
		{/if}

		{if $item->order}
			{call e field=order type=read}
		{/if}

		{if $item->fields}
			{$item->set(fields,json_encode(json_decode($item->fields), $smarty.const.JSON_PRETTY_PRINT))}
			{call e field=fields type=read}
		{/if}

		{call e field=page_by type=number}	
		
		{if $item->group_by}
			{call e field=group_by type=read}
		{/if}	
		{if $item->select}
			{call e field=select type=read}
		{/if}		
	{/if}
	
	
	

{else}
	{call e field=path type=select options=$options.path}
	{call e field=type type=select options=GW::l('/m/OPTIONS/page_view_types')}
	
	{if $smarty.get.saveasorder==1}
		<input name="item[type]" type="hidden" value="order">
		{call e field=order type=text}
	{else}	
		{*{call e field=type type=select options=GW::l('/m/OPTIONS/page_view_types')}*}



		
		{call e field=order_enabled type=bool value=$tmpval}
		{call e field=order type=text rowclass="orderinput"}
		
		
		{if $item->id && $item->group_by}{$item->set(groupby_enabled,1)}{/if}
		{call e field=groupby_enabled type=bool}
		{call e field=group_by type=text rowclass="groupbyinput"}

		{if $item->id && $item->select}{$item->set(select_enabled,1)}{/if}
		{call e field=select_enabled type=bool}
		{call e field=select type=text rowclass="selectinput"}
	

		{call e field=condition_enabled type=bool}
		{call e field=condition type=textarea height=50 rowclass="conditioninput"}

		{call e field=fields_enabled type=bool}
		{call e field=fields type=textarea height=50 rowclass="fieldsinput"}

		{call e field=pageby_enabled type=bool}
		{call e field=page_by type=number rowclass="pagebyinput"}
	
	{/if}
{/if}

{call e field=priority type=number}
{call e field=default type=bool}
{call e field=regular type=bool}
{call e field=dropdown type=bool}
{call e field=active type=bool default=1}


{function "setupEnabler"}
			$('#item__{$trigger}__').change(function(){
				if($(this).val()==1) {
					$('{$target}').fadeIn();
				}else{
					$('{$target}').hide();
				}
			}).change();	
{/function}

<script>
	require(['gwcms'], function(){
		{call "setupEnabler" trigger=order_enabled target=".orderinput"}
		{call "setupEnabler" trigger=condition_enabled target=".conditioninput"}
		{call "setupEnabler" trigger=fields_enabled target=".fieldsinput"}
		{call "setupEnabler" trigger=pageby_enabled target=".pagebyinput"}
		{call "setupEnabler" trigger=groupby_enabled target=".groupbyinput"}
	});
</script>


<style>
	.input_td{ max-width: 1000px;  word-wrap: break-word; }
</style>


{include file="default_form_close.tpl" extra_fields=[id,insert_time,update_time]}
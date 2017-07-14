{include file="default_form_open.tpl"}


{include file="elements/input.tpl" name=title}
{include file="elements/input.tpl" name=title_short}


{if $item->id}
	
	{include file="elements/input.tpl" name=path type=read}
	{include file="elements/input.tpl" name=type type=read value_options=$m->lang.OPTIONS.page_view_types}
	
	
	{if $app->user->isRoot() && $smarty.get.shift_key==1}
		
		{include file="elements/input.tpl" name=condition type=textarea autoresize=1 height=25}
		{include file="elements/input.tpl" name=order type=text autoresize=1 height=25}
		{include file="elements/input.tpl" name=fields type=textarea autoresize=1 height=25}
		{include file="elements/input.tpl" name=page_by type=number autoresize=1 height=25}
	{else}
		
		{if $item->condition}
			{include file="elements/input.tpl" name=condition type=read}
		{/if}

		{if $item->order}
			{include file="elements/input.tpl" name=order type=read}
		{/if}

		{if $item->fields}
			{$item->set(fields,json_encode(json_decode($item->fields), $smarty.const.JSON_PRETTY_PRINT))}
			{include file="elements/input.tpl" name=fields type=read}
		{/if}

		{if $item->page_by}
			{include file="elements/input.tpl" name=page_by type=read}
		{/if}		
		
		
	{/if}
	
	

{else}
	{include file="elements/input.tpl" name=path type=select options=$item->path_options options_fix=1}
	
	{if $smarty.get.saveasorder==1}
		<input name="item[type]" type="hidden" value="order">
		{include file="elements/input.tpl" type=text name=order}
	{else}	

		{*{include file="elements/input.tpl" name=type type=select options=$m->lang.OPTIONS.page_view_types}*}



		{include file="elements/input.tpl" type=bool name=order_enabled}
		{include file="elements/input.tpl" type=text name=order rowclass="orderinput"}

		{include file="elements/input.tpl" type=bool name=condition_enabled}
		{include file="elements/input.tpl" type=textarea name=condition height=50 rowclass="conditioninput"}


		{include file="elements/input.tpl" type=bool name=fields_enabled}
		{include file="elements/input.tpl" type=textarea name=fields height=50 rowclass="fieldsinput"}


		{include file="elements/input.tpl" type=bool name=pageby_enabled}
		{include file="elements/input.tpl" type=number name=page_by rowclass="pagebyinput"}
	
	{/if}

{/if}

{include file="elements/input.tpl" type=number name=priority}
{include file="elements/input.tpl" type=bool name=default}
{include file="elements/input.tpl" type=bool name=dropdown}
{include file="elements/input.tpl" type=bool name=active default=1}





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
	});
</script>


<style>
	.input_td{ max-width: 1000px;  word-wrap: break-word; }
</style>


{include file="default_form_close.tpl" extra_fields=[id,insert_time,update_time]}
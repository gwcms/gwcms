{if !$submit_buttons && $submit_buttons!==false}
	{$submit_buttons=[save,apply,cancel]}
{/if}


{function name=df_submit_button_save}
	{if $m->canBeAccessed($item, [access=>$smarty.const.GW_PERM_WRITE,nodie=>1])}
		{if !$smarty.get.nosavebtn}
			<button class="btn btn-primary"><i class="{$save_button_icon|default:'fa fa-save'}"></i> {$save_button_caption|default:GW::l('/g/SAVE')}</button>
		{/if}
	{else}
		<button class="btn btn-primary" onclick="{if $smarty.get.dialog}window.parent.gwcms.close_dialog_all_types();{else}{if isset($smarty.get.RETURN_TO)}location.href='{$smarty.get.RETURN_TO}';{else}history.go(-1);{/if}{/if}return false"> {GW::l('/g/BACK')}</button>
	{/if}
{/function}

{function name=df_submit_button_submit}
	{call df_submit_button_save save_button_caption=GW::l('/g/SUBMIT')}
{/function}



{function name=df_submit_button_apply}
	{if $m->canBeAccessed($item, [access=>$smarty.const.GW_PERM_WRITE,nodie=>1])}
		<button class="btn btn-info" onclick="this.form.elements['submit_type'].value=1;"><i class="fa fa-save"></i> {GW::l('/g/APPLY')}</button>
	{/if}
{/function}

{function name=df_submit_button_cancel}
	{*location.href='{gw_link levelup=1 path_only=1}'*}

	<button class="btn btn-default pull-right" onclick="{if $smarty.get.dialog}window.parent.gwcms.close_dialog_all_types();{elseif $smarty.get['iframe-under-tr']}parent.window.closeIframeUnderTr1(window);{else}{if isset($smarty.get.RETURN_TO)}location.href='{$smarty.get.RETURN_TO}';{else}history.go(-1);{/if}{/if}return false"><i class="fa fa-times" aria-hidden="true"></i> {GW::l('/g/CANCEL')}</button>
{/function}

<div class="form_action_buttons">
	<input type="hidden" name="submit_type" value="0" />
	
	{if is_array($submit_buttons)}
		{foreach $submit_buttons as $submit_button_f}
			{call name="df_submit_button_`$submit_button_f`"}
		{/foreach}
	{/if}
</div>
	
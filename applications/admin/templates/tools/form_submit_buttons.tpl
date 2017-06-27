{if !$submit_buttons}
	{$submit_buttons=[save,apply,cancel]}
{/if}


{function name=df_submit_button_save}
	<button class="btn btn-primary"><i class="fa fa-save"></i> {$lang.SAVE}</button>
{/function}


{function name=df_submit_button_apply}
	<button class="btn btn-info" onclick="this.form.elements['submit_type'].value=1;"><i class="fa fa-save"></i> {$lang.APPLY}</button>
{/function}

{function name=df_submit_button_cancel}
	{*location.href='{gw_link levelup=1 path_only=1}'*}

	<button class="btn btn-default pull-right" onclick="{if $smarty.get.dialog}window.parent.gwcms.close_dialog_all_types();{else}{if isset($smarty.get.RETURN_TO)}location.href='{$smarty.get.RETURN_TO}';{else}history.go(-1);{/if}{/if}return false"><i class="fa fa-times" aria-hidden="true"></i> {$lang.CANCEL}</button>
{/function}

<div class="form_action_buttons">
	<input type="hidden" name="submit_type" value="0" />
	
	{if is_array($submit_buttons)}
		{foreach $submit_buttons as $submit_button_f}
			{call name="df_submit_button_`$submit_button_f`"}
		{/foreach}
	{/if}
</div>



{if !$submit_buttons}
	{$submit_buttons=[save,apply,cancel]}
{/if}


{function name=df_submit_button_save}
	<input onclick="" type="submit" value="{$lang.SAVE}" /> 
{/function}


{function name=df_submit_button_apply}
	<input onclick="this.form.elements['submit_type'].value=1;" type="submit" value="{$lang.APPLY}"/> 
{/function}

{function name=df_submit_button_cancel}
	{*location.href='{gw_link levelup=1 path_only=1}'*}
		<input onclick="history.go(-1);return false" type="submit" value="{$lang.CANCEL}" />
{/function}

<div class="form_action_buttons">
	<input type="hidden" name="submit_type" value="0" />
	

	{foreach $submit_buttons as $submit_button_f}
		{call name="df_submit_button_`$submit_button_f`"}
	{/foreach}
</div>



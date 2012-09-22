{if !$submit_buttons}
	{$submit_buttons=[save,apply,cancel]}
{/if}

<div class="form_action_buttons">
	<input type="hidden" name="submit_type" value="0" />
	
	{if in_array('save', $submit_buttons)}
		<input onclick="remove_form_data_saver()" type="submit" value="{$lang.SAVE}" /> 
	{/if}
	
	{if in_array('apply', $submit_buttons)}
		<input onclick="this.form.elements['submit_type'].value=1;remove_form_data_saver()" type="submit" value="{$lang.APPLY}"/> 
	{/if}
	
	{if in_array('cancel', $submit_buttons)}
		{*location.href='{gw_link levelup=1 path_only=1}'*}
		<input onclick="history.go(-1);return false" type="submit" value="{$lang.CANCEL}" />
	{/if}
</div>
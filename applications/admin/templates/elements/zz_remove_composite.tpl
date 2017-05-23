
{if !$readonly}
	&nbsp;
	{$input_name_del=$input_name_pattern|sprintf:"delete_composite"}

	{include file="elements/input0.tpl" 
			type=bool 
			name="`$input_name_del`[`$name`]" 
			input_name_pattern='%s' 
			value='' 
			onchange_function=disable_file_input 
			onchange_function_arg=$inp_file_id} {GW::l('/g/REMOVE')}

	<script  type="text/javascript">
		function disable_file_input(id, state)
		{
			if (state)
			{
				$('#' + id).attr('rel', $('#' + id).attr('name'));
				$('#' + id).fadeOut()
			} else {
				$('#' + id).attr('name', $('#' + id).attr('rel'));
				$('#' + id).fadeIn()
			}
		}
	</script>
{/if}
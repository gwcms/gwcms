{$GLOBALS._input_file_n=$GLOBALS._input_file_n+1}
{$suffix=$GLOBALS._input_file_n}

{$file=$value}

{$inp_file_id="input_file_`$name`_`$suffix`"}

{if $file}
	<div style="margin-top: 6px;margin-bottom:6px">
		
	{$filename=pathinfo($file->original_filename)}
	{$title=$filename.filename|truncate:40}
	{if $filename.extension}
		{$title="`$title`.`$filename.extension`"}
	{/if}
	
	
	
	{gw_link fullpath="`$app->sys_base`tools/download/`$file->key`" icon="file" title=$title} ({$file->size_human})
	
	{if !$readonly}
		&nbsp;
		{$input_name_del=$input_name_pattern|sprintf:"delete_composite"}

		{include file="elements/input0.tpl" type=bool name="`$input_name_del`[`$name`]" input_name_pattern='%s' value='' onchange_function=disable_file_input onchange_function_arg=$inp_file_id} {GW::l('/g/REMOVE')}

		<script  type="text/javascript">
			function disable_file_input(id, state)
			{
				if(state)
				{
					$('#'+id).attr('rel', $('#'+id).attr('name'));
					$('#'+id).fadeOut()
				}else{
					$('#'+id).attr('name', $('#'+id).attr('rel'));
					$('#'+id).fadeIn()				
				}
			}
		</script>
	{/if}
	
	</div>	
	
{/if}


{if !$readonly}
	<input id="{$inp_file_id}" type="file" name="{$name}" />
{/if}

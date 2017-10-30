{if $value=="0000-00-00"}
	{$value=""}
{/if}
	<div class="gwcms-dp-component" style="width:150px">
			<div class="input-group date">
				<input type="text" class="form-control" value="{$value|escape}" name="{$input_name}" 
					{if $hidden_note}title="{$hidden_note}"{/if}
					{if $readonly}readonly{/if}
					{if $maxlength}maxlength="{$maxlength}"{/if} 
					{if $placeholder}placeholder="{$placeholder}"{/if} 
					{$input_extra_params}						
					{if $required}required="required"{/if} 
					   >
				<span class="input-group-addon"><i class="gwico-Calendar"></i></span>
			</div>
			
		</div>


					
<script>require(['vendor/bootstrap-datepicker/js'], function(){
		$('.gwcms-dp-component .date').datepicker({ autoclose:true, format: 'yyyy-mm-dd', language:'{$ln}', todayHighlight: true });
	});
	   
</script>					
{*{$m->addIncludes("bs/datepicker", 'js', "`$app_root`static/vendor/bootstrap-datepicker/js.js")}*}

{$m->addIncludes("bs/datepickercss", 'css', "`$app_root`static/vendor/bootstrap-datepicker/css.css")}


{GW::$globals._input_datetime_n=GW::$globals._input_date_n+1}
{$suffix=GW::$globals._input_date_n}

{if $value=="0000-00-00"}
	{$value=""}
{/if}
	<div id="inp-date-{$suffix}" class="gwcms-dp-component inp-date" style="width:150px">
			<div class="input-group date">
				<input type="text" class="form-control {if $class} {$class}{/if}" value="{$value|escape}" name="{$input_name}" 
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


					
<script>
	require(['vendor/bootstrap-datepicker/js'], function(){
		$('#inp-date-{$suffix} .date').datepicker({ 
			autoclose:true, 
			format: 'yyyy-mm-dd', 
			language:'{$ln}', 
			todayHighlight: true,
			//firstDay: 1, // Start with Monday
			weekStart: 1
		});
	});
	   
</script>		
{*
{$m->addIncludes("bs/datepicker", 'js', "`$app_root`static/vendor/bootstrap-datepicker/js.js")}
*}
{$m->addIncludes("bs/datepickercss", 'css', "`$app_root`static/vendor/bootstrap-datepicker/css.css")}


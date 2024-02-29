{$GLOBALS._input_datetime_n=$GLOBALS._input_datetime_n+1}
{$suffix=$GLOBALS._input_datetime_n}


{* su date pickeriu nelabai draugauja *}

{if $value=="0000-00-00"}
	{$value=""}
{else} 
	{$value=explode(' ',$value)}
	{$value.1 = explode(':',$value.1)}
	{$value="{$value.0} {$value.1.0}:{$value.1.1}"}
{/if}


	<div id="inp-datetime_{$suffix}" style="width:220px">
	
				<input type="text" class="form-control {if $class} {$class}{/if} datetime" value="{$value|escape}" name="{$input_name}" 
					{if $hidden_note}title="{$hidden_note}"{/if}
					{if $readonly}readonly{/if}
					{if $maxlength}maxlength="{$maxlength}"{/if} 
					{if $placeholder}placeholder="{$placeholder}"{/if} 
					{$input_extra_params}						
					{if $required}required="required"{/if} 
					autocomplete="off"
					   >

			
		</div>


					
					   
					   
					   
<script>
	require(['gwcms'], function(){
		require(['vendor/datetime/jquery-ui-timepicker-addon'], function(){
			require(['vendor/datetime/i18n/jquery-ui-timepicker-{$ln}'], function(){ 
			
			
				$('#inp-datetime_{$suffix} .datetime').datetimepicker({     
				
				dateFormat: "yy-mm-dd",
				timeFormat:  "{if !$notime}HH:mm{else}{/if}",
				timeInput: false,
				language:'{$ln}'
				//hourGrid: 4,
				//minuteGrid: 10		
				});	
		});
			

			
			
		})
	});
</script>	

<style>
	.ui-datepicker-calendar .ui-state-highlight{ width:auto !important }
{*
	.ui-datepicker-div{ z-index:99 !important; padding: 5px !important; }

	.ui_tpicker_unit_hide { display:none } 
	.ui_tpicker_hour_slider { margin:5px !important;; }
	.ui_tpicker_minute_slider {  margin:5px !important; }
	.ui-timepicker-div{ padding-left:5px; padding-right:3px; }
	*}
</style>


{if $suffix==1}
	<script>
	require(['gwcms'], function(){
		require(['vendor/datetime/jquery-ui-timepicker-addon'], function(){
			
		}	
	);
	})
	</script>
	{$m->addIncludes("bs/datepickercss", 'css', "`$app_root`static/vendor/datetime/jquery-ui-timepicker-addon.min.css")}
{/if}
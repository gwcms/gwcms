{if $readonly}
	{include "inputs/input_text.tpl"}
{else}
	{if $value=='0000-00-00'}
		{$value=''}
	{/if}

	<div class='input-group date birthdate' id='{$id}Container'>

		<input id="{$id}" name="{$input_name}"  type='text' class="form-control" {if $required}required="1"{/if} value="{$value|escape}" />
		{*<span class="input-group-addon"><i class="fa fa-lock"></i></span>*}
		<span class="input-group-addon">
		    <span class="glyphicon glyphicon-calendar"></span>
		</span>
	    </div>	

	{if !$datepickerbs_loaded}{*pakrauti tik karta*}
		<link href="{$app_root}assets/pack/datepicker/bootstrap-datepicker3.standalone.min.css" rel="stylesheet">

		<script src="{$app_root}assets/pack/datepicker/bootstrap-datepicker.js"></script>	
		<script src="{$app_root}assets/pack/datepicker/lang/bootstrap-datepicker.{$app->ln}.min.js"></script> 
		{assign scope=global var=datepickerbs_loaded value=1}
	{/if}

	{*http://eternicode.github.io/bootstrap-datepicker/*}
	<script>

		$(function(){
			$('.input-group.date.birthdate').datepicker({
				startView: 2,
				format: "yyyy-mm-dd",
				language: '{$app->ln}',
				todayHighlight: true,
				autoclose: true,
				defaultViewDate: { year: 1990, month: 04, day: 25 }
			});				
		})


	</script>
{/if}
<input 
	name="{$input_name}" 
	type="{$type}" 
	class="form-control{if $data_inputmask} inputmask{/if}{if $required} required{/if} {if $addclass} {$addclass}{/if}" 
	id="{$id}" 
	value="{$value|escape}" 
	{if $required}required="1"{/if} 
	{if $placeholder}placeholder="{$placeholder|escape}"{/if}
	{if $data_inputmask}data-inputmask="{$data_inputmask|escape}"{/if}
	{if $data_inputmask_regex}data-inputmask-regex="{$data_inputmask_regex}"{/if}
	onchange="{if $type!='password'}this.value=$.trim(this.value);{/if}{if $onchange};{$onchange}{/if}" 
	{if isset($min_value)}min="{$min_value}"{/if}
	{if isset($max_value)}max="{$max_value}"{/if}
	{if $data_source}data-source="{$data_source}"{/if}
	{if $data_url}data-url="{$data_url}"{/if}
	{if $readonly}disabled='disabled'{/if}
	{foreach $tag_params as $attr => $value}{$attr}="{$value|escape}" {/foreach}
>

{if $onchange}<script> $(function(){ $('#{$id}').change(); }) </script>{/if}

{if $m->error_fields.$field}<span class="glyphicon glyphicon-remove form-control-feedback"></span>{/if}
			
{if $data_inputmask || $data_inputmask_regex}		
	{*https://github.com/RobinHerbots/jquery.inputmask*}

	{if !$input_inputmask_loaded}
		{*
		<link href="{$app->sys_base}vendor/bootstrap-ajax-typeahead/demo/assets/css/bootstrap.min.css" media="all" rel="stylesheet" type="text/css" />
		*}
		<script src="{$app->sys_base}vendor/robinherbots/jquery.inputmask/dist/min/jquery.inputmask.bundle.min.js" type="text/javascript"></script>



		{assign scope=global var=input_inputmask_loaded value=1}
	{/if}	

	<script>
		$(function(){
			if($('#{$id}').attr('data-inputmask-regex')){
				$('#{$id}').inputmask("Regex");
			}else{
				$('#{$id}').inputmask();
			}
		})
	</script>
{/if}
	
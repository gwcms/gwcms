{if $readonly}
	{foreach $value as $selected}
		{$options[$selected]}{if !$selected@last},{/if}
	{/foreach}
{else}
	
<select multiple="multiple" class="form-control gwselect2 " name="{$input_name}" 
		style="width: {$width|default:"100%"}; {if $height}height:{$height};{/if}"
		>
	{html_options options=$options selected=$value}
</select>

	
{if !$gwcms_input_select2_loaded}
	{$m->addIncludes("bs/select2css", 'css', "`$app_root`static/vendor/select2/css.css")}
	
	{capture append=footer_hidden}
	<script type="text/javascript">
		require(['vendor/select2/js'], function(){ 
			$('.gwselect2').select2(); 
		});
	</script>
	{/capture}
	
	{assign var=gwcms_input_select2_loaded value=1 scope=global}	
{/if}
	


{/if}
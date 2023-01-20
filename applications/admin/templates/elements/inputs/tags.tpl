{if $type=='text'}
	{*some fckin shit happens asked text type but sends here*}
	{include "elements/inputs/text.tpl"}
	{php}return false;{/php}
{/if}
	
{if !isset($placeholder)}{$placeholder="Add tag"}{/if}

{$tmpopt=['data-role'=>"tagsinput"]}
{if $readonly}
	{$tmpopt["disabled"]="true"}
{/if}
{include "elements/inputs/text.tpl" type="text" tag_params = $tmpopt}
					
<script>
	require(['gwcms'], function(){
		require(['vendor/bootstrap-tagsinput/js.min'], function(){

		});
	})  
</script>					
{*{$m->addIncludes("bs/taginputjs", 'js', "`$app_root`static/vendor/bootstrap-tagsinput/js.min.js")}*}
{$m->addIncludes("bs/taginputcss", 'css', "`$app_root`static/vendor/bootstrap-tagsinput/css.min.css")}


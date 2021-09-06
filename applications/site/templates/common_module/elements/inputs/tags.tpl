{$tag_params['data-role']="tagsinput"}

{if !isset($placeholder)}{$placeholder="Add tag"}{/if}

{include file="elements/inputs/text.tpl" type="text"}
					
<script>
	require(['gwcms'], function(){
		require(['vendor/bootstrap-tagsinput/js.min'], function(){

		});
	})  
</script>					
{*{$m->addIncludes("bs/taginputjs", 'js', "`$app_root`static/vendor/bootstrap-tagsinput/js.min.js")}*}
{$m->addIncludes("bs/taginputcss", 'css', "`$app_root`static/vendor/bootstrap-tagsinput/css.min.css")}


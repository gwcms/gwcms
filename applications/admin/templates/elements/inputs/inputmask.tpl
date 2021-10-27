{if $mask}{$tag_params["data-inputmask"]=$mask}{/if}

	<script>
		require(['gwcms'], function(){
			require(['pack/inputmask/dist/min/jquery.inputmask.bundle.min'], function(){
				
					if($('#{$id}').attr('data-inputmask-regex')){
						$('#{$id}').inputmask("Regex");
					}else{
						$('#{$id}').inputmask();
					}				
			
			})
		})
	</script>
{include file="{$smarty.current_dir}/text.tpl"}

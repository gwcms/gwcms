<textarea name="{$input_name}" type="{$type}" class="form-control" id="{$id}" 
	  {if $limitsymbol}maxlength='{$limitsymbol}'{/if}
       {if $required}required="1"{/if} 
       {if $placeholder}placeholder="{$placeholder|escape}"{/if}
       style="{if $width}width:'{$width}'{/if}"
       {if $rows}rows="{$rows}"{/if}
       {foreach $tag_params as $attr => $value}{$attr}="{$value|escape}" {/foreach}
       >{$value|escape}</textarea>

{if $limitsymbol}       
	<span id="{$id}_symcnt"></span>
{/if}
       


	


{if $limitsymbol}
	<script>
		$(function(){
			$('#{$id}').keyup(function(){
				$('#{$id}_symcnt').text(this.value.length)
			}).keyup();
		})
	</script>	
	
{/if}       
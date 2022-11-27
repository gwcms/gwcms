
{$defaultwidth=floor(100/$parts)}

{for $i=0;$i<$parts;$i++}
<input id="{$id}_{$i}" class="splitinput split{$id} form-control{if $class} {$class}{/if} inp-{$type|default:text}"
	
	
	
	onchange="reconstructsplit({$id})" 
	{if $readonly}readonly{/if}
	{if $maxlength}maxlength="{$maxlength}"{/if} 
	style="width: {$width|default:"{$defaultwidth}%"}; {if $height}height:{$height};{/if}" 

/>


{/for}


		<script type="text/javascript">

			splitchar='{$splitchar}';	

			require(['gwcms'], function(){ 
				
				var text = $('#{$id}').val()
				var parts = text.split(splitchar)
								
				for(var i=0;i<parts.length;i++)
					$('#{$id}_'+i).val(parts[i])
				
			});
			
			function reconstructsplit(id){
				var text=[];
				$('.split{$id}').each(function(){
					text.push(this.value)
				})
				$('#{$id}').val(text.join(splitchar))
			}
			
		</script>
<style>
	.splitinput{ display:inline-block; padding: 3px }
</style>
{include file="elements/inputs/hidden.tpl"}
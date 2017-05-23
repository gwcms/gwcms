{$GLOBALS._input_file_n=$GLOBALS._input_file_n+1}
{$suffix=$GLOBALS._input_file_n}

{$image=$value}
{$inp_file_id="input_file_`$name`_`$suffix`"}

{$preview_container_id="preview_image_`$name`"}

{$img_preview_height = $img_preview_height|default:200}
{$img_preview_width = $img_preview_width|default:200}


<div id="{$preview_container_id}">
	{if $image}
		<p class="gwcms-iinp-prev-exist">
			{include 
				file="tools/image_preview.tpl" 
				image=$image border=1 width=$img_preview_height height=$img_preview_width}

			{include "elements/zz_remove_composite.tpl"}
		</p>	
	{/if}

	<p class="gwcms-iinp-prev-new" style="display:none">
		<img style="max-width: {$img_preview_width}px; max-height: {$img_preview_height}px">
	</p>
</div>


{if !$readonly}
	<input id="{$inp_file_id}" class="imageinputwithpreview" type="file" name="{$name}" data-container="{$preview_container_id}" onchange="readURL(this)" />
{/if}

<script>
	function readURL(input) {

		var container = $(input).data('container');
		
		if (input.files && input.files[0]) {

			var reader = new FileReader();

			reader.onload = function (e) {
				
				//alert(e.target.result)
				
				$('#'+container+' .gwcms-iinp-prev-new img').attr('src', e.target.result);
				$('#'+container+' .gwcms-iinp-prev-new').fadeIn();
				$('#'+container+' .gwcms-iinp-prev-exist').fadeOut();
			}

			reader.readAsDataURL(input.files[0]);
		}
	}


</script>

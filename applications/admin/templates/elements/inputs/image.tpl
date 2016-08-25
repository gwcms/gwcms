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
	<input id="{$inp_file_id}" type="file" name="{$name}" />
{/if}

<script>
	function readURL(input) {

		if (input.files && input.files[0]) {

			var reader = new FileReader();

			reader.onload = function (e) {
				$('#{$preview_container_id} .gwcms-iinp-prev-new img').attr('src', e.target.result);
				$('.gwcms-iinp-prev-new').fadeIn();
				$('.gwcms-iinp-prev-exist').fadeOut();
			}

			reader.readAsDataURL(input.files[0]);
		}
	}

	$("#{$inp_file_id}").change(function(){
		readURL(this);
	});
</script>

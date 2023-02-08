{$GLOBALS._input_file_n=GW::$globals._input_file_n+1}
{$suffix=GW::$globals._input_file_n}

{$image=$value}
{$inp_file_id="input_file_`$name`_`$suffix`"}

{$preview_container_id="preview_image_`$name`"}

{$img_preview_height = $img_preview_height|default:200}
{$img_preview_width = $img_preview_width|default:200}


<div id="{$preview_container_id}">
	{if $image}
		<p class="gwcms-iinp-prev-exist">
			{include in_form=1
				file="tools/image_preview.tpl" 
				image=$image border=1 width=$img_preview_height height=$img_preview_width fancybox=1}

			
		</p>	
	{/if}

	<p class="gwcms-iinp-prev-new" style="display:none">
		<img style="max-width: {$img_preview_width}px; max-height: {$img_preview_height}px">
	</p>
</div>


{if !$readonly}
	<input id="{$inp_file_id}" class="inp-image imageinputwithpreview" type="file" name="{$name}" data-container="{$preview_container_id}" onchange="readURL(this)" />
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

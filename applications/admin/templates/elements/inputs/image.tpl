{$GLOBALS._input_file_n=$GLOBALS._input_file_n+1}
{$suffix=$GLOBALS._input_file_n}

{$image=$value}
{$inp_file_id="input_file_`$name`_`$suffix`"}

{if $image}
	{$preview_container_id="preview_image_`$name`"}
	<p id="{$preview_container_id}">
		{include file="tools/image_preview.tpl" image=$image border=1 width=200 height=200}

		{include "elements/zz_remove_composite.tpl"}
	</p>
		
		
{/if}

{if !$readonly}
	<input id="{$inp_file_id}" type="file" name="{$name}" />
{/if}

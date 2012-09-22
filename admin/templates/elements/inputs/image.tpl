{$GLOBALS._input_file_n=$GLOBALS._input_file_n+1}
{$suffix=$GLOBALS._input_file_n}

{$image=$value}

{if $image}
	Key: {$image->key}
	{$preview_container_id="preview_image_`$name`"}
	<p id="{$preview_container_id}">
		{include file="tools/image_preview.tpl" image=$image border=1 width=200 height=200}

		{if $allow_remove}
		{$remove_act=$remove_act|default:"do:remove_item_image"}

		<a href="#"
onclick="if (!confirm('{$request->lang.CONFIRM_DELETE}'))return;jserver.callmodule('{$m->name}','{$remove_act}', 'id={$item->get('id')}&name={$name}');Element.hide('{$preview_container_id}');return false;"
><img src="img/icons/image_delete.gif" border="0" width="18" height="18" /></a>
  			{/if}
  		</p>
{/if}

<input id="input_file_{$name}_{$suffix}" type="file" name="{$name}" />

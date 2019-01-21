{function "e"}

	{if $eopt}{$tmpO=$eopt}{else}{$tmpO=$options[$field]}{/if}
	{if $efile}{$tmpF=$efile}{else}{$tmpF="elements/input.tpl"}{/if}

	
	{include file=$tmpF options=$tmpO name=$field}
{/function}
{function name="cust_inputs"}
	{if $field==''}
		
	{elseif $field=="relpath"}
		
		{e type="read"}
	{elseif $field==filename}
		{e type="text"}
		
	{elseif $field=="ico"}
		{function "showico"}
			<img class="file" data-file="{$file}" src="{$app->sys_base}tools/img_resize?file={urlencode($item->relpath)}&dirid=repository&size={$size}" title="{$item->filename}" alt="{$item->filename}" />
		{/function}
		{if $smarty.get.form_ajax}
			<td>
				{if $item->type=='image'}
					{call "showico" size="50x50"}
				{/if}		
			</td>
		{else}
			<tr>
				<td colspan="2">
					{if $item->type=='image'}
						{call "showico" size="300x300"}
					{/if}		
				</td>
			</tr>			
		{/if}
	{else}
		{if $smarty.get.form_ajax}
		<td>{$item->$field}</td>
		{/if}
	{/if}

{/function}


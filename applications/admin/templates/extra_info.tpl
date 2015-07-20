{if !count($extra_fields)}
	{$extra_fields=[id,insert_time,update_time]}
{/if}

	
<table class="gwTable">
	<th colspan="2" class="th_h3 th_single">{$lang.EXTRA_INFO}</th>

{foreach from=$extra_fields item=field_id}
	<tr>
		<td width="1%" nowrap align="right"><i>{$app->fh()->fieldTitle($field_id)}</i></td>
		<td width="99%">
			{$x=$item->get($field_id)}
			{if is_array($x)}
				{d::jsonNice($x)}
			{else}
				{$x}
			{/if}
		</td>
	</tr>		
{/foreach}
</table>
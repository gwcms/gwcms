{if $extra_fields !== false}
	{if !$extra_fields}
		{$extra_fields=[id,insert_time,update_time]}
	{/if}



	<table class="gwTable mar-top mar-btm gwExtraInfo {if $corner!='left'}pull-right{/if}">
		<tr><th colspan="2" class="th_h3 th_single">{GW::l('/g/EXTRA_INFO')}</th></tr>

		{foreach from=$extra_fields item=field_id}
			<tr>
				<td nowrap align="right"><i>{$m->fieldTitle($field_id)}</i></td>
				<td>
					{$x=$item->get($field_id)}
					{if is_array($x) || is_object($x)}
						{d::jsonNice($x)}
					{else}
						{$x}
					{/if}
				</td>
			</tr>		
		{/foreach}
	</table>
{/if}
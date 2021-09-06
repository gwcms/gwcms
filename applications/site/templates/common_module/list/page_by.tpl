		{include file="list/paging.tpl" assign=pages}
					
<table cellspacing="0" cellpadding="0">
					<tr>		
		
		{if $paging_tpl_page_count>1}
			{$pages}
		{/if}
		
			<td style="padding-right:5px;" >
					{GW::ln('/g/PAGE_BY')}:
			</td>
			<td style="padding-right:15px;">	
				<input class="gwPageBy form-control setListParams" name="list_params[page_by]" size=2 value="{$m->list_params.page_by}" />
						
			</td>	
			
		{if $query_info}
					<td style="padding-right:5px;">{GW::ln('/g/ITEM_COUNT')}</td>
					<td style="padding-right:5px;"><b>{$query_info.item_count}</b></td>
				

		{/if}
</tr>
</table>

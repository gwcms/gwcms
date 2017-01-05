		{include file="list/paging.tpl" assign=pages}
					
<table cellspacing="0" cellpadding="0">
					<tr>		
		
		{if $paging_tpl_page_count>1}
			{$pages}
		{/if}
		
			<td style="padding-right:5px;" >
					{$lang.PAGE_BY}:
			</td>
			<td style="padding-right:15px;">		
						<form method="post" action="{$smarty.server.REQUEST_URI}" style="display:inline">
						<input type="hidden" name="act" value="do:setListParams" />
						<input class="gwPageBy form-control" onchange="this.form.submit()" name="list_params[page_by]" size=2 value="{$m->list_params.page_by}" />
						<input type="hidden" name="list_params[page]" value="0" />
						</form>	
			</td>	
			
		{if $query_info}
					<td style="padding-right:5px;">{$lang.ITEM_COUNT}</td>
					<td style="padding-right:5px;"><b>{$query_info.item_count}</b></td>
				

		{/if}
</tr>
</table>

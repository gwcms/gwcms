<table class="gwTable" cellspacing="0" cellpadding="0">
	<tr>
		{include file="list/paging.tpl" assign=pages}
						
		{if $paging_tpl_page_count>1}
			<td>{$pages}</td>
		{/if}
		<td>
			<table class="gw_clean_tbl" cellspacing="" cellpadding="1">
				<tr>
					<td nowrap class="fontsz5">{$lang.PAGE_BY}:</td>
					<td>
						<form method="get" action="{$smarty.server.REQUEST_URI}" style="display:inline">
						<input type="hidden" name="act" value="do:setListParams" />
						<input onchange="this.form.submit()" name="list_params[page_by]" size=5 value="{$m->list_params.page_by}" />
						<input type="hidden" name="list_params[page]" value="0" />
						</form>	
					</td>
				</tr>
			</table>
		</td>
		{if $query_info}
			<td nowrap>
				<table class="gw_clean_tbl" cellspacing="" cellpadding="1"><tr><td>
					{$lang.ITEM_COUNT}: <b>{$query_info.item_count}</b>
				</td></tr></table>
			</td>
		{/if}
	</tr>
</table>
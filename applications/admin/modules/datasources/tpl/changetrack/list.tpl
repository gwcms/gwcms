{extends file="default_list.tpl"}


{block name="init"}

		
	<style>
	.changetrack-table{
		margin:0;
		width:auto;
		max-width:100%;
		font-size:inherit;
		line-height:inherit;
		border:none;
		box-shadow:none;
	}
	.changetrack-table th,
	.changetrack-table td{
		border-top:none !important;
		border-left:none !important;
		border-right:none !important;
		font-size:inherit;
		line-height:inherit;
	}
	.changetrack-table tbody tr:first-child th,
	.changetrack-table tbody tr:first-child td{
		border-top:none !important;
	}
	.changetrack-table tbody tr:last-child th,
	.changetrack-table tbody tr:last-child td{
		border-bottom:none !important;
	}
	.changetrack-table tr > th:last-child,
	.changetrack-table tr > td:last-child{
		border-right:none !important;
	}
		.changetrack-table th{
			padding:2px 8px 2px 0;
			vertical-align:top;
			white-space:nowrap;
		}
		.changetrack-field-link{
			margin-left:6px;
			color:#666;
			font-size:12px;
		}
		.changetrack-table td{
			padding:2px 0;
			vertical-align:top;
			width:100%;
		}
		.changetrack-val-old,
		.changetrack-val-new{
			display:inline-block;
			padding:1px 6px;
			border-radius:3px;
			font-size:inherit;
			line-height:inherit;
			vertical-align:baseline;
		}
		.changetrack-val-old{
			background:#fbe3e4;
			color:#8a1f11;
		}
		.changetrack-val-new{
			background:#e6efc2;
			color:#264409;
		}
		.changetrack-arrow{
			display:inline-block;
			padding:0 6px;
			color:#777;
			font-size:inherit;
			line-height:inherit;
		}
		.changetrack-summary{
			line-height:1.45;
			font-size:inherit;
		}
		.changetrack-summary-added{
			color:#264409;
			background:#e6efc2;
			display:inline-block;
			padding:1px 4px;
			border-radius:3px;
		}
		.changetrack-summary-removed{
			color:#8a1f11;
			background:#fbe3e4;
			display:inline-block;
			padding:1px 4px;
			border-radius:3px;
		}
		.changetrack-inline-diff{
			line-height:1.45;
			white-space:normal;
			word-break:break-word;
			font-size:inherit;
		}
		.changetrack-inline-diff del{
			background:#fbe3e4;
			color:#8a1f11;
			text-decoration:none;
			padding:0 1px;
		}
		.changetrack-inline-diff ins{
			background:#e6efc2;
			color:#264409;
			text-decoration:none;
			padding:0 1px;
		}
	.changetrack-inline-diff span{
			color:#555;
		}
		.changetrack-raw-diff{
			white-space:normal;
			word-break:break-word;
			font-family:inherit;
			font-size:inherit;
			line-height:inherit;
			color:#666;
		}
		.list_row.changetrack-row-last > td{
			background:#e8f4ff !important;
		}
		.list_row.changetrack-row-undone > td{
			background:#ffe1d6 !important;
		}
	</style>
	
	{function dl_cell_username}
		{if $item->user_id}
			<a class="iframeopen" href="{$app->buildUri("users/usr/`$item->user_id`/form",[clean=>2])}" title="User info - {$item->usertitle|default:$item->username|escape}">
				{$item->username|escape}
			</a>
		{else}
			{$item->username|escape}
		{/if}
	{/function}

	{function dl_cell_transaction_id}
		{if $item->transaction_id|intval}
			<a class="iframeopen" href="{$app->buildUri("datasources/changetransactions",[transaction_id=>$item->transaction_id,clean=>2])}" title="Transaction #{$item->transaction_id|escape}">
				{$item->transaction_id|escape}
			</a>
		{/if}
	{/function}

	{function dl_cell_owner_type}
		{$owner_meta=$m->getOwnerTypeLabelMeta($item->owner_type)}
		<span title="{$owner_meta.title|escape}">{$owner_meta.short|escape}</span>
	{/function}

	{function dl_cell_changestable}
		{$rows=$m->buildChangeRows($item)}
		{if !$rows}
			<span class="text-muted">-</span>
		{else}
			<table class="table table-condensed changetrack-table">
				<tbody>
					{foreach $rows as $row}
						<tr>
							<th title="{$row.label.title|escape}">
								{$row.label.short nofilter}
								{if $row.versions_link}
									<a class="iframe-under-tr changetrack-field-link" href="{$row.versions_link|escape}" title="{GW::l('/G/changetrack/HUMAN_DIFF/OPEN_FULL_DIFF')}">
										<i class="fa fa-eye"></i>
									</a>
								{/if}
							</th>
							<td>
								{if $row.mode=="summary_link" && $row.summary}
									<div class="text-muted changetrack-summary">
										{if $row.human_summary.title}
											<strong>{$row.human_summary.title|escape}</strong><br>
										{/if}
										{if $row.human_summary.details}
											{foreach $row.human_summary.details as $detail}
												{if $detail.type=="added"}
													<span class="changetrack-summary-added">{$detail.text|escape}</span><br>
												{elseif $detail.type=="removed"}
													<span class="changetrack-summary-removed">{$detail.text|escape}</span><br>
												{else}
													{$detail.text|escape}<br>
												{/if}
											{/foreach}
										{elseif $row.summary}
											{GW::l('/G/changetrack/HUMAN_DIFF/TEXT_LENGTH',['v'=>['old'=>$row.summary.old_len,'new'=>$row.summary.new_len]])}<br>
											{GW::l('/G/changetrack/HUMAN_DIFF/LINES',['v'=>['old'=>$row.summary.old_lines,'new'=>$row.summary.new_lines]])}<br>
											{GW::l('/G/changetrack/HUMAN_DIFF/CHARACTER_CHANGE')}: {if $row.summary.chars_delta>0}+{/if}{$row.summary.chars_delta}<br>
											{GW::l('/G/changetrack/HUMAN_DIFF/LINE_CHANGE')}: {if $row.summary.lines_delta>0}+{/if}{$row.summary.lines_delta}<br>
										{/if}
									</div>
								{elseif $row.mode=="inline_diff" && $row.inline_html}
									<div class="changetrack-inline-diff">{$row.inline_html nofilter}</div>
								{elseif $row.mode=="raw_diff" && $row.diff}
									<div class="changetrack-raw-diff">{call dl_output_filters_expand_truncate val=$row.diff|escape expand_truncate_size=220}</div>
								{else}
									<span class="changetrack-val-old">
										{call dl_output_filters_expand_truncate val=$row.old|escape}
									</span>
									<span class="changetrack-arrow">&rarr;</span>
									<span class="changetrack-val-new">
										{call dl_output_filters_expand_truncate val=$row.new|escape}
									</span>
								{/if}
							</td>
						</tr>
					{/foreach}
				</tbody>
			</table>
		{/if}
	{/function}



	
	
	
	{$do_toolbar_buttons[] = hidden}
	{$do_toolbar_buttons[] = search}
	
	{$do_toolbar_buttons_hidden=[exportdata,importdata,dialogconf,print]}		
	
	
		
	{$dl_actions=[]}
	{$dl_smart_fields=[changestable,username,owner_type,transaction_id]}
	{$dl_output_filters.insert_time=short_time}
	
	
	{$dl_checklist_enabled=1}
	{$dl_cl_actions=[dialogremove]}
	
	{if $m->filters}
		{$do_toolbar_buttons=[]}
		{$dl_filters=[]}
	{/if}	
	
{/block}

{include "default_open.tpl"}

<div class="panel panel-default">
	<div class="panel-heading">
		<b>Delete recovery log</b>
		<div><small>{$undo_delete_logfile|escape}</small></div>
	</div>
	<div class="panel-body">
		{if !$undo_delete_entries}
			<div class="text-muted">No delete recovery entries found in this module log.</div>
		{else}
			{foreach $undo_delete_entries as $entry}
				<div class="panel panel-default" style="margin-bottom:10px">
					<div class="panel-body">
						<div class="clearfix" style="margin-bottom:8px">
							<div class="pull-right">
								<a class="btn btn-xs btn-success" onclick="return confirm('Restore this deleted item from recovery log?')" href="{$m->buildUri(false, [act=>doUndoDelete,recover_line=>$entry.line_num])}">
									Restore
								</a>
							</div>

							<div>
								<b>
									{if $entry.item_id !== ''}#{$entry.item_id|escape} {/if}
									{if $entry.item_class}{$entry.item_class|escape}{/if}
								</b>
							</div>
							<div><small class="text-muted">{$entry.stamp|escape}</small></div>

							{if $entry.user_id || $entry.user_title neq ''}
								<div>
									<small>
										{if $entry.user_id}user: {$entry.user_id|escape}{/if}
										{if $entry.user_id && $entry.user_title neq ''} | {/if}
										{if $entry.user_title neq ''}{$entry.user_title|escape}{/if}
									</small>
								</div>
							{/if}

							{if $entry.reason neq ''}
								<div><small>Reason: {$entry.reason|escape}</small></div>
							{/if}
						</div>

						<details>
							<summary>JSON</summary>
							<pre style="max-height:280px;overflow:auto;margin-top:8px">{$entry.pretty_json|escape}</pre>
						</details>
					</div>
				</div>
			{/foreach}
		{/if}
	</div>
</div>
{include "default_close.tpl"}

{capture assign=tmpformhtml}
	{include file="`$m->tpl_dir`elements.tpl"}
{/capture}

{include file="default_form_open.tpl"}
{include "tools/form_components.tpl"}

{$tmpformhtml}

{if $fields_config}
	{call "build_form"}
{else}
	{foreach $m->list_config.dl_fields as $field}
		<tr>{call "cust_inputs"}</tr>
	{/foreach}
{/if}

{function name="df_after_form"}
	{if $item->id}
		<br>
		<table class="gwTable mar-top" style="width:100%">
			<tr>
				<th colspan="7" class="th_h3 th_single">
					Užsakymo pokyčių išrašas
					<a class="btn btn-xs btn-default iframe-under-tr pull-right"
					   href="{$app->buildUri("datasources/changetransactions",[order_id=>$item->id,clean=>2])}">
						Visas išrašas
					</a>
				</th>
			</tr>
			<tr>
				<th>ID</th>
				<th>Laikas</th>
				<th>Vartotojas</th>
				<th>Veiksmas</th>
				<th>Būklė</th>
				<th>Pokyčiai</th>
				<th>Pastaba</th>
			</tr>
			{foreach $order_change_transactions as $tx}
				<tr>
					<td>
						<a class="iframe-under-tr" href="{$app->buildUri("datasources/changetransactions",[transaction_id=>$tx->id,clean=>2])}">
							#{$tx->id}
						</a>
					</td>
					<td>{$tx->insert_time}</td>
					<td>
						{if $tx->user_id}
							<a class="iframeopen" href="{$app->buildUri("users/usr/`$tx->user_id`/form",[clean=>2])}">
								{$tx->usertitle|default:$tx->username|default:$tx->user_id|escape}
							</a>
						{else}
							-
						{/if}
					</td>
					<td>{$tx->action_type|escape}</td>
					<td>{$tx->status|escape}</td>
					<td>
						{if $tx->changetrack_count}
							<a class="badge bg-brown iframe-under-tr"
							   href="{$app->buildUri("datasources/changetrack",[transaction_id=>$tx->id,clean=>2])}">
								{$tx->changetrack_count}
							</a>
						{else}
							-
						{/if}
					</td>
					<td>{$tx->note|escape}</td>
				</tr>
			{foreachelse}
				<tr>
					<td colspan="7" class="text-muted">Pokyčių įrašų nėra.</td>
				</tr>
			{/foreach}
		</table>
	{/if}
{/function}

{include file="default_form_close.tpl"}

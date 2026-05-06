{include file="default_open.tpl"}

<style>
	{literal}
.pushsubs-wrap{background:#fff;border:1px solid #d8e0e8;border-radius:10px;overflow:hidden}
.pushsubs-head{padding:14px 16px;border-bottom:1px solid #e7edf3;background:#f8fafc}
.pushsubs-title{font-size:18px;font-weight:700;color:#101828}
.pushsubs-subtitle{margin-top:4px;font-size:12px;color:#667085}
.pushsubs-head-row{display:flex;justify-content:space-between;align-items:center;gap:12px}
.pushsubs-empty{padding:18px 16px;color:#667085}
.pushsubs-table{margin:0}
.pushsubs-table td,.pushsubs-table th{vertical-align:top!important}
.pushsubs-table tr.active_subscription td{background:#fff7cc!important}
.pushsubs-muted{color:#667085;font-size:12px}
.pushsubs-code{font-family:monospace;font-size:12px;word-break:break-all}
.pushsubs-pre{margin:0;white-space:pre-wrap;font-size:11px;max-width:100%;overflow:auto;background:#f8fafc;border:1px solid #e7edf3;border-radius:6px;padding:10px}
.pushsubs-actions{display:flex;gap:8px;flex-wrap:wrap;margin-top:10px}
.pushsubs-grid{padding:16px;display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:14px}
.pushsubs-card{border:1px solid #e7edf3;border-radius:12px;background:#fff;padding:16px;box-shadow:0 1px 2px rgba(16,24,40,.04)}
.pushsubs-card.active_subscription{background:#fff7cc;border-color:#f3d26a}
.pushsubs-card-head{display:flex;align-items:center;gap:12px}
.pushsubs-browser-icon{width:42px;height:42px;border-radius:10px;background:#f2f4f7;color:#344054;display:flex;align-items:center;justify-content:center;font-size:21px}
.pushsubs-card-title{font-size:16px;font-weight:700;color:#101828}
.pushsubs-card-meta{margin-top:2px;font-size:12px;color:#667085}
.pushsubs-card-body{margin-top:12px}
.pushsubs-card-line{font-size:13px;color:#344054;margin-top:6px}
.pushsubs-card-secondary{font-size:12px;color:#667085;margin-top:6px}
.pushsubs-browser-version{font-size:12px;color:#667085;font-weight:400;margin-left:6px}
.pushsubs-badge{display:inline-block;margin-top:10px;padding:3px 8px;border-radius:999px;background:#ecfdf3;color:#027a48;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.03em}
	{/literal}
</style>

<div class="pushsubs-wrap">
	<div class="pushsubs-head">
		<div class="pushsubs-head-row">
			<div>
				<div class="pushsubs-title">My Push Subscriptions</div>
				<div class="pushsubs-subtitle">
					User: {$target_user->title|escape} (#{$target_user->id}){if $target_user->username}, {$target_user->username|escape}{/if}
				</div>
			</div>
		</div>
	</div>

	{if !$subscriptions}
		<div class="pushsubs-empty">No push subscriptions found for this user.</div>
	{else}
		{if $is_my_subscriptions}
			<div class="pushsubs-grid">
				{foreach $subscriptions as $sub}
					<div class="pushsubs-card" data-subscription-endpoint="{$sub.endpoint|escape}">
						<div class="pushsubs-card-head">
							<div class="pushsubs-browser-icon">
								<i class="{$sub.browser_icon_class|default:'fa fa-globe'|escape}"></i>
							</div>
							<div>
								<div class="pushsubs-card-title">
									{$sub.browser_name|default:'Unknown browser'|escape}
									{if $sub.browser_version}
										<span class="pushsubs-browser-version text-muted">v{$sub.browser_version|escape}</span>
									{/if}
								</div>
							</div>
						</div>
						<div class="pushsubs-card-body">
							<div class="pushsubs-card-line">
								<strong>Device:</strong> {$sub.device_type|default:'Desktop'|escape},
								<strong>OS:</strong> {$sub.os_label|default:'-'|escape},
								{if $sub.device_model}<strong>Model:</strong> {$sub.device_model|escape},{/if}
								<strong>IP:</strong> {$sub.ip|default:'-'|escape}
							</div>
								<div class="pushsubs-card-line"><strong>Subscribed at:</strong> {if $sub.insert_time}{$app->fh()->shortTime($sub.insert_time)|escape}{else}-{/if}</div>
								<div class="pushsubs-card-line"><strong>Domain:</strong> {$sub.site_host|default:'(legacy / unknown)'|escape}</div>
								<div class="pushsubs-card-secondary">Provider: {$sub.endpoint_host|default:'-'|escape}</div>
							{if $sub.has_expiration_time}
								<div class="pushsubs-card-line"><strong>Expires:</strong> {$sub.expiration_time|escape}</div>
							{/if}
							<div class="pushsubs-actions">
								<a href="{$sub.test_url|escape}" class="btn btn-xs btn-default">Test</a>
								<a href="{$sub.remove_url|escape}" class="btn btn-xs btn-danger js-remove-subscription" data-subscription-endpoint="{$sub.endpoint|escape}" onclick="return confirm('Remove this push subscription?')">Remove</a>
							</div>
							<div class="pushsubs-badge" style="display:none;">This browser</div>
						</div>
					</div>
				{/foreach}
			</div>
		{else}
			<table class="table table-striped table-bordered pushsubs-table">
				<thead>
					<tr>
						<th style="width:170px;">Subscribed At</th>
						<th style="width:170px;">Domain</th>
						<th style="width:180px;">Provider</th>
						<th>Endpoint</th>
						<th style="width:320px;">User Agent</th>
					</tr>
				</thead>
					<tbody>
						{foreach $subscriptions as $sub}
							<tr data-subscription-endpoint="{$sub.endpoint|escape}">
								<td>
									<div>{$sub.insert_time|default:'-'}</div>
									<div class="pushsubs-muted">key: {$sub.storage_key|escape}</div>
								<div class="pushsubs-actions">
									<a href="{$sub.test_url|escape}" class="btn btn-xs btn-default">Test</a>
									<a href="{$sub.remove_url|escape}" class="btn btn-xs btn-danger" onclick="return confirm('Remove this push subscription?')">Remove</a>
								</div>
							</td>
							<td>
								<div>{$sub.site_host|default:'(legacy / unknown)'|escape}</div>
								{if $sub.site_origin}
									<div class="pushsubs-muted">origin: {$sub.site_origin|escape}</div>
								{/if}
							</td>
							<td>
								<div>{$sub.endpoint_host|default:'-'|escape}</div>
								<div class="pushsubs-muted">expires: {if $sub.has_expiration_time}{$sub.expiration_time|escape}{else}-{/if}</div>
							</td>
							<td>
								<div class="pushsubs-code">{$sub.endpoint|escape}</div>
								{if $sub.endpoint_path}
									<div class="pushsubs-muted">path: {$sub.endpoint_path|escape}</div>
								{/if}
								<details style="margin-top:8px;">
									<summary class="pushsubs-muted" style="cursor:pointer;">Raw JSON</summary>
									<pre class="pushsubs-pre">{$sub.raw_json|escape}</pre>
								</details>
							</td>
							<td>
								<div>{$sub.user_agent|default:'-'|escape}</div>
							</td>
						</tr>
					{/foreach}
				</tbody>
			</table>
		{/if}
	{/if}
</div>

{if $is_my_subscriptions}
<script>
require(['gwcms'], function(){
	var removedEndpointStorageKey = 'gw_push_removed_endpoint_pending';

	var syncRemovedCurrentBrowserSubscription = function(){
		var removedEndpoint = sessionStorage.getItem(removedEndpointStorageKey) || '';

		if (!removedEndpoint || !navigator.serviceWorker)
			return;

		navigator.serviceWorker.ready
			.then(function(reg){ return reg.pushManager.getSubscription(); })
			.then(function(subscription){
				var currentEndpoint = subscription && subscription.endpoint ? String(subscription.endpoint) : '';

				if (!currentEndpoint || currentEndpoint !== removedEndpoint) {
					sessionStorage.removeItem(removedEndpointStorageKey);
					return;
				}

				return subscription.unsubscribe().then(function(){
					sessionStorage.removeItem(removedEndpointStorageKey);
					localStorage.setItem('subscriptionid', false);
					if (window.GW_SW) {
						GW_SW.subscription = false;
						if (GW_SW.stateChange)
							GW_SW.stateChange(false, 'Subscription removed');
					}
					if (window.gw_adm_sys && gw_adm_sys.notify)
						gw_adm_sys.notify('info', 'Current browser subscription was removed and local subscription was cleared');
					location.reload();
				});
			})
			.catch(function(){
				sessionStorage.removeItem(removedEndpointStorageKey);
			});
	};

	syncRemovedCurrentBrowserSubscription();

	navigator.serviceWorker.ready
		.then(function(reg){ return reg.pushManager.getSubscription(); })
		.then(function(subscription){
			var endpoint = subscription && subscription.endpoint ? String(subscription.endpoint) : '';

			if (!endpoint)
				return;

			$('[data-subscription-endpoint]').each(function(){
				if (String($(this).attr('data-subscription-endpoint') || '') === endpoint) {
					$(this).addClass('active_subscription');
					$(this).find('.pushsubs-badge').show();
				}
			});
		})
		.catch(function(){});

	$('.js-remove-subscription').on('click', function(){
		sessionStorage.setItem(removedEndpointStorageKey, String($(this).attr('data-subscription-endpoint') || ''));
	});
});
</script>
{/if}

{include file="default_close.tpl"}

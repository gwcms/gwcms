{$new_messages=$app->user->countNewMessages()}
{$notif_list=GW_Message::singleton()->findAll(['user_id=? AND seen=0',$app->user->id],['order'=>'insert_time DESC','limit'=>7])}


{if GW_Config::singleton()->get('sys/VAPID_PUBLIC_KEY')}
	<li id="push-activation-banner" class="dropdown notifications" style="display:none">
		
		
		<a id="subscribe_btn" onclick="return false" class="notifications-selector dropdown-toggle" href="#" style="color: orange" data-default-label="Subscribe push notifications here">

			Subscribe push notifications here
	
		</a>

	</li>

{/if}


<li class="dropdown" style="">
	<a onclick="return false" class="notifications-selector dropdown-toggle" href="#" data-toggle="dropdown">
		<span class="material-symbols-outlined" translate="no">
		notifications
		</span>
		{if $new_messages}
			<span class="badge badge-danger" style="position:absolute; top:2px; right:0;">{if $new_messages>99}99+{else}{$new_messages}{/if}</span>
		{/if}
	</a>

	<ul class="dropdown-menu dropdown-menu-md" style="min-width:360px; padding:0;">
		<li class="pad-all bord-btm">
			<div style="display:flex; justify-content:space-between; align-items:center; gap:10px;">
				<strong>Notifications</strong>
				<a href="{$app->buildUri('users/messages')}" style="font-size:12px;">Open all</a>
			</div>
		</li>

		<li style="max-height:420px; overflow:auto;">
			{if $notif_list}
				{foreach $notif_list as $item}
					<a href="{$app->buildUri('users/messages/view',[id=>$item->id])}" class="list-group-item" style="display:block; border-left:0; border-right:0;">
						<div style="display:flex; justify-content:space-between; gap:10px; align-items:flex-start;">
							<div style="min-width:0; flex:1;">
								<div style="font-weight:600; color:#101828;">{$item->subject|default:'Notification'|truncate:60}</div>
								<div style="font-size:12px; color:#667085; margin-top:2px;">{$item->message|strip_tags|truncate:90}</div>
							</div>
							<div style="text-align:right; white-space:nowrap;">
								<span class="badge badge-danger">new</span>
								<div style="font-size:11px; color:#98a2b3; margin-top:4px;">{$item->insert_time|regex_replace:'/^\d{4}-\d{2}-\d{2}\s+/':''}</div>
							</div>
						</div>
					</a>
				{/foreach}
			{else}
				<div class="pad-all text-muted">No new notifications</div>
			{/if}
		</li>
		
		<li class="bord-top">
			<a href="{$app->buildUri('users/userspushsubscriptions/managemysubscriptions')}" data-default-href="{$app->buildUri('users/userspushsubscriptions/managemysubscriptions')}" class="list-group-item text-muted js-manage-push-subscriptions-link" style="display:block; border-left:0; border-right:0;">
				Manage my subscriptions
			</a>
		</li>
	</ul>
</li>

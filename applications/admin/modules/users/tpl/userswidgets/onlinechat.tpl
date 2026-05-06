<div class="mainnav-widget">
	<style>
		#mainnav-widget-onlinechat .mainnav-onlinechat-user:hover,
		#mainnav-widget-onlinechat .mainnav-onlinechat-user:focus {
			background: rgba(255,255,255,0.06) !important;
			color: #c7d0db !important;
		}
		#mainnav-widget-onlinechat .mainnav-onlinechat-user:hover .fa-angle-right,
		#mainnav-widget-onlinechat .mainnav-onlinechat-user:focus .fa-angle-right {
			color: #fff;
		}
		#mainnav-widget-onlinechat .mainnav-onlinechat-user-actions {
			display:inline-flex;
			align-items:center;
			justify-content:center;
			width:26px;
			height:26px;
			margin-left:4px;
			border-radius:999px;
			color:#9fb0c7;
			flex:0 0 26px;
		}
#mainnav-widget-onlinechat .mainnav-onlinechat-user-actions:hover,
#mainnav-widget-onlinechat .mainnav-onlinechat-user-actions:focus {
	background: transparent !important;
	color:#fff;
	text-decoration:none;
}
		#mainnav-widget-onlinechat #mainnav_onlinechat_count {
			background: transparent !important;
			border: 0 !important;
			box-shadow: none !important;
			color: #34c759 !important;
			padding: 0 !important;
			font-size: 12px;
			font-weight: 600;
		}
		#mainnav-widget-onlinechat .reactws-status-link {
			color: #9fb0c7;
			display: inline-block;
			font-size: 11px;
			line-height: 1;
			padding: 2px 2px;
		}
		#mainnav-widget-onlinechat .reactws-status-dot {
			color: #98a2b3;
			font-size: 8px;
		}
	</style>
	<div class="show-small">
		<a href="#" data-toggle="menu-widget" data-target="#mainnav-widget-onlinechat">
			<i class="fa fa-comments-o"></i>
		</a>
	</div>

	<div id="mainnav-widget-onlinechat" class="hide-small mainnav-widget-content">
		<ul class="list-group">
			<li class="list-header pad-no pad-ver">
				<div style="display:flex; justify-content:space-between; align-items:center; gap:8px;">
					<span>Online chat</span>
					<div class="dropdown" style="display:flex; align-items:center; gap:6px;">
						<span id="mainnav_onlinechat_count" style="display:none;"></span>
						{if $show_ws_protocol_link}
							<a id="reactws_status_link" class="reactws-status-link" href="{$ws_protocol_url}" title="ReactPHP WS status">
								<i id="reactws_status_dot" class="fa fa-circle reactws-status-dot"></i>
							</a>
						{/if}
						<a href="#" class="dropdown-toggle text-muted" data-toggle="dropdown" onclick="return false" title="Chat actions" style="font-size:11px; line-height:1; padding:2px 4px; display:inline-block;">
							<i class="fa fa-angle-down"></i>
						</a>
						<ul class="dropdown-menu dropdown-menu-right" style="min-width:180px;">
							<li><a href="{$new_private_url}">Start new conversation</a></li>
							<li><a href="{$chat_list_url}">All chats</a></li>
							{if $show_last_request_uri_debug}
								<li class="divider"></li>
								<li>
									<a href="{$toggle_last_request_uri_url}">
										last_request_uri [{if $show_last_request_uri_debug_on}On{else}Off{/if}]
									</a>
								</li>
							{/if}
						</ul>
					</div>
				</div>
			</li>
			<li id="mainnav_onlinechat_empty" class="pad-all" style="color:#9fb0c7;">Loading online users...</li>
			<li class="pad-no">
				<div id="mainnav_onlinechat_list" style="max-height:340px; overflow:auto;"></div>
			</li>
		</ul>
	</div>
</div>

{capture append=footer_hidden}
<script type="text/javascript">
require(['gwcms'], function(){
require(['js/gwchat_app'], function(GWChatApp){
	var endpoint = '{$chat_endpoint|escape:"javascript"}';
	var wsUrl = '{$ws_path|escape:"javascript"}';
	var pollTimer = 0;
	var cacheKey = 'gw_admin_onlinechat_widget_v1';
	var showLastRequestUriDebugOn = {if $show_last_request_uri_debug_on}true{else}false{/if};
	var showUserActions = {if $app->user && $app->user->id == 9}true{else}false{/if};
	var usersById = {};

	GWChatApp.init({
		wsUrl: wsUrl,
		httpEndpoint: endpoint,
		chatListUrl: '{$chat_list_url|escape:"javascript"}',
		wssLogToConsole: {if $wss_log_to_console}true{else}false{/if}
	});

	function escapeHtml(value)
	{
		return String(value == null ? '' : value)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#039;');
	}

	function formatTime(value)
	{
		value = String(value || '');
		return value.replace(/^(\d{4}-\d{2}-\d{2})\s+/, '');
	}

	function truncateText(value, maxLength)
	{
		value = String(value || '');
		maxLength = parseInt(maxLength || 0, 10) || 40;

		if (value.length <= maxLength)
			return value;

		return value.substr(0, maxLength - 1) + '…';
	}

	function renderOnlineUsers(users, onlineCount)
	{
		var wrap = $('#mainnav_onlinechat_list');
		var empty = $('#mainnav_onlinechat_empty');
		var count = $('#mainnav_onlinechat_count');
		wrap.empty();
		usersById = {};

		if (!users || !users.length) {
			count.hide().text('');
			empty.text('No online users').show();
			return;
		}

		onlineCount = parseInt(onlineCount || 0, 10) || 0;
		if (onlineCount > 0)
			count.text(onlineCount).show();
		else
			count.hide().text('');

		empty.hide();

		for (var i = 0; i < users.length; i++) {
			var user = users[i];
			usersById[String(user.id)] = user;
			var subtitleParts = [];
			if (user.last_contact_time)
				subtitleParts.push('Last chat ' + formatTime(user.last_contact_time));
			if (user.last_request_ago)
				subtitleParts.push('Seen ' + user.last_request_ago + ' ago');
			var subtitle = subtitleParts.join(' | ');
			var namePrefix = user.is_admin ? '<span style="color:#47c7ff; font-weight:700; font-size:12px;">@</span>' : '';
			var nameColor = user.is_ws_online ? '#d7fbe3' : '#aeb9c6';
			var userActionsUrl = '{$app->buildUri("users/usr/itemactions",[id=>"__USER_ID__",outside=>1])|escape:"javascript"}'.replace('__USER_ID__', encodeURIComponent(user.id));
			var userActionsHtml = showUserActions ?
				'<div class="btn-group dropright gwcmsAction" style="display:inline-flex; align-items:center;">' +
					'<i class="fa fa-angle-right dropdown-toggle dropdown-toggle-icon gwcms-ajax-dd mainnav-onlinechat-user-actions" data-toggle="dropdown" data-url="' + escapeHtml(userActionsUrl) + '" title="User actions"></i>' +
					'<ul class="dropdown-menu mainnav-onlinechat-user-menu" style="min-width:180px;"><li><i class="fa fa-spinner fa-pulse"></i></li></ul>' +
				'</div>' :
				'';
			var lastRequestUri = showLastRequestUriDebugOn && user.last_request_uri ?
				'<div style="font-size:10px; line-height:1.25; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; margin-top:2px;">' +
					'<span title="' + escapeHtml(user.last_request_uri) + '" style="color:#98a2b3;">' + escapeHtml(truncateText(user.last_request_uri, 40)) + '</span>' +
				'</div>' :
				'';
			var html =
				'<div role="button" tabindex="0" class="list-group-item mainnav-onlinechat-user" data-user-id="' + escapeHtml(user.id) + '" title="' + escapeHtml(subtitle) + '" style="border:0; background:transparent; color:#aeb9c6; padding:7px 12px; cursor:pointer;">' +
					'<div style="display:flex; align-items:center; justify-content:space-between; gap:8px;">' +
						'<div style="display:flex; gap:6px; min-width:0; flex:1; align-items:center;">' +
							'<div style="min-width:0; flex:1;">' +
								'<div style="display:flex; align-items:center; gap:0; min-width:0;">' +
									namePrefix +
									'<span style="font-weight:' + (user.has_private_room ? '600' : '500') + '; color:' + nameColor + '; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; font-size:12px;">' + escapeHtml(user.name || user.username || ('User #' + user.id)) + '</span>' +
								'</div>' +
								lastRequestUri +
							'</div>' +
						'</div>' +
						'<div style="display:flex; align-items:center; gap:4px; white-space:nowrap; color:#9fb0c7;">' +
							(user.unread_count ? '<span class="badge badge-danger" style="font-size:10px;">' + escapeHtml(user.unread_count) + '</span>' : '') +
							userActionsHtml +
						'</div>' +
					'</div>' +
				'</div>';
			wrap.append(html);
		}

		if (showUserActions)
			gwcms.initDropdowns();
	}

	function saveCache(users, onlineCount, reactWsStatus)
	{
		try {
			localStorage.setItem(cacheKey, JSON.stringify({
				users: users || [],
				online_count: parseInt(onlineCount || 0, 10) || 0,
				react_ws_status: reactWsStatus || null,
				saved_at: Date.now()
			}));
		} catch (e) {}
	}

	function loadCache()
	{
		try {
			var raw = localStorage.getItem(cacheKey);
			if (!raw)
				return null;

			var data = JSON.parse(raw);
			if (!data || !data.users || !data.users.length)
				return null;

			return data;
		} catch (e) {
			return null;
		}
	}

	function loadOnlineUsers()
	{
		var req = GWChatApp.loadBubbleData();
		var onFail = function(){
			var cached = loadCache();
			if (cached) {
				renderOnlineUsers(cached.users || [], cached.online_count || 0);
				return;
			}

			$('#mainnav_onlinechat_count').hide().text('');
			$('#mainnav_onlinechat_empty').text('Failed to load online users').show();
		};

		if (req.fail)
			return req.fail(onFail);

		return req.then(null, onFail);
	}

	function setReactWsStatus(state, title)
	{
		var dot = $('#reactws_status_dot');
		var link = $('#reactws_status_link');
		if (!dot.length || !link.length)
			return;

		var color = '#98a2b3';
		if (state === 'healthy')
			color = '#34c759';
		else if (state === 'warning')
			color = '#f59e0b';
		else if (state === 'error')
			color = '#ef4444';

		dot.css('color', color);
		link.attr('title', title || 'ReactPHP WS status');
	}

	function startPolling()
	{
		clearInterval(pollTimer);
		GWChatApp.startBubblePolling(45000);
	}

	GWChatApp.on('bubbleData', function(resp){
		renderOnlineUsers(resp.online_users || [], resp.online_count || 0);
		if (resp.react_ws_status)
			setReactWsStatus(resp.react_ws_status.state, resp.react_ws_status.title);
		saveCache(resp.online_users || [], resp.online_count || 0, resp.react_ws_status || null);
	});

	function restoreOnlinechatUserMenus(exceptGroup)
	{
		$('body > .mainnav-onlinechat-user-menu').each(function(){
			var menu = $(this);
			var original = menu.data('onlinechat-original-parent');

			if (!original || !original.length)
				return;

			if (exceptGroup && original[0] === exceptGroup[0])
				return;

			original.removeClass('open').find('[aria-expanded="true"]').attr('aria-expanded', 'false');
			menu.appendTo(original).removeAttr('style');
		});

		$('#mainnav-widget-onlinechat .gwcmsAction.btn-group.open').not(exceptGroup || $()).removeClass('open');
	}

	$(document)
		.on('show.bs.dropdown', '#mainnav-widget-onlinechat .gwcmsAction.btn-group', function(){
			var group = $(this);
			var trigger = group.find('[data-toggle="dropdown"]').first();
			var menu = group.find('.dropdown-menu').first();
			var rect = trigger[0].getBoundingClientRect();
			var menuWidth = Math.max(menu.outerWidth(), 180);

			restoreOnlinechatUserMenus(group);

			if (!menu.data('onlinechat-original-parent'))
				menu.data('onlinechat-original-parent', group);

			menu.appendTo('body').css({
				position: 'fixed',
				display: 'block',
				top: Math.max(6, rect.top - 4) + 'px',
				left: Math.min(rect.right + 6, window.innerWidth - menuWidth - 8) + 'px',
				right: 'auto',
				zIndex: 10000
			});
		})
		.on('hide.bs.dropdown', '#mainnav-widget-onlinechat .gwcmsAction.btn-group', function(){
			var group = $(this);

			$('body > .mainnav-onlinechat-user-menu').each(function(){
				var menu = $(this);
				var original = menu.data('onlinechat-original-parent');

				if (original && original.length && original[0] === group[0])
					menu.appendTo(group).removeAttr('style');
			});
		});

	$(document).on('click', '.mainnav-onlinechat-user', function(e){
		if ($(e.target).closest('a,.gwcmsAction').length)
			return;

		var userId = String($(this).data('user-id'));

		GWChatApp.openPrivate(userId);
	});

	$(document).on('keydown', '.mainnav-onlinechat-user', function(e){
		if (e.key !== 'Enter' && e.key !== ' ')
			return;

		e.preventDefault();
		$(this).trigger('click');
	});

	var cached = loadCache();
	if (cached) {
		renderOnlineUsers(cached.users || [], cached.online_count || 0);
		if (cached.react_ws_status)
			setReactWsStatus(cached.react_ws_status.state, cached.react_ws_status.title);
	}

	loadOnlineUsers();
	startPolling();
	gwcms.initDropdowns();
});
});
</script>
{/capture}

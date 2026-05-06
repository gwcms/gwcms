<li class="dropdown notifications chatbubble-nav-item">
	<a id="chatbubble_btn" onclick="return false" class="notifications-selector dropdown-toggle" href="#" data-toggle="dropdown">
		<span class="material-symbols-outlined" translate="no">chat_bubble</span>
		<span id="chatbubble_badge" class="badge badge-danger" style="display:none; position:absolute; top:2px; right:0;"></span>
	</a>
	<ul class="dropdown-menu dropdown-menu-md" style="min-width:360px; padding:0;">
		<li class="pad-all bord-btm">
			<div style="display:flex; justify-content:space-between; align-items:center; gap:10px;">
				<strong>Chats</strong>
				<div style="display:flex; gap:10px; align-items:center; font-size:12px;">
					<a href="{$new_private_url}">Start new conversation</a>
					<a href="{$new_room_url}">Start new room</a>
					<a href="{$chat_list_url}">Open chat</a>
				</div>
			</div>
		</li>
		<li id="chatbubble_rooms_wrap" style="max-height:420px; overflow:auto;">
			<div id="chatbubble_rooms_empty" class="pad-all text-muted">Loading conversations...</div>
			<div id="chatbubble_rooms"></div>
		</li>
	</ul>
</li>

{capture append=footer_hidden}
<script type="text/javascript">
require(['gwcms'], function(){
require(['js/gwchat_app'], function(GWChatApp){
	var endpoint = '{$http_endpoint|escape:"javascript"}';
	var wsUrl = '{$ws_path|escape:"javascript"}';
	var pollTimer = 0;
	var acknowledgeInFlight = false;

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

	function avatarHtml(room)
	{
		var user = room && room.display_user ? room.display_user : null;
		var imageUrl = user && user.image_url ? String(user.image_url) : '';
		var fullName = user && user.name ? String(user.name) : String(formatRoomTitle(room));
		var initials = fullName.split(/\s+/).slice(0, 2).map(function(part){
			return part ? part.charAt(0).toUpperCase() : '';
		}).join('') || '?';

		if (imageUrl)
			return '<img src="' + escapeHtml(imageUrl) + '" alt="' + escapeHtml(fullName) + '" style="width:15px;height:15px;border-radius:50%;object-fit:cover;display:block;">';

		return '<span style="width:15px;height:15px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;background:#eaf2ff;color:#1d4ed8;font-size:9px;font-weight:700;">' + escapeHtml(initials) + '</span>';
	}

	function formatRoomTitle(room)
	{
		if (!room)
			return 'Unknown chat';

		if (room.type === 'group')
			return '#' + String(room.display_title || room.title || ('room-' + room.id)).replace(/^#+/, '');

		if (room.display_user && room.display_user.name)
			return room.display_user.name;

		return room.display_title || ('Chat #' + room.id);
	}

	function renderRooms(rooms)
	{
		var wrap = $('#chatbubble_rooms');
		var empty = $('#chatbubble_rooms_empty');
		wrap.empty();

		if (!rooms || !rooms.length) {
			empty.text('No conversations yet').show();
			return;
		}

		empty.hide();

		for (var i = 0; i < rooms.length; i++) {
			var room = rooms[i];
			var unread = parseInt(room.unread_count || '0', 10) || 0;
			var hasUnreadActivity = parseInt(room.bubble_has_unread_activity || room.unread_activity_count || '0', 10) > 0;
			var html =
				'<a href="' + escapeHtml(room.room_url || '#') + '" class="list-group-item chatbubble-room-link" data-room-id="' + escapeHtml(room.id) + '" style="display:block; border-left:0; border-right:0;">' +
					'<div style="display:flex; justify-content:space-between; gap:10px; align-items:flex-start;">' +
						'<div style="display:flex; gap:8px; min-width:0; flex:1; align-items:center;">' +
							((room.type === 'private') ? ('<span style="flex:0 0 auto;">' + avatarHtml(room) + '</span>') : '') +
							'<div style="min-width:0; flex:1;">' +
								'<div style="font-weight:' + ((unread || hasUnreadActivity) ? '600' : '400') + '; color:#101828;">' + escapeHtml(formatRoomTitle(room)) + '</div>' +
								(hasUnreadActivity && !unread ? '<div style="font-size:11px; color:#475467; margin-top:3px;">New activity</div>' : '') +
							'</div>' +
						'</div>' +
						'<div style="text-align:right; white-space:nowrap;">' +
							(unread ? '<span class="badge badge-danger">' + unread + '</span>' : '') +
							(!unread && hasUnreadActivity ? '<span class="badge badge-warning">new</span>' : '') +
							'<div style="font-size:11px; color:#98a2b3; margin-top:4px;">' + escapeHtml(String(room.last_event_time || room.last_message_time || '').replace(/^(\d{4}-\d{2}-\d{2})\s+/, '')) + '</div>' +
						'</div>' +
					'</div>' +
				'</a>';
			wrap.append(html);
		}
	}

	function renderUnreadBadge(count)
	{
		var badge = $('#chatbubble_badge');
		count = parseInt(count || '0', 10) || 0;

		if (!count) {
			badge.hide();
			return;
		}

		badge.text(count > 99 ? '99+' : count).show();
	}

	function loadChatBubbleData()
	{
		var req = GWChatApp.loadBubbleData();
		var onFail = function(){
			$('#chatbubble_rooms_empty').text('Failed to load conversations').show();
		};

		if (req.fail)
			return req.fail(onFail);

		return req.then(null, onFail);
	}

	function acknowledgeChatBubble()
	{
		if (acknowledgeInFlight)
			return;

		acknowledgeInFlight = true;

		$.ajax({
			url: endpoint,
			method: 'GET',
			dataType: 'json',
			data: { act: 'doMarkChatbubbleAcknowledgeTime' }
		}).always(function(){
			acknowledgeInFlight = false;
			loadChatBubbleData();
		});
	}

	function startPolling()
	{
		clearInterval(pollTimer);
		GWChatApp.startBubblePolling(20000);
	}

	GWChatApp.on('bubbleData', function(resp){
		renderRooms(resp.rooms || []);
		renderUnreadBadge(resp.unread_total || 0);
	});

	$('#chatbubble_btn').on('click', function(){
		loadChatBubbleData();
		acknowledgeChatBubble();
	});

	$('.chatbubble-nav-item').on('hidden.bs.dropdown', function(){
		acknowledgeChatBubble();
	});

	$(document).on('click', '.chatbubble-room-link', function(e){
		var roomId = parseInt($(this).data('room-id') || '0', 10) || 0;
		if (!roomId)
			return;

		e.preventDefault();
		GWChatApp.openRoom(roomId);
	});

	loadChatBubbleData();
	startPolling();
});
});
</script>
{/capture}

{include file="default_open.tpl"}

{literal}
<style>
.chat-test-meta,.chat-test-note,.chat-room-shell{background:#fff;border:1px solid #d8e0e8;border-radius:8px;padding:12px;margin:0 0 14px}
.chat-test-results{width:100%;border-collapse:collapse;background:#fff;border:1px solid #d8e0e8}
.chat-test-results th,.chat-test-results td{padding:8px 10px;border-bottom:1px solid #e7edf3;text-align:left;vertical-align:top}
.chat-test-ok{color:#0a7c2f;font-weight:bold}
.chat-test-fail{color:#b42318;font-weight:bold}
.chat-test-log{background:#0f1720;color:#d6e2f0;padding:12px;border-radius:8px;overflow:auto;white-space:pre-wrap;min-height:220px}
.chat-ws-control{display:inline-block;position:relative;margin-left:8px}
.chat-ws-control-btn{display:inline-flex;align-items:center;gap:8px}
.chat-ws-control-label{display:inline-flex;align-items:center}
.chat-ws-control-caret{display:inline-flex;align-items:center;justify-content:center;color:#667085;font-size:16px;line-height:1;margin-left:2px}
.chat-ws-light{width:10px;height:10px;border-radius:50%;display:inline-block;background:#98a2b3;box-shadow:0 0 0 3px rgba(152,162,179,.18)}
.chat-ws-light.is-up{background:#16a34a;box-shadow:0 0 0 3px rgba(22,163,74,.18)}
.chat-ws-light.is-down{background:#dc2626;box-shadow:0 0 0 3px rgba(220,38,38,.18)}
.chat-ws-light.is-working{background:#d97706;box-shadow:0 0 0 3px rgba(217,119,6,.18)}
.chat-ws-menu{position:absolute;top:100%;left:0;z-index:20;min-width:180px;margin-top:8px;padding:8px;background:#fff;border:1px solid #d8e0e8;border-radius:8px;box-shadow:0 10px 30px rgba(15,23,32,.14);display:none}
.chat-ws-menu.open{display:block}
.chat-ws-menu button{display:block;width:100%;margin:0 0 6px;text-align:left}
.chat-ws-menu button:last-child{margin-bottom:0}
.chat-ws-status-line{margin-top:10px;color:#475467}
.chat-user-avatar{width:40px;height:40px;border-radius:50%;object-fit:cover;display:inline-block;vertical-align:middle;background:#f2f4f7}
.chat-user-fallback{display:inline-block;vertical-align:middle}
.chat-room-shell{padding:0;overflow:hidden}
.chat-room-header{padding:16px 18px;border-bottom:1px solid #e7edf3;display:flex;justify-content:space-between;gap:12px;align-items:flex-start}
.chat-room-title{font-size:22px;font-weight:700;line-height:1.2}
.chat-room-subtitle,.chat-room-presence-text,.chat-room-typing,.chat-room-loadmore{color:#475467}
.chat-room-subtitle,.chat-room-presence-text,.chat-room-typing{margin-top:6px}
.chat-room-presence-text{font-size:13px}
.chat-room-presence-list{display:flex;flex-wrap:wrap;gap:8px;margin-top:10px}
.chat-room-presence-chip{display:inline-flex;align-items:center;gap:8px;padding:6px 10px;border:1px solid #d8e0e8;border-radius:999px;background:#fff}
.chat-room-presence-chip .chat-user-avatar,.chat-room-presence-chip .chat-user-fallback{width:28px;height:28px}
.chat-room-presence-chip .chat-user-avatar{display:block}
.chat-room-presence-chip .chat-user-fallback{display:inline-flex;align-items:center;justify-content:center;border-radius:50%;background:#eaf2ff;font-size:11px;font-weight:700;color:#1d4ed8;overflow:hidden;text-align:center;padding:0 4px}
.chat-room-presence-dot{width:8px;height:8px;border-radius:50%;background:#16a34a;box-shadow:0 0 0 3px rgba(22,163,74,.15)}
.chat-room-presence-name{font-size:13px;color:#101828}
.chat-room-presence-count{font-size:11px;color:#667085}
.chat-room-panel{display:flex;flex-direction:column;height:500px}
.chat-room-messages{flex:1;overflow-y:auto;padding:16px 18px;background:#f7f9fc}
.chat-room-loadmore{text-align:center;font-size:12px;padding:6px 0 14px}
.chat-room-message{display:flex;gap:10px;margin:0 0 14px;align-items:flex-end}
.chat-room-message.is-me{justify-content:flex-end}
.chat-room-message.is-me .chat-room-message-avatar{display:none}
.chat-room-message-avatar{width:40px;flex:0 0 40px}
.chat-room-message-main{max-width:min(72%,780px)}
.chat-room-message.is-me .chat-room-message-main{display:flex;flex-direction:column;align-items:flex-end}
.chat-room-message-name{font-size:12px;color:#475467;margin:0 0 4px}
.chat-room-message-bubble{background:#fff;border:1px solid #d8e0e8;border-radius:16px;padding:10px 14px;color:#101828;word-break:break-word;box-shadow:0 1px 2px rgba(16,24,40,.04)}
.chat-room-message.is-me .chat-room-message-bubble{background:#dbeafe;border-color:#bfdbfe;border-bottom-right-radius:4px}
.chat-room-message.is-other .chat-room-message-bubble{border-bottom-left-radius:4px}
.chat-room-message-time{display:inline-block;margin-top:5px;font-size:11px;color:#667085}
.chat-room-message.is-system{justify-content:center}
.chat-room-message.is-system .chat-room-message-main{max-width:100%;display:block}
.chat-room-message.is-system .chat-room-message-bubble{background:#eef4ff;border-color:#c7d7fe;color:#1d4ed8;border-radius:999px;padding:8px 14px}
.chat-room-empty{padding:30px 12px;text-align:center;color:#667085}
.chat-room-composer{border-top:1px solid #e7edf3;padding:14px 18px;background:#fff}
.chat-room-input-wrap{display:flex;gap:10px;align-items:flex-end}
.chat-room-input{width:100%;min-height:64px;max-height:160px;resize:vertical;border:1px solid #d0d5dd;border-radius:12px;padding:12px 14px;font:inherit}
.chat-room-send{min-width:110px}
</style>
{/literal}

<div class="chat-test-meta">
	<div><b>WS path:</b> <span id="chatTestWsUrl">{$ws_path}</span></div>
	<div><b>HTTP endpoint:</b> <span id="chatTestHttpEndpoint">{$http_endpoint}</span></div>
	<div><b>User:</b> {$current_username} (#{$current_user_id})</div>
	<div><b>Requested room_id:</b> <span id="chatRequestedRoomId">{$requested_room_id}</span></div>
	<div><b>WS status:</b> <span id="chatWsStatus">initializing</span></div>
</div>

<div class="chat-test-note">
	<button type="button" class="btn btn-primary" id="runChatProtocolTests">Run Protocol Tests</button>
	<button type="button" class="btn btn-default" id="runChatHealthDebug">React Health Debug</button>
	<div class="chat-ws-control">
		<button type="button" class="btn btn-default chat-ws-control-btn" id="chatWsControlToggle">
			<span class="chat-ws-light" id="chatWsControlLight"></span>
			<span class="chat-ws-control-label">React WS</span>
			<span class="chat-ws-control-caret" aria-hidden="true"><i class="fa fa-angle-down"></i></span>
		</button>
		<div class="chat-ws-menu" id="chatWsControlMenu">
			<button type="button" class="btn btn-default" data-react-ws-action="doReactStartNow">Start</button>
			<button type="button" class="btn btn-default" data-react-ws-action="doReactStopNow">Stop</button>
			<button type="button" class="btn btn-warning" data-react-ws-action="doReactRestartNow">Restart</button>
		</div>
	</div>
	<a href="{$m->buildUri(false,[act=>doLiveChatProtocol])}" class="btn btn-primary" target="_blank">Run PHP Backend Tests</a>
	<div class="chat-ws-status-line">React WS status: <span id="chatWsBackendStatus">checking</span></div>
</div>

<div class="chat-room-shell">
	<div class="chat-room-header">
		<div>
			<div class="chat-room-title" id="chatRoomTitle">Loading room...</div>
			<div class="chat-room-subtitle" id="chatRoomSubtitle">Connecting...</div>
			<div class="chat-room-presence-text" id="chatRoomPresence">Presence loading...</div>
			<div class="chat-room-presence-list" id="chatRoomPresenceList"></div>
			<div class="chat-room-typing" id="chatRoomTyping"></div>
		</div>
		<div>
			<button type="button" class="btn btn-default" id="chatRoomRefreshPresence">Refresh Presence</button>
		</div>
	</div>
	<div class="chat-room-panel">
		<div class="chat-room-messages" id="chatRoomMessages">
			<div class="chat-room-empty" id="chatRoomEmpty">Loading messages...</div>
		</div>
		<div class="chat-room-composer">
			<div class="chat-room-input-wrap">
				<textarea class="chat-room-input" id="chatRoomInput" placeholder="Write a message"></textarea>
				<button type="button" class="btn btn-primary chat-room-send" id="chatRoomSend">Send</button>
			</div>
		</div>
	</div>
</div>

<table class="chat-test-results" id="chatProtocolResults">
	<thead>
		<tr>
			<th>Test</th>
			<th>Status</th>
			<th>Details</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>

<h3 style="margin-top:16px">Event Log</h3>
<pre class="chat-test-log" id="chatProtocolEventLog"></pre>

<h3 style="margin-top:16px">React Debug</h3>
<pre class="chat-test-log" id="chatProtocolReactDebug"></pre>

<div style="margin-top:16px">
	<a href="{$m->buildUri(false,[act=>doLiveChatProtocol])}" class="btn btn-primary" target="_blank">Run PHP Backend Tests</a>
</div>

{capture append=footer_hidden}
<script type="text/javascript">
require(['gwcms'], function(){
	var bootStartedAt = Date.now();
	var booted = false;

	function failBoot(message, extra)
	{
		var wsStatus = document.getElementById('chatWsStatus');
		if (wsStatus)
			wsStatus.textContent = message;

		console.error('[GWChatTest] ' + message, extra || {});
	}

	function startWith($)
	{
		if (booted)
			return;

		booted = true;

		var resultsBody = document.querySelector('#chatProtocolResults tbody');
		var eventLog = document.getElementById('chatProtocolEventLog');
		var reactDebugLog = document.getElementById('chatProtocolReactDebug');
		var wsUrl = document.getElementById('chatTestWsUrl').textContent;
		var httpEndpoint = document.getElementById('chatTestHttpEndpoint').textContent;
		var requestedRoomId = parseInt(document.getElementById('chatRequestedRoomId').textContent || '0', 10) || 0;
		var wsStatus = document.getElementById('chatWsStatus');
		var wsBackendStatus = document.getElementById('chatWsBackendStatus');
		var wsControlLight = document.getElementById('chatWsControlLight');
		var wsControlToggle = document.getElementById('chatWsControlToggle');
		var wsControlMenu = document.getElementById('chatWsControlMenu');
		var roomTitle = document.getElementById('chatRoomTitle');
		var roomSubtitle = document.getElementById('chatRoomSubtitle');
		var roomPresence = document.getElementById('chatRoomPresence');
		var roomPresenceList = document.getElementById('chatRoomPresenceList');
		var roomTyping = document.getElementById('chatRoomTyping');
		var roomMessages = document.getElementById('chatRoomMessages');
		var roomEmpty = document.getElementById('chatRoomEmpty');
		var roomInput = document.getElementById('chatRoomInput');
		var roomSend = document.getElementById('chatRoomSend');
		var pageClient = null;
		var activeRoom = null;
		var currentUser = null;
		var joinedRoomId = 0;
		var roomJoinInFlight = false;
		var loadingOlder = false;
		var historyExhausted = false;
		var messageIds = {};
		var imageUrlCache = {};
		var presenceUsers = {};
		var typingUsers = {};
		var typingStopTimer = 0;
		var selfTypingActive = false;
		var welcomeShown = false;

		function logEvent(label, data)
		{
			eventLog.textContent += '[' + new Date().toISOString() + '] ' + label + ' ' + (data ? JSON.stringify(data) : '') + "\n";
		}

		function setWsStatus(text)
		{
			wsStatus.textContent = text;
		}

		function logReactDebug(label, data)
		{
			reactDebugLog.textContent += '[' + new Date().toISOString() + '] ' + label + ' ' + (data ? JSON.stringify(data) : '') + "\n";
		}

		function setBackendStatus(text, mode)
		{
			wsBackendStatus.textContent = text;
			wsControlLight.className = 'chat-ws-light';

			if (mode)
				wsControlLight.classList.add('is-' + mode);
		}

		function addResult(name, ok, details)
		{
			var tr = document.createElement('tr');
			tr.innerHTML = '<td></td><td></td><td></td>';
			tr.children[0].textContent = name;
			tr.children[1].textContent = ok ? 'OK' : 'FAIL';
			tr.children[1].className = ok ? 'chat-test-ok' : 'chat-test-fail';
			tr.children[2].textContent = typeof details === 'string' ? details : JSON.stringify(details || {});
			resultsBody.appendChild(tr);
		}

		function escapeHtml(value)
		{
			return String(value == null ? '' : value)
				.replace(/&/g, '&amp;')
				.replace(/</g, '&lt;')
				.replace(/>/g, '&gt;')
				.replace(/"/g, '&quot;')
				.replace(/'/g, '&#039;');
		}

		function renderUserIdentity(userId, fullName, imageUrl)
		{
			var safeName = escapeHtml(fullName || '');
			var safeUserId = escapeHtml(userId || '');
			var safeImageUrl = String(imageUrl || '').trim();

			if (safeImageUrl)
				return '<img class="chat-user-avatar" src="' + escapeHtml(safeImageUrl) + '" alt="' + safeName + '" title="' + safeName + '" data-userid="' + safeUserId + '">';

			return '<span class="chat-user-fallback" data-userid="' + safeUserId + '">' + safeName + '</span>';
		}

		function renderUserBadge(userId, fullName, imageUrl)
		{
			var safeName = String(fullName || '').trim();
			var initials = safeName ? safeName.split(/\s+/).slice(0, 2).map(function(part){ return part.charAt(0).toUpperCase(); }).join('') : ('U' + String(userId || ''));
			var safeImageUrl = String(imageUrl || '').trim();

			if (safeImageUrl)
				return '<img class="chat-user-avatar" src="' + escapeHtml(safeImageUrl) + '" alt="' + escapeHtml(safeName) + '" title="' + escapeHtml(safeName) + '" data-userid="' + escapeHtml(userId || '') + '">';

			return '<span class="chat-user-fallback" data-userid="' + escapeHtml(userId || '') + '" title="' + escapeHtml(safeName) + '">' + escapeHtml(initials) + '</span>';
		}

		function roomEndpoint(action, extra)
		{
			return $.ajax({
				url: httpEndpoint,
				method: 'GET',
				dataType: 'json',
				data: $.extend({ act: action }, extra || {})
			});
		}

		function attachDebugHandlers(client, name)
		{
			client.on('connect', function(){
				var info = { url: client.getResolvedUrl() };
				logEvent(name + '_connect', info);
				if (name === 'page')
					setWsStatus('connected ' + client.getResolvedUrl());
			});

			client.on('disconnect', function(info){
				logEvent(name + '_disconnect', info || {});
				if (name === 'page') {
					setWsStatus('connection lost, maybe offline');
					roomSubtitle.textContent = 'WebSocket disconnected';
				}
			});

			client.on('error', function(err){
				var info = { message: err && err.message ? err.message : 'socket error', url: client.getResolvedUrl() };
				logEvent(name + '_error', info);
				if (name === 'page')
					setWsStatus('failed to connect ' + client.getResolvedUrl());
			});

			client.on('reconnect_scheduled', function(info){
				logEvent(name + '_reconnect_scheduled', info || {});
				if (name === 'page')
					setWsStatus('connection lost, trying reconnect in ' + Math.round((info.delay_ms || 0) / 1000) + ' secs');
			});

			client.on('reconnect_attempt', function(info){
				logEvent(name + '_reconnect_attempt', info || {});
				if (name === 'page')
					setWsStatus('reconnecting ' + (info.url || ''));
			});
		}

		function waitForEvent(client, action, timeout)
		{
			timeout = timeout || 4000;

			return new Promise(function(resolve, reject){
				var done = false;
				var timer = setTimeout(function(){
					if (done) return;
					done = true;
					reject(new Error('Timeout waiting for ' + action));
				}, timeout);

				client.on('action:' + action, function(packet){
					if (done) return;
					done = true;
					clearTimeout(timer);
					resolve(packet);
				});
			});
		}

		function formatRoomTitle(room)
		{
			if (!room)
				return 'Unknown room';

			if (room.type === 'group')
				return '#' + (room.display_title || room.title || ('room-' + room.id)).replace(/^#+/, '');

			return room.display_title || ('Room #' + room.id);
		}

		function formatShortTime(value)
		{
			if (!value)
				return '';

			var parts = String(value).split(' ');
			return parts[1] ? parts[1].slice(0, 5) : value;
		}

		function scrollToBottom()
		{
			roomMessages.scrollTop = roomMessages.scrollHeight;
		}

		function getFirstMessageId()
		{
			var nodes = roomMessages.querySelectorAll('.chat-room-message[data-message-id]');
			var first = 0;
			for (var i = 0; i < nodes.length; i++) {
				var id = parseInt(nodes[i].getAttribute('data-message-id') || '0', 10) || 0;
				if (!first || id < first)
					first = id;
			}
			return first;
		}

		function getLastMessageId()
		{
			var nodes = roomMessages.querySelectorAll('.chat-room-message[data-message-id]');
			var last = 0;
			for (var i = 0; i < nodes.length; i++) {
				var id = parseInt(nodes[i].getAttribute('data-message-id') || '0', 10) || 0;
				if (id > last)
					last = id;
			}
			return last;
		}

		function normalizeMessage(message)
		{
			return {
				id: parseInt(message.id || message.message_id || '0', 10) || 0,
				room_id: parseInt(message.room_id || '0', 10) || 0,
				sender_id: parseInt(message.sender_id || '0', 10) || 0,
				sender_title: message.sender_title || message.sender_name || message.sender_username || ('User #' + (message.sender_id || '0')),
				message: message.message || '',
				insert_time: message.insert_time || ''
			};
		}

		function appendSystemMessage(text)
		{
			var wrap = document.createElement('div');
			wrap.className = 'chat-room-message is-system';
			wrap.innerHTML = '<div class="chat-room-message-main"><div class="chat-room-message-bubble">' + escapeHtml(text) + '</div></div>';
			roomMessages.appendChild(wrap);
		}

		function createMessageNode(message)
		{
			var isMe = currentUser && parseInt(currentUser.id || '0', 10) === message.sender_id;
			var node = document.createElement('div');
			node.className = 'chat-room-message ' + (isMe ? 'is-me' : 'is-other');
			node.setAttribute('data-message-id', String(message.id || 0));

			var avatarHtml = renderUserIdentity(message.sender_id, message.sender_title, imageUrlCache[message.sender_id] || '');
			node.innerHTML =
				'<div class="chat-room-message-avatar" data-avatar-userid="' + escapeHtml(message.sender_id) + '">' + avatarHtml + '</div>' +
				'<div class="chat-room-message-main">' +
					(isMe ? '' : '<div class="chat-room-message-name">' + escapeHtml(message.sender_title) + '</div>') +
					'<div class="chat-room-message-bubble">' + escapeHtml(message.message) + '</div>' +
					'<span class="chat-room-message-time" title="time:' + escapeHtml(message.insert_time) + '">' + escapeHtml(formatShortTime(message.insert_time)) + '</span>' +
				'</div>';

			if (!isMe)
				hydrateUserAvatar(message.sender_id, message.sender_title, node.querySelector('[data-avatar-userid]'));

			return node;
		}

		function renderMessages(messages, mode)
		{
			messages = messages || [];

			if (mode === 'replace') {
				roomMessages.innerHTML = '';
				messageIds = {};
			}

			if (!messages.length && mode !== 'prepend' && roomEmpty) {
				roomMessages.innerHTML = '<div class="chat-room-empty" id="chatRoomEmpty">No messages yet</div>';
				roomEmpty = document.getElementById('chatRoomEmpty');
				return;
			}

			if (roomEmpty && roomEmpty.parentNode)
				roomEmpty.parentNode.removeChild(roomEmpty);

			var previousHeight = roomMessages.scrollHeight;
			var frag = document.createDocumentFragment();

			for (var i = 0; i < messages.length; i++) {
				var msg = normalizeMessage(messages[i]);

				if (!msg.id || messageIds[msg.id])
					continue;

				messageIds[msg.id] = 1;
				frag.appendChild(createMessageNode(msg));
			}

			if (mode === 'prepend')
				roomMessages.insertBefore(frag, roomMessages.firstChild);
			else
				roomMessages.appendChild(frag);

			if (mode === 'prepend')
				roomMessages.scrollTop = roomMessages.scrollHeight - previousHeight;
			else
				scrollToBottom();
		}

		async function hydrateUserAvatar(userId, fullName, container)
		{
			userId = parseInt(userId || '0', 10) || 0;
			if (!userId || !container)
				return;

			if (typeof imageUrlCache[userId] !== 'undefined') {
				container.innerHTML = renderUserIdentity(userId, fullName, imageUrlCache[userId]);
				return;
			}

			try {
				var url = await $.ajax({
					url: httpEndpoint + '/userimage',
					method: 'GET',
					dataType: 'text',
					data: { id: userId }
				});

				imageUrlCache[userId] = String(url || '').trim();
				container.innerHTML = renderUserIdentity(userId, fullName, imageUrlCache[userId]);
			} catch (err) {
				imageUrlCache[userId] = '';
				container.innerHTML = renderUserIdentity(userId, fullName, '');
			}
		}

		function updatePresenceText()
		{
			var list = [];
			var ids = Object.keys(presenceUsers);

			for (var i = 0; i < ids.length; i++)
				list.push(presenceUsers[ids[i]]);

			list.sort(function(a, b){
				return String(a.name || '').localeCompare(String(b.name || ''));
			});

			if (!list.length) {
				roomPresence.textContent = 'No active users visible in this room right now';
				roomPresenceList.innerHTML = '';
				return;
			}

			var names = [];
			for (var j = 0; j < list.length; j++)
				names.push(list[j].name + (list[j].connection_count > 1 ? ' (' + list[j].connection_count + ')' : ''));

			roomPresence.textContent = 'Online in room: ' + names.join(', ');
			renderPresenceChips(list);
		}

		function renderPresenceChips(list)
		{
			roomPresenceList.innerHTML = '';

			for (var i = 0; i < list.length; i++) {
				var item = list[i];
				var chip = document.createElement('div');
				chip.className = 'chat-room-presence-chip';
				chip.innerHTML =
					'<span class="chat-room-presence-dot"></span>' +
					renderUserBadge(item.id, item.name, item.image_url || '') +
					'<span class="chat-room-presence-name">' + escapeHtml(item.name || item.username || ('User #' + item.id)) + '</span>' +
					((item.connection_count || 0) > 1 ? '<span class="chat-room-presence-count">' + escapeHtml(item.connection_count) + 'x</span>' : '');
				roomPresenceList.appendChild(chip);
			}
		}

		function setTypingUser(userId, name, isTyping)
		{
			userId = parseInt(userId || '0', 10) || 0;
			if (!userId || (currentUser && userId === parseInt(currentUser.id || '0', 10)))
				return;

			if (isTyping)
				typingUsers[userId] = name || ('User #' + userId);
			else
				delete typingUsers[userId];

			var names = [];
			var ids = Object.keys(typingUsers);
			for (var i = 0; i < ids.length; i++)
				names.push(typingUsers[ids[i]]);

			roomTyping.textContent = names.length ? names.join(', ') + ' typing...' : '';
		}

		async function refreshRoomPresence()
		{
			if (!activeRoom)
				return;

			try {
				var resp = await roomEndpoint('doRoomPresence', { room_id: activeRoom.id });
				presenceUsers = {};

				for (var i = 0; i < (resp.online_users || []).length; i++) {
					var item = resp.online_users[i];
					presenceUsers[item.id] = item;
				}

				updatePresenceText();

				if (!welcomeShown) {
					welcomeShown = true;
					appendSystemMessage('Server: joined ' + formatRoomTitle(activeRoom) + ', online now ' + (resp.online_users || []).length + ', history limit ' + (activeRoom.room_history_limit || 0));
					scrollToBottom();
				}
			} catch (err) {
				roomPresence.textContent = 'Presence unavailable';
			}
		}

		async function joinActiveRoom()
		{
			if (!activeRoom || !pageClient || !pageClient.socket || pageClient.socket.readyState !== 1)
				return;

			if (roomJoinInFlight || joinedRoomId === parseInt(activeRoom.id || '0', 10))
				return;

			roomJoinInFlight = true;

			try {
				await pageClient.joinRoom(activeRoom.id);
				joinedRoomId = parseInt(activeRoom.id || '0', 10);
				await refreshRoomPresence();
				roomSubtitle.textContent = 'Room #' + activeRoom.id + ', history limit ' + (activeRoom.room_history_limit || 0);
			} catch (err) {
				logEvent('room_join_failed', { room_id: activeRoom.id, error: err && err.error ? err.error : String(err) });
				roomSubtitle.textContent = 'Room join failed';
			} finally {
				roomJoinInFlight = false;
			}
		}

		async function loadOlderMessages()
		{
			if (!activeRoom || loadingOlder || historyExhausted)
				return;

			var firstMessageId = getFirstMessageId();
			if (!firstMessageId)
				return;

			loadingOlder = true;
			var note = document.createElement('div');
			note.className = 'chat-room-loadmore';
			note.textContent = 'Loading older messages...';
			roomMessages.insertBefore(note, roomMessages.firstChild);

			try {
				var resp = await roomEndpoint('doLoadMessages', {
					room_id: activeRoom.id,
					before_message_id: firstMessageId,
					limit: 100
				});

				if (note.parentNode)
					note.parentNode.removeChild(note);

				if (!(resp.messages || []).length) {
					historyExhausted = true;
					var endNote = document.createElement('div');
					endNote.className = 'chat-room-loadmore';
					endNote.textContent = 'Older history exhausted';
					roomMessages.insertBefore(endNote, roomMessages.firstChild);
				} else {
					renderMessages(resp.messages, 'prepend');
				}
			} catch (err) {
				if (note.parentNode)
					note.parentNode.removeChild(note);
			} finally {
				loadingOlder = false;
			}
		}

		async function bootstrapRoomChat()
		{
			var resp = await roomEndpoint('doRoomBootstrap', { room_id: requestedRoomId });
			activeRoom = resp.room;
			currentUser = resp.current_user || null;
			roomTitle.textContent = formatRoomTitle(activeRoom);
			roomSubtitle.textContent = 'Room #' + activeRoom.id + ', loading presence...';
			historyExhausted = false;
			welcomeShown = false;
			renderMessages(resp.messages || [], 'replace');
			await joinActiveRoom();
			var lastMessageId = getLastMessageId();
			if (lastMessageId && pageClient && pageClient.socket && pageClient.socket.readyState === 1)
				pageClient.markSeen(activeRoom.id, lastMessageId).catch(function(){});
		}

		async function loadReactDebug()
		{
			reactDebugLog.textContent = '';
			logReactDebug('request', { act: 'doReactDebugStatus' });
			setBackendStatus('checking', 'working');

			try {
				var resp = await roomEndpoint('doReactDebugStatus');
				logReactDebug('response', resp);
				addResult('react debug status', !!resp.ok, resp);
				setBackendStatus(resp.ok ? 'running' : 'down', resp.ok ? 'up' : 'down');
				return resp;
			} catch (err) {
				logReactDebug('error', err);
				addResult('react debug status', false, err && err.responseText ? err.responseText : err);
				setBackendStatus('down', 'down');
			}
		}

		async function runReactWsAction(action)
		{
			logReactDebug('request', { act: action });
			setBackendStatus(action.replace('doReact', '').replace('Now', '').toLowerCase() + '...', 'working');

			try {
				var resp = await roomEndpoint(action);
				logReactDebug('action_response', resp);
				addResult('react ws ' + action, !!resp.ok, resp);
				setBackendStatus(resp.ok ? 'running' : 'down', resp.ok ? 'up' : 'down');
				return resp;
			} catch (err) {
				logReactDebug('action_error', err);
				addResult('react ws ' + action, false, err && err.responseText ? err.responseText : err);
				setBackendStatus('down', 'down');
				throw err;
			}
		}

		function ensurePageConnection()
		{
			if (pageClient)
				return pageClient;

			pageClient = new GWChatWSClient({
				url: wsUrl,
				debug: true,
				autoReconnect: true,
				reconnectDelayMs: 5000
			});

			attachDebugHandlers(pageClient, 'page');

			pageClient.on('action:hello', function(packet){
				logEvent('page_hello', packet);
				setWsStatus('connected ' + pageClient.getResolvedUrl());
				roomSubtitle.textContent = activeRoom ? ('Room #' + activeRoom.id + ', websocket connected') : 'WebSocket connected';
				joinActiveRoom();
			});

			pageClient.on('action:error', function(packet){
				logEvent('page_server_error', packet);
				setWsStatus('server rejected: ' + (packet.error || 'unknown error'));
			});

			pageClient.on('action:chat_message', function(packet){
				var msg = normalizeMessage(packet);
				if (!activeRoom || msg.room_id !== parseInt(activeRoom.id || '0', 10))
					return;

				renderMessages([msg], 'append');
				pageClient.markSeen(activeRoom.id, msg.id).catch(function(){});
			});

			pageClient.on('action:chat_typing', function(packet){
				if (activeRoom && parseInt(packet.room_id || '0', 10) === parseInt(activeRoom.id || '0', 10))
					setTypingUser(packet.user_id, packet.user_name, true);
			});

			pageClient.on('action:chat_stop_typing', function(packet){
				if (activeRoom && parseInt(packet.room_id || '0', 10) === parseInt(activeRoom.id || '0', 10))
					setTypingUser(packet.user_id, packet.user_name, false);
			});

			pageClient.on('action:room_user_joined', function(packet){
				if (!activeRoom || parseInt(packet.room_id || '0', 10) !== parseInt(activeRoom.id || '0', 10))
					return;

				refreshRoomPresence();
				appendSystemMessage((packet.user && packet.user.name ? packet.user.name : 'User') + ' joined the room');
				scrollToBottom();
			});

			pageClient.on('action:room_user_left', function(packet){
				if (!activeRoom || parseInt(packet.room_id || '0', 10) !== parseInt(activeRoom.id || '0', 10))
					return;

				refreshRoomPresence();
				appendSystemMessage((packet.user && packet.user.name ? packet.user.name : 'User') + ' left the room');
				scrollToBottom();
			});

			pageClient.on('action:user_offline', function(packet){
				delete presenceUsers[packet && packet.user ? packet.user.id : 0];
				delete typingUsers[packet && packet.user ? packet.user.id : 0];
				updatePresenceText();
				setTypingUser(0, '', false);
			});

			setWsStatus('connecting ' + pageClient.getResolvedUrl());
			pageClient.connect();
			return pageClient;
		}

		async function sendCurrentMessage()
		{
			if (!activeRoom || !pageClient)
				return;

			var text = String(roomInput.value || '').trim();
			if (!text)
				return;

			roomSend.disabled = true;

			try {
				await pageClient.sendMessage(activeRoom.id, text);
				roomInput.value = '';
				roomInput.focus();
				if (selfTypingActive) {
					selfTypingActive = false;
					pageClient.typing(activeRoom.id, false).catch(function(){});
				}
			} catch (err) {
				appendSystemMessage('Send failed: ' + (err && err.error ? err.error : 'unknown error'));
			} finally {
				roomSend.disabled = false;
			}
		}

		function scheduleTypingStop()
		{
			clearTimeout(typingStopTimer);
			typingStopTimer = setTimeout(function(){
				if (!activeRoom || !pageClient || !selfTypingActive)
					return;

				selfTypingActive = false;
				pageClient.typing(activeRoom.id, false).catch(function(){});
			}, 1200);
		}

		$('#runChatProtocolTests').on('click', async function(){
			resultsBody.innerHTML = '';
			eventLog.textContent = '';

			var monitor = new GWChatWSClient({ url: wsUrl, debug: true, autoReconnect: false });
			var actor = new GWChatWSClient({ url: wsUrl, debug: true, autoReconnect: false });

			attachDebugHandlers(monitor, 'monitor');
			attachDebugHandlers(actor, 'actor');
			monitor.on('packet', function(packet){ logEvent('monitor', packet); });
			actor.on('packet', function(packet){ logEvent('actor', packet); });

			try {
				addResult('resolved ws url', true, { requested: wsUrl, resolved: monitor.resolveUrl(wsUrl) });

				var monitorHello = waitForEvent(monitor, 'hello');
				monitor.connect();
				addResult('monitor hello', true, await monitorHello);

				var connectedEvent = waitForEvent(monitor, 'user_connected');
				var actorHello = waitForEvent(actor, 'hello');
				actor.connect();
				addResult('actor hello', true, await actorHello);
				addResult('user connected notification', true, await connectedEvent);

				var roomResp = await $.ajax({
					url: httpEndpoint,
					method: 'POST',
					dataType: 'json',
					data: {
						act: 'doCreateGroupRoom',
						title: 'Protocol Test ' + Date.now(),
						user_ids: [],
						room_history_limit: 100
					}
				});
				var roomId = roomResp.room.id;
				addResult('create room', true, roomResp.room);

				var monitorJoined = await monitor.joinRoom(roomId);
				addResult('monitor join room', true, monitorJoined);

				var roomJoinedEvent = waitForEvent(monitor, 'room_user_joined');
				var actorJoined = await actor.joinRoom(roomId);
				addResult('actor join room', true, actorJoined);
				addResult('room joined notification', true, await roomJoinedEvent);

				var messageEvent = waitForEvent(monitor, 'chat_message');
				var sentAck = await actor.sendMessage(roomId, 'Protocol self test ' + Date.now());
				addResult('send message ack', true, sentAck);
				addResult('receive chat message', true, await messageEvent);

				var typingEvent = waitForEvent(monitor, 'chat_typing');
				await actor.typing(roomId, true);
				addResult('typing notification', true, await typingEvent);

				var typingStopEvent = waitForEvent(monitor, 'chat_stop_typing');
				await actor.typing(roomId, false);
				addResult('typing stop notification', true, await typingStopEvent);

				var seenEvent = waitForEvent(monitor, 'chat_seen');
				await actor.markSeen(roomId, sentAck.message.message_id);
				addResult('seen notification', true, await seenEvent);

				var leftEvent = waitForEvent(monitor, 'room_user_left');
				await actor.leaveRoom(roomId);
				addResult('room left notification', true, await leftEvent);

				var disconnectedEvent = waitForEvent(monitor, 'user_disconnected');
				actor.close();
				addResult('user disconnected notification', true, await disconnectedEvent);

				addResult('user_offline automatic test', false, 'Need another logged-in user or a last-socket observer scenario. Event is implemented server-side but not fully auto-testable from one page with one user session.');

				monitor.close();
			} catch (err) {
				addResult('protocol test', false, err && err.message ? err.message : err);
				logEvent('protocol_test_error', {
					message: err && err.message ? err.message : String(err),
					stack: err && err.stack ? err.stack : ''
				});
			}
		});

		$('#runChatHealthDebug').on('click', function(){
			loadReactDebug();
		});

		$(wsControlToggle).on('click', function(){
			wsControlMenu.classList.toggle('open');
		});

		$(document).on('click', function(e){
			if (!wsControlMenu.contains(e.target) && !wsControlToggle.contains(e.target))
				wsControlMenu.classList.remove('open');
		});

		$('#chatWsControlMenu [data-react-ws-action]').on('click', function(){
			var action = this.getAttribute('data-react-ws-action');
			wsControlMenu.classList.remove('open');
			runReactWsAction(action);
		});

		$('#chatRoomRefreshPresence').on('click', function(){
			refreshRoomPresence();
		});

		$(roomSend).on('click', function(){
			sendCurrentMessage();
		});

		$(roomInput).on('keydown', function(e){
			if (e.key === 'Enter' && !e.shiftKey) {
				e.preventDefault();
				sendCurrentMessage();
			}
		});

		$(roomInput).on('input', function(){
			if (!activeRoom || !pageClient || !pageClient.socket || pageClient.socket.readyState !== 1)
				return;

			var hasText = String(roomInput.value || '').trim() !== '';

			if (hasText && !selfTypingActive) {
				selfTypingActive = true;
				pageClient.typing(activeRoom.id, true).catch(function(){});
			}

			if (!hasText && selfTypingActive) {
				selfTypingActive = false;
				pageClient.typing(activeRoom.id, false).catch(function(){});
			}

			if (hasText)
				scheduleTypingStop();
		});

		$(roomMessages).on('scroll', function(){
			if (roomMessages.scrollTop <= 10)
				loadOlderMessages();
		});

		ensurePageConnection();
		loadReactDebug();
		bootstrapRoomChat().catch(function(err){
			appendSystemMessage('Room bootstrap failed: ' + (err && err.responseJSON && err.responseJSON.error ? err.responseJSON.error : 'unknown error'));
			roomTitle.textContent = 'Room failed to load';
			roomSubtitle.textContent = requestedRoomId ? ('Requested room_id=' + requestedRoomId) : 'Default room bootstrap failed';
		});
	}

	function tryBoot()
	{
		if (window.jQuery && window.GWChatWSClient)
			return startWith(window.jQuery);

		if (typeof require === 'function') {
			try {
				require(['jquery','js/gwchat_ws_client'], function($){
					startWith($ || window.jQuery);
				});
			} catch (e) {
				failBoot('frontend bootstrap require failed', e);
			}
		}

		setTimeout(function(){
			if (!booted && Date.now() - bootStartedAt >= 4000)
				failBoot('frontend bootstrap failed, JS client not loaded in 4s', {
					hasRequire: typeof require === 'function',
					hasJquery: !!window.jQuery,
					hasClient: !!window.GWChatWSClient
				});
		}, 4100);
	}

	tryBoot();
});
</script>
{/capture}

{include file="default_close.tpl"}

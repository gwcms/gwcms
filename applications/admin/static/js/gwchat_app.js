define(['jquery', 'js/gwchat_ws_client'], function($){
	var App = window.GWChatApp || {};
	var reactionOptions = ['❤️', '👍', '😂', '😮', '😢', '🔥'];
	var stateKey = 'gw_admin_chat_dock_state_v1';
	var leaderKey = 'gw_admin_chat_leader_v1';
	var channelName = 'gw_admin_chat_v1';
	var leaderTtlMs = 6000;
	var heartbeatMs = 2000;

	App.opts = App.opts || {};
	App.client = App.client || null;
	App.events = App.events || {};
	App.rooms = App.rooms || {};
	App.joinedRooms = App.joinedRooms || {};
	App.activeRooms = App.activeRooms || {};
	App.windows = App.windows || {};
	App.currentUser = App.currentUser || null;
	App.initialized = App.initialized || false;
	App.dockReady = App.dockReady || false;
	App.knownRoomsLoaded = App.knownRoomsLoaded || false;
	App.bubblePollTimer = App.bubblePollTimer || 0;
	App.bubbleRequest = App.bubbleRequest || null;
	App.bubblePollInterval = App.bubblePollInterval || 0;
	App.tabId = App.tabId || ('tab-' + Date.now() + '-' + Math.random().toString(16).slice(2));
	App.channel = App.channel || null;
	App.crossTabEnabled = typeof window.BroadcastChannel !== 'undefined';
	App.crossTabReady = App.crossTabReady || false;
	App.isLeader = App.isLeader || false;
	App.leaderId = App.leaderId || '';
	App.heartbeatTimer = App.heartbeatTimer || 0;
	App.leaderWatchTimer = App.leaderWatchTimer || 0;
	App.pendingProxy = App.pendingProxy || {};
	App.proxyReqId = App.proxyReqId || 0;
	App.seenState = App.seenState || {};

	App.isWsDebugEnabled = function()
	{
		if (this.opts && this.opts.wssLogToConsole)
			return true;

		try {
			return localStorage.getItem('gwchat_ws_debug') === '1'
				|| /(?:^|[?&])gwchat_ws_debug=1(?:&|$)/.test(location.search || '');
		} catch (e) {
			return false;
		}
	};

	App.debugLog = function(label, data)
	{
		if (!this.isWsDebugEnabled() || !window.console)
			return;

		if (typeof data === 'undefined')
			console.log('[GWChatApp]', label);
		else
			console.log('[GWChatApp]', label, data);
	};

	function escapeHtml(value)
	{
		return String(value == null ? '' : value)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#039;');
	}

	function linkifyText(value)
	{
		var text = String(value == null ? '' : value);
		var pattern = /\b((?:https?:\/\/|www\.)[^\s<]+)/gi;
		var out = '';
		var lastIndex = 0;
		var match;

		while ((match = pattern.exec(text)) !== null) {
			var raw = match[0];
			var href = raw;
			var suffix = '';

			while (/[.,!?;:)]+$/.test(href)) {
				suffix = href.slice(-1) + suffix;
				href = href.slice(0, -1);
			}

			out += escapeHtml(text.slice(lastIndex, match.index));
			href = href.replace(/&amp;/gi, '&');
			var url = /^www\./i.test(href) ? ('https://' + href) : href;
			var label = href.length > 58 ? (href.slice(0, 34) + '...' + href.slice(-18)) : href;
			out += '<a href="' + escapeHtml(url) + '" target="_blank" rel="noopener noreferrer" class="gwchat-link" data-full-url-label="' + escapeHtml(href) + '" data-short-url-label="' + escapeHtml(label) + '">' + escapeHtml(label) + '</a>' + escapeHtml(suffix);
			lastIndex = match.index + raw.length;
		}

		out += escapeHtml(text.slice(lastIndex));
		return out;
	}

	function shortTime(value)
	{
		value = String(value || '');
		var parts = value.split(' ');
		return parts[1] ? parts[1].slice(0, 5) : value;
	}

	function normalizeEntry(message)
	{
		message = message || {};
		var entryType = String(message.entry_type || '').toLowerCase();

		if (entryType === 'event' || message.event_id || message.event_type) {
			return {
				entry_type: 'event',
				entry_key: message.entry_key || ('e' + (parseInt(message.event_id || '0', 10) || 0)),
				event_id: parseInt(message.event_id || '0', 10) || 0,
				room_id: parseInt(message.room_id || '0', 10) || 0,
				user_id: parseInt(message.user_id || '0', 10) || 0,
				event_type: String(message.event_type || ''),
				text: message.text || '',
				insert_time: message.insert_time || ''
			};
		}

		return {
			entry_type: 'message',
			entry_key: message.entry_key || ('m' + (parseInt(message.id || message.message_id || '0', 10) || 0)),
			id: parseInt(message.id || message.message_id || '0', 10) || 0,
			room_id: parseInt(message.room_id || '0', 10) || 0,
			sender_id: parseInt(message.sender_id || '0', 10) || 0,
			sender_title: message.sender_title || message.sender_name || message.sender_username || ('User #' + (message.sender_id || '0')),
			message: message.message || '',
			is_seen: parseInt(message.is_seen || '0', 10) || 0,
			reactions: $.isArray(message.reactions) ? message.reactions : [],
			attachments: $.isArray(message.attachments) ? message.attachments : [],
			insert_time: message.insert_time || '',
			_highlight_unread: message._highlight_unread ? 1 : 0
		};
	}

	function roomTitle(room)
	{
		if (!room)
			return 'Chat';

		if (room.type === 'group')
			return '#' + String(room.display_title || room.title || ('room-' + room.id)).replace(/^#+/, '');

		if (room.display_user && room.display_user.name)
			return room.display_user.name;

		return room.display_title || ('Chat #' + room.id);
	}

	function avatarHtml(room)
	{
		var user = room && room.display_user ? room.display_user : null;
		var name = user && user.name ? user.name : roomTitle(room);
		var imageUrl = user && user.image_url ? String(user.image_url) : '';
		var initials = String(name || '?').split(/\s+/).slice(0, 2).map(function(part){
			return part ? part.charAt(0).toUpperCase() : '';
		}).join('') || '?';

		if (imageUrl)
			return '<img src="' + escapeHtml(imageUrl) + '" alt="' + escapeHtml(name) + '">';

		return '<span>' + escapeHtml(initials) + '</span>';
	}

	function activeAgo(room)
	{
		var user = room && room.display_user ? room.display_user : null;
		return user && user.last_request_ago ? ('active ' + user.last_request_ago + ' ago') : '';
	}

	function safeRoomUrl(room)
	{
		if (room && room.room_url)
			return room.room_url;

		return App.opts.httpEndpoint + '/room?id=' + encodeURIComponent(room.id) + '&room_type=' + encodeURIComponent(room.type || 'private');
	}

	function http(action, data)
	{
		var started = Date.now();
		var payload = $.extend({ act: action }, data || {});
		if (App.isWsDebugEnabled())
			payload.gwchat_debug = 1;

		return $.ajax({
			url: App.opts.httpEndpoint,
			method: 'GET',
			dataType: 'json',
			data: payload
		}).done(function(resp){
			App.debugLog('http_done', {
				action: action,
				elapsed_ms: Date.now() - started,
				server_timing: resp && resp._debug_timing ? resp._debug_timing : null
			});
		}).fail(function(xhr, status, err){
			App.debugLog('http_fail', {
				action: action,
				elapsed_ms: Date.now() - started,
				status: status,
				error: err || ''
			});
		});
	}

	function httpForm(action, formData)
	{
		formData = formData || new FormData();
		formData.append('act', action);

		return $.ajax({
			url: App.opts.httpEndpoint,
			method: 'POST',
			dataType: 'json',
			data: formData,
			processData: false,
			contentType: false
		});
	}

	function renderAttachments(attachments, compact)
	{
		attachments = $.isArray(attachments) ? attachments : [];
		if (!attachments.length)
			return '';

		var out = '<div class="' + (compact ? 'gwchat-attachments' : 'chat-room-attachments') + '">';
		attachments.forEach(function(file){
			var kind = String(file.kind || 'file');
			var url = String(file.public_url || '');
			var thumb = String(file.thumb_url || url);
			var name = String(file.original_filename || file.stored_filename || 'file');
			var size = String(file.size_human || '');
			if (kind === 'image' && thumb) {
				out += '<a class="' + (compact ? 'gwchat-attachment-image' : 'chat-room-attachment-image') + '" href="' + escapeHtml(url || thumb) + '" target="_blank" rel="noopener noreferrer">' +
					'<img src="' + escapeHtml(thumb) + '" alt="' + escapeHtml(name) + '">' +
				'</a>';
			} else if (url) {
				out += '<a class="' + (compact ? 'gwchat-attachment-file' : 'chat-room-attachment-file') + '" href="' + escapeHtml(url) + '" target="_blank" rel="noopener noreferrer">' +
					'<span>📎</span><span>' + escapeHtml(name) + (size ? ' <small>' + escapeHtml(size) + '</small>' : '') + '</span>' +
				'</a>';
			}
		});
		out += '</div>';

		return out;
	}

	function loadState()
	{
		try {
			return JSON.parse(localStorage.getItem(stateKey) || '{}') || {};
		} catch (e) {
			return {};
		}
	}

	function saveState()
	{
		var out = { windows: {} };
		Object.keys(App.windows).forEach(function(roomId){
			var win = App.windows[roomId];
			if (!win)
				return;

			out.windows[roomId] = {
				room_id: parseInt(roomId, 10) || 0,
				minimized: !!win.minimized,
				closed: !!win.closed,
				maximized: !!win.maximized,
				room_url: win.room ? safeRoomUrl(win.room) : (win.roomUrl || '')
			};
		});

		try {
			localStorage.setItem(stateKey, JSON.stringify(out));
		} catch (e) {}
	}

	App.on = function(event, callback)
	{
		if (!this.events[event])
			this.events[event] = [];

		this.events[event].push(callback);
		return this;
	};

	App.emit = function(event, payload)
	{
		var list = this.events[event] || [];
		for (var i = 0; i < list.length; i++)
			list[i](payload);
	};

	App.postChannel = function(message)
	{
		if (!this.channel)
			return;

		this.channel.postMessage($.extend({
			from: this.tabId,
			ts: Date.now()
		}, message || {}));
	};

	App.setupCrossTab = function()
	{
		var app = this;

		if (!this.crossTabEnabled || this.crossTabReady)
			return;

		this.crossTabReady = true;
		this.channel = new BroadcastChannel(channelName);
		this.channel.onmessage = function(e){
			app.handleChannelMessage(e.data || {});
		};

		window.addEventListener('pagehide', function(){
			if (app.isLeader) {
				app.postChannel({ type: 'leader_leaving' });
				app.clearLeader(app.tabId);
			}
		});

		this.claimLeadershipIfNeeded();
		this.leaderWatchTimer = setInterval(function(){
			app.claimLeadershipIfNeeded();
		}, 2500);
	};

	App.readLeader = function()
	{
		try {
			return JSON.parse(localStorage.getItem(leaderKey) || '{}') || {};
		} catch (e) {
			return {};
		}
	};

	App.writeLeader = function(tabId)
	{
		try {
			localStorage.setItem(leaderKey, JSON.stringify({
				tabId: tabId,
				expiresAt: Date.now() + leaderTtlMs
			}));
		} catch (e) {}
	};

	App.clearLeader = function(tabId)
	{
		try {
			var leader = this.readLeader();
			if (!tabId || !leader.tabId || leader.tabId === tabId)
				localStorage.removeItem(leaderKey);
		} catch (e) {}

		if (!tabId || this.leaderId === tabId)
			this.leaderId = '';
	};

	App.claimLeadershipIfNeeded = function(force)
	{
		if (!this.crossTabEnabled)
			return;

		var leader = this.readLeader();
		var now = Date.now();

		if (!force && leader.tabId && leader.expiresAt > now && leader.tabId !== this.tabId) {
			this.leaderId = leader.tabId;
			if (this.isLeader)
				this.becomeFollower(leader.tabId);
			return;
		}

		this.writeLeader(this.tabId);
		leader = this.readLeader();

		if (leader.tabId === this.tabId)
			this.becomeLeader();
	};

	App.hasFreshLeader = function()
	{
		if (!this.crossTabEnabled)
			return false;

		var leader = this.readLeader();
		var fresh = !!(leader.tabId && leader.expiresAt && leader.expiresAt > Date.now());
		if (fresh)
			this.leaderId = leader.tabId;

		return fresh;
	};

	App.becomeLeader = function()
	{
		var app = this;

		if (this.isLeader)
			return;

		this.debugLog('become_leader', { tabId: this.tabId });
		this.isLeader = true;
		this.leaderId = this.tabId;
		this.client = null;
		this.createRealClient();

		clearInterval(this.heartbeatTimer);
		this.heartbeatTimer = setInterval(function(){
			app.writeLeader(app.tabId);
			app.postChannel({ type: 'leader_heartbeat' });
		}, heartbeatMs);

		this.writeLeader(this.tabId);
		this.postChannel({ type: 'leader_claim' });
		this.loadBubbleData();
		if (this.bubblePollInterval)
			this.startBubblePolling(this.bubblePollInterval);
	};

	App.becomeFollower = function(leaderId)
	{
		this.debugLog('become_follower', { tabId: this.tabId, leaderId: leaderId || this.leaderId });
		this.isLeader = false;
		this.leaderId = leaderId || this.leaderId;
		clearInterval(this.heartbeatTimer);
		clearInterval(this.bubblePollTimer);
		this.bubblePollTimer = 0;

		if (this.client && this.client.__gwRealClient) {
			this.client.close();
			this.client = null;
		}

		if (!this.client)
			this.client = this.createProxyClient();
	};

	App.handleChannelMessage = function(message)
	{
		if (!message || message.from === this.tabId)
			return;

		if (message.type === 'leader_heartbeat' || message.type === 'leader_claim') {
			this.leaderId = message.from;
			if (this.isLeader && message.from !== this.tabId)
				this.becomeFollower(message.from);
			return;
		}

		if (message.type === 'leader_leaving') {
			if (!this.isLeader && (!this.leaderId || this.leaderId === message.from)) {
				this.clearLeader(message.from);
				setTimeout(this.claimLeadershipIfNeeded.bind(this, true), 80 + Math.floor(Math.random() * 220));
			}
			return;
		}

		if (message.type === 'bubble_data') {
			this.ingestRooms(message.data && message.data.rooms || []);
			this.emit('bubbleData', message.data || {});
			this.emit('rooms', message.data || {});
			return;
		}

		if (message.type === 'chat_packet')
			return this.handleRemotePacket(message.event, message.payload);

		if (message.type === 'proxy_response' && (!message.to || message.to === this.tabId) && this.pendingProxy[message.req_id]) {
			var pending = this.pendingProxy[message.req_id];
			delete this.pendingProxy[message.req_id];
			if (message.ok)
				pending.resolve(message.payload);
			else
				pending.reject(message.payload);
			return;
		}

		if (message.type === 'proxy_request' && this.isLeader)
			this.handleProxyRequest(message);
	};

	App.handleRemotePacket = function(event, payload)
	{
		this.debugLog('remote_packet', { event: event, payload: payload });

		if (event === 'hello') {
			this.currentUser = payload.user || this.currentUser;
			this.emit('action:hello', payload);
			this.emit('hello', payload);
		} else if (event === 'message') {
			this.emit('action:chat_message', payload);
			this.emit('message', payload);
			this.handleIncomingMessage(payload);
		} else if (event === 'event') {
			this.emit('action:chat_event', { event: payload });
			this.emit('event', payload);
			if (this.windows[payload.room_id])
				this.windows[payload.room_id].appendEntries([payload], 'append');
		} else if (event === 'reaction') {
			this.emit('action:chat_reaction_update', payload);
			this.emit('reaction', payload);
			Object.keys(this.windows).forEach(function(roomId){
				App.windows[roomId].updateReactions(payload.message_id, payload.reactions || []);
			});
		} else if (event === 'seen') {
			this.emit('action:chat_seen', payload);
			this.emit('seen', payload);
			if (this.windows[payload.room_id])
				this.windows[payload.room_id].updateSeen(payload.last_message_id, payload.user_id);
		} else if (event === 'typing') {
			this.emit('action:chat_typing', payload);
			this.emit('typing', payload);
			if (this.windows[payload.room_id])
				this.windows[payload.room_id].setTyping(payload.user_id, payload.user_name, event === 'typing');
		} else if (event === 'stop_typing') {
			this.emit('action:chat_stop_typing', payload);
			this.emit('typing', payload);
			if (this.windows[payload.room_id])
				this.windows[payload.room_id].setTyping(payload.user_id, payload.user_name, false);
		} else if (event === 'connect' || event === 'disconnect') {
			this.emit(event, payload);
		}
	};

	App.proxyCommand = function(command, args)
	{
		var app = this;
		var reqId = ++this.proxyReqId;
		var leaderFresh = this.hasFreshLeader();
		this.debugLog('proxy_command', {
			command: command,
			args: args || [],
			isLeader: this.isLeader,
			leaderId: this.leaderId,
			leaderFresh: leaderFresh
		});

		if (!leaderFresh || !this.leaderId)
			this.claimLeadershipIfNeeded(true);

		if (this.isLeader)
			return this.runLocalCommand(command, args || []);

		return new Promise(function(resolve, reject){
			app.pendingProxy[reqId] = { resolve: resolve, reject: reject };
			app.postChannel({
				type: 'proxy_request',
				to: app.leaderId,
				req_id: reqId,
				command: command,
				args: args || []
			});

			setTimeout(function(){
				if (!app.pendingProxy[reqId])
					return;

				delete app.pendingProxy[reqId];
				app.debugLog('proxy_timeout_takeover', {
					command: command,
					leaderId: app.leaderId
				});
				app.claimLeadershipIfNeeded(true);
				if (app.isLeader) {
					Promise.resolve(app.runLocalCommand(command, args || [])).then(resolve).catch(reject);
					return;
				}
				reject({ ok: 0, error: 'Chat leader timeout' });
			}, 1200);
		});
	};

	App.runLocalCommand = function(command, args)
	{
		args = args || [];

		if (command === 'loadBubbleData')
			return this.loadBubbleData();
		if (command === 'openPrivateHttp')
			return http('doOpenPrivateRoom', { user_id: args[0] });
		if (command === 'markSeen')
			return this.markSeen(args[0], args[1]);
		if (command === 'sendMessage') {
			var client = this.createRealClient();
			if (client && client.socket && client.socket.readyState === 1)
				return client.sendMessage(args[0], args[1], args[2] || {});

			return http('doSendMessage', { room_id: args[0], message: args[1] });
		}
		if (command === 'typing') {
			var typingClient = this.createRealClient();
			if (typingClient && typingClient.socket && typingClient.socket.readyState === 1)
				return typingClient.typing(args[0], args[1]);

			return Promise.resolve({ ok: 1, skipped: 1 });
		}
		if (command === 'joinRoom') {
			this.pendingJoinRooms = this.pendingJoinRooms || {};
			this.pendingJoinRooms[parseInt(args[0] || '0', 10) || 0] = 1;
			var joinClient = this.createRealClient();
			if (joinClient && joinClient.socket && joinClient.socket.readyState === 1)
				return joinClient.joinRoom(args[0]);

			return Promise.resolve({ ok: 1, pending: 1 });
		}

		var leaderClient = this.createRealClient();
		if (leaderClient && leaderClient.socket && leaderClient.socket.readyState === 1 && typeof leaderClient[command] === 'function')
			return leaderClient[command].apply(leaderClient, args);

		return Promise.reject({ ok: 0, error: 'WebSocket not connected' });
	};

	App.handleProxyRequest = function(message)
	{
		if (message.to && message.to !== this.tabId)
			return;

		var client = this.createRealClient();
		var command = message.command;
		var args = message.args || [];

		if (command === 'loadBubbleData') {
			this.loadBubbleData().then(function(resp){
				App.postChannel({ type: 'proxy_response', to: message.from, req_id: message.req_id, ok: 1, payload: resp });
			}).catch(function(err){
				App.postChannel({ type: 'proxy_response', to: message.from, req_id: message.req_id, ok: 0, payload: err });
			});
			return;
		}

		if (command === 'openPrivateHttp') {
			http('doOpenPrivateRoom', { user_id: args[0] }).then(function(resp){
				App.postChannel({ type: 'proxy_response', to: message.from, req_id: message.req_id, ok: 1, payload: resp });
			}).catch(function(err){
				App.postChannel({ type: 'proxy_response', to: message.from, req_id: message.req_id, ok: 0, payload: err });
			});
			return;
		}

		if (command === 'markSeen') {
			this.markSeen(args[0], args[1]).then(function(resp){
				App.postChannel({ type: 'proxy_response', to: message.from, req_id: message.req_id, ok: 1, payload: resp });
			}).catch(function(err){
				App.postChannel({ type: 'proxy_response', to: message.from, req_id: message.req_id, ok: 0, payload: err });
			});
			return;
		}

		if (!client || typeof client[command] !== 'function') {
			this.postChannel({ type: 'proxy_response', to: message.from, req_id: message.req_id, ok: 0, payload: { error: 'Unsupported command' } });
			return;
		}

		Promise.resolve(client[command].apply(client, args)).then(function(resp){
			App.postChannel({ type: 'proxy_response', to: message.from, req_id: message.req_id, ok: 1, payload: resp });
		}).catch(function(err){
			App.postChannel({ type: 'proxy_response', to: message.from, req_id: message.req_id, ok: 0, payload: err });
		});
	};

	App.init = function(opts)
	{
		this.opts = $.extend(this.opts || {}, opts || {});
		this.ensureDock();
		this.setupCrossTab();
		this.ensureClient();

		if (!this.initialized) {
			this.initialized = true;
			this.restoreDockState();
		}

		return this;
	};

	App.ensureClient = function()
	{
		if (this.client)
			return this.client;

		if (this.crossTabEnabled && !this.isLeader)
			return this.client = this.createProxyClient();

		return this.createRealClient();
	};

	App.createProxyClient = function()
	{
		var app = this;
		this.debugLog('create_proxy_client', { leaderId: this.leaderId });
		return {
			__gwProxyClient: true,
			socket: { readyState: 1 },
			getResolvedUrl: function(){ return 'cross-tab leader ' + (app.leaderId || 'pending'); },
			on: function(event, callback){ app.on(event, callback); return this; },
			joinRoom: function(roomId){ return app.proxyCommand('joinRoom', [roomId]); },
			sendMessage: function(roomId, message, opts){ return app.proxyCommand('sendMessage', [roomId, message, opts || {}]); },
			typing: function(roomId, typing){ return app.proxyCommand('typing', [roomId, typing]); },
			markSeen: function(roomId, lastMessageId){ return app.proxyCommand('markSeen', [roomId, lastMessageId]); },
			toggleReaction: function(messageId, reaction){ return app.proxyCommand('toggleReaction', [messageId, reaction]); },
			openPrivateRoom: function(userId){ return app.proxyCommand('openPrivateRoom', [userId]); },
			loadMessages: function(roomId, beforeMessageId, limit, afterMessageId){ return app.proxyCommand('loadMessages', [roomId, beforeMessageId, limit, afterMessageId]); },
			getMyRooms: function(){ return app.proxyCommand('getMyRooms', []); }
		};
	};

	App.createRealClient = function()
	{
		var app = this;

		if (this.client && this.client.__gwRealClient)
			return this.client;

		this.debugLog('create_real_client', { wsUrl: this.opts.wsUrl });
		this.client = new GWChatWSClient({
			url: this.opts.wsUrl,
			debug: this.isWsDebugEnabled(),
			autoReconnect: true,
			reconnectDelayMs: 5000
		});
		this.client.__gwRealClient = true;

		this.client.on('action:hello', function(packet){
			app.currentUser = packet.user || app.currentUser;
			app.syncRooms();
			app.emit('hello', packet);
			app.postChannel({ type: 'chat_packet', event: 'hello', payload: packet });
		});

		this.client.on('connect', function(){
			app.debugLog('ws_connect', { url: app.client ? app.client.getResolvedUrl() : '' });
			app.emit('connect');
			app.postChannel({ type: 'chat_packet', event: 'connect', payload: {} });
			if (app.pendingJoinRooms) {
				Object.keys(app.pendingJoinRooms).forEach(function(roomId){
					delete app.pendingJoinRooms[roomId];
					app.joinRoom(roomId);
				});
			}
		});

		this.client.on('disconnect', function(info){
			app.debugLog('ws_disconnect', info);
			app.emit('disconnect', info);
			app.postChannel({ type: 'chat_packet', event: 'disconnect', payload: info });
		});

		this.client.on('error', function(err){
			app.debugLog('ws_error', err);
		});

		this.client.on('reconnect_scheduled', function(info){
			app.debugLog('ws_reconnect_scheduled', info);
		});

		this.client.on('reconnect_attempt', function(info){
			app.debugLog('ws_reconnect_attempt', info);
		});

		this.client.on('action:chat_message', function(packet){
			var msg = normalizeEntry(packet);
			app.emit('message', msg);
			app.handleIncomingMessage(msg);
			app.postChannel({ type: 'chat_packet', event: 'message', payload: msg });
		});

		this.client.on('action:chat_event', function(packet){
			var entry = normalizeEntry(packet.event || {});
			app.emit('event', entry);
			if (App.windows[entry.room_id])
				App.windows[entry.room_id].appendEntries([entry], 'append');
			app.postChannel({ type: 'chat_packet', event: 'event', payload: entry });
		});

		this.client.on('action:chat_reaction_update', function(packet){
			app.emit('reaction', packet);
			Object.keys(App.windows).forEach(function(roomId){
				App.windows[roomId].updateReactions(packet.message_id, packet.reactions || []);
			});
			app.postChannel({ type: 'chat_packet', event: 'reaction', payload: packet });
		});

		this.client.on('action:chat_seen', function(packet){
			app.emit('seen', packet);
			if (App.windows[packet.room_id])
				App.windows[packet.room_id].updateSeen(packet.last_message_id, packet.user_id);
			app.postChannel({ type: 'chat_packet', event: 'seen', payload: packet });
		});

		this.client.on('action:chat_typing', function(packet){
			app.emit('typing', packet);
			if (App.windows[packet.room_id])
				App.windows[packet.room_id].setTyping(packet.user_id, packet.user_name, true);
			app.postChannel({ type: 'chat_packet', event: 'typing', payload: packet });
		});

		this.client.on('action:chat_stop_typing', function(packet){
			app.emit('typing', packet);
			if (App.windows[packet.room_id])
				App.windows[packet.room_id].setTyping(packet.user_id, packet.user_name, false);
			app.postChannel({ type: 'chat_packet', event: 'stop_typing', payload: packet });
		});

		this.client.connect(this.opts.wsUrl);
		return this.client;
	};

	App.getClient = function()
	{
		return this.ensureClient();
	};

	App.syncRooms = function()
	{
		return this.loadBubbleData();
	};

	App.loadBubbleData = function()
	{
		var app = this;
		var started = Date.now();

		if (this.crossTabEnabled && !this.isLeader)
			return this.proxyCommand('loadBubbleData', []);

		if (this.bubbleRequest) {
			this.debugLog('bubble_data_reuse_inflight', {});
			return this.bubbleRequest;
		}

		this.debugLog('bubble_data_start', {});
		this.bubbleRequest = http('doChatBubbleData').done(function(resp){
			app.debugLog('bubble_data_done', {
				elapsed_ms: Date.now() - started,
				rooms: resp && resp.rooms ? resp.rooms.length : 0,
				online_users: resp && resp.online_users ? resp.online_users.length : 0,
				unread_total: resp ? resp.unread_total : null,
				server_timing: resp && resp._debug_timing ? resp._debug_timing : null
			});
			app.ingestRooms(resp.rooms || []);
			app.emit('bubbleData', resp);
			app.emit('rooms', resp);
			app.postChannel({ type: 'bubble_data', data: resp });
		}).fail(function(xhr, status, err){
			app.debugLog('bubble_data_fail', {
				elapsed_ms: Date.now() - started,
				status: status,
				error: err || ''
			});
		}).always(function(){
			app.bubbleRequest = null;
		});

		return this.bubbleRequest;
	};

	App.markSeen = function(roomId, messageId)
	{
		var app = this;
		roomId = parseInt(roomId || '0', 10) || 0;
		messageId = parseInt(messageId || '0', 10) || 0;

		if (!roomId || !messageId)
			return Promise.reject({ ok: 0, error: 'Bad markSeen request' });

		var key = String(roomId);
		var state = this.seenState[key] || (this.seenState[key] = {
			lastDone: 0,
			inFlight: 0,
			pending: 0,
			promise: null
		});
		var maxKnown = Math.max(state.lastDone || 0, state.inFlight || 0, state.pending || 0);

		if (messageId <= maxKnown)
			return state.promise || Promise.resolve({ ok: 1, skipped: 1, room_id: roomId, last_message_id: messageId });

		state.pending = Math.max(state.pending || 0, messageId);

		if (state.promise)
			return state.promise;

		state.promise = new Promise(function(resolve, reject){
			var sendPending = function(){
				var sendId = parseInt(state.pending || '0', 10) || 0;
				state.pending = 0;

				if (!sendId) {
					state.promise = null;
					resolve({ ok: 1, skipped: 1, room_id: roomId });
					return;
				}

				state.inFlight = sendId;
				app.debugLog('mark_seen_send', { room_id: roomId, last_message_id: sendId });

				var client = app.getClient();
				var req = client && client.socket && client.socket.readyState === 1 ?
					client.markSeen(roomId, sendId) :
					http('doMarkSeen', { room_id: roomId, last_message_id: sendId });

				Promise.resolve(req).then(function(resp){
					state.lastDone = Math.max(state.lastDone || 0, sendId);
					state.inFlight = 0;

					if (state.pending && state.pending > state.lastDone) {
						setTimeout(sendPending, 30);
						return;
					}

					state.promise = null;
					resolve(resp);
				}).catch(function(err){
					state.inFlight = 0;
					state.promise = null;
					reject(err);
				});
			};

			setTimeout(sendPending, 30);
		});

		return state.promise;
	};

	App.loadMessagesAfter = function(roomId, afterMessageId, limit)
	{
		return http('doLoadMessages', {
			room_id: roomId,
			after_message_id: afterMessageId || 0,
			limit: limit || 100
		});
	};

	App.shouldRecoverGap = function(lastKnownMessageId, msg)
	{
		var prevRoomMessageId = parseInt(msg && msg.prev_room_message_id || '0', 10) || 0;
		lastKnownMessageId = parseInt(lastKnownMessageId || '0', 10) || 0;

		return !!(prevRoomMessageId && prevRoomMessageId !== lastKnownMessageId);
	};

	App.startBubblePolling = function(intervalMs)
	{
		var app = this;
		intervalMs = parseInt(intervalMs || '0', 10) || 20000;
		this.bubblePollInterval = this.bubblePollInterval ? Math.min(this.bubblePollInterval, intervalMs) : intervalMs;

		if (this.crossTabEnabled && !this.isLeader)
			return;

		if (this.bubblePollTimer && this.bubblePollInterval <= intervalMs)
			return;

		clearInterval(this.bubblePollTimer);
		this.bubblePollTimer = setInterval(function(){
			app.loadBubbleData();
		}, this.bubblePollInterval);
	};

	App.ingestRooms = function(rooms)
	{
		rooms = rooms || [];
		for (var i = 0; i < rooms.length; i++) {
			var wasKnown = !!this.rooms[rooms[i].id];
			this.rooms[rooms[i].id] = rooms[i];
			if (this.isLeader || !this.crossTabEnabled)
				this.joinRoom(rooms[i].id);

			if (this.windows[rooms[i].id])
				this.windows[rooms[i].id].recoverIfBehindRoom(rooms[i]);

			if (this.knownRoomsLoaded && !wasKnown && rooms[i].type === 'private' && (parseInt(rooms[i].bubble_unread_count || rooms[i].unread_count || '0', 10) > 0))
				this.openRoom(rooms[i].id, { room: rooms[i] });
		}
		this.knownRoomsLoaded = true;
	};

	App.joinRoom = function(roomId)
	{
		roomId = parseInt(roomId || '0', 10) || 0;
		if (!roomId || this.joinedRooms[roomId] || !this.client || !this.client.socket || this.client.socket.readyState !== 1)
			return;

		this.joinedRooms[roomId] = 1;
		this.client.joinRoom(roomId).catch(function(){
			delete App.joinedRooms[roomId];
		});
	};

	App.openPrivate = function(userId)
	{
		userId = parseInt(userId || '0', 10) || 0;
		if (!userId)
			return $.Deferred().reject().promise();

		var req = this.crossTabEnabled && !this.isLeader ?
			this.proxyCommand('openPrivateHttp', [userId]) :
			http('doOpenPrivateRoom', { user_id: userId });

		return req.then(function(resp){
			var room = resp.room || {};
			room.room_url = resp.room_url || room.room_url;
			App.rooms[room.id] = room;
			App.openRoom(room.id, { room: room });
		});
	};

	App.openRoom = function(roomId, opts)
	{
		roomId = parseInt(roomId || '0', 10) || 0;
		opts = opts || {};

		if (!roomId)
			return null;

		this.ensureDock();
		this.joinRoom(roomId);

		if (this.windows[roomId]) {
			this.windows[roomId].closed = false;
			if (!this.windows[roomId].node || !this.windows[roomId].node.closest('body').length) {
				this.windows[roomId].mount();
				this.windows[roomId].maximized = false;
			}
			this.windows[roomId].setMinimized(false);
			this.windows[roomId].focus();
			saveState();
			return this.windows[roomId];
		}

		var win = new ChatDockWindow(roomId, opts.room || this.rooms[roomId] || null, opts);
		this.windows[roomId] = win;
		win.mount();
		win.bootstrap();

		if (opts.minimized)
			win.setMinimized(true);

		saveState();
		return win;
	};

	App.handleIncomingMessage = function(msg)
	{
		var ownId = this.currentUser ? parseInt(this.currentUser.id || '0', 10) : 0;
		var win = this.windows[msg.room_id] || null;

		if (win) {
			win.handleIncomingMessage(msg);
			return;
		}

		if (this.activeRooms[msg.room_id])
			return;

		if (msg.sender_id && ownId && msg.sender_id !== ownId)
			this.openRoom(msg.room_id, { auto: true });
	};

	App.setActiveRoom = function(roomId, isActive)
	{
		roomId = parseInt(roomId || '0', 10) || 0;
		if (!roomId)
			return;

		if (isActive)
			this.activeRooms[roomId] = 1;
		else
			delete this.activeRooms[roomId];
	};

	App.closeDockRoom = function(roomId)
	{
		roomId = parseInt(roomId || '0', 10) || 0;
		if (!roomId || !this.windows[roomId])
			return;

		this.windows[roomId].close();
	};

	App.suppressDockRoom = function(roomId)
	{
		roomId = parseInt(roomId || '0', 10) || 0;
		if (!roomId || !this.windows[roomId])
			return;

		this.windows[roomId].suspendForMaximize();
	};

	App.ensureDock = function()
	{
		if (this.dockReady)
			return;

		this.dockReady = true;
		if (!this.focusSeenReady) {
			this.focusSeenReady = true;
			var app = this;
			var scheduleVisibleSeen = function(){
				if (document.visibilityState && document.visibilityState !== 'visible')
					return;

				Object.keys(app.windows || {}).forEach(function(roomId){
					var win = app.windows[roomId];
					if (!win || win.closed)
						return;

					win.seenInteraction = true;
					win.scheduleMarkSeen();
				});
			};
			window.addEventListener('focus', scheduleVisibleSeen);
			document.addEventListener('visibilitychange', scheduleVisibleSeen);
		}
		if (!document.getElementById('gwchatDock')) {
			$('body').append(
				'<div id="gwchatDock" class="gwchat-dock"></div>' +
				'<style>' +
				'.gwchat-dock{position:fixed;right:18px;bottom:0;z-index:9000;display:flex;align-items:flex-end;gap:10px;max-width:calc(100vw - 36px);pointer-events:none}' +
				'.gwchat-window{width:309px;height:397px;background:#fff;border:1px solid #b8c5d6;border-radius:12px 12px 0 0;box-shadow:0 14px 34px rgba(15,23,42,.24);display:flex;flex-direction:column;overflow:hidden;pointer-events:auto;animation:gwchatSlideUp .18s ease-out}' +
				'.gwchat-window.is-minimized{height:46px}' +
				'.gwchat-window.is-minimized .gwchat-body,.gwchat-window.is-minimized .gwchat-composer{display:none}' +
				'.gwchat-header{height:46px;display:flex;align-items:center;gap:8px;padding:7px 8px;background:#172033;color:#fff;cursor:pointer;flex:0 0 46px}' +
				'.gwchat-avatar{width:32px;height:32px;border-radius:50%;overflow:hidden;flex:0 0 32px;background:#dbeafe;color:#1e40af;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700}' +
				'.gwchat-avatar img{width:32px;height:32px;object-fit:cover;display:block}' +
				'.gwchat-title{min-width:0;flex:1;line-height:1.1}' +
				'.gwchat-name{font-size:12px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}' +
				'.gwchat-active{font-size:10px;color:#b6c2d5;margin-top:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}' +
				'.gwchat-actions{display:flex;gap:3px}' +
				'.gwchat-action{border:0;background:rgba(255,255,255,.12);color:#fff;width:23px;height:23px;border-radius:7px;line-height:1;padding:0}' +
				'.gwchat-body{flex:1;min-height:0;overflow-y:auto;background:#f5f7fb;padding:9px}' +
				'.gwchat-load{font-size:11px;color:#667085;text-align:center;padding:5px}' +
				'.gwchat-empty{font-size:12px;color:#667085;text-align:center;padding:18px 6px}' +
				'.gwchat-msg{display:flex;margin:0 0 8px;gap:6px;align-items:flex-end}' +
				'.gwchat-msg.is-me{justify-content:flex-end}' +
				'.gwchat-msg-main{max-width:78%;position:relative;padding-bottom:2px}' +
				'.gwchat-msg-name{font-size:10px;color:#667085;margin:0 0 2px}' +
				'.gwchat-bubble{background:#fff;border:1px solid #d8e0e8;border-radius:14px;padding:7px 9px;line-height:1.35;font-size:12px;color:#111827;word-break:break-word}' +
				'.gwchat-msg .gwchat-bubble{transition:background-color .65s ease,border-color .65s ease,box-shadow .65s ease}' +
				'.gwchat-msg.is-unread .gwchat-bubble{background:#fff4b8;border-color:#f2c94c;box-shadow:0 1px 6px rgba(242,201,76,.32)}' +
				'.gwchat-link{color:#1d4ed8;text-decoration:underline;text-underline-offset:2px}' +
				'.gwchat-attachments{display:flex;flex-direction:column;gap:5px;margin-top:6px}' +
				'.gwchat-attachment-image{display:block;max-width:180px;border-radius:8px;overflow:hidden;border:1px solid rgba(15,23,42,.12);background:#fff}' +
				'.gwchat-attachment-image img{display:block;max-width:100%;height:auto;max-height:130px;object-fit:cover}' +
				'.gwchat-attachment-file{display:flex;gap:6px;align-items:center;color:#1d4ed8;text-decoration:none;border:1px solid #d8e0e8;border-radius:8px;background:#f8fafc;padding:5px 7px;max-width:190px}' +
				'.gwchat-attachment-file span:last-child{min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}' +
				'.gwchat-attachment-file small{color:#667085}' +
				'.gwchat-msg.is-me .gwchat-bubble{background:#dbeafe;border-color:#bfdbfe;border-bottom-right-radius:4px}' +
				'.gwchat-msg.is-other .gwchat-bubble{border-bottom-left-radius:4px}' +
				'.gwchat-meta{font-size:9px;color:#667085;margin-left:5px;white-space:nowrap}' +
				'.gwchat-system{text-align:center;font-size:11px;color:#475467;margin:8px 0}' +
				'.gwchat-reactions{display:flex;gap:3px;flex-wrap:wrap;margin-top:-4px;position:relative;z-index:2;padding-left:8px}' +
				'.gwchat-msg.is-me .gwchat-reactions{justify-content:flex-end;padding-left:0;padding-right:8px}' +
				'.gwchat-reaction-chip{border:1px solid #d8e0e8;background:#fff;border-radius:999px;font-size:11px;padding:1px 5px}' +
				'.gwchat-reaction-launcher{position:absolute;right:-18px;bottom:12px;border:1px solid #d8e0e8;background:#fff;color:#98a2b3;border-radius:999px;width:22px;height:22px;font-size:15px;line-height:18px;padding:0;opacity:0;pointer-events:none;transition:opacity .15s ease;box-shadow:0 1px 2px rgba(16,24,40,.12);z-index:3}' +
				'.gwchat-msg.is-me .gwchat-reaction-launcher{right:auto;left:-18px}' +
				'.gwchat-msg:hover .gwchat-reaction-launcher,.gwchat-msg.is-reacting .gwchat-reaction-launcher{opacity:1;pointer-events:auto}' +
				'.gwchat-reaction-picker{display:none;gap:3px;margin-top:-2px;margin-bottom:2px;flex-wrap:wrap;position:relative;z-index:4;background:rgba(255,255,255,.94);border:1px solid #d8e0e8;border-radius:999px;padding:2px 4px;box-shadow:0 4px 12px rgba(16,24,40,.14);width:max-content;max-width:100%}' +
				'.gwchat-msg.is-me .gwchat-reaction-picker{align-self:flex-end}' +
				'.gwchat-reaction-btn{border:1px solid #d8e0e8;background:#fff;border-radius:999px;font-size:11px;padding:1px 5px}' +
				'.gwchat-msg.is-reacting .gwchat-reaction-picker{display:flex}' +
				'@media (hover:none){.gwchat-reaction-launcher{display:none}.gwchat-msg.is-reacting .gwchat-reaction-launcher{display:inline-block;opacity:1;pointer-events:auto}}' +
				'.gwchat-typing{height:16px;font-size:11px;color:#667085;padding:0 9px;background:#f5f7fb}' +
				'.gwchat-composer{border-top:1px solid #e5eaf2;background:#fff;padding:6px;display:flex;gap:5px;align-items:flex-end}' +
				'.gwchat-attach{border:1px solid #cfd8e3;background:#fff;color:#475467;border-radius:10px;width:32px;height:32px;padding:0;font-size:15px;line-height:1}' +
				'.gwchat-input{flex:1;min-height:32px;max-height:64px;border:1px solid #cfd8e3;border-radius:10px;padding:7px 8px;resize:none;font-size:12px;line-height:1.25}' +
				'.gwchat-send{border:0;background:#2563eb;color:#fff;border-radius:10px;height:32px;padding:0 10px;font-size:12px}' +
				'.gwchat-selected-files{display:none;position:absolute;left:6px;right:6px;bottom:42px;background:#fff;border:1px solid #d8e0e8;border-radius:8px;padding:4px 7px;font-size:11px;color:#475467;box-shadow:0 4px 12px rgba(16,24,40,.12);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}' +
				'.gwchat-composer.has-files .gwchat-selected-files{display:block}' +
				'@keyframes gwchatSlideUp{from{transform:translateY(40px);opacity:.5}to{transform:translateY(0);opacity:1}}' +
				'</style>'
			);
		}
	};

	App.restoreDockState = function()
	{
		var state = loadState();
		var windows = state.windows || {};
		Object.keys(windows).forEach(function(roomId){
			var item = windows[roomId] || {};
			if (item.closed)
				return;
			App.openRoom(item.room_id || roomId, {
				minimized: !!item.minimized,
				room_url: item.room_url || ''
			});
		});
	};

	function ChatDockWindow(roomId, room, opts)
	{
		opts = opts || {};
		this.roomId = parseInt(roomId || '0', 10) || 0;
		this.room = room || null;
		this.roomUrl = opts.room_url || (room && room.room_url ? room.room_url : '');
		this.node = null;
		this.messagesNode = null;
		this.inputNode = null;
		this.fileNode = null;
		this.filesLabelNode = null;
		this.typingNode = null;
		this.messageKeys = {};
		this.loadingOlder = false;
		this.historyExhausted = false;
		this.minimized = false;
		this.closed = false;
		this.maximized = false;
		this.typingUsers = {};
		this.typingStopTimer = 0;
		this.selfTyping = false;
		this.lastSeenSent = 0;
		this.markSeenTimer = 0;
		this.seenInteraction = false;
		this.lastKnownMessageId = 0;
		this.gapRecoveryInFlight = false;
	}

	ChatDockWindow.prototype.mount = function()
	{
		var dock = $('#gwchatDock');
		var node = $('<div class="gwchat-window" data-room-id="' + escapeHtml(this.roomId) + '"></div>');
		this.node = node;
		dock.prepend(node);
		this.renderShell();
		this.bind();
	};

	ChatDockWindow.prototype.renderShell = function()
	{
		var room = this.room || { id: this.roomId };
		this.node.html(
			'<div class="gwchat-header" data-gwchat-minimize="1">' +
				'<div class="gwchat-avatar">' + avatarHtml(room) + '</div>' +
				'<div class="gwchat-title">' +
					'<div class="gwchat-name">' + escapeHtml(roomTitle(room)) + '</div>' +
					'<div class="gwchat-active">' + escapeHtml(activeAgo(room)) + '</div>' +
				'</div>' +
				'<div class="gwchat-actions">' +
					'<button type="button" class="gwchat-action" data-gwchat-maximize="1" title="Open full chat">□</button>' +
					'<button type="button" class="gwchat-action" data-gwchat-close="1" title="Close">×</button>' +
				'</div>' +
			'</div>' +
			'<div class="gwchat-body"><div class="gwchat-empty">Loading messages...</div></div>' +
			'<div class="gwchat-typing"></div>' +
			'<div class="gwchat-composer">' +
				'<button type="button" class="gwchat-attach" title="Attach file">📎</button>' +
				'<input type="file" class="gwchat-file" multiple style="display:none">' +
				'<textarea class="gwchat-input" placeholder="Write a message"></textarea>' +
				'<button type="button" class="gwchat-send">Send</button>' +
				'<div class="gwchat-selected-files"></div>' +
			'</div>'
		);
		this.messagesNode = this.node.find('.gwchat-body')[0];
		this.inputNode = this.node.find('.gwchat-input')[0];
		this.fileNode = this.node.find('.gwchat-file')[0];
		this.filesLabelNode = this.node.find('.gwchat-selected-files')[0];
		this.typingNode = this.node.find('.gwchat-typing')[0];
	};

	ChatDockWindow.prototype.bind = function()
	{
		var win = this;

		this.node.on('click', '[data-gwchat-minimize]', function(e){
			if ($(e.target).closest('[data-gwchat-maximize], [data-gwchat-close]').length)
				return;
			win.setMinimized(!win.minimized);
		});

		this.node.on('click', '[data-gwchat-maximize]', function(e){
			e.stopPropagation();
			win.suspendForMaximize();
			window.location.href = safeRoomUrl(win.room || { id: win.roomId, type: 'private' });
		});

		this.node.on('click', '[data-gwchat-close]', function(e){
			e.stopPropagation();
			win.close();
		});

		this.node.on('click', '.gwchat-send', function(){
			win.sendCurrentMessage();
		});

		this.node.on('click', '.gwchat-attach', function(){
			win.fileNode.click();
		});

		this.node.on('change', '.gwchat-file', function(){
			win.updateSelectedFilesLabel();
		});

		this.node.on('keydown', '.gwchat-input', function(e){
			if (e.key === 'Enter' && !e.shiftKey) {
				e.preventDefault();
				win.sendCurrentMessage();
			}
		});

		this.node.on('input', '.gwchat-input', function(){
			win.handleTypingInput();
		});

		this.node.on('scroll', '.gwchat-body', function(){
			if (win.messagesNode.scrollTop <= 10)
				win.loadOlder();
			win.markSeenVisible();
		});

		this.node.on('click focusin', function(){
			win.seenInteraction = true;
			win.markSeenVisible();
		});

		this.node.on('click', '[data-reaction-launcher]', function(){
			var msg = $(this).closest('.gwchat-msg');
			$('.gwchat-msg.is-reacting', win.node).removeClass('is-reacting');
			msg.toggleClass('is-reacting');
		});

		this.node.on('click', '[data-reaction-message-id]', function(){
			win.toggleReaction($(this).data('reaction-message-id'), $(this).data('reaction'));
		});

		this.node.on('click', '.gwchat-bubble', function(){
			if (window.matchMedia && window.matchMedia('(hover: none)').matches) {
				var msg = $(this).closest('.gwchat-msg');
				$('.gwchat-msg.is-reacting', win.node).removeClass('is-reacting');
				msg.addClass('is-reacting');
			}
		});

		this.node.on('click', '.gwchat-link', function(e){
			if ($(this).data('expanded'))
				return;

			e.preventDefault();
			e.stopPropagation();
			$(this).data('expanded', 1).text($(this).data('full-url-label'));
		});
	};

	ChatDockWindow.prototype.bootstrap = function()
	{
		var win = this;
		return http('doRoomBootstrap', { room_id: this.roomId, limit: 30 }).done(function(resp){
			win.room = resp.room || win.room || { id: win.roomId };
			win.roomUrl = safeRoomUrl(win.room);
			App.rooms[win.roomId] = win.room;
			if (resp.current_user)
				App.currentUser = resp.current_user;
			win.renderShell();
			win.appendEntries(resp.messages || [], 'replace');
			win.scrollBottom();
			App.joinRoom(win.roomId);
		}).fail(function(){
			win.messagesNode.innerHTML = '<div class="gwchat-empty">Failed to load chat</div>';
		});
	};

	ChatDockWindow.prototype.entryNode = function(entry)
	{
		entry = normalizeEntry(entry);

		if (entry.entry_type === 'event')
			return $('<div class="gwchat-system" data-entry-key="' + escapeHtml(entry.entry_key) + '">' + escapeHtml(entry.text) + '</div>')[0];

		var ownId = App.currentUser ? parseInt(App.currentUser.id || '0', 10) : 0;
		var isMe = ownId && entry.sender_id === ownId;
		var reactions = this.renderReactions(entry);
		var picker = reactionOptions.map(function(reaction){
			return '<button type="button" class="gwchat-reaction-btn" data-reaction-message-id="' + escapeHtml(entry.id) + '" data-reaction="' + escapeHtml(reaction) + '">' + escapeHtml(reaction) + '</button>';
		}).join('');

		return $(
			'<div class="gwchat-msg ' + (isMe ? 'is-me' : 'is-other') + (entry._highlight_unread ? ' is-unread' : '') + '" data-message-id="' + escapeHtml(entry.id) + '" data-entry-key="' + escapeHtml(entry.entry_key) + '">' +
				'<div class="gwchat-msg-main">' +
					(isMe ? '' : '<div class="gwchat-msg-name">' + escapeHtml(entry.sender_title) + '</div>') +
					'<div class="gwchat-bubble">' +
						(entry.message ? linkifyText(entry.message) : '') +
						renderAttachments(entry.attachments, true) +
						'<span class="gwchat-meta">' + escapeHtml(shortTime(entry.insert_time)) + (isMe ? ' <span data-seen-marker="1">' + (entry.is_seen ? '✓✓' : '✓') + '</span>' : '') + '</span>' +
					'</div>' +
					'<button type="button" class="gwchat-reaction-launcher" data-reaction-launcher="' + escapeHtml(entry.id) + '">☺</button>' +
					'<div class="gwchat-reaction-picker">' + picker + '</div>' +
					'<div class="gwchat-reactions" data-reactions-wrap="' + escapeHtml(entry.id) + '">' + reactions + '</div>' +
				'</div>' +
			'</div>'
		)[0];
	};

	ChatDockWindow.prototype.updateSelectedFilesLabel = function()
	{
		var files = this.fileNode && this.fileNode.files ? Array.prototype.slice.call(this.fileNode.files) : [];
		var composer = this.node.find('.gwchat-composer');
		if (!files.length) {
			composer.removeClass('has-files');
			if (this.filesLabelNode)
				this.filesLabelNode.textContent = '';
			return;
		}

		var names = files.map(function(file){ return file.name; });
		composer.addClass('has-files');
		if (this.filesLabelNode)
			this.filesLabelNode.textContent = names.join(', ');
	};

	ChatDockWindow.prototype.renderReactions = function(entry)
	{
		var reactions = $.isArray(entry.reactions) ? entry.reactions : [];
		return reactions.map(function(item){
			return '<button type="button" class="gwchat-reaction-chip" title="' + escapeHtml((item.users || []).map(function(user){ return user.name || user.username || ('User #' + user.id); }).join(', ')) + '">' + escapeHtml(item.reaction || '') + ' ' + escapeHtml(item.count || 0) + '</button>';
		}).join('');
	};

	ChatDockWindow.prototype.appendEntries = function(entries, mode)
	{
		entries = entries || [];
		if (mode === 'replace') {
			this.messagesNode.innerHTML = '';
			this.messageKeys = {};
		}

		if (!entries.length && mode === 'replace') {
			this.messagesNode.innerHTML = '<div class="gwchat-empty">No messages yet</div>';
			return;
		}

		$('.gwchat-empty', this.messagesNode).remove();
		var previousHeight = this.messagesNode.scrollHeight;
		var frag = document.createDocumentFragment();

		for (var i = 0; i < entries.length; i++) {
			var entry = normalizeEntry(entries[i]);
			if (!entry.entry_key || this.messageKeys[entry.entry_key])
				continue;
			this.messageKeys[entry.entry_key] = 1;
			if (entry.entry_type === 'message' && entry.id) {
				var ownId = App.currentUser ? parseInt(App.currentUser.id || '0', 10) : 0;
				if (mode === 'append' && ownId && entry.sender_id !== ownId && !entry.is_seen)
					entry._highlight_unread = 1;
				this.lastKnownMessageId = Math.max(this.lastKnownMessageId, entry.id);
			}
			frag.appendChild(this.entryNode(entry));
		}

		if (mode === 'prepend') {
			this.messagesNode.insertBefore(frag, this.messagesNode.firstChild);
			this.messagesNode.scrollTop = this.messagesNode.scrollHeight - previousHeight;
		} else {
			this.messagesNode.appendChild(frag);
			this.scrollBottom();
			this.scrollBottomAfterImagesLoad();
			this.scheduleMarkSeen();
		}
	};

	ChatDockWindow.prototype.getFirstMessageId = function()
	{
		var nodes = this.messagesNode.querySelectorAll('.gwchat-msg[data-message-id]');
		return nodes.length ? (parseInt(nodes[0].getAttribute('data-message-id') || '0', 10) || 0) : 0;
	};

	ChatDockWindow.prototype.getLastMessageId = function()
	{
		var nodes = this.messagesNode.querySelectorAll('.gwchat-msg[data-message-id]');
		return nodes.length ? (parseInt(nodes[nodes.length - 1].getAttribute('data-message-id') || '0', 10) || 0) : 0;
	};

	ChatDockWindow.prototype.getLastVisibleMessageId = function()
	{
		var bodyRect = this.messagesNode.getBoundingClientRect();
		var nodes = this.messagesNode.querySelectorAll('.gwchat-msg[data-message-id]');
		var lastId = 0;

		for (var i = 0; i < nodes.length; i++) {
			var rect = nodes[i].getBoundingClientRect();
			var isVisible = rect.bottom > bodyRect.top && rect.top < bodyRect.bottom;

			if (isVisible)
				lastId = parseInt(nodes[i].getAttribute('data-message-id') || '0', 10) || lastId;
		}

		return lastId;
	};

	ChatDockWindow.prototype.loadOlder = function()
	{
		var win = this;
		var firstId = this.getFirstMessageId();
		if (!firstId || this.loadingOlder || this.historyExhausted)
			return;

		this.loadingOlder = true;
		$(this.messagesNode).prepend('<div class="gwchat-load">Loading older messages...</div>');

		http('doLoadMessages', { room_id: this.roomId, before_message_id: firstId, limit: 30 }).done(function(resp){
			$('.gwchat-load', win.messagesNode).remove();
			if (!(resp.messages || []).length) {
				win.historyExhausted = true;
				$(win.messagesNode).prepend('<div class="gwchat-load">Older history exhausted</div>');
				return;
			}
			win.appendEntries(resp.messages || [], 'prepend');
		}).always(function(){
			win.loadingOlder = false;
		});
	};

	ChatDockWindow.prototype.handleIncomingMessage = function(msg)
	{
		msg = normalizeEntry(msg);

		if (!msg.id)
			return;

		if (App.shouldRecoverGap(this.lastKnownMessageId, msg)) {
			this.loadMissingMessages(this.lastKnownMessageId, msg);
			return;
		}

		if (msg.id <= this.lastKnownMessageId && this.messageKeys[msg.entry_key])
			return;

		this.appendEntries([msg], 'append');
		this.markSeen(msg.id);
	};

	ChatDockWindow.prototype.loadMissingMessages = function(afterMessageId, fallbackMsg)
	{
		var win = this;
		afterMessageId = parseInt(afterMessageId || '0', 10) || 0;

		if (!afterMessageId || this.gapRecoveryInFlight) {
			if (fallbackMsg)
				this.appendEntries([fallbackMsg], 'append');
			return;
		}

		this.gapRecoveryInFlight = true;
		App.debugLog('message_gap_recovery', {
			room_id: this.roomId,
			after_message_id: afterMessageId,
			incoming_message_id: fallbackMsg ? fallbackMsg.id : 0
		});

		App.loadMessagesAfter(this.roomId, afterMessageId, 100).done(function(resp){
			var messages = resp.messages || [];

			if (fallbackMsg)
				messages.push(fallbackMsg);

			win.appendEntries(messages, 'append');
			win.markSeen(win.getLastVisibleMessageId());
		}).fail(function(){
			if (fallbackMsg) {
				win.appendEntries([fallbackMsg], 'append');
				win.markSeen(fallbackMsg.id);
			}
		}).always(function(){
			win.gapRecoveryInFlight = false;
		});
	};

	ChatDockWindow.prototype.recoverIfBehindRoom = function(room)
	{
		var roomLastMessageId = parseInt(room && room.last_message_id || '0', 10) || 0;

		if (!roomLastMessageId || roomLastMessageId <= this.lastKnownMessageId)
			return;

		this.loadMissingMessages(this.lastKnownMessageId, null);
	};

	ChatDockWindow.prototype.sendCurrentMessage = function()
	{
		var win = this;
		var text = String(this.inputNode.value || '').trim();
		var files = this.fileNode && this.fileNode.files ? Array.prototype.slice.call(this.fileNode.files) : [];
		if (!text && !files.length)
			return;

		this.node.find('.gwchat-send').prop('disabled', true);
		var client = App.getClient();
		var promise;

		if (files.length) {
			var formData = new FormData();
			formData.append('room_id', this.roomId);
			formData.append('message', text);
			files.forEach(function(file){
				formData.append('attachments[]', file);
			});
			promise = httpForm('doSendMessage', formData);
		} else {
			promise = client && client.socket && client.socket.readyState === 1 ?
			client.sendMessage(this.roomId, text) :
			http('doSendMessage', { room_id: this.roomId, message: text });
		}

		Promise.resolve(promise).then(function(resp){
			if (resp && resp.message)
				win.appendEntries([resp.message], 'append');
			win.inputNode.value = '';
			if (win.fileNode)
				win.fileNode.value = '';
			win.updateSelectedFilesLabel();
			win.stopTyping();
		}).catch(function(){
			win.appendEntries([{ entry_type: 'event', entry_key: 'sendfail-' + Date.now(), text: 'Send failed' }], 'append');
		}).then(function(){
			win.node.find('.gwchat-send').prop('disabled', false);
			win.inputNode.focus();
		});
	};

	ChatDockWindow.prototype.handleTypingInput = function()
	{
		var client = App.getClient();
		if (!client || !client.socket || client.socket.readyState !== 1)
			return;

		var hasText = String(this.inputNode.value || '').trim() !== '';
		if (hasText && !this.selfTyping) {
			this.selfTyping = true;
			client.typing(this.roomId, true).catch(function(){});
		}
		if (!hasText)
			this.stopTyping();

		clearTimeout(this.typingStopTimer);
		this.typingStopTimer = setTimeout(this.stopTyping.bind(this), 1200);
	};

	ChatDockWindow.prototype.stopTyping = function()
	{
		var client = App.getClient();
		clearTimeout(this.typingStopTimer);
		if (this.selfTyping && client && client.socket && client.socket.readyState === 1)
			client.typing(this.roomId, false).catch(function(){});
		this.selfTyping = false;
	};

	ChatDockWindow.prototype.setTyping = function(userId, name, isTyping)
	{
		var ownId = App.currentUser ? parseInt(App.currentUser.id || '0', 10) : 0;
		userId = parseInt(userId || '0', 10) || 0;
		if (!userId || userId === ownId)
			return;

		if (isTyping)
			this.typingUsers[userId] = name || ('User #' + userId);
		else
			delete this.typingUsers[userId];

		var names = Object.keys(this.typingUsers).map(function(id){ return this.typingUsers[id]; }, this);
		this.typingNode.textContent = names.length ? (names.join(', ') + ' typing...') : '';
	};

	ChatDockWindow.prototype.markSeen = function(messageId)
	{
		var win = this;
		messageId = parseInt(messageId || '0', 10) || 0;
		if (!messageId || !this.canMarkSeen())
			return;

		if (messageId <= this.lastSeenSent) {
			this.clearUnreadHighlights(messageId);
			return;
		}

		this.lastSeenSent = messageId;
		var req = App.markSeen(this.roomId, messageId);

		Promise.resolve(req).then(function(){
			setTimeout(function(){
				win.clearUnreadHighlights(messageId);
			}, 650);
			App.loadBubbleData();
		}).catch(function(){});
	};

	ChatDockWindow.prototype.clearUnreadHighlights = function(messageId)
	{
		messageId = parseInt(messageId || '0', 10) || 0;
		if (!messageId)
			return;

		this.node.find('.gwchat-msg.is-unread[data-message-id]').each(function(){
			var id = parseInt(this.getAttribute('data-message-id') || '0', 10) || 0;
			if (id && id <= messageId)
				$(this).removeClass('is-unread');
		});
	};

	ChatDockWindow.prototype.scheduleMarkSeen = function()
	{
		var win = this;
		clearTimeout(this.markSeenTimer);
		this.markSeenTimer = setTimeout(function(){
			win.markSeenVisible();
		}, 120);
	};

	ChatDockWindow.prototype.markSeenVisible = function()
	{
		this.markSeen(this.getLastVisibleMessageId());
	};

	ChatDockWindow.prototype.canMarkSeen = function()
	{
		return !this.minimized
			&& !this.closed
			&& this.seenInteraction
			&& (!document.hasFocus || document.hasFocus());
	};

	ChatDockWindow.prototype.updateSeen = function(lastMessageId)
	{
		lastMessageId = parseInt(lastMessageId || '0', 10) || 0;
		this.node.find('[data-seen-marker]').each(function(){
			var msg = $(this).closest('.gwchat-msg');
			var id = parseInt(msg.attr('data-message-id') || '0', 10) || 0;
			if (id && id <= lastMessageId)
				this.textContent = '✓✓';
		});
	};

	ChatDockWindow.prototype.updateReactions = function(messageId, reactions)
	{
		messageId = parseInt(messageId || '0', 10) || 0;
		var wrap = this.node.find('[data-reactions-wrap="' + messageId + '"]');
		if (!wrap.length)
			return;
		wrap.html(this.renderReactions({ reactions: reactions || [] }));
	};

	ChatDockWindow.prototype.toggleReaction = function(messageId, reaction)
	{
		var win = this;
		var client = App.getClient();
		var req = client && client.socket && client.socket.readyState === 1 ?
			client.toggleReaction(messageId, reaction) :
			http('doToggleReaction', { message_id: messageId, reaction: reaction });

		Promise.resolve(req).then(function(resp){
			var packet = resp && (resp.packet || resp);
			if (packet && packet.message_id)
				win.updateReactions(packet.message_id, packet.reactions || []);
		});
		this.node.find('.gwchat-msg.is-reacting').removeClass('is-reacting');
	};

	ChatDockWindow.prototype.scrollBottom = function()
	{
		this.messagesNode.scrollTop = this.messagesNode.scrollHeight;
	};

	ChatDockWindow.prototype.scrollBottomAfterImagesLoad = function()
	{
		var win = this;
		$('img', this.messagesNode).each(function(){
			if (this.complete)
				return;

			$(this).one('load', function(){
				win.scrollBottom();
				win.scheduleMarkSeen();
			});
		});
	};

	ChatDockWindow.prototype.setMinimized = function(minimized)
	{
		this.minimized = !!minimized;
		this.node.toggleClass('is-minimized', this.minimized);
		if (!this.minimized)
			this.scheduleMarkSeen();
		saveState();
	};

	ChatDockWindow.prototype.focus = function()
	{
		this.node.appendTo('#gwchatDock');
		if (!this.minimized && this.seenInteraction) {
			this.scheduleMarkSeen();
		}
		if (!this.minimized && this.inputNode)
			this.inputNode.focus();
	};

	ChatDockWindow.prototype.close = function()
	{
		this.closed = true;
		this.node.remove();
		delete App.windows[this.roomId];
		saveState();
	};

	ChatDockWindow.prototype.suspendForMaximize = function()
	{
		this.maximized = true;
		if (this.node)
			this.node.remove();
		saveState();
	};

	window.GWChatApp = App;
	return App;
});

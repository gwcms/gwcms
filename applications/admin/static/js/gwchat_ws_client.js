function GWChatWSClient(opts)
{
	this.opts = $.extend({
		url: null,
		debug: true,
		autoReconnect: true,
		reconnectDelayMs: 1500,
		keepAliveMs: 25000
	}, opts || {});

	this.socket = null;
	this.reqId = 0;
	this.pending = {};
	this.events = {};
	this.reconnectTimer = 0;
	this.reconnectAttempts = 0;
	this.recentSeenPackets = {};
	this.manualClose = false;
	this.disableReconnect = false;
	this.resolvedUrl = null;
	this.connectStartedAt = 0;
	this.openedAt = 0;
	this.keepAliveTimer = 0;
}

GWChatWSClient.prototype.resolveUrl = function(url)
{
	url = url || this.opts.url;

	if (!url)
		return null;

	if (/^wss?:\/\//i.test(url))
		return url;

	if (/^https?:\/\//i.test(url))
		return url.replace(/^http/i, 'ws');

	var secure = typeof location !== 'undefined' && location.protocol === 'https:';
	var host = typeof location !== 'undefined' ? location.host : '';

	if (url.charAt(0) === '/')
		return (secure ? 'wss://' : 'ws://') + host + url;

	return (secure ? 'wss://' : 'ws://') + url;
};

GWChatWSClient.prototype.log = function(label, data)
{
	if (!this.opts.debug)
		return;

	if (typeof data === 'undefined')
		console.log('[GWChatWS]', label);
	else
		console.log('[GWChatWS]', label, data);
};

GWChatWSClient.prototype.on = function(event, callback)
{
	if (!this.events[event])
		this.events[event] = [];

	this.events[event].push(callback);
	return this;
};

GWChatWSClient.prototype.emit = function(event, payload)
{
	var list = this.events[event] || [];
	for (var i = 0; i < list.length; i++)
		list[i](payload);
};

GWChatWSClient.prototype.connect = function(url)
{
	var client = this;

	if (url)
		this.opts.url = url;

	this.opts.url = this.resolveUrl(this.opts.url);
	this.resolvedUrl = this.opts.url;

	if (!this.opts.url)
		throw new Error('Missing websocket url');

	this.manualClose = false;
	this.disableReconnect = false;
	this.connectStartedAt = Date.now();
	this.openedAt = 0;
	this.log('connect_attempt', { url: this.opts.url, attempt: this.reconnectAttempts + 1 });
	this.socket = new WebSocket(this.opts.url);

	this.socket.onopen = function() {
		client.openedAt = Date.now();
		client.reconnectAttempts = 0;
		client.log('connect', {
			url: client.opts.url,
			elapsed_ms: client.openedAt - client.connectStartedAt
		});
		client.startKeepAlive();
		client.emit('connect');
	};

	this.socket.onmessage = function(e) {
		var packet = null;

		try {
			packet = JSON.parse(e.data);
		} catch (err) {
			client.log('invalid_packet', e.data);
			return;
		}

		client.handlePacket(packet);
	};

	this.socket.onclose = function(e) {
		var info = {
			code: e && typeof e.code !== 'undefined' ? e.code : null,
			reason: e && typeof e.reason !== 'undefined' ? e.reason : '',
			wasClean: e && typeof e.wasClean !== 'undefined' ? e.wasClean : null
		};
		client.log('disconnect', info);
		client.stopKeepAlive();
		client.rejectPending('WebSocket disconnected');
		client.emit('disconnect', info);

		if (!client.manualClose && !client.disableReconnect && client.opts.autoReconnect) {
			clearTimeout(client.reconnectTimer);
			client.reconnectAttempts++;
			client.log('reconnect_scheduled', {
				delay_ms: client.opts.reconnectDelayMs,
				url: client.getResolvedUrl(),
				attempt: client.reconnectAttempts
			});
			client.emit('reconnect_scheduled', {
				delay_ms: client.opts.reconnectDelayMs,
				url: client.getResolvedUrl(),
				attempt: client.reconnectAttempts
			});
			client.reconnectTimer = setTimeout(function(){
				client.log('reconnect_attempt', {
					url: client.getResolvedUrl(),
					attempt: client.reconnectAttempts
				});
				client.emit('reconnect_attempt', {
					url: client.getResolvedUrl(),
					attempt: client.reconnectAttempts
				});
				client.connect();
			}, client.opts.reconnectDelayMs);
		}
	};

	this.socket.onerror = function(err) {
		client.log('socket_error', err);
		client.emit('error', err);
	};

	return this;
};

GWChatWSClient.prototype.startKeepAlive = function()
{
	var client = this;
	var interval = parseInt(this.opts.keepAliveMs, 10) || 0;

	this.stopKeepAlive();

	if (interval <= 0)
		return;

	this.keepAliveTimer = setInterval(function(){
		if (!client.socket || client.socket.readyState !== 1)
			return;

		client.ping().catch(function(err){
			client.log('keepalive_failed', err && err.message ? err.message : err);
		});
	}, interval);
};

GWChatWSClient.prototype.stopKeepAlive = function()
{
	if (this.keepAliveTimer) {
		clearInterval(this.keepAliveTimer);
		this.keepAliveTimer = 0;
	}
};

GWChatWSClient.prototype.getResolvedUrl = function()
{
	return this.resolvedUrl || this.resolveUrl(this.opts.url);
};

GWChatWSClient.prototype.close = function()
{
	this.manualClose = true;
	clearTimeout(this.reconnectTimer);
	this.stopKeepAlive();

	if (this.socket)
		this.socket.close();
};

GWChatWSClient.prototype.rejectPending = function(message)
{
	var pending = this.pending || {};
	this.pending = {};

	for (var reqId in pending) {
		if (!Object.prototype.hasOwnProperty.call(pending, reqId))
			continue;

		pending[reqId].reject(new Error(message || 'WebSocket request failed'));
	}
};

GWChatWSClient.prototype.handlePacket = function(packet)
{
	if (this.isDuplicateSeenPacket(packet))
		return;

	this.log('packet', packet);
	if (packet && packet.action === 'hello') {
		this.log('hello_timing', {
			connect_to_open_ms: this.openedAt && this.connectStartedAt ? this.openedAt - this.connectStartedAt : null,
			connect_to_hello_ms: this.connectStartedAt ? Date.now() - this.connectStartedAt : null,
			open_to_hello_ms: this.openedAt ? Date.now() - this.openedAt : null
		});
	}

	if (packet && packet.action === 'error' && this.isFatalAuthError(packet))
		this.disableReconnect = true;

	if (packet.req_id && this.pending[packet.req_id]) {
		var pending = this.pending[packet.req_id];
		delete this.pending[packet.req_id];

		if (packet.ok)
			pending.resolve(packet);
		else
			pending.reject(packet);
	}

	this.emit('packet', packet);
	this.emit('action:' + packet.action, packet);
};

GWChatWSClient.prototype.isDuplicateSeenPacket = function(packet)
{
	if (!packet || packet.action !== 'chat_seen')
		return false;

	var key = [
		packet.room_id || 0,
		packet.user_id || 0,
		packet.last_message_id || 0,
		packet.last_event_id || 0
	].join(':');
	var now = Date.now();
	var last = this.recentSeenPackets[key] || 0;
	this.recentSeenPackets[key] = now;

	return last && now - last < 3000;
};

GWChatWSClient.prototype.isFatalAuthError = function(packet)
{
	var errorText = packet && packet.error ? String(packet.error) : '';
	return /unauthori[sz]ed|not\s+authori[sz]ed|auth/i.test(errorText);
};

GWChatWSClient.prototype.send = function(action, payload)
{
	if (!this.socket || this.socket.readyState !== 1)
		throw new Error('WebSocket not connected');

	var packet = $.extend({ action: action }, payload || {});
	this.socket.send(JSON.stringify(packet));
	this.log('send', packet);
	return packet;
};

GWChatWSClient.prototype.request = function(action, payload)
{
	var client = this;
	var reqId = ++this.reqId;

	return new Promise(function(resolve, reject){
		client.pending[reqId] = { resolve: resolve, reject: reject };
		try {
			client.send(action, $.extend({}, payload || {}, { req_id: reqId }));
		} catch (err) {
			delete client.pending[reqId];
			client.log('send_failed', {
				action: action,
				req_id: reqId,
				readyState: client.socket ? client.socket.readyState : null,
				error: err && err.message ? err.message : err
			});
			reject(err);
		}
	});
};

GWChatWSClient.prototype.ping = function()
{
	return this.request('ping');
};

GWChatWSClient.prototype.getMyRooms = function()
{
	return this.request('my_rooms');
};

GWChatWSClient.prototype.openPrivateRoom = function(userId)
{
	return this.request('open_private_room', { user_id: userId });
};

GWChatWSClient.prototype.joinRoom = function(roomId)
{
	return this.request('join_room', { room_id: roomId });
};

GWChatWSClient.prototype.leaveRoom = function(roomId)
{
	return this.request('leave_room', { room_id: roomId });
};

GWChatWSClient.prototype.loadMessages = function(roomId, beforeMessageId, limit, afterMessageId)
{
	return this.request('load_messages', {
		room_id: roomId,
		before_message_id: beforeMessageId || 0,
		after_message_id: afterMessageId || 0,
		limit: limit || 50
	});
};

GWChatWSClient.prototype.sendMessage = function(roomId, message, opts)
{
	opts = opts || {};

	return this.request('send_message', {
		room_id: roomId,
		message: message,
		reply_to_message_id: opts.reply_to_message_id || 0,
		attachments: opts.attachments || []
	});
};

GWChatWSClient.prototype.typing = function(roomId, typing)
{
	return this.request('typing', {
		room_id: roomId,
		typing: typing ? 1 : 0
	});
};

GWChatWSClient.prototype.markSeen = function(roomId, lastMessageId)
{
	return this.request('seen', {
		room_id: roomId,
		last_message_id: lastMessageId || 0
	});
};

GWChatWSClient.prototype.toggleReaction = function(messageId, reaction)
{
	return this.request('toggle_reaction', {
		message_id: messageId,
		reaction: reaction || ''
	});
};

if (typeof window !== 'undefined')
	window.GWChatWSClient = GWChatWSClient;

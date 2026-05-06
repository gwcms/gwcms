function GWChatConsole(opts)
{
	this.opts = $.extend({
		httpEndpoint: GW.app_base + GW.ln + '/users/chat',
		wsUrl: null,
		debug: true
	}, opts || {});

	this.client = null;
	this.activeRoomId = 0;
}

GWChatConsole.prototype.log = function(label, data)
{
	if (!this.opts.debug)
		return;

	if (typeof data === 'undefined')
		console.log('[GWChat]', label);
	else
		console.log('[GWChat]', label, data);
};

GWChatConsole.prototype.connectWs = function(url)
{
	var chat = this;
	var wsUrl = url || this.opts.wsUrl || '/ws';

	return new Promise(function(resolve){
		require(['js/gwchat_ws_client'], function(){
			chat.client = new GWChatWSClient({
				url: wsUrl,
				debug: chat.opts.debug
			});

			chat.client.on('connect', function(){ chat.log('ws_connect', wsUrl); });
			chat.client.on('action:hello', function(packet){
				chat.log('hello', packet);
				resolve(packet);
			});
			chat.client.on('action:chat_message', function(packet){
				console.log('[GWChat][message]', packet.room_id, packet.message);
			});
			chat.client.on('action:chat_typing', function(packet){
				console.log('[GWChat][typing]', packet.room_id, packet.user_name + ' typing...');
			});
			chat.client.on('action:chat_stop_typing', function(packet){
				console.log('[GWChat][typing_stop]', packet.room_id, packet.user_name);
			});
			chat.client.on('action:chat_seen', function(packet){
				console.log('[GWChat][seen]', packet.room_id, packet.last_message_id);
			});
			chat.client.on('action:error', function(packet){
				console.log('[GWChat][error]', packet.error || packet);
			});

			chat.client.connect();
		});
	});
};

GWChatConsole.prototype.getMyRooms = function()
{
	return this.client.getMyRooms();
};

GWChatConsole.prototype.getRoom = function(roomId)
{
	return $.getJSON(this.opts.httpEndpoint, { act: 'doGetRoom', room_id: roomId });
};

GWChatConsole.prototype.joinRoom = function(roomId)
{
	this.activeRoomId = roomId;
	return this.client.joinRoom(roomId);
};

GWChatConsole.prototype.leaveRoom = function(roomId)
{
	roomId = roomId || this.activeRoomId;

	if (this.activeRoomId == roomId)
		this.activeRoomId = 0;

	return this.client.leaveRoom(roomId);
};

GWChatConsole.prototype.openPrivateRoom = function(userId)
{
	return this.client.openPrivateRoom(userId);
};

GWChatConsole.prototype.createGroupRoom = function(title, userIds, roomHistoryLimit)
{
	return $.ajax({
		url: this.opts.httpEndpoint,
		method: 'POST',
		dataType: 'json',
		data: {
			act: 'doCreateGroupRoom',
			title: title || '',
			user_ids: userIds || [],
			room_history_limit: roomHistoryLimit
		}
	});
};

GWChatConsole.prototype.loadMessages = function(roomId, beforeMessageId, limit)
{
	return this.client.loadMessages(roomId, beforeMessageId, limit);
};

GWChatConsole.prototype.sendMessage = function(roomId, message, opts)
{
	return this.client.sendMessage(roomId, message, opts || {});
};

GWChatConsole.prototype.markSeen = function(roomId, lastMessageId)
{
	return this.client.markSeen(roomId, lastMessageId);
};

GWChatConsole.prototype.typingStart = function(roomId)
{
	return this.client.typing(roomId, true);
};

GWChatConsole.prototype.typingStop = function(roomId)
{
	return this.client.typing(roomId, false);
};

GWChatConsole.prototype.help = function()
{
	var help = [
		'GWChat shared console client:',
		'new GWChatConsole()',
		'chat.connectWs()',
		'chat.getMyRooms()',
		'chat.getRoom(roomId)',
		'chat.joinRoom(roomId)',
		'chat.leaveRoom(roomId)',
		'chat.openPrivateRoom(userId)',
		'chat.createGroupRoom(title, userIds, roomHistoryLimit)',
		'chat.loadMessages(roomId, beforeMessageId, limit)',
		'chat.sendMessage(roomId, message, opts)',
		'chat.markSeen(roomId, lastMessageId)',
		'chat.typingStart(roomId)',
		'chat.typingStop(roomId)',
		'chat.help()',
		'',
		'Protocol target:',
		'Both ReactPHP and OpenSwoole must implement the same gwchat.v1 websocket packet contract.'
	];

	console.log(help.join('\n'));
	return help;
};

if (typeof window !== 'undefined') {
	window.GWChatConsole = GWChatConsole;
	window.chathelp = function()
	{
		if (window.chat && typeof window.chat.help === 'function')
			return window.chat.help();

		var help = [
			'GWChat console bootstrap:',
			"require(['js/gwchat_console'], function(){ window.chat = new GWChatConsole(); chathelp(); });"
		];

		console.log(help.join('\n'));
		return help;
	};
}

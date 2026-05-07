{include file="default_open.tpl"}

{literal}
<style>
.chat-room-page{display:flex;flex-direction:column;height:calc(100vh - 165px);min-height:640px}
.chat-room-shell{background:#fff;border:1px solid #d8e0e8;border-radius:10px;overflow:hidden;display:flex;flex-direction:column;height:100%}
.chat-room-header{padding:16px 18px;border-bottom:1px solid #e7edf3;display:flex;justify-content:space-between;gap:12px;align-items:flex-start}
.chat-room-title{font-size:22px;font-weight:700;line-height:1.2}
.chat-room-title-row{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.chat-room-peer-status{display:inline-flex;align-items:center;gap:8px}
.chat-room-peer-dot{width:10px;height:10px;border-radius:50%;display:inline-block;background:#d0d5dd}
.chat-room-peer-dot.is-on{background:#16a34a;box-shadow:0 0 0 3px rgba(22,163,74,.15)}
.chat-room-tools{position:relative}
.chat-room-info-btn{width:34px;height:34px;border:1px solid #d8e0e8;border-radius:999px;background:#fff;color:#475467;display:inline-flex;align-items:center;justify-content:center;text-decoration:none}
.chat-room-info-btn:hover,.chat-room-info-btn:focus{color:#101828;text-decoration:none}
.chat-room-info-menu{min-width:260px;padding:12px 14px}
.chat-room-info-line{font-size:12px;color:#475467;margin:0 0 8px;word-break:break-word}
.chat-room-info-line:last-child{margin-bottom:0}
.chat-room-subtitle,.chat-room-presence-text,.chat-room-typing,.chat-room-loadmore,.chat-room-status{color:#475467}
.chat-room-subtitle,.chat-room-presence-text,.chat-room-typing,.chat-room-status{margin-top:6px}
.chat-room-presence-text{font-size:13px}
.chat-room-presence-list{display:flex;flex-wrap:wrap;gap:8px;margin-top:10px}
.chat-room-presence-chip{display:inline-flex;align-items:center;gap:8px;padding:6px 10px;border:1px solid #d8e0e8;border-radius:999px;background:#fff}
.chat-user-avatar{width:40px;height:40px;border-radius:50%;object-fit:cover;display:inline-block;vertical-align:middle;background:#f2f4f7}
.chat-user-fallback{display:inline-flex;align-items:center;justify-content:center;vertical-align:middle;border-radius:50%;background:#eaf2ff;color:#1d4ed8;overflow:hidden;text-align:center;font-size:13px;font-weight:700;width:40px;height:40px}
.chat-room-presence-chip .chat-user-avatar,.chat-room-presence-chip .chat-user-fallback{width:28px;height:28px}
.chat-room-presence-chip .chat-user-fallback{font-size:11px;font-weight:700;padding:0 4px}
.chat-room-presence-dot{width:8px;height:8px;border-radius:50%;background:#16a34a;box-shadow:0 0 0 3px rgba(22,163,74,.15)}
.chat-room-presence-name{font-size:13px;color:#101828}
.chat-room-presence-count{font-size:11px;color:#667085}
.chat-room-panel{display:flex;flex-direction:column;flex:1;min-height:0}
.chat-room-messages{flex:1;overflow-y:auto;padding:16px 18px;background:#f7f9fc}
.chat-room-loadmore{text-align:center;font-size:12px;padding:6px 0 14px}
.chat-room-message{display:flex;gap:10px;margin:0 0 14px;align-items:flex-end}
.chat-room-message.is-me{justify-content:flex-end}
.chat-room-message.is-me .chat-room-message-avatar{display:none}
.chat-room-message-avatar{width:40px;flex:0 0 40px}
.chat-room-message-main{max-width:min(72%,780px)}
.chat-room-message.is-me .chat-room-message-main{display:flex;flex-direction:column;align-items:flex-end}
.chat-room-message.is-me .chat-room-message-main{margin-right:-10px}
.chat-room-message-name{font-size:12px;color:#475467;margin:0 0 4px}
.chat-room-message-body{display:flex;align-items:flex-end;gap:3px}
.chat-room-message-bubble{background:#fff;border:1px solid #d8e0e8;border-radius:16px;padding:12px 13px;color:#101828;word-break:break-word;box-shadow:0 1px 2px rgba(16,24,40,.04);line-height:1.45}
.chat-room-message .chat-room-message-bubble{transition:background-color .65s ease,border-color .65s ease,box-shadow .65s ease}
.chat-room-message.is-unread .chat-room-message-bubble{background:#fff4b8;border-color:#f2c94c;box-shadow:0 1px 8px rgba(242,201,76,.32)}
.chat-room-message.is-me .chat-room-message-bubble{background:#dbeafe;border-color:#bfdbfe;border-bottom-right-radius:4px}
.chat-room-message.is-other .chat-room-message-bubble{border-bottom-left-radius:4px}
.chat-room-message:hover .chat-room-reaction-launcher,.chat-room-message.is-reaction-open .chat-room-reaction-launcher{opacity:1;pointer-events:auto}
.chat-room-message-text{white-space:pre-wrap}
.chat-room-message-text a{color:#1d4ed8;text-decoration:underline;text-underline-offset:2px}
.chat-room-attachments{display:flex;flex-direction:column;gap:8px;margin-top:8px}
.chat-room-attachment-image{display:block;max-width:min(420px,100%);border:1px solid rgba(15,23,42,.12);border-radius:10px;overflow:hidden;background:#fff}
.chat-room-attachment-image img{display:block;max-width:100%;height:auto;max-height:360px;object-fit:contain}
.chat-room-attachment-file{display:flex;align-items:center;gap:8px;max-width:420px;border:1px solid #d8e0e8;border-radius:10px;background:#f8fafc;color:#1d4ed8;text-decoration:none;padding:8px 10px}
.chat-room-attachment-file:hover{text-decoration:none;background:#eef4ff}
.chat-room-attachment-file span:last-child{min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.chat-room-attachment-file small{color:#667085}
.chat-room-message-meta{display:inline-flex;align-items:flex-end;gap:4px;color:#667085;line-height:1;margin-left:8px;white-space:nowrap;vertical-align:baseline}
.chat-room-message-time,.chat-room-message-seen{display:inline-flex;font-size:10px;line-height:1;position:relative;top:3px}
.chat-room-message-seen{color:#16a34a;letter-spacing:-1px}
.chat-room-reaction-launcher{border:0;background:transparent;color:#98a2b3;display:inline-flex;align-items:center;justify-content:center;padding:0;font-size:33px;line-height:1;opacity:0;pointer-events:none;transition:opacity .15s ease;transform:translateY(-7px);margin-left:-3px}
.chat-room-reaction-picker{display:none;flex-wrap:wrap;gap:6px;margin-top:6px}
.chat-room-reaction-picker.is-open{display:flex}
.chat-room-reaction-option{border:1px solid #d8e0e8;background:#fff;border-radius:999px;padding:2px 8px;cursor:pointer}
.chat-room-reactions{display:flex;flex-wrap:wrap;gap:6px;margin-top:-8px;position:relative;z-index:2;padding-left:12px}
.chat-room-message.is-me .chat-room-reactions{justify-content:flex-end;padding-left:0;padding-right:12px}
.chat-room-reaction-chip{border:1px solid #d8e0e8;background:#fff;border-radius:999px;padding:2px 8px;font-size:12px;cursor:pointer;box-shadow:0 1px 2px rgba(16,24,40,.08)}
.chat-room-reaction-chip.is-me{border-color:#93c5fd;background:#eff6ff}
@media (hover:none){
	.chat-room-reaction-launcher{display:none}
	.chat-room-message.is-reaction-open .chat-room-reaction-launcher{display:inline-flex;opacity:1;pointer-events:auto}
}
.chat-room-message.is-system{justify-content:center}
.chat-room-message.is-system .chat-room-message-main{max-width:100%;display:block}
.chat-room-message.is-system .chat-room-message-bubble{background:#eef4ff;border-color:#c7d7fe;color:#1d4ed8;border-radius:999px;padding:8px 14px}
.chat-room-empty{padding:30px 12px;text-align:center;color:#667085}
.chat-room-composer{border-top:1px solid #e7edf3;padding:14px 18px;background:#fff}
.chat-room-input-wrap{display:flex;gap:10px;align-items:flex-end}
.chat-room-composer-tools{position:relative;display:flex;align-items:flex-end}
.chat-room-emoji-toggle,.chat-room-attach-toggle{border:1px solid #d0d5dd;background:#fff;color:#667085;border-radius:12px;width:44px;height:44px;display:inline-flex;align-items:center;justify-content:center;font-size:22px;padding:0}
.chat-room-tool-stack{display:flex;gap:8px;align-items:flex-end}
.chat-room-selected-files{display:none;margin:0 0 8px 54px;border:1px solid #d8e0e8;border-radius:10px;background:#f8fafc;color:#475467;padding:7px 10px;font-size:12px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.chat-room-selected-files.is-visible{display:block}
.chat-room-emoji-menu{display:none;position:absolute;bottom:54px;left:0;width:320px;max-width:min(320px,90vw);background:#fff;border:1px solid #d8e0e8;border-radius:14px;box-shadow:0 12px 24px rgba(16,24,40,.14);padding:10px;z-index:20}
.chat-room-emoji-menu.is-open{display:block}
.chat-room-emoji-search{width:100%;border:1px solid #d0d5dd;border-radius:10px;padding:8px 10px;font-size:13px}
.chat-room-emoji-list{display:grid;grid-template-columns:repeat(7,minmax(0,1fr));gap:6px;max-height:220px;overflow:auto;margin-top:10px}
.chat-room-emoji-item{border:1px solid transparent;background:#fff;border-radius:10px;height:36px;display:inline-flex;align-items:center;justify-content:center;font-size:22px;padding:0;cursor:pointer}
.chat-room-emoji-item:hover{background:#f8fafc;border-color:#d8e0e8}
.chat-room-emoji-empty{font-size:12px;color:#667085;padding:10px 2px 4px}
.chat-room-input{width:100%;min-height:64px;max-height:160px;resize:vertical;border:1px solid #d0d5dd;border-radius:12px;padding:12px 14px;font:inherit}
.chat-room-send{min-width:110px}
</style>
{/literal}

<div class="chat-room-page">
	<div class="chat-room-shell">
			<div class="chat-room-header">
				<div>
					<div class="chat-room-title-row">
						<div class="chat-room-title" id="chatRoomTitle">Loading room...</div>
						<div class="chat-room-peer-status" id="chatRoomPeerStatus" style="display:none;"></div>
					</div>
					<div class="chat-room-presence-list" id="chatRoomPresenceList"></div>
					<div class="chat-room-typing" id="chatRoomTyping"></div>
				</div>
				<div class="chat-room-tools dropdown">
					<a href="#" class="chat-room-info-btn dropdown-toggle" data-toggle="dropdown" onclick="return false;">
						<i class="fa fa-info-circle-o"></i>
					</a>
					<div class="dropdown-menu dropdown-menu-right chat-room-info-menu">
						<div class="chat-room-info-line" id="chatRoomSubtitle">Connecting...</div>
						<div class="chat-room-info-line" id="chatRoomPresence">Presence loading...</div>
						<div class="chat-room-info-line" id="chatWsStatus">initializing</div>
						<div class="chat-room-info-line">
							<button type="button" class="btn btn-default btn-sm" id="chatRoomRefreshPresence">Refresh Presence</button>
						</div>
					</div>
				</div>
			</div>
		<div class="chat-room-panel">
			<div class="chat-room-messages" id="chatRoomMessages">
				<div class="chat-room-empty" id="chatRoomEmpty">Loading messages...</div>
			</div>
				<div class="chat-room-composer">
					<div class="chat-room-selected-files" id="chatRoomSelectedFiles"></div>
					<div class="chat-room-input-wrap">
						<div class="chat-room-tool-stack">
							<div class="chat-room-composer-tools">
								<button type="button" class="chat-room-emoji-toggle" id="chatRoomEmojiToggle" title="Insert emoji">&#9786;</button>
								<div class="chat-room-emoji-menu" id="chatRoomEmojiMenu">
									<input type="text" class="chat-room-emoji-search" id="chatRoomEmojiSearch" placeholder="Search emoji">
									<div class="chat-room-emoji-list" id="chatRoomEmojiList"></div>
									<div class="chat-room-emoji-empty" id="chatRoomEmojiEmpty" style="display:none;">No emoji found</div>
								</div>
							</div>
							<div class="chat-room-composer-tools">
								<button type="button" class="chat-room-attach-toggle" id="chatRoomAttachToggle" title="Attach file">&#128206;</button>
								<input type="file" id="chatRoomFileInput" multiple style="display:none;">
							</div>
						</div>
						<textarea class="chat-room-input" id="chatRoomInput" placeholder="Write a message"></textarea>
						<button type="button" class="btn btn-primary chat-room-send" id="chatRoomSend">Send</button>
					</div>
				</div>
		</div>
	</div>
</div>

{capture append=footer_hidden}
<script type="text/javascript">
require(['gwcms'], function(){
require(['js/gwchat_app'], function(GWChatApp){
	var wsUrl = '{$ws_path|escape:"javascript"}';
	var httpEndpoint = '{$http_endpoint|escape:"javascript"}';
	var requestedRoomId = parseInt('{$requested_room_id|escape:"javascript"}' || '0', 10) || 0;
	var wsStatus = document.getElementById('chatWsStatus');
	var roomTitle = document.getElementById('chatRoomTitle');
	var roomPeerStatus = document.getElementById('chatRoomPeerStatus');
	var roomSubtitle = document.getElementById('chatRoomSubtitle');
	var roomPresence = document.getElementById('chatRoomPresence');
	var roomPresenceList = document.getElementById('chatRoomPresenceList');
	var roomTyping = document.getElementById('chatRoomTyping');
	var roomMessages = document.getElementById('chatRoomMessages');
	var roomEmpty = document.getElementById('chatRoomEmpty');
	var roomEmojiToggle = document.getElementById('chatRoomEmojiToggle');
	var roomEmojiMenu = document.getElementById('chatRoomEmojiMenu');
	var roomEmojiSearch = document.getElementById('chatRoomEmojiSearch');
	var roomEmojiList = document.getElementById('chatRoomEmojiList');
	var roomEmojiEmpty = document.getElementById('chatRoomEmojiEmpty');
	var roomAttachToggle = document.getElementById('chatRoomAttachToggle');
	var roomFileInput = document.getElementById('chatRoomFileInput');
	var roomSelectedFiles = document.getElementById('chatRoomSelectedFiles');
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
	var imageUrlRequestCache = {};
	var presenceUsers = {};
	var wsOnlineUsers = {};
	var typingUsers = {};
	var typingStopTimer = 0;
	var selfTypingActive = false;
	var welcomeShown = false;
	var pendingSeenMessageId = 0;
	var lastKnownMessageId = 0;
	var gapRecoveryInFlight = false;
	var emojiCatalog = [
		{ emoji:'😀', name:'grinning' }, { emoji:'😄', name:'smile happy' }, { emoji:'😁', name:'beam grin' }, { emoji:'😊', name:'blush happy' },
		{ emoji:'😉', name:'wink' }, { emoji:'😍', name:'heart eyes love' }, { emoji:'😘', name:'kiss' }, { emoji:'🥰', name:'in love hearts' },
		{ emoji:'😎', name:'cool sunglasses' }, { emoji:'🤩', name:'star struck' }, { emoji:'😇', name:'angel innocent' }, { emoji:'🤗', name:'hug' },
		{ emoji:'🤔', name:'thinking hmm' }, { emoji:'😴', name:'sleepy' }, { emoji:'🥳', name:'party celebration' }, { emoji:'😢', name:'cry sad' },
		{ emoji:'😭', name:'sob cry' }, { emoji:'😂', name:'joy laugh tears' }, { emoji:'🤣', name:'rofl laugh' }, { emoji:'😅', name:'sweat smile' },
		{ emoji:'😮', name:'wow surprised' }, { emoji:'😱', name:'scream shocked' }, { emoji:'😡', name:'angry mad' }, { emoji:'🤯', name:'mind blown' },
		{ emoji:'👍', name:'thumbs up like' }, { emoji:'👎', name:'thumbs down dislike' }, { emoji:'👏', name:'clap applause' }, { emoji:'🙌', name:'raise hands' },
		{ emoji:'🙏', name:'pray thanks' }, { emoji:'💪', name:'strong muscle' }, { emoji:'👀', name:'eyes look' }, { emoji:'🔥', name:'fire lit' },
		{ emoji:'❤️', name:'heart love' }, { emoji:'💙', name:'blue heart' }, { emoji:'💚', name:'green heart' }, { emoji:'💛', name:'yellow heart' },
		{ emoji:'💯', name:'hundred perfect' }, { emoji:'✨', name:'sparkles' }, { emoji:'⭐', name:'star' }, { emoji:'🌟', name:'glowing star' },
		{ emoji:'🎉', name:'party popper' }, { emoji:'🎶', name:'music notes' }, { emoji:'☕', name:'coffee' }, { emoji:'🍀', name:'luck clover' }
	];

	GWChatApp.init({
		wsUrl: wsUrl,
		httpEndpoint: httpEndpoint,
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
			out += '<a href="' + escapeHtml(url) + '" target="_blank" rel="noopener noreferrer" class="chat-room-link" data-full-url-label="' + escapeHtml(href) + '" data-short-url-label="' + escapeHtml(label) + '">' + escapeHtml(label) + '</a>' + escapeHtml(suffix);
			lastIndex = match.index + raw.length;
		}

		out += escapeHtml(text.slice(lastIndex));
		return out;
	}

	function renderEmojiList(filterText)
	{
		filterText = String(filterText || '').trim().toLowerCase();
		var out = [];
		var visibleCount = 0;

		for (var i = 0; i < emojiCatalog.length; i++) {
			var item = emojiCatalog[i];
			var haystack = (item.name + ' ' + item.emoji).toLowerCase();

			if (filterText && haystack.indexOf(filterText) === -1)
				continue;

			visibleCount++;
			out.push('<button type="button" class="chat-room-emoji-item" data-chat-emoji="' + escapeHtml(item.emoji) + '" title="' + escapeHtml(item.name) + '">' + escapeHtml(item.emoji) + '</button>');
		}

		roomEmojiList.innerHTML = out.join('');
		roomEmojiEmpty.style.display = visibleCount ? 'none' : 'block';
	}

	function setEmojiMenuOpen(isOpen)
	{
		if (!roomEmojiMenu)
			return;

		roomEmojiMenu.classList.toggle('is-open', !!isOpen);
		if (isOpen) {
			renderEmojiList(roomEmojiSearch ? roomEmojiSearch.value : '');
			if (roomEmojiSearch)
				roomEmojiSearch.focus();
		}
	}

	function insertEmojiAtCursor(emoji)
	{
		emoji = String(emoji || '');
		if (!emoji || !roomInput)
			return;

		var start = roomInput.selectionStart || 0;
		var end = roomInput.selectionEnd || 0;
		var value = String(roomInput.value || '');
		roomInput.value = value.slice(0, start) + emoji + value.slice(end);
		roomInput.focus();
		roomInput.selectionStart = roomInput.selectionEnd = start + emoji.length;
		$(roomInput).trigger('input');
	}

	function setWsStatus(text)
	{
		wsStatus.textContent = text;
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

	function renderUserIdentity(userId, fullName, imageUrl)
	{
		var rawName = String(fullName || '').trim();
		var safeName = escapeHtml(rawName);
		var safeUserId = escapeHtml(userId || '');
		var safeImageUrl = String(imageUrl || '').trim();
		var initials = rawName ? rawName.split(/\s+/).slice(0, 2).map(function(part){ return part.charAt(0).toUpperCase(); }).join('') : ('U' + String(userId || ''));

		if (safeImageUrl)
			return '<img class="chat-user-avatar" src="' + escapeHtml(safeImageUrl) + '" alt="' + safeName + '" title="' + safeName + '" data-userid="' + safeUserId + '">';

		return '<span class="chat-user-fallback" data-userid="' + safeUserId + '" title="' + safeName + '">' + escapeHtml(initials) + '</span>';
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

	function renderPeerStatusDots()
	{
		if (!roomPeerStatus)
			return;

		var user = activeRoom && activeRoom.display_user ? activeRoom.display_user : null;
		var isPrivate = activeRoom && activeRoom.type === 'private';
		var peerId = user ? (parseInt(user.id || '0', 10) || 0) : 0;
		var recentOnline = !!(user && parseInt(user.recently_online || '0', 10));
		var liveWs = false;

		if (!isPrivate || !peerId) {
			roomPeerStatus.style.display = 'none';
			roomPeerStatus.innerHTML = '';
			return;
		}

		if (peerId && wsOnlineUsers[peerId])
			liveWs = true;

		roomPeerStatus.style.display = 'inline-flex';
		roomPeerStatus.innerHTML =
			'<span class="chat-room-peer-dot ' + (recentOnline ? 'is-on' : '') + '" title="user was recently online"></span>' +
			'<span class="chat-room-peer-dot ' + (liveWs ? 'is-on' : '') + '" title="user is connected live via websocket"></span>';
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

	function getLastMessageNode()
	{
		var nodes = roomMessages.querySelectorAll('.chat-room-message[data-message-id]');
		return nodes.length ? nodes[nodes.length - 1] : null;
	}

	function isElementVisibleInContainer(node, container)
	{
		if (!node || !container)
			return false;

		var nodeRect = node.getBoundingClientRect();
		var containerRect = container.getBoundingClientRect();

		return nodeRect.top >= containerRect.top && nodeRect.bottom <= containerRect.bottom;
	}

	function canMarkSeen()
	{
		if (!activeRoom || !roomMessages)
			return false;

		if (document.visibilityState !== 'visible')
			return false;

		if (typeof document.hasFocus === 'function' && !document.hasFocus())
			return false;

		if (!roomMessages.offsetParent)
			return false;

		return isElementVisibleInContainer(getLastMessageNode(), roomMessages);
	}

	async function flushSeenIfPossible()
	{
		if (!pendingSeenMessageId || !activeRoom || !canMarkSeen())
			return;

		var messageId = pendingSeenMessageId;
		pendingSeenMessageId = 0;

		try {
			await GWChatApp.markSeen(activeRoom.id, messageId);
			setTimeout(function(){
				clearUnreadHighlights(messageId);
			}, 650);
		} catch (err) {
			pendingSeenMessageId = Math.max(pendingSeenMessageId, messageId);
		}
	}

	function clearUnreadHighlights(messageId)
	{
		messageId = parseInt(messageId || '0', 10) || 0;
		if (!messageId)
			return;

		var nodes = roomMessages.querySelectorAll('.chat-room-message.is-unread[data-message-id]');
		for (var i = 0; i < nodes.length; i++) {
			var id = parseInt(nodes[i].getAttribute('data-message-id') || '0', 10) || 0;
			if (id && id <= messageId)
				nodes[i].classList.remove('is-unread');
		}
	}

	function scheduleSeen(messageId)
	{
		messageId = parseInt(messageId || '0', 10) || 0;
		if (!messageId)
			return;

		pendingSeenMessageId = Math.max(pendingSeenMessageId, messageId);
		flushSeenIfPossible();
	}

	function normalizeEntry(message)
	{
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
			insert_time: message.insert_time || ''
		};
	}

	function getReactionOptions()
	{
		return ['❤️', '👍', '😂', '😮', '😢', '🔥'];
	}

	function renderReactionPicker(messageId)
	{
		var opts = getReactionOptions();
		var out = [];

		for (var i = 0; i < opts.length; i++) {
			out.push('<button type="button" class="chat-room-reaction-option" data-reaction-message-id="' + escapeHtml(messageId) + '" data-reaction="' + escapeHtml(opts[i]) + '">' + escapeHtml(opts[i]) + '</button>');
		}

		return out.join('');
	}

	function getReactionUsersText(reactionItem)
	{
		var users = reactionItem && $.isArray(reactionItem.users) ? reactionItem.users : [];
		var names = [];

		for (var i = 0; i < users.length; i++)
			names.push(users[i].name || users[i].username || ('User #' + users[i].id));

		return names.join(', ');
	}

	function renderReactionSummary(reactions, messageId)
	{
		reactions = $.isArray(reactions) ? reactions : [];

		if (!reactions.length)
			return '';

		var out = [];

		for (var i = 0; i < reactions.length; i++) {
			var item = reactions[i] || {};
			var usersText = getReactionUsersText(item);
			out.push(
				'<button type="button" class="chat-room-reaction-chip ' + (item.reacted_by_me ? 'is-me' : '') + '" data-reaction-chip-message-id="' + escapeHtml(messageId) + '" data-reaction="' + escapeHtml(item.reaction || '') + '" data-users="' + escapeHtml(usersText) + '" title="' + escapeHtml(usersText) + '">' +
					escapeHtml(item.reaction || '') + ' ' + escapeHtml(item.count || 0) +
				'</button>'
			);
		}

		return out.join('');
	}

	function renderAttachments(attachments)
	{
		attachments = $.isArray(attachments) ? attachments : [];
		if (!attachments.length)
			return '';

		var out = '<div class="chat-room-attachments">';
		for (var i = 0; i < attachments.length; i++) {
			var file = attachments[i] || {};
			var kind = String(file.kind || 'file');
			var url = String(file.public_url || '');
			var thumb = String(file.thumb_url || url);
			var name = String(file.original_filename || file.stored_filename || 'file');
			var size = String(file.size_human || '');

			if (kind === 'image' && thumb) {
				out += '<a class="chat-room-attachment-image" href="' + escapeHtml(url || thumb) + '" target="_blank" rel="noopener noreferrer">' +
					'<img src="' + escapeHtml(thumb) + '" alt="' + escapeHtml(name) + '">' +
				'</a>';
			} else if (url) {
				out += '<a class="chat-room-attachment-file" href="' + escapeHtml(url) + '" target="_blank" rel="noopener noreferrer">' +
					'<span>&#128206;</span><span>' + escapeHtml(name) + (size ? ' <small>' + escapeHtml(size) + '</small>' : '') + '</span>' +
				'</a>';
			}
		}
		out += '</div>';

		return out;
	}

	function appendSystemMessage(text)
	{
		roomMessages.appendChild(createSystemNode({
			entry_key: 'local-' + String(new Date().getTime()),
			text: text || ''
		}));
	}

	function createSystemNode(entry)
	{
		var wrap = document.createElement('div');
		wrap.className = 'chat-room-message is-system';
		wrap.setAttribute('data-entry-key', String(entry.entry_key || ''));
		wrap.innerHTML = '<div class="chat-room-message-main"><div class="chat-room-message-bubble" title="' + escapeHtml(entry.insert_time || '') + '">' + escapeHtml(entry.text || '') + '</div></div>';
		return wrap;
	}

	function createMessageNode(message)
	{
		if (message.entry_type === 'event')
			return createSystemNode(message);

		var isMe = currentUser && parseInt(currentUser.id || '0', 10) === message.sender_id;
		var hideSenderName = !!(activeRoom && activeRoom.type === 'private');
		var node = document.createElement('div');
		node.className = 'chat-room-message ' + (isMe ? 'is-me' : 'is-other') + (message._highlight_unread ? ' is-unread' : '');
		node.setAttribute('data-message-id', String(message.id || 0));
		node.setAttribute('data-entry-key', String(message.entry_key || ''));

			var avatarHtml = renderUserIdentity(message.sender_id, message.sender_title, imageUrlCache[message.sender_id] || '');
				node.innerHTML =
					'<div class="chat-room-message-avatar" data-avatar-userid="' + escapeHtml(message.sender_id) + '">' + avatarHtml + '</div>' +
						'<div class="chat-room-message-main">' +
							(isMe || hideSenderName ? '' : '<div class="chat-room-message-name">' + escapeHtml(message.sender_title) + '</div>') +
							'<div class="chat-room-message-body">' +
								'<div class="chat-room-message-bubble">' +
									'<span class="chat-room-message-text">' + linkifyText(message.message) + '</span>' +
									renderAttachments(message.attachments) +
									'<span class="chat-room-message-meta">' +
										'<small class="chat-room-message-time" title="time:' + escapeHtml(message.insert_time) + '">' + escapeHtml(formatShortTime(message.insert_time)) + '</small>' +
										(isMe ? '<small class="chat-room-message-seen" data-seen-marker="1">' + (message.is_seen ? '✓✓' : '✓') + '</small>' : '') +
									'</span>' +
								'</div>' +
								'<button type="button" class="chat-room-reaction-launcher" data-reaction-launcher="' + escapeHtml(message.id) + '" title="Add reaction">&#9786;</button>' +
							'</div>' +
							'<div class="chat-room-reaction-picker" data-reaction-picker="' + escapeHtml(message.id) + '">' + renderReactionPicker(message.id) + '</div>' +
							'<div class="chat-room-reactions" data-reactions-wrap="' + escapeHtml(message.id) + '">' + renderReactionSummary(message.reactions, message.id) + '</div>' +
						'</div>';

		if (!isMe)
			hydrateUserAvatar(message.sender_id, message.sender_title, node.querySelector('[data-avatar-userid]'));

		return node;
	}

	function updateSeenMarkers(lastMessageId, peerUserId)
	{
		lastMessageId = parseInt(lastMessageId || '0', 10) || 0;
		peerUserId = parseInt(peerUserId || '0', 10) || 0;

		if (!activeRoom || activeRoom.type !== 'private' || !lastMessageId)
			return;

		var peerId = activeRoom.display_user ? (parseInt(activeRoom.display_user.id || '0', 10) || 0) : 0;
		if (peerUserId && peerId && peerUserId !== peerId)
			return;

		var ownUserId = currentUser ? (parseInt(currentUser.id || '0', 10) || 0) : 0;
		var nodes = roomMessages.querySelectorAll('.chat-room-message[data-message-id]');

		for (var i = 0; i < nodes.length; i++) {
			var node = nodes[i];
			var messageId = parseInt(node.getAttribute('data-message-id') || '0', 10) || 0;
			var marker = node.querySelector('[data-seen-marker]');
			var senderId = 0;

			if (!marker || !messageId)
				continue;

			senderId = ownUserId;
			if (!senderId || messageId > lastMessageId)
				continue;

			marker.textContent = '✓✓';
		}
	}

	function updateMessageReactions(messageId, reactions)
	{
		messageId = parseInt(messageId || '0', 10) || 0;
		if (!messageId)
			return;

		var node = roomMessages.querySelector('.chat-room-message[data-message-id="' + messageId + '"]');
		if (!node)
			return;

		var wrap = node.querySelector('[data-reactions-wrap="' + messageId + '"]');
		if (!wrap)
			return;

		wrap.innerHTML = renderReactionSummary(reactions, messageId);
	}

	function renderMessages(messages, mode)
	{
		messages = messages || [];

		if (mode === 'replace') {
			roomMessages.innerHTML = '';
			messageIds = {};
			lastKnownMessageId = 0;
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
			var msg = normalizeEntry(messages[i]);
			var key = String(msg.entry_key || '');
			if (!key || messageIds[key])
				continue;
			messageIds[key] = 1;
			if (msg.entry_type === 'message' && msg.id) {
				var ownId = currentUser ? parseInt(currentUser.id || '0', 10) : 0;
				if (mode === 'append' && ownId && msg.sender_id !== ownId && !msg.is_seen)
					msg._highlight_unread = 1;
				lastKnownMessageId = Math.max(lastKnownMessageId, msg.id);
			}
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

	async function recoverMessageGap(afterMessageId, fallbackMsg)
	{
		afterMessageId = parseInt(afterMessageId || '0', 10) || 0;
		if (!activeRoom || !afterMessageId)
			return;

		if (gapRecoveryInFlight) {
			if (fallbackMsg)
				renderMessages([fallbackMsg], 'append');
			return;
		}

		gapRecoveryInFlight = true;
		try {
			var resp = await GWChatApp.loadMessagesAfter(activeRoom.id, afterMessageId, 100);
			var messages = resp.messages || [];
			if (fallbackMsg)
				messages.push(fallbackMsg);
			renderMessages(messages, 'append');
			scheduleSeen(getLastMessageId());
		} catch (err) {
			if (fallbackMsg) {
				renderMessages([fallbackMsg], 'append');
				scheduleSeen(fallbackMsg.id);
			}
		} finally {
			gapRecoveryInFlight = false;
		}
	}

	function handleIncomingChatMessage(msg)
	{
		msg = normalizeEntry(msg);

		if (!msg.id)
			return;

		if (GWChatApp.shouldRecoverGap(lastKnownMessageId, msg)) {
			recoverMessageGap(lastKnownMessageId, msg);
			return;
		}

		if (msg.id <= lastKnownMessageId && messageIds[msg.entry_key])
			return;

		renderMessages([msg], 'append');
		scheduleSeen(msg.id);
	}

	function recoverIfBehindRoom(room)
	{
		if (!activeRoom || !room || parseInt(room.id || '0', 10) !== parseInt(activeRoom.id || '0', 10))
			return;

		var roomLastMessageId = parseInt(room.last_message_id || '0', 10) || 0;
		if (!roomLastMessageId || roomLastMessageId <= lastKnownMessageId)
			return;

		recoverMessageGap(lastKnownMessageId, null);
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
			if (!imageUrlRequestCache[userId]) {
				imageUrlRequestCache[userId] = $.ajax({
					url: httpEndpoint + '/userimage',
					method: 'GET',
					dataType: 'text',
					data: { id: userId }
				});
			}

			var url = await imageUrlRequestCache[userId];

			imageUrlCache[userId] = String(url || '').trim();
			container.innerHTML = renderUserIdentity(userId, fullName, imageUrlCache[userId]);
		} catch (err) {
			imageUrlCache[userId] = '';
			container.innerHTML = renderUserIdentity(userId, fullName, '');
		} finally {
			delete imageUrlRequestCache[userId];
		}
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
			renderPeerStatusDots();
			return;
		}

		roomPresence.textContent = 'Online in room: ' + list.length;
		renderPresenceChips(list);
		renderPeerStatusDots();
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
			wsOnlineUsers = {};

			for (var i = 0; i < (resp.online_users || []).length; i++) {
				var item = resp.online_users[i];
				presenceUsers[item.id] = item;
			}

			for (var j = 0; j < (resp.ws_online_users || []).length; j++) {
				var wsItem = resp.ws_online_users[j];
				wsOnlineUsers[wsItem.id] = wsItem;
			}

			updatePresenceText();

			if (!welcomeShown) {
				welcomeShown = true;
				appendSystemMessage('Joined ' + formatRoomTitle(activeRoom) + ', online now ' + (resp.online_users || []).length + ', history limit ' + (activeRoom.room_history_limit || 0));
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
			roomSubtitle.textContent = 'Room join failed, DB mode only';
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
		var resp = await roomEndpoint('doRoomBootstrap', { room_id: requestedRoomId, limit: 200 });
		activeRoom = resp.room;
		currentUser = resp.current_user || null;
		GWChatApp.setActiveRoom(activeRoom.id, true);
		GWChatApp.suppressDockRoom(activeRoom.id);
		roomTitle.textContent = formatRoomTitle(activeRoom);
		roomSubtitle.textContent = 'Room #' + activeRoom.id + ', loading presence...';
		renderPeerStatusDots();
		historyExhausted = false;
		welcomeShown = false;
		renderMessages(resp.messages || [], 'replace');
		await refreshRoomPresence();
		await joinActiveRoom();
		var lastMessageId = getLastMessageId();
		if (lastMessageId)
			scheduleSeen(lastMessageId);
	}

	function ensurePageConnection()
	{
		if (pageClient)
			return pageClient;

		pageClient = GWChatApp.getClient();

		pageClient.on('connect', function(){
			setWsStatus('connected ' + pageClient.getResolvedUrl());
		});

		pageClient.on('disconnect', function(){
			setWsStatus('websocket disconnected, DB mode still active');
		});

		pageClient.on('error', function(){
			setWsStatus('websocket unavailable, DB mode still active');
		});

		pageClient.on('reconnect_scheduled', function(info){
			setWsStatus('reconnecting in ' + Math.round((info.delay_ms || 0) / 1000) + 's');
		});

		pageClient.on('reconnect_attempt', function(){
			setWsStatus('reconnecting...');
		});

		pageClient.on('action:hello', function(){
			setWsStatus('connected ' + pageClient.getResolvedUrl());
			joinActiveRoom();
		});

		pageClient.on('action:chat_message', function(packet){
			var msg = normalizeEntry(packet);
			if (!activeRoom || msg.room_id !== parseInt(activeRoom.id || '0', 10))
				return;

			handleIncomingChatMessage(msg);
		});

		pageClient.on('action:chat_event', function(packet){
			var entry = normalizeEntry(packet.event || {});
			if (!activeRoom || entry.room_id !== parseInt(activeRoom.id || '0', 10))
				return;

			renderMessages([entry], 'append');
			scheduleSeen(getLastMessageId());
		});

		pageClient.on('action:chat_reaction_update', function(packet){
			if (!activeRoom || parseInt(packet.room_id || '0', 10) !== parseInt(activeRoom.id || '0', 10))
				return;

			updateMessageReactions(packet.message_id, packet.reactions || []);
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
		});

		pageClient.on('action:room_user_left', function(packet){
			if (!activeRoom || parseInt(packet.room_id || '0', 10) !== parseInt(activeRoom.id || '0', 10))
				return;

			refreshRoomPresence();
		});

		if (pageClient.socket && pageClient.socket.readyState === 1) {
			setWsStatus('connected ' + pageClient.getResolvedUrl());
			joinActiveRoom();
		} else {
			setWsStatus('connecting ' + pageClient.getResolvedUrl());
		}

		return pageClient;
	}

	async function sendCurrentMessage()
	{
		if (!activeRoom)
			return;

		var text = String(roomInput.value || '').trim();
		var files = roomFileInput && roomFileInput.files ? Array.prototype.slice.call(roomFileInput.files) : [];
		if (!text && !files.length)
			return;

		roomSend.disabled = true;

		try {
			if (files.length) {
				var formData = new FormData();
				formData.append('act', 'doSendMessage');
				formData.append('room_id', activeRoom.id);
				formData.append('message', text);
				for (var i = 0; i < files.length; i++)
					formData.append('attachments[]', files[i]);

				var uploadResp = await $.ajax({
					url: httpEndpoint,
					method: 'POST',
					dataType: 'json',
					data: formData,
					processData: false,
					contentType: false
				});
				if (uploadResp && uploadResp.message)
					renderMessages([uploadResp.message], 'append');
			} else if (pageClient && pageClient.socket && pageClient.socket.readyState === 1) {
				var wsResp = await pageClient.sendMessage(activeRoom.id, text);
				if (wsResp && wsResp.message)
					renderMessages([wsResp.message], 'append');
			} else {
				await roomEndpoint('doSendMessage', { room_id: activeRoom.id, message: text });
				var refresh = await roomEndpoint('doLoadMessages', { room_id: activeRoom.id, limit: 200 });
				renderMessages(refresh.messages || [], 'replace');
			}

			roomInput.value = '';
			if (roomFileInput)
				roomFileInput.value = '';
			updateSelectedFiles();
			roomInput.focus();
			if (selfTypingActive && pageClient && pageClient.socket && pageClient.socket.readyState === 1) {
				selfTypingActive = false;
				pageClient.typing(activeRoom.id, false).catch(function(){});
			}
		} catch (err) {
			appendSystemMessage('Send failed');
		} finally {
			roomSend.disabled = false;
		}
	}

	function scheduleTypingStop()
	{
		clearTimeout(typingStopTimer);
		typingStopTimer = setTimeout(function(){
			if (!activeRoom || !pageClient || !selfTypingActive || !pageClient.socket || pageClient.socket.readyState !== 1)
				return;

			selfTypingActive = false;
			pageClient.typing(activeRoom.id, false).catch(function(){});
		}, 1200);
	}

	function updateSelectedFiles()
	{
		if (!roomSelectedFiles || !roomFileInput)
			return;

		var files = roomFileInput.files ? Array.prototype.slice.call(roomFileInput.files) : [];
		if (!files.length) {
			roomSelectedFiles.classList.remove('is-visible');
			roomSelectedFiles.textContent = '';
			return;
		}

		roomSelectedFiles.textContent = files.map(function(file){ return file.name; }).join(', ');
		roomSelectedFiles.classList.add('is-visible');
	}

		$('#chatRoomRefreshPresence').on('click', function(){
			refreshRoomPresence();
		});

		$(roomEmojiToggle).on('click', function(e){
			e.preventDefault();
			e.stopPropagation();
			setEmojiMenuOpen(!roomEmojiMenu.classList.contains('is-open'));
		});

		$(roomEmojiSearch).on('input', function(){
			renderEmojiList(roomEmojiSearch.value || '');
		});

		$(roomEmojiList).on('click', '[data-chat-emoji]', function(){
			insertEmojiAtCursor($(this).data('chat-emoji'));
			setEmojiMenuOpen(false);
		});

		$(roomAttachToggle).on('click', function(e){
			e.preventDefault();
			if (roomFileInput)
				roomFileInput.click();
		});

		$(roomFileInput).on('change', function(){
			updateSelectedFiles();
		});

		$(document).on('click', function(e){
			if (!roomEmojiMenu || !roomEmojiToggle)
				return;

			if (roomEmojiMenu.contains(e.target) || roomEmojiToggle.contains(e.target))
				return;

			setEmojiMenuOpen(false);
		});

	$(roomSend).on('click', function(){
		sendCurrentMessage();
	});

		$(roomInput).on('keydown', function(e){
			if (e.key === 'Enter' && !e.shiftKey) {
				e.preventDefault();
				sendCurrentMessage();
			}

			if (e.key === 'Escape')
				setEmojiMenuOpen(false);
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

			flushSeenIfPossible();
		});

		$(roomMessages).on('click', '[data-reaction-launcher]', function(){
			var messageNode = this.closest('.chat-room-message');
			var messageId = $(this).data('reaction-launcher');
			var picker = roomMessages.querySelector('[data-reaction-picker="' + messageId + '"]');
			if (!picker)
				return;

			$('.chat-room-message.is-reaction-open', roomMessages).removeClass('is-reaction-open');
			picker.classList.toggle('is-open');
			if (messageNode && picker.classList.contains('is-open'))
				messageNode.classList.add('is-reaction-open');
		});

		$(roomMessages).on('click', '.chat-room-message-bubble', function(){
			if (window.matchMedia && window.matchMedia('(hover: none)').matches) {
				var messageNode = this.closest('.chat-room-message');
				if (!messageNode)
					return;

				$('.chat-room-message.is-reaction-open', roomMessages).removeClass('is-reaction-open');
				messageNode.classList.add('is-reaction-open');
			}
		});

		$(roomMessages).on('click', '[data-reaction-message-id]', async function(){
			var messageId = $(this).data('reaction-message-id');
			var reaction = $(this).data('reaction');
			var picker = roomMessages.querySelector('[data-reaction-picker="' + messageId + '"]');

			function appendReactionEvent(packet)
			{
				if (!packet || !packet.event_id || !packet.event_text)
					return;

				renderMessages([{
					entry_type: 'event',
					entry_key: 'e' + String(packet.event_id || ''),
					event_id: packet.event_id || 0,
					room_id: packet.room_id || 0,
					user_id: packet.user_id || 0,
					event_type: packet.event_type || '',
					text: packet.event_text || '',
					insert_time: packet.insert_time || ''
				}], 'append');
			}

			try {
				if (pageClient && pageClient.socket && pageClient.socket.readyState === 1) {
					try {
						var resp = await pageClient.toggleReaction(messageId, reaction);
						if (resp && resp.packet)
							updateMessageReactions(resp.packet.message_id, resp.packet.reactions || []);
					} catch (wsErr) {
						var ajaxFallbackResp = await roomEndpoint('doToggleReaction', { message_id: messageId, reaction: reaction });
						if (ajaxFallbackResp && ajaxFallbackResp.packet) {
							updateMessageReactions(ajaxFallbackResp.packet.message_id, ajaxFallbackResp.packet.reactions || []);
							appendReactionEvent(ajaxFallbackResp.packet);
						}
					}
				} else {
					var ajaxResp = await roomEndpoint('doToggleReaction', { message_id: messageId, reaction: reaction });
					if (ajaxResp && ajaxResp.packet) {
						updateMessageReactions(ajaxResp.packet.message_id, ajaxResp.packet.reactions || []);
						appendReactionEvent(ajaxResp.packet);
					}
				}
			} catch (err) {
				appendSystemMessage('Reaction update failed');
			}

			if (picker)
				picker.classList.remove('is-open');
			$('.chat-room-message.is-reaction-open', roomMessages).removeClass('is-reaction-open');
		});

		$(roomMessages).on('click', '[data-reaction-chip-message-id]', function(){
			var users = String($(this).data('users') || '').trim();
			if (users)
				alert(users);
		});

		$(roomMessages).on('click', '.chat-room-link', function(e){
			if ($(this).data('expanded'))
				return;

			e.preventDefault();
			e.stopPropagation();
			$(this).data('expanded', 1).text($(this).data('full-url-label'));
		});

		$(window).on('focus', function(){
			flushSeenIfPossible();
		});

	$(document).on('visibilitychange', function(){
		flushSeenIfPossible();
	});

	GWChatApp.on('rooms', function(resp){
		var rooms = resp && resp.rooms ? resp.rooms : [];
		for (var i = 0; i < rooms.length; i++)
			recoverIfBehindRoom(rooms[i]);
	});

	GWChatApp.on('seen', function(packet){
		if (!activeRoom || parseInt(packet.room_id || '0', 10) !== parseInt(activeRoom.id || '0', 10))
			return;

		if (currentUser && parseInt(packet.user_id || '0', 10) === parseInt(currentUser.id || '0', 10))
			return;

		updateSeenMarkers(packet.last_message_id, packet.user_id);
	});

	ensurePageConnection();
	bootstrapRoomChat().then(function(){
		GWChatApp.loadBubbleData();
	}).catch(function(err){
		appendSystemMessage('Room bootstrap failed: ' + (err && err.responseJSON && err.responseJSON.error ? err.responseJSON.error : 'unknown error'));
		roomTitle.textContent = 'Room failed to load';
		roomSubtitle.textContent = requestedRoomId ? ('Requested room id=' + requestedRoomId) : 'Default room bootstrap failed';
	});
});
});
</script>
{/capture}

{include file="default_close.tpl"}

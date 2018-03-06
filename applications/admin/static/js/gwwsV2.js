
//api
function GW_WS() {
		
	this.socket=false;
	this.pingtimer=false;
	this.msgid=0;
	this.callbacks={};
	this.callbackTimeouts={};
	this.debug=1;
	this.messageCallbacks={};
	this.events={};
	this.verbose=true;
	this.auto_reconnect=true;
	this.last_connection_info={};
	this.reconnect_speed=3;
	this.init_reconn_speed=3;
	
	var ws = this;
	
	
	this.fireEvent= function(event, context)
	{
		if(ws.events.hasOwnProperty(event))
			for(name in ws.events[event])
				ws.events[event][name](context);
		
	};
	
	this.procCallback= function(msgid, payload) {
		if (this.callbacks[msgid]) {
			ws.callbacks[msgid](payload);
			delete ws.callbacks[msgid];
			clearTimeout(ws.callbackTimeouts[msgid])
			delete ws.callbackTimeouts[msgid];

			//this.log("response received");
		}
	};	
	
	
	this.connect=function(url, user, pass)
	{
		this.last_connection_info={ url:url, user:user, pass:pass };
		
		if (this.socket)
		{
			return this.log('Already connected');
		}


		this.socket = new WebSocket(url);
		this.socket.onmessage = this.onmessage;
		

		this.socket.onopen = function () {

			ws.fireEvent('connect');

			ws.pingStart();

			if(ws.verbose)
				ws.log('connected');
			
			ws.reconnect_speed = ws.init_reconn_speed;
		}


		this.socket.onclose = this.close;
	};
	
	this.auth= function(user, pass) {
		ws.send('auth', false, JSON.stringify({user: user, pass: pass}))
	};
	this.generalAction= function(action, data, callback)
	{
		ws.send0({action: action, data: data},
				function (data) {
					if (!data) {
						var success = false;
						this.log(action + ' timeout');
					} else {
						var success = data.data == 'SUCCESS'

						if (success) {
							
							if(this.verbose)
								this.log(action + ' was successfull');
							
						} else {
							this.log(action + ' failed. Errors: ' + JSON.stringify(data.errors));
						}
					}
					
					if(callback)
						callback(success, data);//callback with success state
				},
				10000
				); //10s timeout		
	};
	this.createuser= function(userdata, callback)
	{
		ws.generalAction('createuser', userdata, callback);
	};
	this.authorise= function(userdata, callback)
	{
		ws.generalAction('authorise', userdata, function(success, data){
			if(success)
				ws.fireEvent('authorise', data);
			
			if(callback)
				callback(success, data);
		});
		
		
	};
	this.createchan= function(chandata, callback){
		this.generalAction('createchan', chandata, callback);
	};
	
	//chandata {channel: "yourchannel", pass: "SpecifyIfItIsNeeded"}
	this.joinchan= function(chandata, callback){
		this.generalAction('joinchan', chandata, function(success, data){
			if(success)
				ws.fireEvent('joinchan', data);
			
			if(callback)
				callback(success, data)
		});
	};	
	this.infochan= function(chandata, callback){
		this.generalAction('infochan', chandata, function(success, data){
			if(success)
				ws.fireEvent('infochan_receive', data);
			
			if(callback)
				callback(success, data)
		});
	};
	this.chanlist= function(callback){
		ws.generalAction('chanlist', {}, callback);		
	};
	
	this.messagechan= function(channel, message, callback){
		ws.generalAction('messagechan', {channel: channel, message: message}, callback);		
	};
	this.messageprivate= function(user, message, callback){
		ws.generalAction('messageprivate', {user: user, message: message}, callback);		
	};
	
	
	this.onmessage= function(e) {

		if(this.verbose)
			this.log("Text message received: " + e.data);
		
		var msg = JSON.parse(e.data);

		if (msg.msgid)
			ws.procCallback(msg.msgid, msg);

		
		ws.processMessageCallback(msg.action, msg)
		ws.processMessageCallback('any', msg)
	};
	
	this.registerMessageCallback= function(action, name, callback){
		
		if(!ws.messageCallbacks.hasOwnProperty(action))
			ws.messageCallbacks[action] = {}
		
		ws.messageCallbacks[action][name] = callback;
	};
	
	this.processMessageCallback= function(action, msg)
	{
		if(ws.messageCallbacks.hasOwnProperty(action))
			for(name in ws.messageCallbacks[action])
				ws.messageCallbacks[action][name](msg);	
	};
	
	this.registerEvent= function(event, name, callback){
		if(!this.events.hasOwnProperty(event))
			this.events[event] = {}
		
		this.events[event][name] = callback;		
	};	
	
	this.close= function() {

		ws.log("Connection closed.");
		ws.socket = null;

		ws.pingStop()

		ws.fireEvent('disconnect');
		
		if(this.auto_reconnect)
		{
			setTimeout(ws.reconnect, ws.reconnect_speed*1000);
			//incremental - prevent overloading
			ws.reconnect_speed++;
		}
	};
	
	this.reconnect= function()
	{
		this.log('Trying reconnect');
		
		ws.connect(ws.last_connection_info.url, ws.last_connection_info.user, ws.last_connection_info.pass)
		
		//if connection will fail, close event will work
	};
	
	//simlified
	this.send= function(action, channel, data)
	{
		var msg = {action: action, '#': channel, data: data};

		ws.send0(msg);
	};
	//low level
	this.send0= function(data, callback, timeout) {

		if (!this.socket) {
			this.log('Not connected');
			return false;
		}
		data.msgid = ++this.msgid;
		
		ws.fireEvent('before_message', data);
		
		if(ws.socket.readyState!=1)
			ws.log('Connection not open');
		
		ws.socket.send(JSON.stringify(data));

		if (callback)
			ws.callbacks[data.msgid] = callback;

		if (timeout)
			ws.callbackTimeouts[data.msgid] = setTimeout(function () {
				ws.callbackTimeout(data.msgid)
			}, timeout);
	};
	this.callbackTimeout= function(msgid) {
		ws.log('timeout msgid' + msgid);

		ws.procCallback(msgid, false)
	};

	this.disconnect= function()
	{
		ws.socket.close();

		ws.log('Disconnect');
	};
	this.ping= function(){
		ws.send0({action: 'ping'}, function (data) {
			//process reply
			if (data) {
				//pong received
			} else {
				ws.fireEvent('ping_timeout')
			}
		}, 5000);
	};
	this.pingStart= function() {
		ws.pingtimer = setInterval(ws.ping, 60000);
	};
	this.pingStop= function() {
		clearInterval(ws.pingtimer);
	};
	this.log= function(data)
	{
		if (ws.debug)
			console.log( typeof data == 'string' ? data : JSON.stringify(data));
	};
	this.testLoad= function(){
		
		for(var i=0;i<100;i++)
			ws.ping();
			
	}

}


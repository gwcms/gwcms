var GW_SW = {
		//will be owerwriten this is just example
		params: {
				registration_url: '',
				auto_subscribe: 1,
				btn_controls: 0
				
		},
		can_enable_push: false,
		is_enabled_push: false,
		subscription: false,
		
		init: function (params)
		{
				GW_SW.params = params;

				// Check that service workers are supported, if so, progressively  
				// enhance and add push messaging support, otherwise continue without it.  
				if ('serviceWorker' in navigator) {

						navigator.serviceWorker.register(GW.base + "sw.js")
								.then(GW_SW.initialiseState);
				} else {
						GW_SW.console().warn('Service workers aren\'t supported in this browser.');
				}
				
				if(GW_SW.params.btn_controls)
					GW_SW.initBtns();	
		},
		
		initBtns: function()
		{
				GW_SW.params.btn_controls=true;
				
				$('#subscribe_btn').click(function(){
						$('#subscribe_btn').get(0).disabled = true;
						
						if(GW_SW.is_enabled_push){
								GW_SW.unsubscribe();
								
								$.cookie('no_auto_subscription', 1, {expires: 120, path: GW.app_base});
						}else{
								
								$.cookie('no_auto_subscription', null);
								GW_SW.subscribe();
						}
				})	
				
				GW_SW.stateChangeBtns();
		},
		
		//will be called when state changes
		
		stateChange: function(is_enabled_push, can_enable_push)
		{
				GW_SW.is_enabled_push = is_enabled_push;
				GW_SW.can_enable_push = can_enable_push;
				
				if(GW_SW.params.btn_controls)
						GW_SW.stateChangeBtns()
				
				if(GW_SW.params.auto_subscribe && !is_enabled_push && can_enable_push){
						
						if($.cookie('no_auto_subscription'))
						{
								GW_SW.console().log('no_auto_subscription');
						}else{
								GW_SW.console().log('auto subscribe');
								GW_SW.subscribe();	
						}
						
						
				}
		},
		
		stateChangeBtns: function()
		{
				$('#subscribe_btn').toggle(GW_SW.can_enable_push);
				$('#subscribe_btn').text(GW_SW.is_enabled_push ? $('#subscribe_btn').attr('data-disable') : $('#subscribe_btn').attr('data-enable'));
								
				if(GW_SW.can_enable_push)
						$('#subscribe_btn').get(0).disabled = false;
				
				$('#test_subscribe_btn').toggle(GW_SW.can_enable_push && GW_SW.is_enabled_push);
		},
		
		// Once the service worker is registered set the initial state 
		initialiseState: function (reg) {

				//GW_SW.console().log('Registration succeeded. Scope is ' + reg.scope);

				// Are Notifications supported in the service worker?  
				if (!('showNotification' in ServiceWorkerRegistration.prototype)) {
						GW_SW.console().warn('Notifications aren\'t supported.');
						return;
				}

				// Check the current Notification permission.  
				// If its denied, it's a permanent block until the  
				// user changes the permission  
				if (Notification.permission === 'denied') {
						GW_SW.console().warn('The user has blocked notifications.');
						return;
				}

				// Check if push messaging is supported  
				if (!('PushManager' in window)) {
						GW_SW.console().warn('Push messaging isn\'t supported.');
						return;
				}



				// We need the service worker registration to check for a subscription  
				navigator.serviceWorker.ready.then(function (serviceWorkerRegistration) {
						

						// Do we already have a push message subscription?  
						serviceWorkerRegistration.pushManager.getSubscription()
								.then(function (subscription) {
										
										GW_SW.console().log(GW_SW)
								
								
										if (!subscription) {
												return GW_SW.stateChange(false, true);
										}

										GW_SW.stateChange(true, true);
										
										GW_SW.subscription = subscription;
										GW_SW.registersubscription();

								})
								.catch(function (err) {
										GW_SW.console().warn('Error during getSubscription()', err);
								});
				});
		},
		
		registersubscription: function () {

				GW_SW.console().log(GW_SW.subscription.endpoint);

				subscriptionid = GW_SW.subscription.endpoint.replace('https://android.googleapis.com/gcm/send/', '');


				if ($.cookie('android_subscription') == subscriptionid) //registration already present
						return GW_SW.console().log('already subscribed')

				$.cookie('android_subscription', subscriptionid, {expires: 30, path: GW.app_base});
				$.ajax(
						{
								url: GW_SW.params.registration_url,
								data: {subscription: subscriptionid}


						}
				).done(function (data) {
						GW_SW.console().log("Response:" + data);

				});

		},
		subscribe: function () {
				GW_SW.console().log('subscribe...');
				
				navigator.serviceWorker.ready.then(function (serviceWorkerRegistration) {

						serviceWorkerRegistration.pushManager.subscribe({userVisibleOnly: true})
								.then(function (subscription) {
										// The subscription was successful  
										GW_SW.stateChange(true, true)
										GW_SW.subscription=subscription;
										
										// TODO: Send the subscription.endpoint to your server  
										// and save it to send a push message at a later date
										return GW_SW.registersubscription();
								})
								.catch(function (e) {
										if (Notification.permission === 'denied') {
												// The user denied the notification permission which  
												// means we failed to subscribe and the user will need  
												// to manually change the notification permission to  
												// subscribe to push messages  
												GW_SW.console().warn('Permission for Notifications was denied');
												GW_SW.stateChange(true, false);
										} else {
												// A problem occurred with the subscription; common reasons  
												// include network errors, and lacking gcm_sender_id and/or  
												// gcm_user_visible_only in the manifest.  
												GW_SW.console().error('Unable to subscribe to push.', e);
												GW_SW.stateChange(true, false);
										}
								});
				});
		},
		
		unsubscribe: function()
		{
				GW_SW.console().log('unsubscribe...');
				
				GW_SW.can_enable_push = false;

				navigator.serviceWorker.ready.then(function (serviceWorkerRegistration) {
						// To unsubscribe from push messaging, you need get the  
						// subscription object, which you can call unsubscribe() on.  
						serviceWorkerRegistration.pushManager.getSubscription().then(
								function (pushSubscription) {
										// Check we have a subscription to unsubscribe  
										if (!pushSubscription) {
												// No subscription object, so set the state  
												// to allow the user to subscribe to push  
												GW_SW.stateChange(false, true);
										}

										var subscriptionId = pushSubscription.subscriptionId;
										// TODO: Make a request to your server to remove  
										// the subscriptionId from your data store so you
										// don't attempt to send them push messages anymore

										// We have a subscription, so call unsubscribe on it  
										pushSubscription.unsubscribe().then(function (successful) {
												GW_SW.stateChange(false, true);
										}).catch(function (e) {
												// We failed to unsubscribe, this can lead to  
												// an unusual state, so may be best to remove
												// the users data from your data store and
												// inform the user that you have done so

												GW_SW.console().log('Unsubscription error: ', e);
												GW_SW.stateChange(true, true);
										});
								}).catch(function (e) {
								GW_SW.console().error('Error thrown while unsubscribing from push messaging.', e);
						});
				});
				
		},
		
		console: function ()
		{
				if(this.params.debug){
						return console;
				}
				
				if(this.params.alert){
						return {
								log:function(msg){alert(msg)},
								warn:function(msg){alert(msg)},
								error:function(msg){alert(msg);console.error(msg)}
						}
				}
				
						return {
								log:function(msg){},
								warn:function(msg){},
								error:function(msg){console.error(msg)}
						}				
		}		
}



$(document).ready(function(){
	GW_SW.init({ 
		registration_url: GW.app_base+GW.ln+'/users/profile&act=doStoreSubscription',
		auto_subscribe:true

	})
});	
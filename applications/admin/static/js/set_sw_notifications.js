//todo unsubscribe

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
		GW_SW.log('Init start');

		GW_SW.params = params;
		// Check that service workers are supported, if so, progressively  
		// enhance and add push messaging support, otherwise continue without it.  
		if ('serviceWorker' in navigator) {

			navigator.serviceWorker.register("sw.js", {scope: './'})
				.then(GW_SW.initialiseState);
		} else {
			GW_SW.stateChange(false, false);
			GW_SW.log('Service workers aren\'t supported in this browser. or maybe not https?');
		}

		if (GW_SW.params.btn_controls)
			GW_SW.initBtns();
	},
	// Once the service worker is registered set the initial state 
	initialiseState: function (reg) {

		GW_SW.log('reg. success');
		GW_SW.log(reg);




		if (!('serviceWorker' in navigator)) {
			GW_SW.log('Service workers are not supported by this browser');
			GW_SW.stateChange(false, false);
			return;
		}

		if (!('PushManager' in window)) {
			GW_SW.log('Push notifications are not supported by this browser');
			GW_SW.stateChange(false, false);
			return;
		}

		if (!('showNotification' in ServiceWorkerRegistration.prototype)) {
			GW_SW.log('Notifications are not supported by this browser');
			GW_SW.stateChange(false, false);
			return;
		}

		// Check the current Notification permission.
		// If its denied, the button should appears as such, until the user changes the permission manually
		if (Notification.permission === 'denied') {
			GW_SW.log('Notifications are denied by the user');
			GW_SW.stateChange(false, false);
			return;
		}



		// We need the service worker registration to check for a subscription  
		navigator.serviceWorker.ready.then(function (serviceWorkerRegistration) {

			// Do we already have a push message subscription?  
			serviceWorkerRegistration.pushManager.getSubscription()
				.then(function (subscription) {

					if (!subscription) {
						GW_SW.log('No subsription');
						return GW_SW.stateChange(false, true);
					}

					GW_SW.stateChange(true, true);

					GW_SW.subscription = subscription;

					if (GW_SW.params.auto_subscribe)
					{
						GW_SW.log('Auto subscribe');
						GW_SW.subscribe();
					}
					//GW_SW.subs();

				})
				.catch(function (err) {
					GW_SW.log('Error during getSubscription()', err);
				});
		});




	},

	checkNotificationPermission: function () {
		return new Promise((resolve, reject) => {
			if (Notification.permission === 'denied') {
				return reject(new Error('Push messages are blocked.'));
			}

			if (Notification.permission === 'granted') {
				return resolve();
			}

			if (Notification.permission === 'default') {
				return Notification.requestPermission().then(result => {
					if (result !== 'granted') {
						reject(new Error('Bad permission result'));
					}

					resolve();
				});
			}
		});
	},

	urlBase64ToUint8Array: function (base64String) {
		const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
		const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');

		const rawData = window.atob(base64);
		const outputArray = new Uint8Array(rawData.length);

		for (let i = 0; i < rawData.length; ++i) {
			outputArray[i] = rawData.charCodeAt(i);
		}
		return outputArray;
	},

	subscribe: function () {
		
		
		
		

		return GW_SW.checkNotificationPermission()
			.then(() => navigator.serviceWorker.ready)
			.then(serviceWorkerRegistration =>
				serviceWorkerRegistration.pushManager.subscribe({
					userVisibleOnly: true,
					applicationServerKey: GW_SW.urlBase64ToUint8Array(GW.vapid),
				})
			)
			.then(subscription => {
				// Subscription was successful
				// create subscription on your server

				GW_SW.sendSubscriptionToServer(subscription, 'POST');
				//return push_sendSubscriptionToServer(subscription, 'POST');
			})
			.then(subscription => subscription && GW_SW.stateChange(true, true)) // update your UI
			.catch(e => {
				if (Notification.permission === 'denied') {
					// The user denied the notification permission which
					// means we failed to subscribe and the user will need
					// to manually change the notification permission to
					// subscribe to push messages
					GW_SW.log('Notifications are denied by the user.');
					GW_SW.stateChange(false, false);

				} else {
					// A problem occurred with the subscription; common reasons
					// include network errors or the user skipped the permission
					GW_SW.log('Impossible to subscribe to push notifications', e);
					GW_SW.stateChange(false, false);
				}
			});


	},
	
	unsubscribe: function(){

		GW_SW.stateChange(false, false);

		// To unsubscribe from push messaging, you need to get the subscription object
		navigator.serviceWorker.ready
		  .then(serviceWorkerRegistration => serviceWorkerRegistration.pushManager.getSubscription())
		  .then(subscription => {
		    // Check that we have a subscription to unsubscribe
		    if (!subscription) {
		      // No subscription object, so set the state
		      // to allow the user to subscribe to push
		      GW_SW.stateChange(false, true);
		      return;
		    }

		    // We have a subscription, unsubscribe
		    // Remove push subscription from server
			localStorage.setItem("subscriptionid", false);

			const key = subscription.getKey('p256dh');
			const token = subscription.getKey('auth');
			const contentEncoding = (PushManager.supportedContentEncodings || ['aesgcm'])[0];
			var subscriptiondata = JSON.stringify({
				endpoint: subscription.endpoint,
				publicKey: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null,
				authToken: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null,
				contentEncoding,
			});

			$.ajax({
				type: "POST",
				url: GW_SW.getSubscriptionEndpoint(),
				data: {data: subscriptiondata, unsubscribe: 1 },
				success: function (data) {
					GW_SW.log("Server response:" + data);

					gw_adm_sys.notify('success', data, { timer: 10000 });
				}
			});
			

			return subscription;
				
		  })
		  .then(subscription => subscription.unsubscribe())
		  .then(() => GW_SW.stateChange(false, true))
		  .catch(e => {
		    // We failed to unsubscribe, this can lead to
		    // an unusual state, so  it may be best to remove
		    // the users data from your data store and
		    // inform the user that you have done so
		    console.log('Error when unsubscribing the user');
		    console.log(e)
		    GW_SW.stateChange(false, false);
		  });
  		
	},

	sendSubscriptionToServer: function (subscription, method) {
		const key = subscription.getKey('p256dh');
		const token = subscription.getKey('auth');
		const contentEncoding = (PushManager.supportedContentEncodings || ['aesgcm'])[0];


		var subscriptiondata = JSON.stringify({
			endpoint: subscription.endpoint,
			publicKey: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null,
			authToken: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null,
			contentEncoding,
		});

		var subscriptionid = GW_SW.subscription.endpoint;


		if(localStorage.getItem("subscriptionid") == subscriptionid){
			
			GW_SW.log('Already set. no server inform');
			
		}else{
			localStorage.setItem("subscriptionid", subscriptionid);
			
			var ua = Sniffer(window.navigator.userAgent);
			
			$.ajax({
				type: "POST",
				url: GW_SW.getSubscriptionEndpoint(),
				data: {data: subscriptiondata, user_agent: JSON.stringify(ua) },
				success: function (data) {
					GW_SW.log("Server response:" + data);


					gw_adm_sys.notify('success', data, { timer: 10000 });

				}
			})			
			
		}

	


	},

	initBtns: function ()
	{
		GW_SW.params.btn_controls = true;
		GW_SW.log('Init bell btn');

		$('#updateserviceworker').click(function () {
			GW_SW.log('update service worker');
			navigator.serviceWorker.register('/sw.js').then(reg => {
				// sometime later…
				reg.update();
			});

		})


		$('#subscribe_btn').click(function () {

			if (!GW_SW.is_enabled_push && !GW_SW.can_enable_push) {
				alert('Notifications cant be enabled');
			} else if( GW_SW.can_enable_push && GW_SW.is_enabled_push) {
				//alert('bell btn click');
				GW_SW.log('Start unsubscribe');
				GW_SW.unsubscribe();
			} else if( GW_SW.can_enable_push && !GW_SW.is_enabled_push) {
				GW_SW.log('Start subscribe');
				GW_SW.subscribe();
				
			}
			//$('#subscribe_btn').get(0).disabled = true;



			/*
			 if(GW_SW.is_enabled_push){
			 GW_SW.unsubscribe();
			 
			 $.cookie('no_auto_subscription', 1, {expires: 120, path: GW.app_base});
			 }else{
			 //GW_SW.unsubscribe();
			 $.cookie('no_auto_subscription', null);
			 GW_SW.subscribe();
			 }
			 * 
			 */
		});
		
		

		$('#unregister_sw').click(function () {
			reg.unregister().then(function (result) {
				GW_SW.log('Unregistered done', result);
			});
		});

		//GW_SW.stateChangeBtns();
	},

	stateChange: function (is_enabled_push, can_enable_push)
	{
		GW_SW.is_enabled_push = is_enabled_push;
		GW_SW.can_enable_push = can_enable_push;

		//istestuot be https kai
		if (!is_enabled_push && !can_enable_push) {
			//GW_SW.log('Notifications cant run');
			$('#subscribe_btn').css('color', 'red')
			$('#subscribe_btn').html('<span class="material-symbols-outlined"  translate="no">notifications_off</span>')
		}else if(is_enabled_push && can_enable_push) {

			$('#subscribe_btn').html('<span class="material-symbols-outlined"  translate="no">notifications</span>')

			$('#subscribe_btn').css('color', 'inherit');
			GW_SW.log('Notifications ready');
		}else if(!is_enabled_push && can_enable_push) {

			$('#subscribe_btn').html('<span class="material-symbols-outlined"  translate="no">notifications_off</span>')

			$('#subscribe_btn').css('color', 'inherit');
		}

	},

	getSubscriptionEndpoint: function ()
	{
		return GW.app_base + GW.ln + '/users/profile&act=doStoreSubscription'
	},

	log: function (data) {
		console.log("GW_SW: " + data);
	}
}

require(['gwcms'], function () {
	$(document).ready(function () {

		GW_SW.init({
			auto_subscribe: true,
			btn_controls: true

		})
	});

});


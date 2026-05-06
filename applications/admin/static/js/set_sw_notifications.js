//todo unsubscribe

var GW_SW = {
	//will be owerwriten this is just example
	params: {
		registration_url: '',
		auto_subscribe: 1,
		btn_controls: 0,
		main_host: '',
		main_host_manage_url: ''

	},
	can_enable_push: false,
	is_enabled_push: false,
	subscription: false,
	is_busy: false,
	mainHostPushStatus: null,
	mainHostManageOnly: false,

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

	getMainHostPushStatusEndpoint: function ()
	{
		return GW.app_base + GW.ln + '/users/profile&act=doGetMainHostPushStatus';
	},

	getMainHost: function ()
	{
		return String(GW_SW.params.main_host || GW.push_main_host || '').toLowerCase();
	},

	getMainHostManageUrl: function ()
	{
		return String(GW_SW.params.main_host_manage_url || GW.push_main_host_manage_url || '');
	},

	getCurrentHost: function ()
	{
		return String(window.location.hostname || '').toLowerCase();
	},

	isCurrentHostMainHost: function ()
	{
		var mainHost = GW_SW.getMainHost();
		return !mainHost || GW_SW.getCurrentHost() === mainHost;
	},

	checkMainHostPushStatus: function ()
	{
		// Main-host push coordination is only relevant in MULTISITE setups.
		// Single-site projects should keep the original simple local subscription flow.
		if (!GW.multisite)
			return Promise.resolve(false);

		if (GW_SW.mainHostPushStatus)
			return Promise.resolve(GW_SW.mainHostPushStatus);

		if (GW_SW.isCurrentHostMainHost())
			return Promise.resolve(false);

		return $.ajax({
			type: 'GET',
			url: GW_SW.getMainHostPushStatusEndpoint(),
			dataType: 'json'
		}).then(function(resp){
			GW_SW.mainHostPushStatus = resp || false;
			return GW_SW.mainHostPushStatus;
		}).catch(function(){
			return false;
		});
	},

	shouldUseMainHostManageOnly: function (status, hasLocalSubscription)
	{
		if (!status || !parseInt(status.ok || 0, 10))
			return false;

		if (parseInt(status.is_main_host || 0, 10))
			return false;

		if (hasLocalSubscription)
			return false;

		return !!parseInt(status.has_main_host_subscription || 0, 10);
	},

	updateManageLinks: function (mainHostUrl)
	{
		$('.js-manage-push-subscriptions-link').each(function(){
			var $link = $(this);
			var defaultHref = String($link.attr('data-default-href') || '');
			var url = mainHostUrl || defaultHref || GW_SW.getMainHostManageUrl();

			if (url)
				$link.attr('href', url);
		});
	},

	applyMainHostManageMode: function (status)
	{
		var mainHost = String((status && status.main_host) || GW_SW.getMainHost() || '');
		var manageUrl = String((status && status.main_host_manage_url) || GW_SW.getMainHostManageUrl() || '');
		var hostLabel = mainHost || 'main host';

		GW_SW.mainHostManageOnly = true;
		GW_SW.updateManageLinks(manageUrl);

		$('#subscribe_btn')
			.attr('href', manageUrl || '#')
			.attr('target', manageUrl ? '_blank' : '')
			.attr('rel', manageUrl ? 'noopener' : '')
			.html('Push notifications are already managed on ' + hostLabel);

		$('#push-activation-banner').stop(true, true).show();
		$('#push_activate_btn').prop('disabled', false).text('Open settings');
	},

	clearMainHostManageMode: function ()
	{
		GW_SW.mainHostManageOnly = false;
		GW_SW.updateManageLinks(false);

		var $btn = $('#subscribe_btn');
		var defaultLabel = $btn.attr('data-default-label') || 'Subscribe push notifications here';
		$btn.attr('href', '#').removeAttr('target').removeAttr('rel').html(defaultLabel);
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
						GW_SW.clearLocalSubscriptionBinding();
						return GW_SW.checkMainHostPushStatus().then(function(status){
							if (GW_SW.shouldUseMainHostManageOnly(status, false)) {
								GW_SW.applyMainHostManageMode(status);
								return GW_SW.stateChange(false, false);
							}

							GW_SW.clearMainHostManageMode();
							return GW_SW.stateChange(false, true);
						});
					}

					GW_SW.clearMainHostManageMode();
					GW_SW.stateChange(true, true);

					GW_SW.subscription = subscription;
					return GW_SW.ensureCurrentUserOwnership(subscription).then(function(ownershipResult){
						if (ownershipResult === false && !GW_SW.subscription)
							return false;

						return GW_SW.maybeRestoreDetachedSubscriptionForCurrentUser(subscription).then(function(restoreResult){
							if (restoreResult === false)
								return false;

							return GW_SW.maybeSyncSubscriptionState(subscription).then(function(syncResult){
								if (syncResult === false && !GW_SW.subscription)
									return false;

								return true;
							});
						});
					});

				})
				.catch(function (err) {
					GW_SW.log('Error during getSubscription()', err);
				});
		});




	},

	getSyncEndpoint: function ()
	{
		return GW.app_base + GW.ln + '/users/profile&act=doCheckSubscriptionSync';
	},

	getReleaseOwnershipEndpoint: function ()
	{
		return GW.app_base + GW.ln + '/users/profile&act=doReleaseSubscriptionOwnership';
	},

	getOwnerUserIdStorageKey: function ()
	{
		return 'gw_push_owner_user_id';
	},

	getDetachedOwnerUserIdStorageKey: function ()
	{
		return 'gw_push_detached_owner_user_id';
	},

	getStoredOwnerUserId: function ()
	{
		return parseInt(localStorage.getItem(GW_SW.getOwnerUserIdStorageKey()) || '0', 10) || 0;
	},

	getCurrentUserId: function ()
	{
		return parseInt(GW.user_id || 0, 10) || 0;
	},

	getDetachedOwnerUserId: function ()
	{
		return parseInt(localStorage.getItem(GW_SW.getDetachedOwnerUserIdStorageKey()) || '0', 10) || 0;
	},

	hasStoredOwnerUserId: function ()
	{
		return !!GW_SW.getStoredOwnerUserId();
	},

	clearLocalSubscriptionBinding: function ()
	{
		localStorage.setItem('subscriptionid', false);
		localStorage.removeItem(GW_SW.getOwnerUserIdStorageKey());
	},

	clearDetachedOwnerUserId: function ()
	{
		localStorage.removeItem(GW_SW.getDetachedOwnerUserIdStorageKey());
	},

	buildSubscriptionPayload: function (subscription)
	{
		if (!subscription)
			return null;

		const key = subscription.getKey('p256dh');
		const token = subscription.getKey('auth');
		const contentEncoding = (PushManager.supportedContentEncodings || ['aesgcm'])[0];

		return {
			endpoint: subscription.endpoint,
			publicKey: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null,
			authToken: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null,
			contentEncoding,
		};
	},

	checkSubscriptionWithBackend: function (subscription)
	{
		var subscriptiondata = GW_SW.buildSubscriptionPayload(subscription);

		if (!subscriptiondata)
			return Promise.resolve({ ok: 0, has_backend_subscription: 0 });

		return $.ajax({
			type: 'POST',
			url: GW_SW.getSyncEndpoint(),
			dataType: 'json',
			data: { data: JSON.stringify(subscriptiondata) }
		});
	},

	releaseSubscriptionOwnership: function (subscription, ownerUserId)
	{
		var subscriptiondata = GW_SW.buildSubscriptionPayload(subscription);

		if (!subscriptiondata || !ownerUserId)
			return Promise.resolve({ ok: 0 });

		return $.ajax({
			type: 'POST',
			url: GW_SW.getReleaseOwnershipEndpoint(),
			dataType: 'json',
			data: {
				data: JSON.stringify(subscriptiondata),
				owner_user_id: ownerUserId
			}
		});
	},

	ensureCurrentUserOwnership: function (subscription)
	{
		var ownerUserId = GW_SW.getStoredOwnerUserId();
		var currentUserId = GW_SW.getCurrentUserId();

		if (!subscription || !ownerUserId || !currentUserId || ownerUserId === currentUserId)
			return Promise.resolve(true);

		GW_SW.log('Detected subscription owner mismatch: stored=' + ownerUserId + ' current=' + currentUserId);

		return GW_SW.releaseSubscriptionOwnership(subscription, ownerUserId).then(function(resp){
			GW_SW.log('Ownership release response: ' + JSON.stringify(resp));
			localStorage.setItem(GW_SW.getDetachedOwnerUserIdStorageKey(), ownerUserId);
			GW_SW.clearLocalSubscriptionBinding();
			GW_SW.subscription = false;
			GW_SW.stateChange(false, Notification.permission !== 'denied');
			GW_SW.notifyAdmin('info', 'This browser push subscription belonged to another user, so it was detached. Subscribe again if needed.');
			return false;
		}).catch(function(err){
			GW_SW.log('Ownership release failed');
			GW_SW.log(err);
			localStorage.setItem(GW_SW.getDetachedOwnerUserIdStorageKey(), ownerUserId);
			GW_SW.clearLocalSubscriptionBinding();
			GW_SW.subscription = false;
			GW_SW.stateChange(false, Notification.permission !== 'denied');
			return false;
		});
	},

	maybeRestoreDetachedSubscriptionForCurrentUser: function (subscription)
	{
		var detachedOwnerUserId = GW_SW.getDetachedOwnerUserId();
		var currentUserId = GW_SW.getCurrentUserId();

		if (!subscription || !detachedOwnerUserId || !currentUserId || detachedOwnerUserId !== currentUserId)
			return Promise.resolve(true);

		GW_SW.log('Restoring detached subscription for current user ' + currentUserId);

		return GW_SW.sendSubscriptionToServer(subscription, 'POST').then(function(){
			GW_SW.clearDetachedOwnerUserId();
			GW_SW.stateChange(true, true);
			GW_SW.notifyAdmin('success', 'Your browser push subscription was restored for the current user');
			return true;
		}).catch(function(err){
			GW_SW.log('Detached subscription restore failed');
			GW_SW.log(err);
			return false;
		});
	},

	needsForcedSyncCheck: function ()
	{
		return !GW_SW.hasStoredOwnerUserId();
	},

	maybeSyncSubscriptionState: function (subscription)
	{
		if (!subscription)
			return Promise.resolve(false);

		if (GW_SW.needsForcedSyncCheck()) {
			GW_SW.log('Running forced subscription sync check');
			return GW_SW.syncSubscriptionState(subscription);
		}

		if (Math.floor(Math.random() * 25) + 1 !== 1) {
			GW_SW.log('Skipping subscription sync check by lottery');
			return Promise.resolve(true);
		}

		GW_SW.log('Running subscription sync check');
		return GW_SW.syncSubscriptionState(subscription);
	},

	syncSubscriptionState: function (subscription)
	{
		GW_SW.log('Checking local subscription against backend');
			return GW_SW.checkSubscriptionWithBackend(subscription).then(function(resp){
				GW_SW.log('Sync response: ' + JSON.stringify(resp));

				if (!resp || !parseInt(resp.ok || 0, 10))
					return true;

				if (parseInt(resp.has_backend_subscription || 0, 10)) {
					localStorage.setItem(GW_SW.getOwnerUserIdStorageKey(), GW_SW.getCurrentUserId());
					GW_SW.clearDetachedOwnerUserId();
					return true;
				}

				GW_SW.clearLocalSubscriptionBinding();

				return subscription.unsubscribe().then(function(){
					GW_SW.subscription = false;
					GW_SW.stateChange(false, Notification.permission !== 'denied');
					GW_SW.log('Local push subscription removed because backend record was missing');
					GW_SW.notifyAdmin('info', 'Your browser push subscription was removed from backend, sync done, local subscription was cleared');
					return false;
				});
		}).catch(function(err){
			GW_SW.log('Subscription sync check failed');
			GW_SW.log(err);
			return false;
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

	getUserAgentMeta: function () {
		var ua = Sniffer(window.navigator.userAgent);
		var userAgentData = navigator.userAgentData || null;

		if (!userAgentData || typeof userAgentData.getHighEntropyValues !== 'function') {
			return Promise.resolve($.extend({}, ua, { raw_user_agent: window.navigator.userAgent || '' }));
		}

		return userAgentData.getHighEntropyValues(['platform', 'platformVersion', 'model', 'uaFullVersion'])
			.then(function(extra){
				return $.extend({}, ua, {
					raw_user_agent: window.navigator.userAgent || '',
					user_agent_data: extra || {}
				});
			})
			.catch(function(){
				return $.extend({}, ua, { raw_user_agent: window.navigator.userAgent || '' });
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
				GW_SW.subscription = subscription;

				return GW_SW.sendSubscriptionToServer(subscription, 'POST');
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
					$('#push-activation-banner').stop(true, true).show();

				} else {
					// A problem occurred with the subscription; common reasons
					// include network errors or the user skipped the permission
					GW_SW.log('Impossible to subscribe to push notifications', e);
					GW_SW.stateChange(false, false);
					$('#push-activation-banner').stop(true, true).show();
				}
			});


	},
	
	unsubscribe: function(){
		const canEnablePush = Notification.permission !== 'denied';
		GW_SW.stateChange(false, canEnablePush);

		// To unsubscribe from push messaging, you need to get the subscription object
		return navigator.serviceWorker.ready
		  .then(serviceWorkerRegistration => serviceWorkerRegistration.pushManager.getSubscription())
		  .then(subscription => {
		    // Check that we have a subscription to unsubscribe
		    if (!subscription) {
		      // No subscription object, so set the state
		      // to allow the user to subscribe to push
		      GW_SW.subscription = false;
		      GW_SW.stateChange(false, canEnablePush);
		      return;
		    }

		    // We have a subscription, unsubscribe
		    // Remove push subscription from server
			GW_SW.clearLocalSubscriptionBinding();

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
		  .then(subscription => subscription && subscription.unsubscribe())
		  .then(() => {
			GW_SW.subscription = false;
			GW_SW.stateChange(false, canEnablePush);
		  })
		  .catch(e => {
		    // We failed to unsubscribe, this can lead to
		    // an unusual state, so  it may be best to remove
		    // the users data from your data store and
		    // inform the user that you have done so
		    console.log('Error when unsubscribing the user');
		    console.log(e)
		    GW_SW.stateChange(false, canEnablePush);
		  });
  		
	},

	sendSubscriptionToServer: function (subscription, method) {
		var subscriptiondata = JSON.stringify(GW_SW.buildSubscriptionPayload(subscription));

		var subscriptionid = subscription.endpoint;


		if(localStorage.getItem("subscriptionid") == subscriptionid){
			
			GW_SW.log('Already set. no server inform');
			return Promise.resolve(subscription);
			
		}else{
			localStorage.setItem("subscriptionid", subscriptionid);

			return GW_SW.getUserAgentMeta().then(function(uaMeta){
				return $.ajax({
					type: "POST",
					url: GW_SW.getSubscriptionEndpoint(),
					data: {data: subscriptiondata, user_agent: JSON.stringify(uaMeta) },
					success: function (data) {
						GW_SW.log("Server response:" + data);
						gw_adm_sys.notify('success', data, { timer: 10000 });
					}
				}).then(function () {
					localStorage.setItem(GW_SW.getOwnerUserIdStorageKey(), GW_SW.getCurrentUserId());
					GW_SW.clearDetachedOwnerUserId();
					return subscription;
				});
			});			
			
		}

	


	},

	// Pririša UI mygtukus prie subscribe/unsubscribe veiksmų.
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


		$('#subscribe_btn').click(function (e) {
			if (e) {
				e.preventDefault();
				e.stopPropagation();
			}

			if (GW_SW.is_busy)
				return false;

			if (GW_SW.mainHostManageOnly) {
				var manageUrl = GW_SW.getMainHostManageUrl();
				if (manageUrl)
					window.open(manageUrl, '_blank');
				return false;
			}

			if (!GW_SW.is_enabled_push && !GW_SW.can_enable_push) {
				alert('Notifications cant be enabled');
			} else if( GW_SW.can_enable_push && GW_SW.is_enabled_push) {
				//alert('bell btn click');
				GW_SW.log('Start unsubscribe');
				GW_SW.setBusyState(true, 'Working...');
				Promise.resolve(GW_SW.unsubscribe()).then(function(){
					GW_SW.setBusyState(false);
				}).catch(function(){
					GW_SW.setBusyState(false);
				});
			} else if( GW_SW.can_enable_push && !GW_SW.is_enabled_push) {
				GW_SW.log('Start subscribe');
				GW_SW.setBusyState(true, 'Subscribing...');
				Promise.resolve(GW_SW.subscribe()).then(function(){
					GW_SW.setBusyState(false);
				}).catch(function(){
					GW_SW.setBusyState(false);
				});
				
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
		
		$('#push-deactivation').click(function (e) {
			
			GW_SW.log('Start unsubscribe');
			GW_SW.unsubscribe();
			
		});		

		$('#push_activate_btn').click(function () {
			if (GW_SW.mainHostManageOnly) {
				var manageUrl = GW_SW.getMainHostManageUrl();
				if (manageUrl)
					window.open(manageUrl, '_blank');
				return;
			}

			if (GW_SW.is_enabled_push)
				return;

			$('#push-activation-banner').stop(true, true).fadeOut();
			GW_SW.subscribe();
		});
		
		

		$('#unregister_sw').click(function () {
			reg.unregister().then(function (result) {
				GW_SW.log('Unregistered done', result);
			});
		});

		//GW_SW.stateChangeBtns();
	},

	// Atnaujina varpelio būseną ir aktyvavimo bannerį pagal push statusą.
	stateChange: function (is_enabled_push, can_enable_push)
	{
		GW_SW.is_enabled_push = is_enabled_push;
		GW_SW.can_enable_push = can_enable_push;

		//istestuot be https kai
		if (!is_enabled_push && !can_enable_push) {
			//GW_SW.log('Notifications cant run');
			//$('#subscribe_btn').css('color', 'red')
			//$('#subscribe_btn').html('<span class="material-symbols-outlined"  translate="no">notifications_off</span>')
			$('#push-activation-banner').fadeIn();
			//turetu raudonai nusidazyt kad nera galimybes push notificationams
		}else if(is_enabled_push && can_enable_push) {

			//$('#subscribe_btn').html('<span class="material-symbols-outlined"  translate="no">notifications</span>')

			//$('#subscribe_btn').css('color', 'inherit');
			GW_SW.log('Notifications ready');
			$('#push-deactivation').fadeIn();
		}else if(!is_enabled_push && can_enable_push) {

			//$('#subscribe_btn').html('<span class="material-symbols-outlined"  translate="no">notifications_off</span>')

			//$('#subscribe_btn').css('color', 'inherit');
			$('#push-deactivation').fadeIn();
		}

		GW_SW.updateActivationBanner();

	},

	setBusyState: function (isBusy, label)
	{
		var $btn = $('#subscribe_btn');
		var originalHtml = $btn.data('original-html');

		GW_SW.is_busy = !!isBusy;

		if (!$btn.length)
			return;

		if (!originalHtml) {
			originalHtml = $btn.html();
			$btn.data('original-html', originalHtml);
		}

		if (isBusy) {
			$btn.addClass('disabled');
			$btn.css('pointer-events', 'none');
			$btn.html('<i class="fa fa-spinner fa-spin"></i> ' + (label || 'Loading...'));
			return;
		}

		$btn.removeClass('disabled');
		$btn.css('pointer-events', '');
		$btn.html(originalHtml);
	},

	// Parodo arba paslepia didelį aktyvavimo bloką pagal dabartinę push būseną.
	updateActivationBanner: function ()
	{
		const $banner = $('#push-activation-banner');

		if (!$banner.length)
			return;

		if (GW_SW.is_enabled_push) {
			$banner.hide();
			return;
		}

		if (GW_SW.mainHostManageOnly) {
			$banner.show();
			$('#push_activate_btn').prop('disabled', false).text('Open settings');
			return;
		}

		if (Notification.permission === 'denied') {
			$banner.show();
			$('#push_activate_btn').prop('disabled', true).text('Blocked in browser');
			return;
		}

		$banner.show();
		$('#push_activate_btn').prop('disabled', false).text('Activate');
	},

	getSubscriptionEndpoint: function ()
	{
		return GW.app_base + GW.ln + '/users/profile&act=doStoreSubscription'
	},

	log: function (data) {
		console.log("GW_SW: " + data);
	},

	notifyAdmin: function (type, text) {
		if (window.gw_adm_sys && typeof gw_adm_sys.notify === 'function')
			gw_adm_sys.notify(type || 'info', text || '', { timer: 12000 });
	}
}

require(['gwcms'], function () {
	$(document).ready(function () {

		GW_SW.init({
			auto_subscribe: true,
			btn_controls: true,
			main_host: GW.push_main_host || '',
			main_host_manage_url: GW.push_main_host_manage_url || ''

		})
	});

});

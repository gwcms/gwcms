self.addEventListener('push', function (event) {
		console.log('Received a push message', event);

		event.waitUntil(
		fetch("/lt/users/messages/newjson", { credentials: 'include' }).then(function (res) {

				return res.json().then(function (data) {

						//event.waitUntil(  	
						self.registration.showNotification(data.title, {
								body: data.body,
								icon: data.icon,
								tag: data.tag,
								data: data
						})
				})
		})
		)



});


self.addEventListener('notificationclick', function (event) {
		console.log('On notification click: ', event);

		if (Notification.prototype.hasOwnProperty('data')) {
				console.log('Using Data');
				var url = event.notification.data.url;
				clients.openWindow(url);
		}else{
				clients.openWindow('/');
		}
		
		event.notification.close();
});

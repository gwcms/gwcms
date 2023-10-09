self.addEventListener('push', function (event) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        return;
    }

    const sendNotification = data => {
        // you could refresh a notification badge here with postMessage API
        
         

						//event.waitUntil(  	
						self.registration.showNotification(data.title, data)
			    
    };

    if (event.data) {
        const message = event.data.text();
        event.waitUntil(sendNotification(JSON.parse(message)));
    }
});


self.addEventListener('notificationclick', function(event) {
  event.notification.close();
  
  if (event.action) {
    // Archive action was clicked
    clients.openWindow('/action='+event.action);
  } else {
    // Main body of notification was clicked
      console.log(event.notification.data);
      
    clients.openWindow(event.notification.data.url);
  }
}, false);

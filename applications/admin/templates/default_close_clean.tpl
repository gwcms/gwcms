	{include "includes.tpl"}

	
    </body>
</html>


<script type="module">
  // Import the functions you need from the SDKs you need
  import { initializeApp } from "https://www.gstatic.com/firebasejs/9.15.0/firebase-app.js";
  import { getAnalytics } from "https://www.gstatic.com/firebasejs/9.15.0/firebase-analytics.js";
  import { getMessaging, getToken } from "https://www.gstatic.com/firebasejs/9.15.0/firebase-messaging.js";

  // TODO: Add SDKs for Firebase products that you want to use
  // https://firebase.google.com/docs/web/setup#available-libraries

  // Your web app's Firebase configuration
  // For Firebase JS SDK v7.20.0 and later, measurementId is optional
  const firebaseConfig = {
    apiKey: "AIzaSyA6Zi24Pu940O9C7pfkizliFMy14YWALvo",
    authDomain: "gw-lt-8bada.firebaseapp.com",
    projectId: "gw-lt-8bada",
    storageBucket: "gw-lt-8bada.appspot.com",
    messagingSenderId: "177891399074",
    appId: "1:177891399074:web:dc80588c0548bac7de0efb",
    measurementId: "G-11QHKVTKMB"
  };

  // Initialize Firebase
  const app = initializeApp(firebaseConfig);
  const analytics = getAnalytics(app);
	const messaging = getMessaging(app);  
	
console.log(messaging);

const token='BF0PbodFQuUEv6olrShTXJ-N9x5aqKeWnsHMnrqbTi9Hy8Bq8yy2b6M_5QJO0it8S2P56K8Ac7TuKTYUJVsnJRg';


getToken(messaging, { vapidKey: token }).then((currentToken) => {
  if (currentToken) {
    // Send the token to your server and update the UI if necessary
    // ...
    console.log('send to server '+ currentToken);
  } else {
    // Show permission request UI
    console.log('No registration token available. Request permission to generate one.');
    // ...
  }
}).catch((err) => {
  console.log('An error occurred while retrieving token. ', err);
  // ...
});
</script>


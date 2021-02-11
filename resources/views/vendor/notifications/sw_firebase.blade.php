importScripts('https://www.gstatic.com/firebasejs/8.2.5/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.2.5/firebase-messaging.js');

@include('vendor.notifications.init_firebase')

const messaging = firebase.messaging();
const broadcastChannel = new BroadcastChannel('message-channel');

self.addEventListener('install', event => event.waitUntil(self.skipWaiting()));

self.addEventListener('activate', event => event.waitUntil(self.clients.claim()));


messaging.onBackgroundMessage((payload) => {

    console.log('[firebase-messaging-sw.js] Received background message ', payload);

    // Customize notification here
    const notificationTitle = payload.data.title;

    const notificationOptions = {
      body: payload.data.body,
      icon: payload.data.icon,
    };

    broadcastChannel.postMessage({});

});


// {{env('APP_NAME')}}

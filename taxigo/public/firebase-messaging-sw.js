// public/firebase-messaging-sw.js

importScripts('https://www.gstatic.com/firebasejs/11.9.1/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/11.9.1/firebase-messaging-compat.js');

firebase.initializeApp({
  apiKey: "AIzaSyC4KSOC2F2v_Dxo1jCoUbR3lCfTKjSjVRo",
  authDomain: "goataxi-5a99b.firebaseapp.com",
  projectId: "goataxi-5a99b",
  messagingSenderId: "226628351677",
  appId: "1:226628351677:web:a85d340622b829600869d4"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {
  console.log('[firebase-messaging-sw.js] Received background message ', payload);
  const notificationTitle = payload.notification.title;
  const notificationOptions = {
    body: payload.notification.body,
    icon: '/firebase-logo.png' // or your own icon
  };

  self.registration.showNotification(notificationTitle, notificationOptions);
});

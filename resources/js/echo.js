// import Echo from 'laravel-echo';
//
// import Pusher from 'pusher-js';
// window.Pusher = Pusher;
//
// window.Echo = new Echo({
//     broadcaster: 'reverb',
//     key: import.meta.env.VITE_REVERB_APP_KEY,
//     wsHost: import.meta.env.VITE_REVERB_HOST,
//     wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
//     wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
//     encrypted: true,
//     disableStats: true,
// });
//


import Echo from '@ably/laravel-echo';
import * as Ably from 'ably';
import {onMounted, onUnmounted} from "vue";

window.Ably = Ably; // make globally accessible to Echo
window.Echo = new Echo({
    broadcaster: 'ably',
});

window.Echo.connector.ably.connection.on(stateChange => {
    console.log(`${stateChange.previous} ==> ${stateChange.current} as ${stateChange.reason ?? ''}`)
    if (stateChange.current === 'connected') {
        console.log('connected to ably server');
    }
});

//
// const channels = []
//
// onMounted(() => {
//     const channel = window.Echo.private('HoC.Stock');
//
//     channels.push(channel)
//     console.log(channel);
//     console.log(channel.name);
//     console.log(new Date)
//     console.log(new Date)
//     console.log(new Date)
//
//     channel.listen('.PoApproved', (s) => {
//         console.log('----------')
//         console.log(s)
//     })
//
//
//     channel.listen('PoApproved', (s) => {
//         console.log('----------')
//         console.log(s)
//     })
// })
//
// onUnmounted(() => {
//     channels.forEach((e) => {
//         console.log("leaveChannel=>" + e.name)
//         window.Echo.leaveChannel(e.name)
//     })
// })


// .listen('.po.approved', (e) => {
//     console.log('--------');
//     console.log(e);
//     console.log(e.order);
// });

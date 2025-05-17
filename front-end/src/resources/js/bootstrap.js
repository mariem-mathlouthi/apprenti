import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// window.Pusher = require('pusher-js');
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: "edc2943b2a2068f8b38c",
    cluster: "eu",
    forceTLS: true,
    encrypted: true
});
import Pusher from 'pusher-js'

export default {
  install(app, options) {
    // Only initialize Pusher for tuteur accounts
    if (true) { // Replace with your actual check
      const pusher = new Pusher(options.key, {
        cluster: options.cluster,
        encrypted: true,
      })
      
      // Make pusher available globally
      app.config.globalProperties.$pusher = pusher
      
      // Store the channel subscriptions
      const channels = {}
      
      // Method to subscribe to channels
      app.config.globalProperties.$subscribeToChannel = (channelName, events) => {
        if (!channels[channelName]) {
          channels[channelName] = pusher.subscribe(channelName)
        }
        
        Object.entries(events).forEach(([eventName, callback]) => {
          channels[channelName].bind(eventName, callback)
        })
      }
      
      // Method to unsubscribe
      app.config.globalProperties.$unsubscribeFromChannel = (channelName) => {
        if (channels[channelName]) {
          pusher.unsubscribe(channelName)
          delete channels[channelName]
        }
      }
      
      // Cleanup when app unmounts
      app.provide('pusher', pusher)
    }
  }
}

// function userIsStudent() {
//   // Implement your logic to check if user is tuteur
//   return JSON.parse(localStorage.getItem('StudentAccountInfo')).role === 'student'
// }
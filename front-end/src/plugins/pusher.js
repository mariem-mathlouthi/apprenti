import Pusher from 'pusher-js'

export default {
  install(app, options) {
    // Initialiser Pusher pour tous les utilisateurs connectés
    const userInfo = JSON.parse(localStorage.getItem('userInfo')) || {}
    if (userInfo.id) {
      const pusher = new Pusher(options.key, {
        cluster: options.cluster,
        encrypted: true,
        authEndpoint: 'http://localhost:8000/api/pusher/auth',
        auth: {
          headers: {
            'X-User-ID': userInfo.id,
            'X-User-Role': userInfo.role || 'guest'
          }
        }
      })
      
      // Rendre Pusher disponible globalement
      app.config.globalProperties.$pusher = pusher
      
      // Stocker les abonnements aux canaux
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
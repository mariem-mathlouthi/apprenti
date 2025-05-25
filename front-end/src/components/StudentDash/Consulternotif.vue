<template>
  <div>
    <Navbar />
    <div class="grid grid-cols-12 gap-4">
      <div class="col-span-3">
        <Sidebar />
      </div>
      <div class="col-span-9 mt-24 mr-24">
        <!-- Notification Popup
        <transition name="notification-popup">
          <div v-if="showPopup" class="fixed top-5 right-5 max-w-md w-full bg-white rounded-lg shadow-lg overflow-hidden z-50">
            <div :class="getNotificationTypeClass(currentNotification.type)" class="h-2"></div>
            <div class="p-4">
              <div class="flex items-start">
                <div class="flex-shrink-0 mr-3">
                  <div :class="getNotificationTypeClass(currentNotification.type)" class="w-10 h-10 rounded-full flex items-center justify-center">
                    <i :class="getNotificationIcon(currentNotification.type)" class="text-white"></i>
                  </div>
                </div>
                <div class="flex-1">
                  <h3 class="text-lg font-semibold text-gray-800">{{ currentNotification.title }}</h3>
                  <p class="text-gray-600 mt-1">{{ currentNotification.message }}</p>
                  <div class="mt-2 text-sm text-gray-500">{{ formatDate(currentNotification.date) }}</div>
                </div>
                <button @click="closePopup" class="text-gray-400 hover:text-gray-600">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
              <div class="mt-3 flex justify-end space-x-2">
                <router-link v-if="currentNotification.type === 'offre'" to="/OffersListStd" class="inline-flex items-center px-3 py-1 rounded-md bg-blue-100 text-blue-700 hover:bg-blue-200 transition-colors duration-200">
                  <span>Consulter</span>
                  <i class="fas fa-chevron-right ml-2"></i>
                </router-link>
                <button v-if="currentNotification.type === 'attestation'" @click="voirAttestation(currentNotification.attestation)" class="inline-flex items-center px-3 py-1 rounded-md bg-orange-100 text-orange-700 hover:bg-orange-200 transition-colors duration-200">
                  <i class="fas fa-file-pdf mr-2"></i>
                  <span>Voir l'attestation</span>
                </button>
              </div>
            </div>
          </div>
        </transition> -->
        <header class="text-center mb-8">
          <h1 class="text-3xl font-bold text-purple-800">Notifications</h1>
        </header>

        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center py-8">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-700"></div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
          <strong class="font-bold">Erreur!</strong>
          <span class="block sm:inline">{{ error }}</span>
        </div>

        <!-- Content -->
        <div v-else class="bg-purple-50 rounded-lg shadow-md p-6">
          <div v-if="notifications.length === 0" 
               class="text-center text-gray-500 py-8">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <p>Aucune notification pour le moment.</p>
          </div>

          <div v-else class="space-y-4">
            <div v-for="(notification, index) in sortedNotifications" 
                 :key="index"
                 class="bg-white p-4 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
              <div class="flex items-start">
                <!-- Icon based on notification type -->
                <div class="flex-shrink-0 mr-4">
                  <div :class="getNotificationTypeClass(notification.type)" 
                       class="w-10 h-10 rounded-full flex items-center justify-center">
                    <i :class="getNotificationIcon(notification.type)" 
                       class="text-white"></i>
                  </div>
                </div>

                <div class="flex-grow">
                  <h2 class="text-lg font-semibold text-gray-800">
                    {{ notification.title }}
                  </h2>
                  <p class="text-gray-600 mt-1">{{ notification.message }}</p>
                  <div class="mt-2 flex items-center justify-between">
                    <span class="text-sm text-gray-500">
                      {{ formatDate(notification.date) }}
                    </span>
                    <div class="space-x-2">
                      <router-link v-if="notification.type === 'offre'"
                                 to="/OffersListStd"
                                 class="inline-flex items-center px-3 py-1 rounded-md bg-blue-100 text-blue-700 hover:bg-blue-200 transition-colors duration-200">
                        <span>Consulter</span>
                        <i class="fas fa-chevron-right ml-2"></i>
                      </router-link>
                      <button v-if="notification.type === 'attestation'"
                              @click="voirAttestation(notification.attestation)"
                              class="inline-flex items-center px-3 py-1 rounded-md bg-orange-100 text-orange-700 hover:bg-orange-200 transition-colors duration-200">
                        <i class="fas fa-file-pdf mr-2"></i>
                        <span>Voir l'attestation</span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Navbar from './NavBarStd.vue'
import Sidebar from './Sidebar.vue'

import axios from "axios"

export default {
  name: 'ConsulterNotifications',
  
  components: {
    Navbar,
    Sidebar
  },

  data() {
    return {
      notifications: [],
      loading: true,
      error: null,
      idEtudiant: "",
      fullname: "",
      email: "",
      showPopup: false,
      currentNotification: {},
      popupTimer: null
    }
  },

  computed: {
    sortedNotifications() {
      return [...this.notifications].sort((a, b) => 
        new Date(b.date) - new Date(a.date)
      )
    }
  },

  created() {
    this.initializePusher()
    this.requestNotificationPermission()
  },

  methods: {

    initializePusher() {
      Pusher.logToConsole = true;

      this.pusher = new Pusher("edc2943b2a2068f8b38c", {
        cluster: "eu",
      });
      this.idEtudiant = JSON.parse(localStorage.getItem("StudentAccountInfo")).id;

      this.channel = this.pusher.subscribe(`notifications.${this.idEtudiant}`);
      this.channel.bind("notification-event", (data) => {
        if (data) {
          this.$nextTick(() => {
            this.processNotifications([data]);
            // Show popup notification
            this.showPopupNotification(data);
          });
        }
        // Show browser notification
        this.showNotification("Nouvelle notification", {
          body: data.message,
          icon: "https://cdn0.iconfinder.com/data/icons/customicondesignoffice5/256/examples.png",
          sound: "../assets/sounds/mixkit-software-interface-start-2574.mp3"
        });
      });
    },

    showNotification(title, options) {
      if (Notification.permission === "granted") {
        new Notification(title, options);
      } else if (Notification.permission !== "denied") {
        requestNotificationPermission();
      }
    },

    
    requestNotificationPermission() {
      if ("Notification" in window) {
        Notification.requestPermission().then((permission) => {
          if (permission === "granted") {
            console.log("Notification permission granted");
          }
        });
      }
    },

    getAccountData() {
      try {
        const storedData = localStorage.getItem("StudentAccountInfo")
        if (!storedData) {
          throw new Error("Aucune donnée d'utilisateur trouvée")
        }
        const parsedData = JSON.parse(storedData)
        this.idEtudiant = parsedData.id
        this.fullname = parsedData.fullname
        this.email = parsedData.email
      } catch (error) {
        console.error("Erreur lors de la récupération des données:", error)
        console.error("Erreur d'authentification")
      }
    },

    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    },

    getNotificationTypeClass(type) {
      const classes = {
        offre: 'bg-blue-500',
        demande: 'bg-purple-500',
        attestation: 'bg-orange-500',
        cours: 'bg-green-500'
      }
      return classes[type] || 'bg-gray-500'
    },

    getNotificationIcon(type) {
      const icons = {
        offre: 'fas fa-briefcase',
        demande: 'fas fa-file-alt',
        attestation: 'fas fa-certificate',
        cours: 'fas fa-book'
      }
      return icons[type] || 'fas fa-bell'
    },

    async getNotifications() {
      try {
        this.loading = true
        this.error = null
        
        // Get all notifications
        const [notifResponse, attestResponse] = await Promise.all([
          axios.get("http://localhost:8000/api/getAllNotifications",
            {
              headers: {
                Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`
              }
            }
          ),
          axios.get(`http://localhost:8000/api/getAttestation/${this.idEtudiant}`,
            {
              headers: {
                Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`
              }
            }
          )
        ])

        // Process notifications
        if (notifResponse.data.check) {
          this.processNotifications(notifResponse.data.notifications)
        }

        // Process attestations
        if (attestResponse.data.check) {
          this.processAttestations(attestResponse.data.attestation)
        }

        // Store in localStorage
        localStorage.setItem("notifications", JSON.stringify({
          notifications: this.notifications
        }))

      } catch (error) {
        console.error("Erreur:", error)
        this.error = "Impossible de charger les notifications"
        console.error("Erreur de chargement des notifications")
      } finally {
        this.loading = false
      }
    },

    processNotifications(notifications) {
      notifications.forEach(notif => {
        if (notif.idEtudiant === this.idEtudiant && notif.type === "demande") {
          this.notifications.push({
            title: "Notification de votre demande stage",
            message: notif.message,
            date: notif.date,
            type: "demande"
          })
        } else if (notif.type === "offre") {
          this.notifications.push({
            title: "Nouvelle Offre de stage",
            message: notif.message,
            date: notif.date,
            type: "offre"
          })
        } else if (notif.type === "cours") {
          this.notifications.push({
            title: "Nouveau cours",
            message: notif.message,
            date: notif.date,
            type: "cours"
          })
        }
      })
    },
    
    showPopupNotification(notification) {
      // Create notification object based on type
      let notifObj = {};
      
      if (notification.idEtudiant === this.idEtudiant && notification.type === "demande") {
        notifObj = {
          title: "Notification de votre demande stage",
          message: notification.message,
          date: notification.date,
          type: "demande"
        };
      } else if (notification.type === "offre") {
        notifObj = {
          title: "Nouvelle Offre de stage",
          message: notification.message,
          date: notification.date,
          type: "offre"
        };
      } else if (notification.type === "attestation") {
        notifObj = {
          title: "Notification d'attestation de stage",
          message: notification.message,
          date: notification.date,
          attestation: notification.attestation,
          type: "attestation"
        };
      }
      
      // Set current notification and show popup
      this.currentNotification = notifObj;
      this.showPopup = true;
      
      // Auto-close popup after 5 seconds
      if (this.popupTimer) {
        clearTimeout(this.popupTimer);
      }
      
      this.popupTimer = setTimeout(() => {
        this.closePopup();
      }, 5000);
    },
    
    closePopup() {
      this.showPopup = false;
    },

    processAttestations(attestations) {
      attestations.forEach(attestation => {
        this.notifications.push({
          title: "Notification d'attestation de stage",
          message: attestation.message,
          date: attestation.date,
          attestation: attestation.attestation,
          type: "attestation"
        })
      })
    },
    
    // Method to manually test notification popup
    testNotificationPopup(type = 'offre') {
      // Create a test notification based on type
      const testNotification = {
        title: type === 'offre' ? 'Nouvelle Offre de stage' : 
               type === 'demande' ? 'Notification de votre demande stage' : 
               'Notification d\'attestation de stage',
        message: 'Ceci est un message de test pour le popup de notification.',
        date: new Date().toISOString(),
        type: type
      };
      
      // If attestation type, add attestation property
      if (type === 'attestation') {
        testNotification.attestation = '/storage/attestations/sample.pdf';
      }
      
      // Show the popup
      this.currentNotification = testNotification;
      this.showPopup = true;
      
      // Auto-close popup after 5 seconds
      if (this.popupTimer) {
        clearTimeout(this.popupTimer);
      }
      
      this.popupTimer = setTimeout(() => {
        this.closePopup();
      }, 5000);
    },

    voirAttestation(filename) {
      if (!filename) {
        console.error("Fichier d'attestation non trouvé")
        return
      }
      const fileURL = `http://localhost:8000${filename}`
      window.open(fileURL, '_blank')
    }
  },

  async mounted() {
    // this.idEtudiant = JSON.parse(localStorage.getItem("StudentAccountInfo")).id;

    // if (this.$pusher) {
    //   this.$subscribeToChannel(`notifications.${this.idEtudiant}`, {
    //     'notification-event': (data) => {
    //       // Handle notification
    //       this.processNotifications([data]);
    //         // Show popup notification
    //       this.showPopupNotification(data);
    //       this.showNotification("New", {
    //         body: data.message,
    //         icon: "https://cdn0.iconfinder.com/data/icons/customicondesignoffice5/256/examples.png",
    //         sound: "../assets/sounds/mixkit-software-interface-start-2574.mp3"
    //       });
    //     }
    //   })
    // }
    this.getAccountData()
    await this.getNotifications()
    
    // Show popup for the most recent notification if available
    if (this.notifications.length > 0) {
      // Get the most recent notification
      const latestNotification = this.sortedNotifications[0];
      // Show popup with a slight delay to ensure component is fully mounted
      setTimeout(() => {
        this.currentNotification = latestNotification;
        this.showPopup = true;
        
        // Auto-close popup after 5 seconds
        this.popupTimer = setTimeout(() => {
          this.closePopup();
        }, 5000);
      }, 1000);
    }
  }
}
</script>

<style scoped>
.notification-enter-active,
.notification-leave-active {
  transition: all 0.5s ease;
}

.notification-enter-from,
.notification-leave-to {
  opacity: 0;
  transform: translateY(-30px);
}

/* Popup animation styles */
.notification-popup-enter-active,
.notification-popup-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.notification-popup-enter-from {
  opacity: 0;
  transform: translateY(-20px) scale(0.95);
}

.notification-popup-leave-to {
  opacity: 0;
  transform: translateX(20px) scale(0.95);
}

.notification-popup-enter-to,
.notification-popup-leave-from {
  opacity: 1;
  transform: translateY(0) scale(1);
}
</style>
<template>
  <div>
    <!-- Material Icons import for notification icons -->
    <link
      href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet"
    />
    <Navbar />
    <div class="grid grid-cols-12 gap-4">
      <div class="col-span-3">
        <Sidebar />
      </div>
      <div class="col-span-9 mt-24 mr-24">
        <header class="text-center mb-8">
          <h1 class="text-3xl font-bold text-gray-800" style="color: purple">
            Notifications
          </h1>
        </header>
        <div class="bg-purple-100 rounded-lg shadow-md p-6">
          <h2 class="text-xl font-semibold text-purple-800 mb-4">
            Notifications
          </h2>

          <!-- Real notifications from API -->
          <div v-if="notifications.length > 0" class="space-y-4">
            <div
              v-for="(notif, index) in notifications"
              :key="index"
              class="notification-card bg-white rounded-lg p-4 border-l-4 shadow-sm flex items-start gap-3"
              :class="{
                'border-purple-500': notif.type === 'demande',
                'border-green-500': notif.type === 'offre',
                'border-blue-500': notif.type === 'cours',
                'border-yellow-500': notif.type === 'attestation',
              }"
            >
              <!-- Icon based on notification type -->
              <div class="notification-icon flex-shrink-0">
                <div
                  class="w-10 h-10 rounded-full flex items-center justify-center"
                  :class="{
                    'bg-purple-100 text-purple-600': notif.type === 'demande',
                    'bg-green-100 text-green-600': notif.type === 'offre',
                    'bg-blue-100 text-blue-600': notif.type === 'cours',
                    'bg-yellow-100 text-yellow-600':
                      notif.type === 'attestation',
                  }"
                >
                  <span v-if="notif.type === 'demande'" class="material-icons"
                    >assignment</span
                  >
                  <span
                    v-else-if="notif.type === 'offre'"
                    class="material-icons"
                    >work</span
                  >
                  <span
                    v-else-if="notif.type === 'cours'"
                    class="material-icons"
                    >school</span
                  >
                  <span
                    v-else-if="notif.type === 'attestation'"
                    class="material-icons"
                    >description</span
                  >
                </div>
              </div>

              <!-- Notification content -->
              <div class="flex-grow">
                <h3 class="font-medium text-gray-800">{{ notif.title }}</h3>
                <p class="text-gray-600 text-sm mt-1">{{ notif.message }}</p>
                <div class="flex justify-between items-center mt-2">
                  <span class="text-xs text-gray-500">{{
                    formatDate(notif.date)
                  }}</span>

                  <!-- Action button for attestations -->
                  <button
                    v-if="notif.type === 'attestation' && notif.attestation"
                    @click="voirAttestation(notif.attestation)"
                    class="text-xs px-3 py-1 bg-purple-100 hover:bg-purple-200 text-purple-700 rounded-full transition-colors"
                  >
                    Voir l'attestation
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty state -->
          <div v-else class="text-center py-8">
            <div class="text-purple-300 flex justify-center">
              <span class="material-icons text-5xl">notifications_off</span>
            </div>
            <p class="mt-2 text-gray-500">Aucune notification pour le moment</p>
          </div>

          <!-- Pusher real-time messages -->
          <div
            v-if="messages.length > 0"
            class="mt-6 pt-6 border-t border-purple-200"
          >
            <h3 class="text-lg font-medium text-purple-800 mb-3">
              Messages en temps réel
            </h3>
            <div
              v-for="(message, index) in messages"
              :key="index"
              class="bg-purple-50 p-3 rounded-md mb-2 border-l-4 border-purple-300 animate-pulse"
            >
              {{ message }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Navbar from "./NavBarStd.vue";
import Sidebar from "./Sidebar.vue";
import axios from "axios";
import Pusher from "pusher-js";

export default {
  data() {
    return {
      notifications: [],
      idEtudiant: null,
      userId: null,
      messages: [],
      pusher: null,
      channel: null,
    };
  },
  components: {
    Sidebar,
    Navbar,
  },

  watch: {
    notifications: {
      handler(newNotifications, oldNotifications) {
        if (
          oldNotifications &&
          newNotifications.length > oldNotifications.length
        ) {
          // Get the new notifications (ones that weren't in the old array)
          const newItems = newNotifications.filter(
            (notification) =>
              !oldNotifications.some(
                (old) =>
                  old.message === notification.message &&
                  old.date === notification.date
              )
          );

          // Show notification for each new item
          newItems.forEach((notification) => {
            this.playNotificationSound(); // Use the new method
            this.showNotification(notification.title, {
              body: notification.message,
              icon:
                notification.type === "attestation"
                  ? "https://cdn-icons-png.flaticon.com/512/2991/2991106.png"
                  : notification.type === "cours"
                  ? "https://cdn-icons-png.flaticon.com/512/2991/2991114.png"
                  : notification.type === "offre"
                  ? "https://cdn-icons-png.flaticon.com/512/2991/2991148.png"
                  : "https://cdn0.iconfinder.com/data/icons/customicondesignoffice5/256/examples.png",
            });
          });
        }
      },
      deep: true,
    },

    messages: {
      handler(newMessages, oldMessages) {
        if (oldMessages && newMessages.length > oldMessages.length) {
          // Get the most recent message
          const latestMessage = newMessages[newMessages.length - 1];

          this.showNotification("Nouveau message en temps réel", {
            body: latestMessage,
            icon: "https://cdn-icons-png.flaticon.com/512/2991/2991135.png",
            sound: "../assets/sounds/mixkit-software-interface-start-2574.mp3",
          });
        }
      },
    },
  },

  created() {
    this.initializePusher();
    this.requestNotificationPermission();
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
        this.messages.push(data.message);
        // Show notification when new message arrives
        this.showNotification("Nouvelle notification", {
          body: data.message,
          icon: "https://cdn0.iconfinder.com/data/icons/customicondesignoffice5/256/examples.png",
          sound: "../assets/sounds/mixkit-software-interface-start-2574.mp3",
        });
      });
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
    beforeDestroy() {
      // Clean up Pusher connection when component is destroyed
      if (this.channel) {
        this.channel.unbind_all();
        this.channel.unsubscribe();
      }
      if (this.pusher) {
        this.pusher.disconnect();
      }
    },

    showNotification(title, options) {
      if (Notification.permission === "granted") {
        new Notification(title, options);
      } else if (Notification.permission !== "denied") {
        requestNotificationPermission();
      }
    },
    getAccountData() {
      const storedData = localStorage.getItem("StudentAccountInfo");
      if (storedData) {
        const parsedData = JSON.parse(storedData);
        this.idEtudiant = parsedData.id;
      }
    },

    async getNotifications() {
      try {
        const [notifResponse, attestResponse] = await Promise.all([
          axios.get("http://localhost:8000/api/getAllNotifications"),
          axios.get(
            `http://localhost:8000/api/getAttestation/${this.idEtudiant}`
          ),
        ]);

        this.notifications = [];

        // Traitement des notifications principales
        notifResponse.data.notifications.forEach((notif) => {
          // Demandes spécifiques
          if (notif.type === "demande" && notif.idEtudiant == this.idEtudiant) {
            this.notifications.push({
              title: "Mise à jour de demande",
              message: notif.message,
              date: notif.date,
              type: "demande",
            });
          }

          // Offres globales ou spécifiques
          if (
            notif.type === "offre" &&
            (notif.idEtudiant == 0 || notif.idEtudiant == this.idEtudiant)
          ) {
            this.notifications.push({
              title: "Nouvelle offre disponible",
              message: notif.message,
              date: notif.date,
              type: "offre",
            });
          }

          // Cours globaux
          if (notif.type === "cours" && notif.idEtudiant == 0) {
            this.notifications.push({
              title: "Nouveau cours publié",
              message: notif.message,
              date: notif.date,
              type: "cours",
            });
          }
        });

        // Ajout des attestations
        if (attestResponse.data?.check) {
          attestResponse.data.attestation.forEach((att) => {
            this.notifications.push({
              title: "Attestation de stage",
              message: att.message,
              date: att.date,
              type: "attestation",
              attestation: att.attestation,
            });
          });
        } // Tri par date décroissante
        this.notifications.sort((a, b) => new Date(b.date) - new Date(a.date));

        // Show notification for new items
        if (this.notifications.length > 0) {
          const latestNotif = this.notifications[0];
          this.showNotification("Nouvelle notification", {
            body: latestNotif.message,
            icon: "https://cdn0.iconfinder.com/data/icons/customicondesignoffice5/256/examples.png",
            sound: "../assets/sounds/mixkit-software-interface-start-2574.mp3",
          });
        }
      } catch (error) {
        console.error("Erreur:", error);
      }
    },

    formatDate(dateString) {
      const options = { year: "numeric", month: "long", day: "numeric" };
      return new Date(dateString).toLocaleDateString("fr-FR", options);
    },

    voirAttestation(filename) {
      const fileURL = `http://localhost:8000${filename}`;
      window.open(fileURL, "_blank");
    },

    playNotificationSound() {
      try {
        const audio = new Audio(
          "/sounds/mixkit-software-interface-start-2574.mp3"
        );
        const playPromise = audio.play();

        if (playPromise !== undefined) {
          playPromise.catch((error) => {
            console.log("Audio playback failed:", error);
          });
        }
      } catch (error) {
        console.error("Error playing sound:", error);
      }
    },
  },
  mounted() {
    this.getAccountData();
    this.getNotifications();
    setTimeout(() => {
      this.playNotificationSound(); // Use the new method
      this.showNotification("Bienvenue sur votre tableau de bord !", {
        body: "Vous avez de nouvelles notifications.",
        icon: "https://cdn0.iconfinder.com/data/icons/customicondesignoffice5/256/examples.png",
      });
    }, 1000);
  },
};
</script>

<style scoped>
.notification-card {
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.notification-card:hover {
  transform: translateX(5px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.notification-card::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 4px;
  height: 100%;
  opacity: 0.7;
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.8;
  }
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .notification-card {
    flex-direction: column;
  }

  .notification-icon {
    margin-bottom: 10px;
  }
}
</style>

<template>
  <div>
    <Navbar/>
    <div class="grid grid-cols-12 gap-4">
      <div class="col-span-3">
        <Sidebar />
      </div>
      <div class="col-span-9 mt-24 mr-24">
        <header class="text-center mb-8">
          <h1 class="text-3xl font-bold text-gray-800" style="color:purple">Notifications</h1>
        </header>
        <div class="bg-purple-100 rounded-lg shadow-md p-6">
            <h2>Notifications</h2>
            <ul>
              <li v-for="(message, index) in messages" :key="index">{{ message }}</li>
            </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Navbar from './NavBarStd.vue'
import Sidebar from './Sidebar.vue'
import axios from "axios";
import Pusher from 'pusher-js';


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
    Navbar
  },
  methods: {
    created() {
      Pusher.logToConsole = true;

      this.pusher = new Pusher("edc2943b2a2068f8b38c", {
        cluster: "eu",
      });

      this.channel = this.pusher.subscribe("notifications");
      this.channel.bind("my-event", (data) => {
        this.messages.push(data.message);
      });

      console.log(data)
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
      if (Notification.permission === 'granted') {
        new Notification(title, options);
      } else if (Notification.permission !== 'denied') {
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
          axios.get(`http://localhost:8000/api/getAttestation/${this.idEtudiant}`)
        ]);

        this.notifications = [];

        // Traitement des notifications principales
        notifResponse.data.notifications.forEach(notif => {
          // Demandes spécifiques
          if (notif.type === 'demande' && notif.idEtudiant == this.idEtudiant) {
            this.notifications.push({
              title: "Mise à jour de demande",
              message: notif.message,
              date: notif.date,
              type: "demande"
            });
          }

          // Offres globales ou spécifiques
          if (notif.type === 'offre' && (notif.idEtudiant == 0 || notif.idEtudiant == this.idEtudiant)) {
            this.notifications.push({
              title: "Nouvelle offre disponible",
              message: notif.message,
              date: notif.date,
              type: "offre"
            });
          }

          // Cours globaux
          if (notif.type === 'cours' && notif.idEtudiant == 0) {
            this.notifications.push({
              title: "Nouveau cours publié",
              message: notif.message,
              date: notif.date,
              type: "cours"
            });
          }
        });

        // Ajout des attestations
        if (attestResponse.data?.check) {
          attestResponse.data.attestation.forEach(att => {
            this.notifications.push({
              title: "Attestation de stage",
              message: att.message,
              date: att.date,
              type: "attestation",
              attestation: att.attestation
            });
          });
        }

        // Tri par date décroissante
        this.notifications.sort((a, b) => new Date(b.date) - new Date(a.date));

      } catch (error) {
        console.error("Erreur:", error);
      }
    },

    formatDate(dateString) {
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      return new Date(dateString).toLocaleDateString('fr-FR', options);
    },

    voirAttestation(filename) {
      const fileURL = `http://localhost:8000${filename}`;
      window.open(fileURL, '_blank');
    }
  },
  mounted() {
    this.created();
    this.beforeDestroy();
    this.getAccountData();
    this.getNotifications();
    this.showNotification("Bienvenue sur votre tableau de bord !", {
      body: "Vous avez de nouvelles notifications.",
      icon: "https://cdn0.iconfinder.com/data/icons/customicondesignoffice5/256/examples.png",
      sound: "../assets/sounds/mixkit-software-interface-start-2574.mp3"
    });
  }
}
</script>

<style scoped>
.notification-card {
  transition: transform 0.2s ease;
}

.notification-card:hover {
  transform: translateX(5px);
}
ul {
  list-style-type: none;
  padding: 0;
}
li {
  margin: 5px 0;
  padding: 8px;
  background: #f5f5f5;
  border-radius: 4px;
}
</style>
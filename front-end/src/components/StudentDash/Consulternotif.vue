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
          <div v-if="notifications.length === 0" class="text-center text-gray-500">
            Aucune notification pour le moment.
          </div>
          <div v-else>
            <div 
              v-for="(notification, index) in notifications" 
              :key="index" 
              class="mb-4 p-4 bg-white rounded-lg shadow-sm"
            >
              <!-- Notification de demande -->
              <div v-if="notification.type === 'demande'" class="border-l-4 border-green-500 pl-3">
                <h2 class="text-lg font-semibold text-green-600">
                  📩 {{ notification.title }}
                </h2>
                <p class="text-gray-600 mt-2">
                  {{ notification.message }}
                  <span class="block text-sm text-gray-400 mt-1">
                    {{ formatDate(notification.date) }}
                  </span>
                </p>
              </div>

              <!-- Notification d'offre -->
              <div v-if="notification.type === 'offre'" class="border-l-4 border-blue-500 pl-3">
                <h2 class="text-lg font-semibold text-blue-600">
                  💼 {{ notification.title }}
                </h2>
                <p class="text-gray-600 mt-2">
                  {{ notification.message }}
                  <router-link 
                    to="/OffersListStd" 
                    class="inline-block mt-2 text-blue-600 hover:text-blue-800"
                  >
                    Voir l'offre →
                  </router-link>
                  <span class="block text-sm text-gray-400 mt-1">
                    {{ formatDate(notification.date) }}
                  </span>
                </p>
              </div>

              <!-- Notification de cours -->
              <div v-if="notification.type === 'cours'" class="border-l-4 border-purple-500 pl-3">
                <h2 class="text-lg font-semibold text-purple-600">
                  📚 {{ notification.title }}
                </h2>
                <p class="text-gray-600 mt-2">
                  {{ notification.message }}
                  <router-link 
                    to="/ConsultListCours" 
                    class="inline-block mt-2 text-purple-600 hover:text-purple-800"
                  >
                    Accéder au cours →
                  </router-link>
                  <span class="block text-sm text-gray-400 mt-1">
                    {{ formatDate(notification.date) }}
                  </span>
                </p>
              </div>

              <!-- Notification d'attestation -->
              <div v-if="notification.type === 'attestation'" class="border-l-4 border-orange-500 pl-3">
                <h2 class="text-lg font-semibold text-orange-600">
                  📄 {{ notification.title }}
                </h2>
                <p class="text-gray-600 mt-2">
                  {{ notification.message }}
                  <button 
                    @click="voirAttestation(notification.attestation)"
                    class="ml-2 text-orange-600 hover:text-orange-800"
                  >
                    Télécharger ↓
                  </button>
                  <span class="block text-sm text-gray-400 mt-1">
                    {{ formatDate(notification.date) }}
                  </span>
                </p>
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
import axios from "axios";

export default {
  data() {
    return {
      notifications: [],
      idEtudiant: null
    };
  },
  components: {
    Sidebar,
    Navbar
  },
  methods: {
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
    this.getAccountData();
    this.getNotifications();
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
</style>
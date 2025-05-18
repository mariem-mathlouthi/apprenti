<template>
  <div class="flex flex-col h-screen bg-gray-100">
    <NavBarTuteur />
    <SidebarTuteur class="w-64" />

    <main class="flex-1 p-8 overflow-auto mt-16 ml-64">
      <!-- Boutons Avis -->
      <div class="flex justify-start space-x-4">
        <button
          @click="toggleAvis"
          class="feedback-btn"
          :class="{ 'feedback-btn-primary': !showAvis, 'feedback-btn-secondary': showAvis }"
        >
          <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
          </svg>
          {{ showAvis ? 'Masquer les Avis' : 'Afficher les Avis' }}
        </button>
      </div>

      <!-- Section Avis -->
      <div v-if="showAvis" class="mt-6 bg-gray-100 rounded-lg p-6 -mx-8">
        <div class="max-w-6xl mx-auto">
          <h1 class="text-2xl font-bold text-gray-800 mb-6">Avis des Étudiants</h1>

          <!-- Barre de recherche et filtre -->
          <div class="flex flex-col sm:flex-row gap-4 mb-6">
            <div class="flex-1 relative">
              <input
                type="text"
                v-model="searchQuery"
                placeholder="Rechercher des avis..."
                class="w-full pl-10 pr-10 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
              <svg
                class="absolute left-3 top-2.5 h-5 w-5 text-gray-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                ></path>
              </svg>
              <button
                v-if="searchQuery"
                @click="clearSearch"
                class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                  />
                </svg>
              </button>
            </div>

            <div class="w-full sm:w-64">
              <select
                v-model="selectedRating"
                class="w-full pl-3 pr-10 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="0">Toutes les notes</option>
                <option value="1">1 étoile</option>
                <option value="2">2 étoiles</option>
                <option value="3">3 étoiles</option>
                <option value="4">4 étoiles</option>
                <option value="5">5 étoiles</option>
              </select>
            </div>
          </div>

          <!-- État de chargement -->
          <div v-if="isLoadingFeedbacks" class="text-center py-10">
            <i class="fas fa-spinner fa-spin text-blue-500 text-2xl"></i>
            <p class="mt-2 text-gray-600">Chargement des avis...</p>
          </div>

          <!-- Message d'erreur -->
          <div v-else-if="feedbackError" class="text-center py-10 text-red-500">
            {{ feedbackError }}
          </div>

          <!-- Liste des avis -->
          <div v-else class="space-y-6 bg-white rounded-lg p-6 shadow-sm">
            <div v-for="feedback in filteredAvis" :key="feedback.id" class="border-b pb-6 last:border-b-0">
              <div class="flex justify-between items-start">
                <div>
                  <h2 class="font-bold text-lg text-gray-800">
                    {{ feedback.etudiant.fullname }}
                  </h2>
                  <p class="text-sm text-gray-500">
                    {{ feedback.etudiant.specialite }} - {{ feedback.etudiant.niveau }}
                  </p>
                </div>
                <div class="flex items-center">
                  <div class="flex mr-2">
                    <span v-for="i in 5" :key="i" class="text-yellow-400 text-xl">
                      <span v-if="i <= feedback.note">★</span>
                      <span v-else>☆</span>
                    </span>
                  </div>
                  <span class="text-gray-500 text-sm">{{ formatDate(feedback.created_at) }}</span>
                </div>
              </div>
              
              <p class="mt-4 text-gray-700">{{ feedback.commentaire }}</p>

              <!-- Bouton Répondre -->
              <div v-if="!feedback.reponse" class="mt-4">
                <button
                  @click="repondreAuFeedback(feedback)"
                  class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                  </svg>
                  Répondre
                </button>
              </div>

              <!-- Section des réponses -->
              <div class="mt-4 space-y-4">
                <!-- État de chargement des réponses -->
                <div v-if="loadingReponses[feedback.id]" class="ml-8 text-gray-500">
                  <i class="fas fa-spinner fa-spin"></i> Chargement des réponses...
                </div>

                <!-- Liste des réponses -->
                <div v-else-if="feedbackReponses[feedback.id] && feedbackReponses[feedback.id].length > 0" class="space-y-4">
                  <div v-for="reponse in feedbackReponses[feedback.id]" :key="reponse.id" class="ml-8 p-4 bg-gray-50 rounded-lg border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                      <div>
                        <span class="font-semibold text-blue-600">{{ reponse.user_role === 'tuteur' ? 'Réponse de l\'enseignant' : 'Réponse de l\'étudiant' }}</span>
                        <p class="mt-2 text-gray-600">{{ reponse.contenu }}</p>
                      </div>
                      <span class="text-sm text-gray-500">{{ formatDate(reponse.created_at) }}</span>
                    </div>
                  </div>
                </div>

                <!-- Message si aucune réponse -->
                <div v-else-if="!loadingReponses[feedback.id]" class="ml-8 text-gray-500">
                  Aucune réponse pour cet avis
                </div>
              </div>
            </div>

            <div v-if="!isLoadingFeedbacks && filteredAvis.length === 0" class="text-center py-10">
              <p class="text-gray-500">Aucun avis disponible pour ce cours.</p>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <!-- Modal de réponse -->
  <div v-if="showReponseModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg max-w-lg w-full p-6">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Répondre à l'avis</h3>
        <button @click="showReponseModal = false" class="text-gray-400 hover:text-gray-500">
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div class="mb-4">
        <p class="text-sm text-gray-500 mb-2">Avis de l'étudiant :</p>
        <p class="text-gray-700 bg-gray-50 p-3 rounded">{{ selectedFeedback?.commentaire }}</p>
      </div>

      <div class="mb-4">
        <label for="reponse" class="block text-sm font-medium text-gray-700 mb-2">Votre réponse</label>
        <textarea
          id="reponse"
          v-model="reponseText"
          rows="4"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          placeholder="Écrivez votre réponse ici..."
        ></textarea>
      </div>

      <div class="flex justify-end space-x-3">
        <button
          @click="showReponseModal = false"
          class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
        >
          Annuler
        </button>
        <button
          @click="submitReponse"
          class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          Envoyer la réponse
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import NavBarTuteur from "./NavBarTut.vue";
import SidebarTuteur from "./SidebarTut.vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

export default {
  name: "AvisTuteur",
  components: { NavBarTuteur, SidebarTuteur },
  data() {
    return {
      idCours: this.$route.params.id,
      showAvis: false,
      listeAvis: [],
      selectedRating: 0,
      searchQuery: "",
      isLoadingFeedbacks: false,
      feedbackError: null,
      showReponseModal: false,
      selectedFeedback: null,
      reponseText: "",
      feedbackReponses: {},
      loadingReponses: {},
    };
  },
  computed: {
    filteredAvis() {
      let filtered = this.listeAvis;
      
      if (this.selectedRating > 0) {
        filtered = filtered.filter(avis => avis.note == this.selectedRating);
      }
      
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase();
        filtered = filtered.filter(avis => 
          (avis.etudiant.fullname && avis.etudiant.fullname.toLowerCase().includes(query)) ||
          (avis.commentaire && avis.commentaire.toLowerCase().includes(query))
        );
      }
      
      return filtered;
    }
  },
  methods: {
    async fetchAvis() {
      this.isLoadingFeedbacks = true;
      this.feedbackError = null;
      
      try {
        const response = await axios.get(
          `http://localhost:8000/api/feedbacks/course/${this.idCours}`
        );
        
        if (response.data.success) {
          this.listeAvis = response.data.feedbacks.map(feedback => {
            return {
              ...feedback,
              etudiant: {
                ...feedback.etudiant,
                fullname: feedback.etudiant.fullname || 'Étudiant'
              }
            };
          }).sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

          // Charger les réponses pour chaque avis
          this.listeAvis.forEach(feedback => {
            this.fetchReponses(feedback.id);
          });
        } else {
          throw new Error(response.data.message || 'Erreur inconnue');
        }
      } catch (error) {
        console.error("Erreur lors du chargement des avis :", error);
        this.feedbackError = error.response?.data?.message || "Impossible de charger les avis";
        toast.error(this.feedbackError);
      } finally {
        this.isLoadingFeedbacks = false;
      }
    },

    async fetchReponses(feedbackId) {
      this.loadingReponses[feedbackId] = true;
      try {
        const response = await axios.get(`http://localhost:8000/api/feedback/${feedbackId}/reponses`);
        this.feedbackReponses[feedbackId] = response.data.reponses.map(reponse => ({
          ...reponse,
          contenu: reponse.reponse
        }));
      } catch (error) {
        console.error(`Erreur lors du chargement des réponses pour l'avis ${feedbackId}:`, error);
      } finally {
        this.loadingReponses[feedbackId] = false;
      }
    },

    toggleAvis() {
      this.showAvis = !this.showAvis;
      if (this.showAvis && this.listeAvis.length === 0) {
        this.fetchAvis();
      }
    },

    formatDate(dateString) {
      const date = new Date(dateString);
      const now = new Date();
      const diff = now - date;
      
      const seconds = Math.floor(diff / 1000);
      const minutes = Math.floor(seconds / 60);
      const hours = Math.floor(minutes / 60);
      const days = Math.floor(hours / 24);
      const months = Math.floor(days / 30);
      const years = Math.floor(months / 12);
      
      if (years > 0) return `${years} an${years > 1 ? 's' : ''}`;
      if (months > 0) return `${months} mois`;
      if (days > 0) return `${days} jour${days > 1 ? 's' : ''}`;
      if (hours > 0) return `${hours} heure${hours > 1 ? 's' : ''}`;
      if (minutes > 0) return `${minutes} minute${minutes > 1 ? 's' : ''}`;
      return `${seconds} seconde${seconds > 1 ? 's' : ''}`;
    },

    clearSearch() {
      this.searchQuery = "";
    },

    repondreAuFeedback(feedback) {
      this.selectedFeedback = feedback;
      this.showReponseModal = true;
      this.reponseText = "";
    },

    async submitReponse() {
      if (!this.reponseText.trim()) {
        toast.error("Veuillez écrire une réponse avant d'envoyer");
        return;
      }

      try {
        const token = localStorage.getItem('token');
        const response = await axios.post(
          `http://localhost:8000/api/feedbacks/${this.selectedFeedback.id}/reponse`,
          {
            reponse: this.reponseText,
            user_id: 1,
            user_role: 'tuteur'
          },
        //   {
        //     headers: {
        //       'Authorization': `Bearer ${token}`
        //     }
        //   }
        );

        if (response.status === 200) {
          toast.success('Votre réponse a été envoyée avec succès');
          this.showReponseModal = false;
          this.fetchReponses(this.selectedFeedback.id);
        } else {
          throw new Error(response.data.message || 'Erreur lors de l\'envoi de la réponse');
        }
      } catch (error) {
        console.error('Erreur lors de l\'envoi de la réponse:', error);
        toast.error(error.response?.data?.message || 'Erreur lors de l\'envoi de la réponse');
      }
    }
  }
};
</script>

<style scoped>
.feedback-btn {
  @apply inline-flex items-center px-4 py-2 rounded-md text-sm font-medium transition-colors duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2;
}

.feedback-btn-primary {
  @apply text-white bg-blue-600 hover:bg-blue-700 focus:ring-blue-500;
}

.feedback-btn-secondary {
  @apply text-blue-700 bg-blue-100 hover:bg-blue-200 focus:ring-blue-500;
}

.btn-icon {
  @apply h-5 w-5 mr-2;
}

.menu-enter-active,
.menu-leave-active {
  transition: opacity 0.2s, transform 0.2s;
}

.menu-enter-from,
.menu-leave-to {
  opacity: 0;
  transform: scale(0.95);
}
</style>
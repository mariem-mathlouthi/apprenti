<template>
  <div id="app" class="flex flex-col h-screen bg-gray-100">
    <!-- Navbar -->
    <NavBarStd />

    <!-- Sidebar + Main Content -->
    <div class="flex flex-1">
      <!-- Sidebar -->
      <Sidebar class="w-64" />

      <!-- Main Content -->
      <section class="flex-1 p-8 overflow-auto mt-16 ml-64">
        <div class="max-w-6xl mx-auto">
          <!-- Header -->
          <div class="flex justify-between items-center mb-6">
            <div class="flex-1 text-center">
              <h1 class="text-2xl font-bold text-gray-800">
                Ressources du Cours
              </h1>
            </div>
            <router-link
              :to="{ name: 'ListeQuizz', params: { idCours: idCours } }"
              class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg shadow-md text-sm font-medium transition-all duration-200 hover:shadow-lg"
            >
              Voir les Quizz
            </router-link>
          </div>

          <!-- Liste des ressources -->
          <div
            v-if="ressources.length"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"
          >
            <div
              v-for="ressource in ressources"
              :key="ressource.id"
              class="card transform transition-all hover:scale-105"
            >
              <div class="p-4">
                <h3 class="font-semibold text-base text-gray-800 mb-1">
                  {{ ressource.titre }}
                </h3>
                <p class="text-gray-600 text-xs">{{ ressource.description }}</p>

                <div v-if="ressource.file" class="mt-2">
                  <video
                    v-if="isVideo(ressource.file)"
                    controls
                    class="w-full rounded-lg"
                  >
                    <source :src="ressource.file" type="video/mp4" />
                    Votre navigateur ne supporte pas la lecture de vidéos.
                  </video>

                  <a
                    v-else
                    :href="ressource.file"
                    target="_blank"
                    class="btn-link flex items-center space-x-1 text-xs"
                  >
                    <span>Voir le fichier</span>
                  </a>
                </div>
              </div>
            </div>
          </div>

          <div v-else class="text-center py-10">
            <p class="text-gray-500 text-sm">
              Aucune ressource disponible pour ce cours.
            </p>
          </div>

          <!-- Boutons Avis + Feedback -->
          <!-- <div class="mt-8 flex justify-start space-x-4">
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
            
            <router-link
              :to="{ name: 'FeedbackForm', params: { idCours: idCours } }"
              class="feedback-btn feedback-btn-accent"
            >
              <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              Donner votre avis
            </router-link>
          </div> -->

          <!-- Section Avis -->
         <!-- <div v-if="showAvis" class="mt-6 bg-gray-100 rounded-lg p-6 -mx-8">
            <div class="max-w-6xl mx-auto">
              <h1 class="text-2xl font-bold text-gray-800 mb-6">Avis des Étudiants</h1>

              Barre de recherche et filtre
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

              État de chargement
              <div v-if="isLoadingFeedbacks" class="text-center py-10">
                <i class="fas fa-spinner fa-spin text-blue-500 text-2xl"></i>
                <p class="mt-2 text-gray-600">Chargement des avis...</p>
              </div>

              Message d'erreur
              <div v-else-if="feedbackError" class="text-center py-10 text-red-500">
                {{ feedbackError }}
              </div>

              Liste des avis
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
                  
                  <div class="mt-2 flex items-center justify-between">
                    <div class="flex items-center">
                      <img // comment
                        v-if="feedback.etudiant.image" 
                        :src="'/storage/' + feedback.etudiant.image" 
                        class="w-8 h-8 rounded-full mr-2"
                        alt="Photo de profil"
                      > // comment
                      <span class="text-sm text-gray-500">
                        Posté le {{ new Date(feedback.created_at).toLocaleDateString() }}
                      </span>
                    </div>
                    
                    Menu déroulant pour les actions
                    <div v-if="isMyFeedback(feedback)" class="relative">
                      Bouton des trois points
                      <button 
                        @click.stop="toggleMenu(feedback.id)"
                        class="text-gray-500 hover:text-gray-700 focus:outline-none p-1 rounded-full hover:bg-gray-200"
                      >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                        </svg>
                      </button>
                      
                      Menu déroulant
                      <transition name="menu">
                        <div 
                          v-if="activeMenu === feedback.id"
                          class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 border border-gray-200"
                          v-click-outside="closeMenu"
                        >
                          Option Modifier
                          <router-link
                            :to="{ name: 'EditFeedback', params: { id: feedback.id, idCours: idCours } }"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center menu-option"
                          >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Modifier
                          </router-link>
                          
                          Option Supprimer
                          <button
                            @click="confirmDeleteFeedback(feedback.id)"
                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center menu-option"
                          >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Supprimer
                          </button>
                          
                          Option Copier
                          <button
                            @click="copyFeedback(feedback.commentaire)"
                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center menu-option"
                          >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                            </svg>
                            Copier
                          </button>
                        </div>
                      </transition>
                    </div>
                  </div>
                </div>

                <div v-if="!isLoadingFeedbacks && filteredAvis.length === 0" class="text-center py-10">
                  <p class="text-gray-500">Aucun avis disponible pour ce cours.</p>
                </div>
              </div>
            </div>
          </div>-->

        </div>
      </section>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import NavBarStd from "./NavBarStd.vue";
import Sidebar from "./Sidebar.vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import clickOutside from '../../directives/clickOutside';
export default {
  name: "ConsultRessource",
  components: { NavBarStd, Sidebar },
  directives: {
    'click-outside': clickOutside
  },
  data() {
    return {
      ressources: [],
      idCours: this.$route.params.id,
      showAvis: false,
      listeAvis: [],
      selectedRating: 0,
      searchQuery: "",
      isLoadingFeedbacks: false,
      feedbackError: null,
      activeMenu: null,
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
    async fetchRessources() {
      try {
        const response = await axios.get("http://localhost:8000/api/ressources");
        this.ressources = response.data.filter(
          ressource => ressource.idCours === parseInt(this.idCours)
        );
      } catch (error) {
        console.error("Erreur lors du chargement des ressources :", error);
        toast.error("Erreur lors du chargement des ressources");
      }
    },

    async fetchAvis() {
      this.isLoadingFeedbacks = true;
      this.feedbackError = null;
      
      try {
        const token = localStorage.getItem('token');
        const response = await axios.get(
          `http://localhost:8000/api/feedbacks/course/${this.idCours}`,
          {
            headers: {
              'Authorization': `Bearer ${token}`
            }
          }
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

    isVideo(file) {
      if (!file) return false;
      const videoExtensions = [".mp4", ".webm", ".ogg"];
      return videoExtensions.some(ext => file.toLowerCase().endsWith(ext));
    },
    
    isMyFeedback(feedback) {
      const studentInfo = JSON.parse(localStorage.getItem('StudentAccountInfo'));
      return studentInfo && studentInfo.id === feedback.etudiant_id;
    },

    async confirmDeleteFeedback(feedbackId) {
      if (confirm('Êtes-vous sûr de vouloir supprimer votre avis ? Cette action est irréversible.')) {
        try {
          const token = localStorage.getItem('token');
          await axios.delete(
            `http://localhost:8000/api/feedbacks/${feedbackId}`,
            {
              headers: {
                'Authorization': `Bearer ${token}`
              }
            }
          );
          
          toast.success('Votre avis a été supprimé avec succès');
          this.fetchAvis();
          this.activeMenu = null;
        } catch (error) {
          console.error("Erreur lors de la suppression:", error);
          const errorMessage = error.response?.data?.message || 
            'Une erreur est survenue lors de la suppression';
          toast.error(errorMessage);
        }
      }
    },

    toggleMenu(feedbackId) {
      this.activeMenu = this.activeMenu === feedbackId ? null : feedbackId;
    },
    
    closeMenu() {
      this.activeMenu = null;
    },
    
    copyFeedback(text) {
      navigator.clipboard.writeText(text)
        .then(() => {
          toast.success('Commentaire copié dans le presse-papier');
          this.activeMenu = null;
        })
        .catch(err => {
          console.error('Erreur lors de la copie:', err);
          toast.error('Échec de la copie');
        });
    },
  },
  mounted() {
    this.fetchRessources();
  }
};
</script>

<style scoped>
.feedback-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  font-size: 0.875rem;
  font-weight: 500;
  transition: all 0.2s ease-in-out;
}

.feedback-btn:hover {
  transform: translateY(-0.125rem);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.feedback-btn-primary {
  background-color: #4f46e5;
  color: white;
}

.feedback-btn-primary:hover {
  background-color: #4338ca;
}

.feedback-btn-secondary {
  background-color: #6b7280;
  color: white;
}

.feedback-btn-secondary:hover {
  background-color: #4b5563;
}

.feedback-btn-accent {
  background-color: #059669;
  color: white;
}

.feedback-btn-accent:hover {
  background-color: #047857;
}

.btn-icon {
  width: 1.25rem;
  height: 1.25rem;
}

.card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s, box-shadow 0.3s;
}

.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.btn-link {
  display: inline-flex;
  align-items: center;
  color: #4f46e5;
  font-weight: bold;
  text-decoration: none;
  transition: 0.3s;
}

.btn-link:hover {
  color: #4338ca;
  text-decoration: underline;
}

/* Animation pour le menu déroulant */
.menu-enter-active, .menu-leave-active {
  transition: opacity 0.2s, transform 0.2s;
}
.menu-enter-from, .menu-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Style pour les options du menu */
.menu-option {
  transition: background-color 0.2s;
}
.menu-option:hover {
  background-color: #f3f4f6;
}

@media (min-width: 640px) {
  .sm\:flex-row {
    flex-direction: row;
  }
  
  .sm\:w-64 {
    width: 16rem;
  }
}
</style>
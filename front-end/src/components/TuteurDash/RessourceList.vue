<template>
  <div id="app" class="flex flex-col h-screen bg-white">
    <!-- Navbar -->
    <NavbarTuteur />

    <!-- Sidebar + Main Content -->
    <div class="flex flex-1">
      <!-- Sidebar -->
      <SidebarTuteur class="w-64" />

      <!-- Main Content -->
      <section class="flex-1 p-8 overflow-auto mt-16 ml-64">
        <div class="max-w-6xl mx-auto">
          <!-- Header avec recherche -->
          <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-6">
            <div class="flex-1 w-full">
              <h1 class="text-2xl font-bold text-gray-800 mb-2">
                Ressources du Cours
                <span class="text-sm font-normal text-gray-500 ml-2">({{ filteredRessources.length }} éléments)</span>
              </h1>
              
              <!-- Barre de recherche -->
              <div class="relative max-w-xl">
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Rechercher une ressource..."
                  class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50"
                >
                <svg class="absolute left-3 top-3 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
              </div>
            </div>

            <!-- Bouton Ajouter -->
            <router-link 
              :to="`/cours/${idCours}/ajouter-ressource`"
              class="btn-primary flex items-center gap-2 px-5 py-2.5 rounded-lg transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
              </svg>
              Nouvelle ressource
            </router-link>
          </div>

          <!-- Liste des ressources -->
          <div v-if="filteredRessources.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div 
              v-for="ressource in filteredRessources" 
              :key="ressource.id" 
              class="bg-white rounded-xl shadow-sm hover:shadow-md transition-all border border-gray-200"
            >
              <div class="p-4">
                <!-- En-tête -->
                <div class="flex justify-between items-start mb-4">
                  <div class="flex-1 pr-2">
                    <h3 class="font-semibold text-gray-800 truncate">{{ ressource.titre }}</h3>
                    <p class="text-gray-500 text-sm mt-1 line-clamp-2">{{ ressource.description }}</p>
                  </div>
                  <div class="flex space-x-1">
                    <!-- Bouton d'édition -->
                    <button 
                      @click="editRessource(ressource)"
                      class="text-blue-600 hover:text-blue-800 p-1.5 rounded-md hover:bg-blue-50"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                      </svg>
                    </button>
                    
                    <!-- Bouton de suppression -->
                    <button 
                      @click="deleteRessource(ressource.id)"
                      class="text-red-500 hover:text-red-700 p-1.5 rounded-md hover:bg-red-50"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                  </div>
                </div>

                <!-- Contenu média -->
                <div class="mt-4 rounded-lg overflow-hidden bg-gray-50 border border-gray-200">
                  <div class="aspect-video flex items-center justify-center">
                    <video 
                      v-if="isVideo(ressource.file)"
                      :src="ressource.file" 
                      controls
                      class="w-full h-full object-cover"
                    ></video>
                    
                    <template v-else>
                      <a 
                        :href="ressource.file" 
                        target="_blank"
                        class="w-full p-6 flex flex-col items-center justify-center hover:bg-gray-100 transition-colors"
                      >
                        <svg class="w-12 h-12 text-blue-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="text-sm font-medium text-blue-600 text-center">
                          Ouvrir le fichier<br>
                          <span class="text-xs text-gray-500">(.{{ getFileType(ressource.file) }})</span>
                        </span>
                      </a>
                    </template>
                  </div>
                </div>

                <!-- Métadonnées -->
                <div class="mt-4 flex items-center justify-between text-sm">
                  <span class="text-gray-500">
                    Ajouté {{ formatRelativeDate(ressource.created_at) }}
                  </span>
                  <span class="flex items-center text-gray-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ formatTime(ressource.created_at) }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- État vide -->
          <div v-else class="text-center py-20">
            <div class="mb-4 inline-flex items-center justify-center w-16 h-16 bg-blue-50 rounded-full">
              <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
            </div>
            <p class="text-gray-600">Aucune ressource trouvée</p>
            <p class="text-sm text-gray-500 mt-2">Commencez par ajouter une nouvelle ressource</p>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import NavbarTuteur from './NavbarTut.vue';
import SidebarTuteur from './SidebarTut.vue';
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

export default {
  name: "RessourceList",
  components: { NavbarTuteur, SidebarTuteur },
  data() {
    return {
      ressources: [],
      idCours: this.$route.params.id,
      searchQuery: "",
    };
  },
  computed: {
    filteredRessources() {
      const query = this.searchQuery.toLowerCase();
      return this.ressources.filter(r => 
        r.titre.toLowerCase().includes(query) ||
        r.description.toLowerCase().includes(query)
      );
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
        toast.error("Erreur lors du chargement des ressources");
      }
    },

    async deleteRessource(id) {
      if(confirm("Êtes-vous sûr de vouloir supprimer cette ressource ?")) {
        try {
          await axios.delete(`http://localhost:8000/api/ressources/${id}`);
          this.ressources = this.ressources.filter(r => r.id !== id);
          toast.success("Ressource supprimée avec succès");
        } catch (error) {
          toast.error("Échec de la suppression");
        }
      }
    },

    editRessource(ressource) {
      this.$router.push({ 
        name: "RessourceEdit", 
        params: { id: ressource.id } 
      });
    },

    isVideo(file) {
      const videoExtensions = ['mp4', 'webm', 'ogg'];
      return videoExtensions.some(ext => file.toLowerCase().endsWith(`.${ext}`));
    },

    getFileType(filePath) {
      return filePath.split('.').pop().toUpperCase();
    },

    formatRelativeDate(dateString) {
      const date = new Date(dateString);
      const options = { day: 'numeric', month: 'long' };
      return `le ${date.toLocaleDateString('fr-FR', options)}`;
    },

    formatTime(dateString) {
      const date = new Date(dateString);
      return date.toLocaleTimeString('fr-FR', { 
        hour: '2-digit', 
        minute: '2-digit' 
      });
    }
  },
  mounted() {
    this.fetchRessources();
  }
};
</script>

<style scoped>
.btn-primary {
  background-color: #2563EB;
  color: white;
  font-weight: 500;
  transition: background-color 0.2s, transform 0.2s;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.btn-primary:hover {
  background-color: #1D4ED8;
  transform: translateY(-1px);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.border-gray-200 {
  border-color: #e5e7eb;
}

.hover\:shadow-md {
  transition: box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
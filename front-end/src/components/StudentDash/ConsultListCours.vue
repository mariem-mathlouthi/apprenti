<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-gray-100">
        <NavBarStd class="fixed top-0 left-0 w-full z-50 shadow-md bg-white" />

  
        <div class="pt-24 grid grid-cols-12 gap-6 p-8 items-stretch min-h-[calc(100vh-5rem)]">

        <div class="col-span-3">
        <Sidebar />
      </div>
  
        <!-- Contenu principal -->
        <div class="col-span-9 h-full">
          <div class="h-full bg-white/80 backdrop-blur-md rounded-2xl shadow-xl p-8 border border-white/30 overflow-y-auto">
            <!-- Barre de recherche -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-12 space-y-6 md:space-y-0">
              <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                Explorer les cours
              </h1>
              
              <div class="flex flex-col md:flex-row items-center gap-4 w-full md:w-auto">
                <div class="relative group w-full md:w-96">
                  <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-purple-400 rounded-xl blur opacity-25 group-hover:opacity-40 transition duration-300"></div>
                  <div class="relative flex items-center bg-white rounded-xl shadow-sm">
                    <select v-model="searchCriteria" class="pl-4 pr-2 py-3 border-none bg-transparent font-semibold text-gray-600 focus:ring-0">
                      <option value="title">Titre</option>
                      <option value="description">Description</option>
                      <option value="tuteur">Tuteur</option>
                    </select>
                    <div class="flex-1 relative">
                      <input
                        type="text"
                        v-model="searchQuery"
                        placeholder="Rechercher un cours..."
                        class="w-full px-4 py-3 border-0 bg-transparent focus:ring-0 placeholder-gray-400"
                      />
                      <svg class="w-5 h-5 absolute right-4 top-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                      </svg>
                    </div>
                  </div>
                </div>
                <button 
                  @click="resetSearch"
                  class="px-6 py-3 bg-gradient-to-r from-gray-100 to-gray-50 hover:from-gray-200 hover:to-gray-150 text-gray-600 rounded-xl font-semibold shadow-sm transition-all duration-300 transform hover:scale-[1.02]"
                >
                  Réinitialiser
                </button>
              </div>
            </div>
  
            <!-- États de chargement -->
            <div v-if="loading" class="animate-pulse space-y-8">
              <div v-for="i in 3" :key="i" class="h-48 bg-gray-100 rounded-2xl"></div>
            </div>
            
            <!-- Message d'erreur -->
            <div v-else-if="error" class="text-red-500 text-center p-6 bg-red-50/50 rounded-xl border border-red-100">
              ⚠️ {{ error }}
            </div>
  
            <!-- Grille des cours -->
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
              <article 
                v-for="cours in filteredCours" 
                :key="cours.id"
                class="group relative bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2"
              >
                <!-- Effet de fond au survol -->
                <div class="absolute inset-0 bg-gradient-to-r from-blue-50 to-purple-50 opacity-0 group-hover:opacity-100 rounded-2xl transition-opacity duration-300"></div>
                
                <div class="relative">
                  <!-- Section image -->
                  <div class="relative h-56 overflow-hidden rounded-t-2xl">
                    <img 
                      :src="'http://localhost:8000' + cours.file" 
                      alt="Cours Photo" 
                      class="w-full h-full object-cover transform transition duration-500 group-hover:scale-105"
                      @error="handleImageError"
                    />
                    <!-- Badge durée + prix -->
                    <div class="absolute top-4 right-4 bg-white/80 backdrop-blur-sm px-3 py-1 rounded-full text-sm font-bold text-gray-700 shadow-sm flex items-center gap-2">
                      <span>⏳ {{ cours.duration }}h</span>
                      <span class="text-emerald-600">•</span>
                      <span class="text-emerald-600">{{ cours.prix?.toFixed(2) || '0.00' }} €</span>
                    </div>
                  </div>
  
                  <!-- Contenu de la carte -->
                  <div class="p-6 space-y-4">
                    <!-- Catégorie -->
                    <div class="inline-flex items-center bg-gradient-to-r from-blue-500 to-purple-500 text-white px-4 py-1 rounded-full">
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                      </svg>
                      <span class="text-sm font-semibold">{{ cours.category_description || 'Général' }}</span>
                    </div>
  
                    <!-- Titre + Description -->
                    <h3 class="text-xl font-bold text-gray-800 leading-tight">{{ cours.titre }}</h3>
                    <p class="text-gray-600 text-sm line-clamp-2 leading-relaxed">
                      {{ cours.description }}
                    </p>
  
                    <!-- Section prix détaillée -->
                    <div class="flex items-center gap-3 p-3 bg-emerald-50/50 rounded-lg">
                      <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                      <div>
                        <p class="text-xs font-semibold text-emerald-700">INVESTISSEMENT</p>
                        <p class="text-lg font-bold text-emerald-600">{{ cours.prix?.toFixed(2) || 'Gratuit' }} €</p>
                      </div>
                    </div>
  
                    <!-- Pied de carte -->
                    <div class="flex justify-between items-center pt-4 border-t border-gray-100 group-hover:border-transparent transition-all">
                      <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                          {{ cours.tuteur?.charAt(0) || '?' }}
                        </div>
                        <span class="text-gray-600 text-sm font-medium transition-colors hover:text-gray-900">
                          {{ cours.tuteur || 'Tuteur inconnu' }}
                        </span>
                      </div>
                      <router-link 
                        :to="'/DetailsCours/' + cours.id" 
                        class="flex items-center px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white rounded-lg transition-all duration-300 transform hover:scale-105"
                      >
                        <span>Voir</span>
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                      </router-link>
                    </div>
                  </div>
                </div>
              </article>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import NavBarStd from './NavBarStd.vue';
import Sidebar from './Sidebar.vue';
  import axios from "axios";
  
  export default {
    data() {
      return {
        cours: [],
        filteredCours: [],
        searchCriteria: "title",
        searchQuery: "",
        loading: true,
        error: null,
        defaultImage: '/placeholder-course.jpg'
      };
    },
    components: { NavBarStd, Sidebar },
    async created() {
      await this.fetchCourses();
    },
    methods: {
      async fetchCourses() {
        try {
          const response = await axios.get("http://localhost:8000/api/cours");
          if (response.data.success) {
            this.cours = response.data.cours.map(c => ({
              id: c.id,
              titre: c.titre,
              description: c.description,
              category_description: c.category?.description,
              tuteur: c.tuteur?.fullname,
              prix: c.prix ? parseFloat(c.prix) : 0.00,
              duration: c.duration,
              file: c.file || this.defaultImage
            }));
            this.filteredCours = this.cours;
          }
        } catch (error) {
          console.error("Erreur:", error);
          this.error = "Une erreur est survenue lors du chargement des cours";
        } finally {
          this.loading = false;
        }
      },
      handleImageError(event) {
        event.target.src = this.defaultImage;
      },
      searchCourses() {
        const query = this.searchQuery.toLowerCase().trim();
        if (!query) {
          this.filteredCours = this.cours;
          return;
        }
        this.filteredCours = this.cours.filter(cours => {
          const target = this.searchCriteria === 'title' ? cours.titre :
                        this.searchCriteria === 'description' ? cours.description :
                        cours.tuteur?.toLowerCase() || '';
          return target.toLowerCase().includes(query);
        });
      },
      resetSearch() {
        this.searchQuery = '';
        this.filteredCours = this.cours;
      }
    },
    watch: {
      searchQuery() {
        this.searchCourses();
      }
    }
  };
  </script>
  
  <style scoped>
  .line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2; 
  -webkit-box-orient: vertical;
  overflow: hidden;
}

  
  @keyframes glow {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }
  
  .glow-effect {
    background-size: 200% 200%;
    animation: glow 3s ease infinite;
  }
  
  /* Correction pour le sticky header */
  .min-h-screen {
    display: flex;
    flex-direction: column;
  }
  </style>
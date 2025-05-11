<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-indigo-50/20">
    <NavBarStd class="fixed top-0 left-0 w-full z-50 shadow-sm bg-white/90 backdrop-blur-md" />
    
    <div class="pt-24 grid grid-cols-12 gap-6 p-8 items-stretch min-h-[calc(100vh-5rem)]">
      <div class="col-span-3">
        <Sidebar />
      </div>

      <div class="col-span-9 h-full">
        <div class="h-full bg-white/95 backdrop-blur-xl rounded-[2.5rem] shadow-2xl p-10 border border-white/20 overflow-y-auto">
          <!-- En-tête amélioré -->
          <div class="mb-16">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8">
              <div class="space-y-4">
                <div class="relative inline-block">
                  <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-purple-500 blur-2xl opacity-30 animate-pulse-slow"></div>
                  <h1 class="relative text-5xl font-bold bg-gradient-to-r from-indigo-600 to-purple-500 bg-clip-text text-transparent">
                    Explorez les cours
                  </h1>
                </div>
                <p class="text-gray-500/90 text-lg font-light">Plongez dans un univers de connaissances</p>
              </div>
              
              <!-- Barre de recherche premium -->
              <div class="relative group w-full md:w-96">
                <div class="absolute inset-0 bg-gradient-to-r from-indigo-300/30 to-purple-300/20 rounded-2xl blur-[30px] opacity-50 group-hover:opacity-70 transition-opacity duration-300"></div>
                <div class="relative flex items-center bg-white/90 backdrop-blur-sm rounded-2xl shadow-lg hover:shadow-md border border-gray-100/50 transition-all duration-300">
                  <input
                    v-model="searchQuery"
                    placeholder="Rechercher un cours..."
                    class="w-full px-6 py-4 border-none bg-transparent focus:ring-0 placeholder-gray-400/80 text-gray-700 font-medium focus:placeholder-transparent transition-all"
                  />
                  <svg class="w-6 h-6 mr-4 text-gray-400/80 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                  </svg>
                </div>
              </div>
            </div>
          </div>

          <!-- Liste des cours améliorée -->
          <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div v-for="i in 3" :key="i" class="h-[400px] bg-gradient-to-br from-gray-100 to-gray-50 rounded-2xl relative overflow-hidden">
              <div class="absolute inset-0 bg-gradient-to-r from-gray-200/50 to-gray-300/0 animate-shimmer"></div>
            </div>
          </div>

          <div v-else-if="error" class="text-red-500 text-center p-8 bg-red-50/50 rounded-2xl border border-red-100 backdrop-blur-sm flex items-center justify-center gap-3">
            <svg class="w-8 h-8 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span class="text-lg">{{ error }}</span>
          </div>

          <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Carte de cours premium -->
            <article 
              v-for="cours in filteredCours" 
              :key="cours.id"
              class="group relative bg-white rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 overflow-hidden isolate"
            >
              <!-- Effet de halo au survol -->
              <div class="absolute inset-0 bg-gradient-to-r from-indigo-500/5 to-purple-500/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 -z-10"></div>
              
              <!-- Image avec effet parallaxe -->
              <div class="relative h-64 overflow-hidden transform perspective-1000">
                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent z-10"></div>
                <div class="absolute inset-0 group-hover:scale-[1.02] transition-transform duration-700 will-change-transform">
                  <img 
                    :src="'http://localhost:8000' + cours.file" 
                    :alt="cours.titre"
                    class="w-full h-full object-cover transform transition-transform duration-700 group-hover:scale-105"
                    @error="handleImageError"
                  />
                </div>
                <div class="absolute bottom-4 right-4 z-20">
                  <span class="bg-gradient-to-r from-indigo-500 to-purple-500 text-white px-4 py-1.5 rounded-full text-sm font-bold shadow-lg hover:shadow-xl transition-shadow duration-300 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ cours.prix?.toFixed(2) || 'Gratuit' }} €
                  </span>
                </div>
              </div>

              <!-- Contenu avec effet de dégradé textuel -->
              <div class="p-6 space-y-5 bg-gradient-to-b from-white via-white to-white/90">
                <h3 class="text-2xl font-bold text-gray-800 leading-tight pr-4 transition-colors duration-300 group-hover:text-indigo-600">
                  {{ cours.titre }}
                </h3>

                <router-link 
                  :to="`/DetailCour/${cours.id}`"
                  class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-500 text-white rounded-xl text-sm font-semibold hover:shadow-lg hover:scale-[1.02] transition-all duration-300 group/button"
                >
                  <span @click="voirPlus" >Voir plus</span>
                  <svg class="w-4 h-4 ml-2 transform group-hover/button:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                  </svg>
                </router-link>
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
            prix: c.prix ? parseFloat(c.prix) : 0.00,
            file: c.file || this.defaultImage
          }));
          this.filteredCours = this.cours;
        }
      } catch (error) {
        console.error("Erreur:", error);
        this.error = "Erreur de chargement des cours";
      } finally {
        this.loading = false;
      }
    },
    handleImageError(event) {
      event.target.src = this.defaultImage;
    },
    filterCourses() {
    this.filteredCours = this.cours.filter(cours => 
      cours.titre.toLowerCase().includes(this.searchQuery.toLowerCase())
    )
    }
},
  watch: {
    searchQuery() {
      this.filterCourses();
    }
  }
};
</script>

<style scoped>
@keyframes pulse-slow {
  0%, 100% { opacity: 0.8; }
  50% { opacity: 0.4; }
}

@keyframes shimmer {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(100%); }
}

.animate-pulse-slow {
  animation: pulse-slow 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.animate-shimmer {
  animation: shimmer 1.5s infinite;
  background-image: linear-gradient(
    90deg,
    transparent 25%,
    rgba(255,255,255,0.5) 50%,
    transparent 75%
  );
  background-size: 200% 100%;
}

.perspective-1000 {
  perspective: 1000px;
}

.group:hover .perspective-1000 {
  transform: rotateX(2deg) rotateY(2deg);
  transition: transform 0.5s cubic-bezier(0.23, 1, 0.32, 1);
}
</style>
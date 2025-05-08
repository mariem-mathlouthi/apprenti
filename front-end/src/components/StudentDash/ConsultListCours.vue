<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-indigo-50/20">
    <NavBarStd class="fixed top-0 left-0 w-full z-50 shadow-sm bg-white" />
    
    <div class="pt-24 grid grid-cols-12 gap-6 p-8 items-stretch min-h-[calc(100vh-5rem)]">
      <div class="col-span-3">
        <Sidebar />
      </div>

      <div class="col-span-9 h-full">
        <div class="h-full bg-white/95 backdrop-blur-xl rounded-[2.5rem] shadow-2xl p-10 border border-white/20 overflow-y-auto">
          <!-- En-tête -->
          <div class="mb-16">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-8">
              <div class="space-y-2">
                <h1 class="text-5xl font-bold bg-gradient-to-r from-indigo-600 to-purple-500 bg-clip-text text-transparent">
                  Explorez les cours
                </h1>
                <p class="text-gray-500 text-lg">Découvrez notre sélection exclusive de formations</p>
              </div>
              
              <!-- Barre de recherche stylisée -->
              <div class="relative group w-full md:w-96">
                <div class="absolute inset-0 bg-gradient-to-r from-indigo-100 to-purple-100 rounded-2xl blur opacity-30 group-hover:opacity-50 transition-opacity"></div>
                <div class="relative flex items-center bg-white rounded-2xl shadow-sm border border-gray-100">
                  <input
                    v-model="searchQuery"
                    placeholder="Rechercher un cours..."
                    class="w-full px-6 py-4 border-none bg-transparent focus:ring-0 placeholder-gray-400 text-gray-700"
                  />
                  <svg class="w-6 h-6 mr-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                  </svg>
                </div>
              </div>
            </div>
          </div>

          <!-- Liste des cours -->
          <div v-if="loading" class="animate-pulse grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div v-for="i in 3" :key="i" class="h-[400px] bg-gradient-to-br from-gray-100 to-gray-50 rounded-2xl"></div>
          </div>

          <div v-else-if="error" class="text-red-500 text-center p-8 bg-red-50/50 rounded-2xl border border-red-100">
            ⚠️ {{ error }}
          </div>

          <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Carte de cours améliorée -->
            <article 
              v-for="cours in filteredCours" 
              :key="cours.id"
              class="group relative bg-white rounded-2xl shadow-2xl hover:shadow-3xl transition-all duration-500 hover:-translate-y-2 overflow-hidden"
            >
              <!-- Image avec effet de profondeur -->
              <div class="relative h-64 overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent z-10"></div>
                <img 
                  :src="'http://localhost:8000' + cours.file" 
                  :alt="cours.titre"
                  class="w-full h-full object-cover transform transition-transform duration-700 group-hover:scale-105"
                  @error="handleImageError"
                />
              </div>

              <!-- Contenu -->
              <div class="p-6 space-y-5">
                <div class="flex justify-between items-start">
                  <h3 class="text-2xl font-bold text-gray-800 leading-tight pr-4">{{ cours.titre }}</h3>
                  <span class="bg-gradient-to-r from-indigo-500 to-purple-500 text-white px-4 py-1.5 rounded-full text-sm font-bold shadow-sm">
                    {{ cours.prix?.toFixed(2) || 'Gratuit' }} €
                  </span>
                </div>

                <router-link 
  :to="`/DetailCour/${cours.id}`"
  class="inline-block mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700 transition"
>
  Voir plus
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
.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}
</style>
<template>
  <div id="app" class="flex flex-col h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- Navbar -->
    <NavBarStd />
    
    <!-- Sidebar -->
    <Sidebar class="w-64" />

    <!-- Main Content -->
    <main class="flex-1 overflow-auto mt-16 ml-64 p-8">
      <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
          <h1 class="text-2xl font-bold text-gray-800">
            Quizz du Cours: {{ coursTitre }}
          </h1>
          <router-link 
            :to="{ name: 'ConsultRessource', params: { id: idCours } }" 
            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors"
          >
            Retour aux ressources
          </router-link>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center py-12">
          <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500 mb-4"></div>
          <p class="text-gray-600">Chargement des quizz...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="quizzList.length === 0" class="text-center py-12 bg-white rounded-lg shadow">
          <div class="mx-auto w-24 h-24 text-gray-400 mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mb-1">Aucun quizz disponible</h3>
          <p class="text-gray-500">Le tuteur n'a pas encore ajouté de quizz pour ce cours.</p>
        </div>

        <!-- Quizz List -->
        <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
          <div 
            v-for="quizz in quizzList" 
            :key="quizz.id" 
            class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow"
          >
            <div class="p-6">
              <div class="flex justify-between items-start mb-2">
                <h2 class="text-xl font-semibold text-gray-800">{{ quizz.titre }}</h2>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                  {{ quizz.score }} pts
                </span>
              </div>
              
              <p class="text-gray-600 mb-4">{{ quizz.description }}</p>
              
              <div class="mt-4">
                <router-link 
                  :to="{ name: 'PasserQuizz', params: { idQuizz: quizz.id } }" 
                  class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  Commencer le quizz
                </router-link>
              </div>
              
              <div class="mt-3 text-xs text-gray-500">
                <div class="flex items-center">
                  <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold mr-2">
                    {{ getInitial(quizz.tuteurNom) }}
                  </div>
                  <span>Créé par: {{ quizz.tuteurNom || 'Tuteur inconnu' }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import axios from 'axios';
import NavBarStd from './NavBarStd.vue';
import Sidebar from './Sidebar.vue';
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

export default {
  name: "ListeQuizz",
  components: { NavBarStd, Sidebar },
  data() {
      return {
          quizzList: [],
          coursTitre: '',
          loading: true,
          idCours: this.$route.params.idCours
      };
  },
  methods: {
      getInitial(name) {
          return name ? name.charAt(0).toUpperCase() : 'T';
      },

      async fetchQuizz() {
          try {
              this.loading = true;
              
              // 1. Récupérer les quizz
              const quizzResponse = await axios.get(`http://localhost:8000/api/quizz`);
              let quizzData = quizzResponse.data?.data || quizzResponse.data || [];
              
              if (!Array.isArray(quizzData)) {
                  quizzData = [];
              }
              
              // 2. Récupérer les tuteurs
              const tuteursResponse = await axios.get('http://localhost:8000/api/tuteurs');
              let tuteursData = tuteursResponse.data?.data || tuteursResponse.data || [];
              
              if (!Array.isArray(tuteursData)) {
                  tuteursData = [];
              }
              
              // Créer un mapping des tuteurs
              const tuteursMap = {};
              tuteursData.forEach(tuteur => {
                  if (tuteur.id) {
                      tuteursMap[tuteur.id] = tuteur.fullname || 
                                            `${tuteur.prenom || ''} ${tuteur.nom || ''}`.trim() || 
                                            'Tuteur inconnu';
                  }
              });
              
              // 3. Filtrer et mapper les quizz
              this.quizzList = quizzData
                  .filter(q => q.idCours == this.idCours)
                  .map(quizz => ({
                      ...quizz,
                      tuteurNom: tuteursMap[quizz.idTuteur] || 'Tuteur inconnu'
                  }));
              
              // 4. Récupérer le titre du cours
              const coursResponse = await axios.get(`http://localhost:8000/api/cours/${this.idCours}`);
              const coursData = coursResponse.data?.data || coursResponse.data || {};
              this.coursTitre = coursData.titre || 'Cours sans titre';

          } catch (error) {
              console.error("Erreur:", error);
              toast.error("Erreur lors du chargement des données");
              
              // En cas d'erreur, initialiser avec des valeurs par défaut
              this.quizzList = [];
              this.coursTitre = 'Cours sans titre';
          } finally {
              this.loading = false;
          }
      }
  },
  mounted() {
      this.fetchQuizz();
  }
};
</script>

<style scoped>
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.transition-shadow {
  transition: box-shadow 0.3s ease;
}
</style>
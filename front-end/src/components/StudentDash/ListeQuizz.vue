<template>
  <div class="min-h-screen bg-gray-50">
    <NavBarStd />

    <div class="flex pt-16">
      <!-- Sidebar -->
      <Sidebar />

      <!-- Main Content -->
      <main class="flex-1 ml-64 p-8">
        <div class="max-w-4xl mx-auto">
          <!-- Header Section -->
          <div class="flex items-center justify-between mb-8">
            <div>
              <h1 class="text-2xl font-bold text-gray-800">
                Quizz pour le cours : {{ coursTitre }}
              </h1>
              <p class="text-gray-500 mt-1">Testez vos connaissances</p>
            </div>
            <router-link 
              :to="{ name: 'ConsultRessource', params: { id: idCours } }" 
              class="flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors"
            >
              <span class="mr-2">←</span>
              Retour aux ressources
            </router-link>
          </div>

          <!-- Loading State -->
          <div v-if="loading" class="text-center py-12">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-600"></div>
          </div>

          <!-- Empty State -->
          <div v-else-if="!groupedQuizz.length" class="text-center p-8 bg-white rounded-xl shadow-sm">
            <div class="inline-block p-4 mb-4 bg-gray-100 rounded-full">
              <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun quizz disponible</h3>
            <p class="text-gray-500">Le tuteur n'a pas encore créé de quizz pour ce cours</p>
          </div>

          <!-- Quizz Cards -->
          <div v-else class="grid gap-6 md:grid-cols-2">
            <div 
              v-for="(quizz, index) in groupedQuizz" 
              :key="index"
              class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300"
            >
              <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                  <h2 class="text-xl font-semibold text-gray-800">{{ quizz.titre }}</h2>
                  <span class="px-3 py-1 text-sm font-medium text-green-800 bg-green-100 rounded-full">
                    {{ quizz.questionCount }} question{{ quizz.questionCount > 1 ? 's' : '' }}
                  </span>
                </div>

                <div class="flex items-center text-sm text-gray-600 mb-4">
                  <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  Durée : {{ quizz.dureeEstimee }}
                </div>

                <router-link
                  :to="{ 
                    name: 'PasserQuizz',
                    params: { 
                      idCours: quizz.idCours,
                      titreQuizz: quizz.titre 
                    }
                  }"
                  class="block w-full px-4 py-2 text-sm font-medium text-center text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors"
                >
                  Commencer le quizz →
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import NavBarStd from './NavBarStd.vue'
import Sidebar from './Sidebar.vue'
import { toast } from "vue3-toastify"
import "vue3-toastify/dist/index.css"

export default {
  name: "ListeQuizz",
  components: { NavBarStd, Sidebar },
  data() {
    return {
      groupedQuizz: [],
      coursTitre: '',
      loading: true,
      idCours: Number(this.$route.params.idCours),
    }
  },
  methods: {
    async fetchQuizz() {
      try {
        const [quizzResponse, coursResponse] = await Promise.all([
          axios.get(`http://localhost:8000/api/quizz`),
          axios.get(`http://localhost:8000/api/cours/${this.idCours}`)
        ])

        this.coursTitre = coursResponse.data.titre

        const filtered = quizzResponse.data.filter(q => q.idCours === this.idCours)
        
        this.groupedQuizz = Object.values(
          filtered.reduce((acc, quiz) => {
            const key = quiz.titre
            if (!acc[key]) {
              acc[key] = {
                titre: quiz.titre,
                idCours: quiz.idCours,
                questionCount: 0,
                dureeEstimee: '0s'
              }
            }
            acc[key].questionCount++
            acc[key].dureeEstimee = this.calculateDuration(acc[key].questionCount)
            return acc
          }, {})
        )

      } catch (error) {
        toast.error("Erreur de chargement des données")
        console.error(error)
      } finally {
        this.loading = false
      }
    },

    calculateDuration(questionCount) {
      const totalSeconds = questionCount * 15
      const minutes = Math.floor(totalSeconds / 60)
      const seconds = totalSeconds % 60
      
      let duration = []
      if (minutes > 0) duration.push(`${minutes}min`)
      if (seconds > 0) duration.push(`${seconds}s`)
      
      return duration.join(' ') || '0s'
    }
  },
  mounted() {
    this.fetchQuizz()
  }
}
</script>

<style scoped>
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.sidebar-enter-active,
.sidebar-leave-active {
  transition: transform 0.3s ease;
}

.sidebar-enter-from,
.sidebar-leave-to {
  transform: translateX(-100%);
}
</style>
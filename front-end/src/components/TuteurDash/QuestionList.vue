<template>
    <div class="min-h-screen bg-gray-50">
      <NavbarTuteur />
      <SidebarTuteur />
  
      <main class="ml-64 p-8 pt-20">
        <div class="max-w-6xl mx-auto">
          <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Gestion des Questions</h1>
            <router-link 
              to="/QuizzAdd" 
              class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors"
            >
              + Nouvelle Question
            </router-link>
          </div>
  
          <!-- Tableau des questions -->
          <div class="bg-white rounded-lg shadow overflow-hidden">
            <div v-if="loading" class="p-8 text-center">
              <div class="animate-spin inline-block w-8 h-8 border-4 border-indigo-500 rounded-full border-t-transparent"></div>
              <p class="text-gray-500 mt-4">Chargement des questions...</p>
            </div>
  
            <div v-else>
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Question</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Réponses Correctes</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Réponses Fausses</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr 
                    v-for="question in quizzListe" 
                    :key="question.id"
                    class="hover:bg-gray-50 transition-colors"
                  >
                    <td class="px-6 py-4 whitespace-normal">
                      <div class="text-sm font-medium text-gray-900">{{ question.question }}</div>
                      <div class="text-sm text-gray-500 mt-1">{{ question.score }} points</div>
                    </td>
  
                    <td class="px-6 py-4 whitespace-normal">
                      <div v-for="(reponse, index) in question.reponseCorrecte" :key="index" class="flex items-center mb-2 last:mb-0">
                        <CheckCircleIcon class="w-4 h-4 text-green-500 mr-2" />
                        <span class="text-sm text-green-700 bg-green-100 px-2 py-1 rounded">{{ reponse }}</span>
                      </div>
                    </td>
  
                    <td class="px-6 py-4 whitespace-normal">
                      <div v-for="(reponse, index) in question.reponsesFausses" :key="index" class="flex items-center mb-2 last:mb-0">
                        <XCircleIcon class="w-4 h-4 text-red-500 mr-2" />
                        <span class="text-sm text-red-700 bg-red-100 px-2 py-1 rounded">{{ reponse }}</span>
                      </div>
                    </td>
  
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center space-x-4">
                        <router-link 
                          :to="`/QuizzEdit/${question.id}`"
                          class="text-indigo-600 hover:text-indigo-900 transition-colors"
                          title="Modifier"
                        >
                          <PencilIcon class="w-5 h-5" />
                        </router-link>
                        <button 
                          @click="deleteQuizz(question.id)"
                          class="text-red-600 hover:text-red-900 transition-colors"
                          title="Supprimer"
                        >
                          <TrashIcon class="w-5 h-5" />
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
  
              <div v-if="!quizzListe.length" class="p-8 text-center text-gray-500">
                Aucune question disponible
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  </template>
  
  <script>
  import NavbarTuteur from "./NavbarTut.vue";
  import SidebarTuteur from "./SidebarTut.vue";
  import { CheckCircleIcon, XCircleIcon, PencilIcon, TrashIcon } from '@heroicons/vue/24/outline';
  import axios from "axios";
  import { toast } from "vue3-toastify";
  import "vue3-toastify/dist/index.css";
  
  export default {
    components: {
      NavbarTuteur,
      SidebarTuteur,
      CheckCircleIcon,
      XCircleIcon,
      PencilIcon,
      TrashIcon
    },
    data() {
      return {
        quizzListe: [],
        loading: true,
      };
    },
    methods: {
      async fetchQuizz() {
        try {
          this.loading = true;
          const tuteurData = JSON.parse(localStorage.getItem("TuteurAccountInfo"));
          const response = await axios.get("http://localhost:8000/api/quizz-by-tuteur", {
            params: { tuteurId: tuteurData?.id }
          });
  
          this.quizzListe = response.data.map(q => ({
            ...q,
            reponseCorrecte: Array.isArray(q.reponseCorrecte) ? q.reponseCorrecte : JSON.parse(q.reponseCorrecte),
            reponsesFausses: Array.isArray(q.reponsesFausses) ? q.reponsesFausses : JSON.parse(q.reponsesFausses)
          }));
  
        } catch (error) {
          toast.error(error.response?.data?.message || "Erreur de chargement");
        } finally {
          this.loading = false;
        }
      },
      async deleteQuizz(id) {
        if (!confirm("Supprimer cette question définitivement ?")) return;
        try {
          await axios.delete(`http://localhost:8000/api/quizz/${id}`);
          this.quizzListe = this.quizzListe.filter(q => q.id !== id);
          toast.success("Question supprimée avec succès");
        } catch (error) {
          toast.error("Échec de la suppression");
        }
      }
    },
    mounted() {
      this.fetchQuizz();
    }
  };
  </script>
  
  <style scoped>
  table {
    @apply min-w-full divide-y divide-gray-200;
  }
  
  th {
    @apply px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider;
  }
  
  td {
    @apply px-6 py-4 whitespace-normal text-sm text-gray-900;
  }
  
  .animate-spin {
    animation: spin 1s linear infinite;
  }
  
  @keyframes spin {
    to {
      transform: rotate(360deg);
    }
  }
  
  .hover\:bg-gray-50:hover {
    background-color: #f9fafb;
  }
  </style>
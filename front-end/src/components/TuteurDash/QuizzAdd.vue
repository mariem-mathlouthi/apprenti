<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navbar fixe -->
    <NavbarTuteur class="fixed top-0 w-full z-50 bg-white shadow-md" />

    <div class="flex pt-16 h-screen">
      <!-- Sidebar fixe -->
      <SidebarTuteur class="fixed left-0 h-full w-64 z-40 border-r bg-white" />

      <!-- Contenu principal scrollable -->
      <main class="flex-1 ml-64 p-8 overflow-y-auto">
        <div class="max-w-4xl mx-auto">
          <h1 class="text-3xl font-bold text-indigo-700 text-center mb-8">
            Créer un Nouveau Quizz
          </h1>

          <div class="bg-white p-8 rounded-xl shadow-lg">
            <form @submit.prevent="submitQuizz" class="space-y-8">
              <!-- Sélection du cours -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Cours associé</label>
                <select 
                  v-model="idCours" 
                  required 
                  class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                >
                  <option disabled value="">Sélectionnez un cours</option>
                  <option 
                    v-for="cours in coursListe" 
                    :key="cours.id" 
                    :value="cours.id"
                    class="p-2"
                  >
                    {{ cours.titre }}
                  </option>
                </select>
              </div>

              <!-- Titre du quizz -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Titre du quizz</label>
                <input 
                  type="text" 
                  v-model="titre" 
                  required 
                  placeholder="Titre global du quizz"
                  class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                />
              </div>

              <!-- Liste des questions -->
              <div 
                v-for="(question, qIndex) in questions" 
                :key="qIndex" 
                class="bg-gray-50 p-6 rounded-xl border border-gray-200"
              >
                <div class="border-l-4 border-indigo-500 pl-4">
                  <div class="flex justify-between items-center mb-6">
                    <h3 class="font-semibold text-lg text-gray-800">Question {{ qIndex + 1 }}</h3>
                    <button 
                      v-if="questions.length > 1" 
                      @click="removeQuestion(qIndex)" 
                      type="button" 
                      class="text-red-500 hover:text-red-700 transition-colors"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                  </div>

                  <!-- Question -->
                  <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Question</label>
                    <input 
                      v-model="question.questionText" 
                      required 
                      placeholder="Énoncé de la question"
                      class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                    />
                  </div>

                  <!-- Réponse correcte -->
                  <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Réponse correcte</label>
                    <input 
                      v-model="question.correctAnswer" 
                      required 
                      placeholder="Réponse attendue"
                      class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                    />
                  </div>

                  <!-- Score -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Score</label>
                    <input 
                      type="number" 
                      v-model.number="question.score" 
                      min="1" 
                      required 
                      class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                    />
                  </div>
                </div>
              </div>

              <!-- Boutons d'action -->
              <div class="flex flex-col gap-4">
                <button 
                  @click="addQuestion" 
                  type="button" 
                  class="w-full py-3 px-6 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition-colors font-medium"
                >
                  + Ajouter une question
                </button>
                <button 
                  type="submit" 
                  class="w-full py-3 px-6 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium"
                >
                  Publier le quizz
                </button>
              </div>
            </form>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script>
import NavbarTuteur from "./NavbarTut.vue";
import SidebarTuteur from "./SidebarTut.vue";
import axios from "axios";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

export default {
  name: "AddQuizz",
  components: {
    NavbarTuteur,
    SidebarTuteur,
  },
  data() {
    return {
      idCours: "",
      titre: "",
      questions: [
        {
          questionText: "",
          correctAnswer: "",
          score: 1,
        },
      ],
      coursListe: [],
      tuteurId: null,
    };
  },
  methods: {
    async fetchCoursTuteur() {
      try {
        const response = await axios.get(
          `http://localhost:8000/api/cours-by-tuteur?tuteurId=${this.tuteurId}`
        );
        this.coursListe = response.data.cours || [];
      } catch (error) {
        toast.error("Erreur lors du chargement des cours");
      }
    },

    addQuestion() {
      this.questions.push({
        questionText: "",
        correctAnswer: "",
        score: 1,
      });
    },

    removeQuestion(index) {
      if (this.questions.length > 1) {
        this.questions.splice(index, 1);
      }
    },

    async submitQuizz() {
      try {
        const requests = this.questions.map(question => {
          const payload = {
            idCours: this.idCours,
            idTuteur: this.tuteurId,
            titre: this.titre,
            question: question.questionText.trim(),
            reponseCorrecte: question.correctAnswer.trim(),
            reponsesFausses: [],
            score: question.score,
          };

          return axios.post("http://localhost:8000/api/quizz", payload);
        });

        await Promise.all(requests);
        toast.success("Quizz créé avec succès !");
        this.$router.push("/QuizzList");
      } catch (error) {
        console.error("Erreur :", error.response?.data || error.message);
        toast.error(`Échec : ${error.response?.data?.message || "Erreur serveur"}`);
      }
    },

    loadTuteurData() {
      const tuteurData = JSON.parse(localStorage.getItem("TuteurAccountInfo"));
      if (tuteurData?.id) {
        this.tuteurId = tuteurData.id;
      } else {
        toast.error("Session expirée, veuillez vous reconnecter");
        this.$router.push("/QuizzList");
      }
    },
  },
  mounted() {
    this.loadTuteurData();
    this.fetchCoursTuteur();
  },
};
</script>

<style scoped>
/* Animation personnalisée pour le scroll */
.overflow-y-auto {
  scroll-behavior: smooth;
}

/* Style personnalisé pour la scrollbar */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb {
  background: #c7d2fe;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #a5b4fc;
}
</style>
<template>
  <div id="app" class="flex flex-col h-screen">
    <NavbarTuteur />
    <SidebarTuteur />

    <section id="content" class="flex-1 flex justify-center items-center py-6">
      <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-gray-800 text-center mb-6">
          Liste des Quizz
        </h1>

        <div class="text-right mb-6">
          <router-link to="/QuizzAdd" class="btn-submit">
            Ajouter un Quizz
          </router-link>
        </div>

        <!-- État de chargement -->
        <div v-if="loading" class="text-center py-8">
          <div class="animate-spin inline-block text-4xl mb-4">🔄</div>
          <p class="text-gray-500">Chargement des quizz en cours...</p>
        </div>

        <!-- Liste des quizz -->
        <div v-else>
          <div v-if="quizzListe.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
              v-for="quizz in quizzListe"
              :key="quizz.id"
              class="card"
            >
              <div class="text">
                <h2 class="title">{{ quizz.titre }}</h2>
                <p class="subtitle">{{ quizz.question }}</p>
              </div>

              <div class="icons">
                <router-link
                  :to="`/QuizzEdit/${quizz.id}`"
                  class="btn"
                >
                  <svg class="svg-icon" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                  </svg>
                </router-link>

                <button
                  @click="deleteQuizz(quizz.id)"
                  class="btn"
                >
                  <svg class="svg-icon" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Aucun résultat -->
          <div v-else class="text-center py-8 bg-gray-50 rounded-lg">
            <p class="text-gray-500 mb-4">Aucun quizz trouvé</p>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import NavbarTuteur from "./NavbarTut.vue";
import SidebarTuteur from "./SidebarTut.vue";
import axios from "axios";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

export default {
  name: "QuizzList",
  components: {
    NavbarTuteur,
    SidebarTuteur,
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
        
        const tuteurData = localStorage.getItem("TuteurAccountInfo");
        if (!tuteurData) throw new Error("Session expirée");

        const tuteurId = JSON.parse(tuteurData).id;
        
        const response = await axios.get(
          "http://localhost:8000/api/quizz-by-tuteur",
          { params: { tuteurId } }
        );

        this.quizzListe = response.data;

      } catch (error) {
        console.error("Erreur :", error);
        toast.error(error.response?.data?.message || error.message);
        if (error.message.includes("Session")) this.$router.push("/login-tuteur");
      } finally {
        this.loading = false;
      }
    },

    async deleteQuizz(id) {
      if (!confirm("Supprimer définitivement ce quizz ?")) return;

      try {
        await axios.delete(`http://localhost:8000/api/quizz/${id}`);
        this.quizzListe = this.quizzListe.filter(q => q.id !== id);
        toast.success("Quizz supprimé avec succès !");
      } catch (error) {
        toast.error("Impossible de supprimer le quizz");
      }
    },
  },
  mounted() {
    this.fetchQuizz();
  },
};
</script>

<style scoped>
.card {
  width: 250px;
  height: 200px; /* Hauteur réduite */
  border-radius: 15px;
  background: rgba(105, 13, 197, 0.103);
  display: flex;
  flex-direction: column;
  justify-content: space-between; /* Meilleure répartition de l'espace */
  padding: 20px;
  transition: transform 0.3s ease;
}

.title {
  font-size: 1.2rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.subtitle {
  color: #4b5563;
  font-size: 0.9rem;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 3; /* Limite à 3 lignes */
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.icons {
  display: flex;
  justify-content: flex-end;
  gap: 0.5rem;
  padding-top: 1rem;
}

.btn-submit {
  background-color: #4f46e5;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  transition: background-color 0.2s;
}

.btn-submit:hover {
  background-color: #4338ca;
}

.svg-icon {
  width: 1.25rem;
  height: 1.25rem;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>
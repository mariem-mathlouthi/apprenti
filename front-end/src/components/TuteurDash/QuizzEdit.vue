<template>
    <div id="app" class="flex flex-col h-screen">
      <NavbarTuteur />
      <SidebarTuteur />
  
      <section id="content" class="flex-1 flex justify-center items-center py-6">
        <div class="container mx-auto p-6">
          <!-- Ajout d'un état de chargement -->
          <div v-if="loading" class="text-center py-8">
  <div class="animate-spin inline-block w-12 h-12 border-4 border-indigo-500 rounded-full border-t-transparent"></div>
  <p class="text-gray-500 mt-4">Chargement en cours...</p>
</div>
  
          <template v-else>
            <h1 class="text-3xl font-bold text-indigo-700 text-center mb-6">
              Modifier le Quizz
            </h1>
  
            <div class="bg-white p-8 rounded-lg shadow-lg w-full sm:w-96 max-w-4xl mx-auto">
              <form @submit.prevent="submitUpdate" class="space-y-6">
                <!-- Sélection du cours -->
                <div>
                  <label class="label">Cours associé</label>
                  <select v-model="formData.idCours" required class="input">
                    <option disabled value="">Sélectionnez un cours</option>
                    <option v-for="cours in coursListe" :key="cours.id" :value="cours.id">
                      {{ cours.titre }}
                    </option>
                  </select>
                </div>
  
                <!-- Titre du quizz -->
                <div>
                  <label class="label">Titre du quizz</label>
                  <input 
                    type="text" 
                    v-model="formData.titre" 
                    required 
                    class="input" 
                    placeholder="Titre global du quizz"
                  />
                </div>
  
                <!-- Question -->
                <div>
                  <label class="label">Question</label>
                  <input 
                    v-model="formData.question" 
                    required 
                    class="input" 
                    placeholder="Énoncé de la question"
                  />
                </div>
  
                <!-- Réponse correcte -->
                <div>
                  <label class="label">Réponse correcte</label>
                  <input 
                    v-model="formData.reponseCorrecte" 
                    required 
                    class="input"
                    placeholder="Réponse attendue"
                  />
                </div>
  
                <!-- Réponses fausses -->
                <div>
                  
                  <div 
                    v-for="(reponse, index) in formData.reponsesFausses" 
                    :key="index" 
                    class="flex gap-2 mb-2"
                  >
                    <input 
                      v-model="formData.reponsesFausses[index]" 
                      class="input flex-1" 
                      placeholder="Réponse incorrecte"
                    />
                    <button 
                      type="button" 
                      @click="removeFalseAnswer(index)" 
                      class="text-red-500 px-2"
                    >
                      ×
                    </button>
                  </div>
                </div>
  
                <!-- Score -->
                <div>
                  <label class="label">Score</label>
                  <input 
                    type="number" 
                    v-model.number="formData.score" 
                    min="1" 
                    required 
                    class="input" 
                  />
                </div>
  
                <!-- Bouton de soumission -->
                <button type="submit" class="btn-submit w-full">
                  Enregistrer les modifications
                </button>
              </form>
            </div>
          </template>
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
    name: "EditQuizz",
    components: {
      NavbarTuteur,
      SidebarTuteur,
    },
    data() {
      return {
        formData: {
          idCours: "",
          titre: "",
          question: "",
          reponseCorrecte: "",
          reponsesFausses: [],
          score: 1,
        },
        coursListe: [],
        tuteurId: null,
        loading: true,
        error: false
      };
    },
    methods: {
        async fetchQuizz() {
  try {
    this.loading = true;
    const quizzId = this.$route.params.id;
    
    // Ajout du header Authorization si nécessaire
    const response = await axios.get(
      `http://localhost:8000/api/quizz/${quizzId}`,
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`
        }
      }
    );

    if (!response.data.success) {
      throw new Error(response.data.message);
    }

    const data = response.data.data;
    
    this.formData = {
      ...data,
      reponsesFausses: data.reponsesFausses ? 
        JSON.parse(data.reponsesFausses) : 
        []
    };

  } catch (error) {
    console.error("Erreur fetchQuizz:", error);
    toast.error(error.message);
    this.$router.push("/QuizzList");
  } finally {
    this.loading = false;
  }
},
  
      async fetchCoursTuteur() {
        try {
          const response = await axios.get(
            `http://localhost:8000/api/cours-by-tuteur?tuteurId=${this.tuteurId}`
          );
          this.coursListe = response.data.cours || [];
        } catch (error) {
          console.error("Erreur fetchCoursTuteur:", error);
          toast.error("Erreur lors du chargement des cours");
        }
      },
  
      addFalseAnswer() {
        this.formData.reponsesFausses.push("");
      },
  
      removeFalseAnswer(index) {
        this.formData.reponsesFausses.splice(index, 1);
      },
  
      async submitUpdate() {
  try {
    const payload = {
      idCours: this.formData.idCours,
      titre: this.formData.titre,
      question: this.formData.question,
      reponseCorrecte: this.formData.reponseCorrecte,
      score: this.formData.score,
      // Toujours envoyer le tableau même s'il est vide
      reponsesFausses: this.formData.reponsesFausses
        .filter(r => r.trim() !== "")
        .map(r => r.trim())
    };

    await axios.put(
      `http://localhost:8000/api/quizz/${this.$route.params.id}`,
      payload
    );
    
    toast.success("Quizz mis à jour avec succès !");
    this.$router.push("/QuizzList");
  } catch (error) {
    console.error("Erreur:", error.response?.data || error.message);
    toast.error(error.response?.data?.message || "Erreur lors de la mise à jour");
  }
},
  
      loadTuteurData() {
        const tuteurData = JSON.parse(localStorage.getItem("TuteurAccountInfo"));
        if (tuteurData?.id) {
          this.tuteurId = tuteurData.id;
        } else {
          this.error = true;
          toast.error("Session expirée, veuillez vous reconnecter");
          this.$router.push("/login-tuteur");
        }
      },
    },
    async mounted() {
      try {
        this.loadTuteurData();
        if (!this.error) {
          await Promise.all([
            this.fetchCoursTuteur(),
            this.fetchQuizz()
          ]);
        }
      } catch (error) {
        console.error("Erreur mounted:", error);
        this.$router.push("/QuizzList");
      }
    },
  };
  </script>
  
  <style scoped>
  .label {
    @apply block text-sm font-medium text-gray-700 mb-2;
  }
  
  .input {
    @apply w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500;
  }
  
  .btn-submit {
    @apply bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 transition-colors;
  }
  
  .animate-spin {
    animation: spin 1s linear infinite;
  }
  
  @keyframes spin {
    to {
      transform: rotate(360deg);
    }
  }
  </style>
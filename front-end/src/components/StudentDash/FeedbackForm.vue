<template>
  <div class="flex flex-col h-screen bg-gray-100">
    <!-- Navbar -->
    <NavBarStd />

    <!-- Sidebar -->
    <Sidebar class="w-64" />

    <!-- Main Content -->
    <section class="flex-1 p-8 overflow-auto mt-16 ml-64">
      <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Donner votre avis</h1>
        
        <div class="bg-white rounded-lg shadow-md p-6">
          <form @submit.prevent="submitFeedback">
            <!-- Notation -->
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">Note</label>
              <div class="flex space-x-2">
                <button
                  v-for="i in 5"
                  :key="i"
                  type="button"
                  @click="form.note = i"
                  class="text-3xl focus:outline-none transition-transform duration-100"
                  :class="{
                    'text-yellow-400': i <= form.note,
                    'text-gray-300': i > form.note,
                    'hover:scale-110': true
                  }"
                >
                  ★
                </button>
              </div>
              <p class="text-sm text-gray-500 mt-1">
                {{ ratingDescription }}
              </p>
            </div>

            <!-- Commentaire -->
            <div class="mb-6">
              <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Votre avis</label>
              <textarea
                id="comment"
                v-model="form.commentaire"
                rows="5"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                required
                placeholder="Décrivez votre expérience avec ce cours..."
              ></textarea>
              <div class="text-xs text-gray-500 text-right mt-1">
                {{ form.commentaire.length }}/500 caractères
              </div>
            </div>

            <!-- Boutons -->
            <div class="flex justify-end space-x-4">
              <router-link
                :to="{ name: 'ConsultRessource', params: { id: idCours } }"
                class="px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200"
              >
                Annuler
              </router-link>
              <button
                type="submit"
                :disabled="isSubmitting || form.note === 0"
                class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 transition-all duration-200 disabled:opacity-50"
              >
                <span v-if="isSubmitting">
                  <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Envoi...
                </span>
                <span v-else>Envoyer</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import axios from "axios";
import NavBarStd from "./NavBarStd.vue";
import Sidebar from "./Sidebar.vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

export default {
  name: "FeedbackForm",
  components: { NavBarStd, Sidebar },
  props: {
    idCours: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      form: {
        note: 0,
        commentaire: ""
      },
      isSubmitting: false
    };
  },
  computed: {
    ratingDescription() {
      const descriptions = {
        1: 'Mauvais - Pas satisfait',
        2: 'Médiocre - Peu satisfait', 
        3: 'Moyen - Correct',
        4: 'Bon - Satisfait',
        5: 'Excellent - Très satisfait'
      };
      return descriptions[this.form.note] || 'Sélectionnez une note';
    }
  },
  methods: {
    async submitFeedback() {
  // ... vos validations existantes ...

  this.isSubmitting = true;

  try {
    // Récupération du token
    const token = localStorage.getItem('token');
    if (!token) {
      toast.error("Veuillez vous connecter");
      this.$router.push('/login'); // Redirection vers la page de connexion
      return;
    }

    // Configuration des headers
    const config = {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      }
    };

    // Données à envoyer
    const data = {
      note: this.form.note,
      commentaire: this.form.commentaire
    };

    // Envoi de la requête
    const response = await axios.post(
      `http://localhost:8000/api/cours/${this.idCours}/feedbacks`,
      data,
      config
    );

    // Gestion de la réponse
    if (response.data.success) {
      toast.success("Feedback envoyé avec succès !");
      this.$router.push({
        name: 'ConsultRessource',
        params: { id: this.idCours }
      });
    }
  } catch (error) {
    console.error("Erreur:", error.response);
    let errorMessage = "Erreur lors de l'envoi du feedback";
    
    if (error.response) {
      if (error.response.status === 401) {
        errorMessage = "Session expirée, veuillez vous reconnecter";
        localStorage.removeItem('token');
        this.$router.push('/login');
      } else if (error.response.data?.message) {
        errorMessage = error.response.data.message;
      }
    }
    
    toast.error(errorMessage);
  } finally {
    this.isSubmitting = false;
  }
}
  }
};
</script>

<style scoped>
/* Animation pour les étoiles */
.star-enter-active, .star-leave-active {
  transition: all 0.3s ease;
}
.star-enter-from, .star-leave-to {
  transform: scale(0.8);
  opacity: 0;
}

/* Style pour le bouton de chargement */
.animate-spin {
  animation: spin 1s linear infinite;
}
@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
</style>
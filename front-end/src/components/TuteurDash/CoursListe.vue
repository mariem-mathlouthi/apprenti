<template>
  <div id="app" class="flex flex-col h-screen">
    <!-- Navbar & Sidebar -->
    <NavbarTuteur />
    <SidebarTuteur />

    <!-- Contenu principal -->
    <section id="content" class="flex-1 flex justify-center items-center py-6">
      <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-gray-800 text-center mb-6">
          Liste des Cours
        </h1>

        <!-- Bouton Ajouter Cours -->
        <div class="text-right mb-6">
          <router-link to="/ajouter-cours" class="btn-submit">
            Ajouter un Cours
          </router-link>
        </div>

        <!-- Liste des Cours sous forme de cartes -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          <!-- Carte pour chaque cours -->
          <div v-for="cours in coursListe" :key="cours.id" class="bg-white rounded-lg shadow-md overflow-hidden relative transform transition-transform hover:scale-102">
            <!-- Bouton Supprimer (icône "x") -->
            <button
              @click="deleteCours(cours.id)"
              class="absolute top-2 right-2 bg-red-100 text-red-600 rounded-full p-1 hover:bg-red-200 transition-colors z-20"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </button>

            <!-- Image du cours -->
            <div class="relative h-32 overflow-hidden">
              <img
                :src="getImageUrl(cours.file)"
                alt="Image du cours"
                class="w-full h-full object-cover"
                @error="handleImageError"
              />
            </div>

            <!-- Titre du cours et boutons directement sous l'image -->
            <div class="p-2">
              <h2 class="text-lg font-semibold text-gray-800 mb-2">{{ cours.titre }}</h2>
              <div class="flex justify-between">
                <!-- Bouton Détails -->
                <router-link
                  :to="`/cours/${cours.id}`"
                  class="bg-blue-100 text-blue-600 py-1 px-3 rounded-md text-sm hover:bg-blue-200 transition-colors"
                >
                  Détails
                </router-link>

                <!-- Bouton Modifier -->
                <router-link
                  :to="`/modifier-cours/${cours.id}`"
                  class="bg-green-100 text-green-600 py-1 px-3 rounded-md text-sm hover:bg-green-200 transition-colors"
                >
                  Modifier
                </router-link>
              </div>
            </div>
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
  name: "CoursListe",
  components: {
    NavbarTuteur,
    SidebarTuteur,
  },
  data() {
    return {
      coursListe: [], // Liste des cours
      defaultImage: "/placeholder-course.jpg", // Image par défaut
    };
  },
  methods: {
    // Récupérer la liste des cours du tuteur connecté
    async fetchCours() {
      try {
        // Récupérer tous les cours
        const response = await axios.get('http://localhost:8000/api/cours');

        // Récupérer l'ID du tuteur connecté
        const tuteurId = JSON.parse(localStorage.getItem("TuteurAccountInfo")).id;

        // Filtrer les cours pour ne garder que ceux du tuteur connecté
        this.coursListe = response.data.cours
          .filter(cours => cours.idTuteur === tuteurId) // Filtrer par idTuteur
          .map(cours => ({
            ...cours,
            file: cours.file || this.defaultImage, // Utiliser l'image par défaut si le fichier est manquant
          }));
      } catch (error) {
        console.error("Erreur lors de la récupération des cours :", error);
        toast.error("Impossible de récupérer les cours.");
      }
    },

    // Supprimer un cours
    async deleteCours(id) {
      try {
        await axios.delete(`http://localhost:8000/api/cours/${id}`);
        toast.success("Cours supprimé avec succès !");
        this.fetchCours(); // Rafraîchir la liste
      } catch (error) {
        console.error("Erreur lors de la suppression du cours :", error);
        toast.error("Erreur lors de la suppression du cours.");
      }
    },

    // Gérer les erreurs d'image
    handleImageError(event) {
      event.target.src = this.defaultImage; // Remplacer par l'image par défaut
    },

    // Construire l'URL de l'image
    getImageUrl(filePath) {
      if (!filePath || filePath === this.defaultImage) {
        return this.defaultImage; // Retourner l'image par défaut si le chemin est vide ou déjà l'image par défaut
      }
      return `http://localhost:8000${filePath}`; // Retourner l'URL complète de l'image
    },
  },
  mounted() {
    this.fetchCours(); // Charger les cours au montage du composant
  },
};
</script>

<style scoped>
.btn-submit {
  background-color: #4f46e5;
  color: white;
  padding: 8px 12px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: bold;
  font-size: 14px;
}
.btn-submit:hover {
  background-color: #4338ca;
}

/* Effet de survol sur les cartes */
.transform:hover {
  transform: scale(1.02);
  transition: transform 0.3s ease;
}

/* Taille de l'image */
.h-32 {
  height: 8rem; /* Ajustez cette valeur pour une carte plus petite */
}
</style>
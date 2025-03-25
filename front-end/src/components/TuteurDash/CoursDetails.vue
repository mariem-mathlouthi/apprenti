<template>
  <div id="app" class="flex flex-col h-screen bg-gray-50">
    <!-- Navbar & Sidebar -->
    <NavbarTuteur />
    <SidebarTuteur />

    <!-- Contenu principal -->
    <section id="content" class="flex-1 flex justify-center items-center py-4 overflow-y-auto">
      <div class="container mx-auto p-4 max-w-2xl">
        <h1 class="text-2xl font-bold text-gray-800 text-center mb-4 transform hover:scale-105 transition-transform">
          Détails du Cours
        </h1>

        <!-- Détails du cours -->
        <div class="bg-white rounded-lg shadow-md p-4 hover:shadow-lg transition-shadow">
          <div class="flex flex-col space-y-3">
            <!-- Titre -->
            <div class="flex items-center">
              <span class="font-medium text-gray-700">Titre :</span>
              <span class="ml-2 text-gray-900">{{ cours.titre }}</span>
            </div>

            <!-- Catégorie -->
            <div class="flex items-center">
              <span class="font-medium text-gray-700">Catégorie :</span>
              <span class="ml-2 text-gray-900">{{ cours.category ? cours.category.description : "Non spécifiée" }}</span>
            </div>

            <!-- Prix -->
            <div class="flex items-center">
              <span class="font-medium text-gray-700">Prix :</span>
              <span class="ml-2 text-gray-900">{{ cours.prix }} €</span>
            </div>

            <!-- Durée -->
            <div class="flex items-center">
              <span class="font-medium text-gray-700">Durée :</span>
              <span class="ml-2 text-gray-900">{{ cours.duration }} heures</span>
            </div>

            <!-- Description -->
            <div class="flex items-center">
              <span class="font-medium text-gray-700">Description :</span>
              <span class="ml-2 text-gray-900">{{ cours.description }}</span>
            </div>
          </div>

          <!-- Bouton Ajouter Ressource -->
          <div class="mt-4 text-center">
            <router-link
              :to="`/ajouter-ressource/${cours.id}`"
              class="btn-submit"
            >
              Ajouter une Ressource
            </router-link>
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
  name: "CoursDetails",
  components: {
    NavbarTuteur,
    SidebarTuteur,
  },
  data() {
    return {
      cours: {}, // Détails du cours
    };
  },
  methods: {
    // Récupérer les détails du cours
    async fetchCoursDetails() {
      try {
        const coursId = this.$route.params.id;
        const response = await axios.get(`http://localhost:8000/api/cours/${coursId}`);
        this.cours = response.data.cours;
      } catch (error) {
        console.error("Erreur lors de la récupération des détails du cours :", error);
        toast.error("Impossible de récupérer les détails du cours.");
      }
    },
  },
  mounted() {
    this.fetchCoursDetails(); // Charger les détails du cours au montage du composant
  },
};
</script>

<style scoped>
.btn-submit {
  background-color: #4f46e5;
  color: white;
  padding: 8px 16px;
  border-radius: 8px;
  cursor: pointer;
  font-weight: bold;
  font-size: 14px;
  transition: background-color 0.3s ease, transform 0.3s ease;
  display: inline-block;
  text-decoration: none;
}

.btn-submit:hover {
  background-color: #4338ca;
  transform: translateY(-1px);
}

/* Animation for the main heading */
h1 {
  animation: fadeInDown 0.8s ease-out;
}

/* Animation for the card */
.bg-white {
  animation: fadeInUp 0.8s ease-out;
}

/* Keyframes for animations */
@keyframes fadeInDown {
  0% {
    opacity: 0;
    transform: translateY(-10px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeInUp {
  0% {
    opacity: 0;
    transform: translateY(10px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
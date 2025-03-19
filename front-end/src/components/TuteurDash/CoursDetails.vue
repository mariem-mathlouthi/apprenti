<template>
    <div id="app" class="flex flex-col h-screen">
      <!-- Navbar & Sidebar -->
      <NavbarTuteur />
      <SidebarTuteur />
  
      <!-- Contenu principal -->
      <section id="content" class="flex-1 flex justify-center items-center py-6">
        <div class="container mx-auto p-6">
          <h1 class="text-3xl font-bold text-gray-800 text-center mb-6">
            Détails du Cours
          </h1>
  
          <!-- Détails du cours -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex flex-col space-y-4">
              <div class="flex items-center">
                <span class="font-medium">Titre :</span>
                <span class="ml-2">{{ cours.titre }}</span>
              </div>
              <div class="flex items-center">
                <span class="font-medium">Catégorie :</span>
                <span class="ml-2">{{ cours.category ? cours.category.description : "Non spécifiée" }}</span>
              </div>
              <div class="flex items-center">
                <span class="font-medium">Prix :</span>
                <span class="ml-2">{{ cours.prix }} €</span>
              </div>
              <div class="flex items-center">
                <span class="font-medium">Durée :</span>
                <span class="ml-2">{{ cours.duration }} heures</span>
              </div>
              <div class="flex items-center">
                <span class="font-medium">Description :</span>
                <span class="ml-2">{{ cours.description }}</span>
              </div>
            </div>
  
            <!-- Bouton Ajouter Ressource -->
            <div class="mt-6">
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
    padding: 8px 12px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    font-size: 14px;
  }
  .btn-submit:hover {
    background-color: #4338ca;
  }
  </style>
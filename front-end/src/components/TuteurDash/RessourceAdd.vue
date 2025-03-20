<template>
  <div id="app" class="flex flex-col h-screen bg-gray-50 overflow-hidden">
    <!-- Navbar Fixe -->
    <NavbarTuteur class="fixed top-0 w-full z-50 shadow-lg" />

    <!-- Contenu Principal -->
    <main class="flex-1 overflow-y-auto pt-16">
      <!-- Section Centrée -->
      <section class="container mx-auto px-4 py-8 max-w-4xl">
        <!-- Bouton Retour -->
        <button
          @click="goBack"
          class="flex items-center text-indigo-600 hover:text-indigo-800 transition duration-300 mb-6"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6 mr-2"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M10 19l-7-7m0 0l7-7m-7 7h18"
            />
          </svg>
          Retour
        </button>

        <!-- Titre -->
        <h1 class="text-4xl font-bold text-gray-800 mb-8 animate-fade-in">
          Ajouter une Ressource
        </h1>

        <!-- Formulaire -->
        <form
          @submit.prevent="createRessource"
          class="bg-white p-8 rounded-xl shadow-2xl transform transition-all duration-500 hover:shadow-3xl"
        >
          <!-- Champ Titre -->
          <div class="mb-6">
            <label for="titre" class="block text-sm font-medium text-gray-700 mb-2">
              Titre
            </label>
            <input
              type="text"
              id="titre"
              v-model="form.titre"
              required
              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition duration-300"
              placeholder="Titre de la ressource"
            />
            <span v-if="errors.titre" class="text-red-500 text-sm mt-2 block">
              {{ errors.titre }}
            </span>
          </div>

          <!-- Champ Description -->
          <div class="mb-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
              Description
            </label>
            <textarea
              id="description"
              v-model="form.description"
              required
              class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition duration-300"
              placeholder="Description de la ressource"
              rows="4"
            ></textarea>
            <span v-if="errors.description" class="text-red-500 text-sm mt-2 block">
              {{ errors.description }}
            </span>
          </div>

          <!-- Champ Fichier -->
          <div class="mb-8">
            <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
              Fichier
            </label>
            <div
              class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-indigo-500 transition duration-300"
            >
              <input
                type="file"
                id="file"
                @change="handleFileUpload"
                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                required
              />
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-12 w-12 mx-auto text-gray-400 mb-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"
                />
              </svg>
              <p class="text-gray-600">
                Glissez-déposez un fichier ou
                <span class="text-indigo-600 font-semibold">cliquez pour téléverser</span>
              </p>
              <p v-if="fileToUpload" class="text-sm text-gray-500 mt-2">
                Fichier sélectionné : {{ fileToUpload.name }}
              </p>
            </div>
            <span v-if="errors.file" class="text-red-500 text-sm mt-2 block">
              {{ errors.file }}
            </span>
          </div>

          <!-- Bouton Soumettre -->
          <button
            type="submit"
            class="w-full bg-indigo-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-indigo-700 transition duration-300 transform hover:scale-105"
          >
            Ajouter la Ressource
          </button>
        </form>
      </section>
    </main>
  </div>
</template>

<script>
import SidebarTuteur from "./SidebarTut.vue";
import NavbarTuteur from "./NavbarTut.vue";
import axios from "axios";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

export default {
  name: "RessourceAdd",
  components: { SidebarTuteur, NavbarTuteur },
  data() {
    return {
      form: {
        titre: "",
        description: "",
        file: null,
        idCours: this.$route.params.id,
      },
      fileToUpload: null,
      errors: {
        titre: "",
        description: "",
        file: "",
      },
    };
  },
  methods: {
    handleFileUpload(event) {
      this.fileToUpload = event.target.files[0];
      this.errors.file = "";
    },
    validateForm() {
      this.errors = { titre: "", description: "", file: "" };
      let isValid = true;

      if (!this.form.titre) {
        this.errors.titre = "Le titre est obligatoire.";
        isValid = false;
      }

      if (!this.form.description) {
        this.errors.description = "La description est obligatoire.";
        isValid = false;
      }

      if (!this.fileToUpload) {
        this.errors.file = "Le fichier est obligatoire.";
        isValid = false;
      }

      return isValid;
    },
    async uploadFile() {
      const formData = new FormData();
      formData.append("file", this.fileToUpload);

      try {
        const response = await axios.post("http://localhost:8000/api/upload", formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        });
        return response.data.filePath;
      } catch (error) {
        console.error("Erreur lors de l'upload du fichier :", error);
        toast.error("Échec de l'upload du fichier.");
        return null;
      }
    },
    async createRessource() {
      if (!this.validateForm()) return;

      const filePath = await this.uploadFile();

      const requestData = {
        titre: this.form.titre,
        description: this.form.description,
        idCours: this.form.idCours,
        file: filePath,
      };

      try {
        const response = await axios.post("http://localhost:8000/api/ressources", requestData);
        toast.success("Ressource ajoutée avec succès !");
        this.$router.push({ name: "RessourceList", params: { id: this.form.idCours } });
      } catch (error) {
        console.error("Erreur lors de l'ajout de la ressource :", error);
        toast.error("Échec de l'ajout de la ressource.");
      }
    },
    goBack() {
      this.$router.push({ name: "RessourceList", params: { id: this.form.idCours } });
    },
  },
};
</script>

<style scoped>
/* Animation pour le titre */
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.5s ease-out;
}

/* Effet de survol pour le formulaire */
form:hover {
  transform: translateY(-5px);
}
</style>
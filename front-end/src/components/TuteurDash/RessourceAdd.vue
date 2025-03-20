<template>
    <div id="app" class="flex flex-col h-screen">
      <!-- Navbar & Sidebar -->
      <NavbarTuteur />
      <SidebarTuteur />
  
      <!-- Main Content -->
      <section id="content" class="flex-1 flex flex-col items-center py-6 relative">
        <div class="container mx-auto p-6 w-full max-w-6xl">
          <!-- Title -->
          <h1 class="text-3xl font-bold text-indigo-700 mb-6">Ajouter une Ressource</h1>
  
          <!-- Formulaire d'ajout de ressource -->
          <form @submit.prevent="createRessource" class="bg-white p-6 rounded-lg shadow-md space-y-4">
            <!-- Champ Titre -->
            <div>
              <label for="titre" class="block text-sm font-medium text-gray-700">Titre</label>
              <input
                type="text"
                id="titre"
                v-model="form.titre"
                required
                class="input"
                placeholder="Titre de la ressource"
              />
            </div>
  
            <!-- Champ Description -->
            <div>
              <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
              <textarea
                id="description"
                v-model="form.description"
                required
                class="input"
                placeholder="Description de la ressource"
              ></textarea>
            </div>
  
            <!-- Champ Fichier -->
            <div>
              <label for="file" class="block text-sm font-medium text-gray-700">Fichier (optionnel)</label>
              <input
                type="file"
                id="file"
                @change="handleFileUpload"
                class="input"
                accept=".pdf,.doc,.docx,.txt,.mp4,.mov,.avi"
              />
            </div>
  
            <!-- Bouton Soumettre -->
            <button type="submit" class="btn-submit">Ajouter la Ressource</button>
          </form>
        </div>
      </section>
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
          file: null, // Ce sera le chemin du fichier après l'upload
          idCours: this.$route.params.id, // ID du cours depuis l'URL
        },
        fileToUpload: null, // Fichier sélectionné par l'utilisateur
      };
    },
    methods: {
      // Gérer l'upload du fichier
      handleFileUpload(event) {
        this.fileToUpload = event.target.files[0]; // Stocker le fichier sélectionné
      },
  
      // Uploader le fichier et obtenir son chemin
      async uploadFile() {
        if (!this.fileToUpload) return null; // Si aucun fichier n'est sélectionné
  
        const formData = new FormData();
        formData.append("file", this.fileToUpload); // Ajouter le fichier à FormData
  
        try {
          const response = await axios.post("http://localhost:8000/api/upload", formData, {
            headers: {
              "Content-Type": "multipart/form-data", // Définir le bon type de contenu
            },
          });
          return response.data.filePath; // Supposons que l'API renvoie le chemin du fichier
        } catch (error) {
          console.error("Erreur lors de l'upload du fichier :", error);
          toast.error("Échec de l'upload du fichier.");
          return null;
        }
      },
  
      // Ajouter une ressource
      async createRessource() {
        // Uploader le fichier et obtenir son chemin
        const filePath = await this.uploadFile();
  
        // Préparer les données pour l'ajout de la ressource
        const requestData = {
          titre: this.form.titre,
          description: this.form.description,
          idCours: this.form.idCours,
          file: filePath, // Utiliser le chemin du fichier uploadé
        };
  
        try {
          const response = await axios.post("http://localhost:8000/api/ressources", requestData);
          toast.success("Ressource ajoutée avec succès !");
  
          // Rediriger vers la liste des ressources
          this.$router.push({ name: "RessourceList", params: { id: this.form.idCours } });
        } catch (error) {
          console.error("Erreur lors de l'ajout de la ressource :", error);
          if (error.response && error.response.data) {
            console.error("Détails de l'erreur :", error.response.data);
          }
          toast.error("Échec de l'ajout de la ressource.");
        }
      },
    },
  };
  </script>
  
  <style scoped>
  .input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
  }
  .btn-submit {
    background-color: #4f46e5;
    color: white;
    padding: 10px 15px;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
  }
  </style>
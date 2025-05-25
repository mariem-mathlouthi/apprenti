<template>
    <div id="app" class="flex flex-col h-screen">
      <!-- Navbar & Sidebar -->
      <NavbarTuteur />
      <SidebarTuteur />
  
      <!-- Main Content -->
      <section id="content" class="flex-1 flex flex-col items-center py-6 relative">
        <div class="container mx-auto p-6 w-full max-w-6xl">
          <!-- Title -->
          <h1 class="text-3xl font-bold text-indigo-700 mb-6">Modifier une Ressource</h1>
  
          <!-- Formulaire de modification de ressource -->
          <form @submit.prevent="updateRessource" class="bg-white p-6 rounded-lg shadow-md space-y-4">
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
            <button type="submit" class="btn-submit">Enregistrer les modifications</button>
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
    name: "RessourceEdit",
    components: { SidebarTuteur, NavbarTuteur },
    data() {
      return {
        form: {
          titre: "",
          description: "",
          file: null, // Ce sera le chemin du fichier après l'upload
          idCours: this.$route.params.idCours, // ID du cours depuis l'URL
        },
        fileToUpload: null, // Fichier sélectionné par l'utilisateur
      };
    },
    async mounted() {
      // Charger les détails de la ressource à modifier
      await this.fetchRessource();
    },
    methods: {
      // Charger les détails de la ressource
      async fetchRessource() {
        try {
          const response = await axios.get(`http://localhost:8000/api/ressources/${this.$route.params.id}`,
            {
              headers: {
                Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`,
              }
            }
          );
          this.form = response.data; // Remplir le formulaire avec les données existantes
        } catch (error) {
          console.error("Erreur lors du chargement de la ressource :", error);
          toast.error("Erreur lors du chargement de la ressource");
        }
      },
  
      // Gérer l'upload du fichier
      handleFileUpload(event) {
        this.fileToUpload = event.target.files[0]; // Stocker le fichier sélectionné
      },
  
      // Uploader le fichier et obtenir son chemin
      async uploadFile() {
        if (!this.fileToUpload) return null; // Si aucun fichier n'est sélectionné
  
        const formData = new FormData();
        formData.append("file", this.fileToUpload);
  
        try {
          const response = await axios.post("http://localhost:8000/api/upload", formData, {
            headers: { 
              "Content-Type": "multipart/form-data",
              Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`,
             },
          });
          return response.data.filePath; // Supposons que l'API renvoie le chemin du fichier
        } catch (error) {
          console.error("Erreur lors de l'upload du fichier :", error);
          toast.error("Échec de l'upload du fichier.");
          return null;
        }
      },
  
      // Mettre à jour la ressource
      async updateRessource() {
        // Uploader le fichier et obtenir son chemin
        const filePath = await this.uploadFile();
  
        // Préparer les données pour la mise à jour de la ressource
        const requestData = {
          titre: this.form.titre,
          description: this.form.description,
          idCours: this.form.idCours,
          file: filePath || this.form.file, // Utiliser le nouveau fichier ou conserver l'ancien
        };
  
        try {
          const response = await axios.put(`http://localhost:8000/api/ressources/${this.$route.params.id}`, requestData,
            {
              headers: {
                Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`,
              }
            }
          );
          toast.success("Ressource mise à jour avec succès !");
  
          // Rediriger vers la liste des ressources
          this.$router.push({ name: "RessourceList", params: { id: this.form.idCours } });
        } catch (error) {
          console.error("Erreur lors de la mise à jour de la ressource :", error);
          if (error.response && error.response.data) {
            console.error("Détails de l'erreur :", error.response.data);
          }
          toast.error("Échec de la mise à jour de la ressource.");
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
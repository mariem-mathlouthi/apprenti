<template>
  <div id="app" class="flex flex-col h-screen">
    <!-- Navbar & Sidebar -->
    <NavbarTuteur />
    <SidebarTuteur />

    <!-- Main Content -->
    <section id="content" class="flex-1 flex flex-col items-center py-6 relative">
      <div class="container mx-auto p-6 w-full max-w-6xl">
        <!-- Title and Add Button -->
        <div class="flex justify-between items-center mb-6">
          <h1 class="text-3xl font-bold text-indigo-700">Ressources</h1>
          <button @click="toggleForm" class="btn-plus">+</button>
        </div>

        <!-- No Resources Message -->
        <div v-if="ressources.length === 0" class="text-gray-500 text-center py-10">
          Pas de ressources pour le moment.
        </div>

        <!-- Resources List -->
        <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <div v-for="ressource in ressources" :key="ressource.id" class="bg-white p-4 rounded-lg shadow-md">
            <div class="flex justify-between items-center">
              <h2 class="text-lg font-semibold">{{ ressource.titre }}</h2>
              <div>
                <button @click="editRessource(ressource)" class="text-blue-500 hover:text-blue-700 mr-2">
                  Modifier
                </button>
                <button @click="deleteRessource(ressource.id)" class="text-red-500 hover:text-red-700">
                  Supprimer
                </button>
              </div>
            </div>
            <p class="text-sm text-gray-600">{{ ressource.description }}</p>
            <a :href="ressource.file" target="_blank" class="text-blue-500 underline">Voir la ressource</a>
          </div>
        </div>
      </div>

      <!-- Add/Edit Resource Form -->
      <div v-if="showForm" class="fixed right-0 top-0 h-full w-96 bg-white p-6 shadow-lg">
        <h2 class="text-xl font-bold text-indigo-700 mb-4">
          {{ isEditing ? 'Modifier la Ressource' : 'Ajouter une Ressource' }}
        </h2>
        <form @submit.prevent="isEditing ? updateRessource() : createRessource()" class="space-y-4">
          <input type="text" v-model="form.titre" required class="input" placeholder="Titre" />
          <textarea v-model="form.description" required class="input" placeholder="Description"></textarea>
          <input type="file" @change="handleFileUpload" class="input" accept=".pdf,.doc,.docx,.txt,.mp4,.mov,.avi" />
          <button type="submit" class="btn-submit">
            {{ isEditing ? 'Mettre à jour' : 'Ajouter Ressource' }}
          </button>
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
  name: "Ressource",
  components: { SidebarTuteur, NavbarTuteur },
  data() {
    return {
      titre: "",
      description: "",
      file: null,
      showForm: false,
      isEditing: false,
      ressources: [],
      form: {
        id: null,
        titre: "",
        description: "",
        file: null,
        idCours: this.$route.params.id,
      },
    };
  },
  methods: {
    toggleForm() {
      this.showForm = !this.showForm;
      if (!this.showForm) {
        this.resetForm();
      }
    },
    async fetchRessources() {
      try {
        const response = await axios.get(`http://localhost:8000/api/ressources/${this.form.idCours}`);
        this.ressources = response.data;
      } catch (error) {
        console.error("Erreur lors du chargement des ressources :", error);
        toast.error("Erreur lors du chargement des ressources.");
      }
    },
    handleFileUpload(event) {
      this.form.file = event.target.files[0];
    },
    async uploadFile() {
      if (!this.form.file) return null;

      const formData = new FormData();
      formData.append("file", this.form.file);

      try {
        const response = await axios.post("http://localhost:8000/api/upload", formData, {
          headers: { "Content-Type": "multipart/form-data" },
        });
        return response.data.filePath; // Supposons que l'API renvoie le chemin du fichier
      } catch (error) {
        console.error("Erreur lors du téléversement du fichier :", error);
        toast.error("Échec de l'upload.");
        return null;
      }
    },
    async createRessource() {
      const filePath = await this.uploadFile();

      const formData = new FormData();
      formData.append("titre", this.form.titre);
      formData.append("description", this.form.description);
      formData.append("idCours", this.form.idCours);
      if (filePath) formData.append("file", filePath);

      try {
        const response = await axios.post("http://localhost:8000/api/ressources", formData, {
          headers: { "Content-Type": "multipart/form-data" },
        });
        this.ressources.push(response.data);
        toast.success("Ressource ajoutée avec succès !");
        this.showForm = false;
        this.resetForm();
      } catch (error) {
        console.error("Erreur d'ajout :", error);
        toast.error("Échec de l'ajout.");
      }
    },
    editRessource(ressource) {
      this.form = { ...ressource };
      this.isEditing = true;
      this.showForm = true;
    },
    async updateRessource() {
      const filePath = await this.uploadFile();

      const formData = new FormData();
      formData.append("titre", this.form.titre);
      formData.append("description", this.form.description);
      formData.append("idCours", this.form.idCours);
      if (filePath) formData.append("file", filePath);

      try {
        const response = await axios.put(`http://localhost:8000/api/ressources/${this.form.id}`, formData, {
          headers: { "Content-Type": "multipart/form-data" },
        });
        const index = this.ressources.findIndex((r) => r.id === this.form.id);
        this.ressources.splice(index, 1, response.data);
        toast.success("Ressource mise à jour avec succès !");
        this.showForm = false;
        this.resetForm();
      } catch (error) {
        console.error("Erreur de mise à jour :", error);
        toast.error("Échec de la mise à jour.");
      }
    },
    async deleteRessource(id) {
      try {
        await axios.delete(`http://localhost:8000/api/ressources/${id}`);
        this.ressources = this.ressources.filter((r) => r.id !== id);
        toast.success("Ressource supprimée avec succès !");
      } catch (error) {
        console.error("Erreur de suppression :", error);
        toast.error("Échec de la suppression.");
      }
    },
    resetForm() {
      this.form = {
        id: null,
        titre: "",
        description: "",
        file: null,
        idCours: this.$route.params.id,
      };
      this.isEditing = false;
    },
  },
  mounted() {
    this.fetchRessources();
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
.btn-plus {
  background-color: #4f46e5;
  color: white;
  padding: 10px 15px;
  border-radius: 50%;
  font-size: 20px;
  cursor: pointer;
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
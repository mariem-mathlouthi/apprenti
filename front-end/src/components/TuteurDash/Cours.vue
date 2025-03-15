<template>
  <div id="app" class="flex flex-col h-screen">
    <!-- SIDEBAR & NAVBAR -->
    <NavbarTuteur />
    <SidebarTuteur />

    <!-- CONTENU -->
    <section id="content" class="flex-1 flex justify-center items-center py-6">
      <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-indigo-700 text-center mb-6">
          Ajouter un Cours
        </h1>
        <div class="bg-white p-8 rounded-lg shadow-lg w-full sm:w-96 max-w-4xl mx-auto">
          <form @submit.prevent="createCours" class="space-y-6">
            <!-- Titre -->
            <div class="form-group">
              <label class="label">Titre</label>
              <input type="text" v-model="titre" required class="input" placeholder="Entrez le titre" />
            </div>

            <!-- Description -->
            <div class="form-group">
              <label class="label">Description</label>
              <textarea v-model="description" required class="input" placeholder="Entrez la description"></textarea>
            </div>

            <!-- Catégorie -->
            <div class="form-group">
              <label class="label">Catégorie</label>
              <select v-model="selectedCategory" required class="input">
                <option disabled value="">Sélectionnez une catégorie</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                  {{ category.description }}
                </option>
              </select>
            </div>

            <!-- Prix -->
            <div class="form-group">
              <label class="label">Prix (€)</label>
              <input type="number" v-model="prix" required class="input" placeholder="Entrez le prix" />
            </div>

            <!-- Durée -->
            <div class="form-group">
              <label class="label">Durée (heures)</label>
              <input type="number" v-model="duree" required class="input" placeholder="Durée du cours" />
            </div>

            <!-- Fichier (PDF ou autre) -->
            <div class="form-group">
              <label class="label">Fichier</label>
              <input type="file" @change="handleFileUpload" required class="input" />
            </div>

            <!-- Bouton d'ajout -->
            <div class="mt-6 flex justify-center">
              <button type="submit" class="btn-submit">
                Ajouter Cours
              </button>
            </div>
          </form>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import SidebarTuteur from "./SidebarTut.vue";
import NavbarTuteur from "./NavbarTut.vue"; // Assurez-vous d'importer NavbarTuteur
import axios from "axios";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

export default {
  name: "Cours",
  components: {
    SidebarTuteur,
    NavbarTuteur,
  },
  data() {
    return {
      titre: "",
      description: "",
      selectedCategory: "", // Catégorie sélectionnée
      categories: [], // Liste des catégories
      prix: "",
      duree: "",
      file: null,
    };
  },
  methods: {
    // Récupérer les catégories depuis l'API
    async fetchCategories() {
      try {
        const response = await axios.get("http://localhost:8000/api/categories");
        this.categories = response.data;
      } catch (error) {
        console.error("Erreur lors de la récupération des catégories :", error);
        toast.error("Impossible de récupérer les catégories.");
      }
    },
    // Gérer l'upload du fichier
    handleFileUpload(event) {
      this.file = event.target.files[0];
    },
    // Créer un nouveau cours
    async createCours() {
      let formData = new FormData();
      formData.append("titre", this.titre);
      formData.append("description", this.description);
      formData.append("category_id", parseInt(this.selectedCategory)); // Convertir en entier
      formData.append("prix", this.prix); // Renamed from "price" to "prix"
      formData.append("duree", this.duree); // Renamed from "duration" to "duree"
      formData.append("file", this.file);
      formData.append("idTuteur", "1"); // Remplacez par l'ID du tuteur connecté
      formData.append("createdBy", "1"); // Remplacez par l'ID de l'utilisateur connecté

      try {
        await axios.post("http://localhost:8000/api/cours", formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        });
        toast.success("Cours ajouté avec succès !");
        this.$router.push("/tuteur/dashboard"); // Rediriger après l'ajout
      } catch (error) {
        if (error.response && error.response.data.message) {
          toast.error(`Erreur: ${error.response.data.message}`);
        } else {
          console.error("Erreur inconnue:", error);
        }
      }
    },
  },
  mounted() {
    this.fetchCategories(); // Charger les catégories au montage du composant
  },
};
</script>

<style scoped>
.input {
  width: 100%;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
}
.btn-submit {
  background-color: #4f46e5;
  color: white;
  padding: 10px 15px;
  border-radius: 4px;
  cursor: pointer;
}
.btn-submit:hover {
  background-color: #4338ca;
}
</style>
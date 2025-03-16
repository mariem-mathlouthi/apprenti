<template>
  <div id="app" class="flex flex-col h-screen">
    <!-- Navbar & Sidebar -->
    <NavbarTuteur />
    <SidebarTuteur />

    <!-- Contenu principal -->
    <section id="content" class="flex-1 flex justify-center items-center py-6">
      <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-indigo-700 text-center mb-6">
          Ajouter un Cours
        </h1>

        <div class="bg-white p-8 rounded-lg shadow-lg w-full sm:w-96 max-w-4xl mx-auto">
          <form @submit.prevent="createCours" class="space-y-6">
            <!-- Titre -->
            <div>
              <label class="label">Titre</label>
              <input type="text" v-model="titre" required class="input" placeholder="Entrez le titre" />
            </div>

            <!-- Description -->
            <div>
              <label class="label">Description</label>
              <textarea v-model="description" required class="input" placeholder="Entrez la description"></textarea>
            </div>

            <!-- Catégorie -->
            <div>
              <label class="label">Catégorie</label>
              <select v-model="selectedCategory" required class="input">
                <option disabled value="">Sélectionnez une catégorie</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                  {{ category.description }}
                </option>
              </select>
            </div>

            <!-- Prix -->
            <div>
              <label class="label">Prix (€)</label>
              <input type="number" v-model="prix" required class="input" placeholder="Entrez le prix" />
            </div>

            <!-- Durée -->
            <div>
              <label class="label">Durée (heures)</label>
              <input type="number" v-model="duration" required class="input" placeholder="Durée du cours" />
            </div>

            <!-- Fichier -->
            <div>
              <label class="label">Fichier</label>
              <input type="file" @change="handleFileUpload" class="input" />
            </div>

            <!-- Bouton -->
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
import NavbarTuteur from "./NavbarTut.vue";
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
      selectedCategory: "",
      categories: [],
      prix: "",
      duration: "",
      file: null,
      idTuteur: null,
      createdBy: null,
    };
  },
  methods: {
    async fetchCategories() {
      try {
        const response = await axios.get("http://localhost:8000/api/categories");
        this.categories = response.data || [];
      } catch (error) {
        console.error("Erreur lors de la récupération des catégories :", error);
        toast.error("Impossible de récupérer les catégories.");
      }
    },

    handleFileUpload(event) {
      this.file = event.target.files[0];
    },

    loadTuteurData() {
      const tuteurData = localStorage.getItem("TuteurAccountInfo");
      if (tuteurData) {
        const parsedData = JSON.parse(tuteurData);
        this.idTuteur = parsedData.id;
        this.createdBy = parsedData.id;
      } else {
        toast.error("Veuillez vous reconnecter");
        this.$router.push("/login");
      }
    },

    async createCours() {
      if (!this.idTuteur) {
        toast.error("Identifiant tuteur non trouvé");
        return;
      }

      let formData = new FormData();
      formData.append("titre", this.titre);
      formData.append("description", this.description);
      formData.append("category_id", Number(this.selectedCategory));
      formData.append("prix", Number(this.prix));
      formData.append("duration", Number(this.duration));
      formData.append("idTuteur", this.idTuteur);
      formData.append("createdBy", this.createdBy);

      if (this.file) {
        formData.append("file", this.file);
      }

      try {
        const response = await axios.post("http://localhost:8000/api/cours", formData, {
          headers: {
            "Content-Type": "multipart/form-data",
            Authorization: `Bearer ${localStorage.getItem("token")}`,
          },
        });

        if (response.data.success) {
          toast.success("Cours ajouté avec succès !");
          this.$router.push("/cours"); // Rediriger vers la liste des cours
        }
      } catch (error) {
        console.error("Erreur complète :", error);
        const errorMessage = error.response?.data?.error || "Erreur inconnue";
        toast.error(`Échec de l'ajout : ${errorMessage}`);
      }
    },
  },
  mounted() {
    this.loadTuteurData();
    this.fetchCategories();
  },
};
</script>

<style scoped>
.input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 6px;
  transition: border-color 0.2s;
}
.input:focus {
  border-color: #4f46e5;
  outline: none;
}

.btn-submit {
  background-color: #4f46e5;
  color: white;
  padding: 10px 15px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: bold;
}
.btn-submit:hover {
  background-color: #4338ca;
}
</style>
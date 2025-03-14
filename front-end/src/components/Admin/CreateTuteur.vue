<template>
  <div id="app" class="flex flex-col h-screen">
    <!-- SIDEBAR AND NAVBAR -->
    <NavbarOne />
    <SidebarMenu />

    <!-- CONTENT -->
    <section id="content" class="flex-1 flex justify-center items-center py-6">
      <!-- MAIN CONTENT (Formulaire pour Créer un Tuteur) -->
      <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-indigo-700 text-center mb-6">
          Créer un Tuteur
        </h1>
        <div class="bg-white p-8 rounded-lg shadow-lg w-full sm:w-96 max-w-4xl mx-auto">
          <form @submit.prevent="createTuteur" class="space-y-6">
            <!-- Nom complet -->
            <div class="form-group">
              <label class="label">Nom complet</label>
              <input type="text" v-model="fullname" required class="input" placeholder="Entrez le nom complet" />
            </div>

            <!-- Email -->
            <div class="form-group">
              <label class="label">Email</label>
              <input type="email" v-model="email" required class="input" placeholder="Entrez l'email" />
            </div>

            <!-- Domaine -->
            <div class="form-group">
              <label class="label">Domaine</label>
              <input type="text" v-model="domaine" required class="input" placeholder="Entrez le domaine" />
            </div>

            <!-- Catégorie (Liste déroulante) -->
            <div class="form-group">
              <label class="label">Catégorie</label>
              <select v-model="selectedCategory" required class="input">
                <option disabled value="">Sélectionnez une catégorie</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                  {{ category.name }}
                </option>
              </select>
            </div>

            <!-- Spécialité -->
            <div class="form-group">
              <label class="label">Spécialité</label>
              <input type="text" v-model="specialite" required class="input" placeholder="Entrez la spécialité" />
            </div>

            <!-- Expérience -->
            <div class="form-group">
              <label class="label">Expérience (en années)</label>
              <input type="number" v-model="experience" required class="input" placeholder="Expérience en années" />
            </div>

            <!-- Téléphone -->
            <div class="form-group">
              <label class="label">Téléphone</label>
              <input type="text" v-model="phone" required class="input" placeholder="Entrez le téléphone" />
            </div>

            <!-- Mot de passe -->
            <div class="form-group">
              <label class="label">Mot de passe</label>
              <input type="password" v-model="password" required class="input" placeholder="Entrez le mot de passe" />
            </div>

            <!-- Bouton d'ajout -->
            <div class="mt-6 flex justify-center">
              <button type="submit" class="btn-submit">
                Ajouter Tuteur
              </button>
            </div>
          </form>
        </div>
      </div>
    </section>
    <!-- END CONTENT -->
  </div>
</template>

<script>
import SidebarMenu from "./SidebarMenu.vue";
import NavbarOne from "./NavbarOne.vue";
import axios from "axios";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

export default {
  name: "CreateTuteur",
  components: {
    SidebarMenu,
    NavbarOne,
  },
  data() {
    return {
      fullname: "",
      email: "",
      domaine: "",
      selectedCategory: "", // Pour stocker la catégorie sélectionnée
      categories: [], // Liste des catégories depuis l'API
      specialite: "",
      experience: "",
      phone: "",
      password: "",
    };
  },
  methods: {
    async fetchCategories() {
      try {
        const response = await axios.get("http://localhost:8000/api/categories");
        this.categories = response.data;
      } catch (error) {
        console.error("Erreur lors de la récupération des catégories :", error);
        toast.error("Impossible de récupérer les catégories.");
      }
    },
    async createTuteur() {
      let newTuteur = {
        fullname: this.fullname,
        email: this.email,
        domaine: this.domaine,
        category_id: this.selectedCategory, // Envoi de l'ID de la catégorie sélectionnée
        specialite: this.specialite,
        experience: parseInt(this.experience), // Convertir en nombre
        phone: this.phone,
        password: this.password,
      };

      try {
        const response = await axios.post(
          "http://localhost:8000/api/admin/tuteur",
          newTuteur,
          {
            headers: {
              "Content-Type": "application/json",
            },
          }
        );
        toast.success("Tuteur ajouté avec succès !");
        this.$router.push("/Admin");
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
    this.fetchCategories(); // Charger les catégories au chargement du composant
  },
};
</script>

<style scoped>
/* Ajoute ici le CSS si nécessaire */
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

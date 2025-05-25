<template>
  <div class="dashboard-container">
    <!-- Sidebar -->
    <Sidebar />

    <div class="main-content">
      <!-- Navbar -->
      <Navbar />

      <!-- Contenu de la page -->
      <div class="add-category">
        <h2>Ajouter une Catégorie</h2>

        <form @submit.prevent="addCategory">
          <div class="form-group">
            <label for="description">Description :</label>
            <input type="text" id="description" v-model="category.description" required />
          </div>

          <button type="submit" :disabled="loading">
            {{ loading ? "Ajout en cours..." : "Ajouter" }}
          </button>
        </form>

        <p v-if="message" class="success">{{ message }}</p>
        <p v-if="errorMessage" class="error">{{ errorMessage }}</p>
      </div>
    </div>
  </div>
</template>

<script>
import Sidebar from "./SidebarMenu.vue";
import Navbar from "./NavbarOne.vue";
import axios from "axios";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

export default {
  components: { Sidebar, Navbar },
  data() {
    return {
      category: {
        description: ""
      },
      message: "",
      errorMessage: "",
      loading: false
    };
  },
  methods: {
    async addCategory() {
      this.loading = true;
      this.message = "";
      this.errorMessage = "";

      try {
        const response = await axios.post("http://127.0.0.1:8000/api/categories", this.category,
        {
          headers: {
            Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`
          }
        }
        );
        
        this.message = "Catégorie ajoutée avec succès !";
        toast.success(this.message, { autoClose: 2000 });

        this.category.description = ""; // Réinitialisation du champ après ajout
      } catch (error) {
        this.errorMessage = "Erreur lors de l'ajout de la catégorie.";
        toast.error(this.errorMessage, { autoClose: 2000 });
        console.error("Erreur lors de l’ajout :", error);
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>

<style scoped>
.dashboard-container {
  display: flex;
}

.main-content {
  flex-grow: 1;
  padding: 20px;
  background: #f4f4f4;
  min-height: 100vh;
}

.add-category {
  max-width: 400px;
  margin: auto;
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 8px;
  box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
  background-color: #fff;
}

h2 {
  text-align: center;
  margin-bottom: 15px;
}

.form-group {
  margin-bottom: 15px;
}

label {
  display: block;
  font-weight: bold;
  margin-bottom: 5px;
}

input {
  width: 100%;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 5px;
}

button {
  width: 100%;
  padding: 10px;
  background-color: #6200ea;
  color: white;
  border: none;
  border-radius: 5px;
  font-size: 16px;
  cursor: pointer;
  transition: background 0.3s;
}

button:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

button:hover:not(:disabled) {
  background-color: #4500b5;
}

.success {
  color: green;
  margin-top: 10px;
  text-align: center;
}

.error {
  color: red;
  margin-top: 10px;
  text-align: center;
}
</style>

<template>
  <div>
    <NavbarTuteur />
    <div class="grid grid-cols-12 gap-4">
      <div class="col-span-3">
        <SidebarTuteur />
      </div>
      <div class="col-span-9 mt-24 mr-24">
        <router-link
          to="/cours"
          class="px-6 py-2 rounded bg-purple-400 hover:bg-purple-500 text-white ml-4 mt-4 no-underline"
        >
          Retour
        </router-link>

        <div class="container mx-auto px-4 py-8">
          <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
              <div>
                <h3 class="text-lg font-medium leading-6 text-gray-900">Modifier le cours</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Mettez à jour ou supprimez le cours</p>
              </div>
            </div>

            <div class="border-t border-gray-200">
              <dl>
                <!-- Titre -->
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Titre</dt>
                  <dd class="mt-1 sm:col-span-2">
                    <input
                      type="text"
                      v-model="titre"
                      class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      :disabled="loading"
                    />
                  </dd>
                </div>

                <!-- Description -->
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Description</dt>
                  <dd class="mt-1 sm:col-span-2">
                    <textarea
                      v-model="description"
                      class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      :disabled="loading"
                    ></textarea>
                  </dd>
                </div>

                <!-- Catégorie -->
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Catégorie</dt>
                  <dd class="mt-1 sm:col-span-2">
                    <select
                      v-model="selectedCategory"
                      class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      :disabled="loading"
                    >
                      <option disabled value="">Sélectionnez une catégorie</option>
                      <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.description }}
                      </option>
                    </select>
                  </dd>
                </div>

                <!-- Prix -->
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Prix (€)</dt>
                  <dd class="mt-1 sm:col-span-2">
                    <input
                      type="number"
                      v-model="prix"
                      class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      :disabled="loading"
                    />
                  </dd>
                </div>

                <!-- Durée -->
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Durée (heures)</dt>
                  <dd class="mt-1 sm:col-span-2">
                    <input
                      type="number"
                      v-model="duration"
                      class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      :disabled="loading"
                    />
                  </dd>
                </div>

                <!-- Fichier -->
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Fichier</dt>
                  <dd class="mt-1 sm:col-span-2">
                    <input
                      type="file"
                      @change="handleFileUpload"
                      class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      :disabled="loading"
                    />
                  </dd>
                </div>
              </dl>
            </div>

            <div class="flex justify-end px-4 py-4 sm:px-6">
              <button
                @click="updateCours"
                :disabled="loading"
                class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
              >
                {{ loading ? "Enregistrement..." : "Enregistrer" }}
              </button>

              <!-- Bouton de suppression -->
              <button
                @click="confirmDelete"
                :disabled="loading"
                class="inline-flex items-center ml-4 px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
              >
                Supprimer
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import NavbarTuteur from "./NavbarTut.vue";
import SidebarTuteur from "./SidebarTut.vue";

export default {
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
      coursId: null,
      loading: false,
      errorMessage: "",
    };
  },
  components: {
    NavbarTuteur,
    SidebarTuteur,
  },
  created() {
    this.coursId = this.$route.params.id; // Récupérer l'ID du cours depuis la route
    this.loadTuteurData();
    this.fetchCategories();
    this.fetchCoursDetails();
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

    async fetchCoursDetails() {
      try {
        const response = await axios.get(`http://localhost:8000/api/cours/${this.coursId}`);
        const cours = response.data.cours;
        this.titre = cours.titre;
        this.description = cours.description;
        this.selectedCategory = cours.category_id;
        this.prix = cours.prix;
        this.duration = cours.duration;
      } catch (error) {
        console.error("Erreur lors de la récupération des détails du cours :", error);
        toast.error("Impossible de récupérer les détails du cours.");
      }
    },

    async updateCours() {
      if (!this.titre.trim() || !this.description.trim() || !this.selectedCategory || !this.prix || !this.duration) {
        this.errorMessage = "Veuillez remplir tous les champs obligatoires.";
        return;
      }

      this.loading = true;
      this.errorMessage = "";

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

      // Log the form data
      for (let [key, value] of formData.entries()) {
        console.log(key, value);
      }

      try {
        const response = await axios.put(`http://localhost:8000/api/cours/${this.coursId}`, formData, {
          headers: {
            "Content-Type": "multipart/form-data",
            Authorization: `Bearer ${localStorage.getItem("token")}`,
          },
        });

        console.log("Réponse du backend :", response.data); // Log the backend response

        if (response.data.success) {
          toast.success("Cours modifié avec succès !", { autoClose: 2000 });
          setTimeout(() => {
            this.$router.push({ name: "CoursListe" }); // Rediriger vers la liste des cours
          }, 2000);
        } else {
          this.errorMessage = response.data.message || "Erreur inconnue lors de la mise à jour.";
          toast.error(this.errorMessage, { autoClose: 2000 });
        }
      } catch (error) {
        console.error("Erreur complète :", error);
        const errorMessage = error.response?.data?.error || "Erreur inconnue";
        toast.error(`Échec de la modification : ${errorMessage}`, { autoClose: 2000 });
      } finally {
        this.loading = false;
      }
    },

    confirmDelete() {
      if (window.confirm("Êtes-vous sûr de vouloir supprimer ce cours ? Cette action est irréversible !")) {
        this.deleteCours();
      }
    },

    async deleteCours() {
      this.loading = true;

      try {
        await axios.delete(`http://localhost:8000/api/cours/${this.coursId}`);
        toast.success("Cours supprimé avec succès !", { autoClose: 2000 });

        setTimeout(() => {
          this.$router.push("/cours");
        }, 2000);
      } catch (error) {
        console.error("Erreur lors de la suppression :", error);
        this.errorMessage = error.response?.data?.message || "Une erreur est survenue lors de la suppression.";
        toast.error(this.errorMessage, { autoClose: 2000 });
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>

<style scoped>
.bg-gray-500 {
  margin-right: 250px;
}
</style>
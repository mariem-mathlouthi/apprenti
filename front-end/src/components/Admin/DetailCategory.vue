<template>
  <div>
    <Navbar />
    <div class="grid grid-cols-12 gap-4">
      <div class="col-span-3">
        <Sidebar />
      </div>
      <div class="col-span-9 mt-24 mr-24">
        <router-link
          to="/CategoriesList"
          class="px-6 py-2 rounded bg-purple-400 hover:bg-purple-500 text-white ml-4 mt-4 no-underline"
        >
          Retour
        </router-link>
        <div class="container mx-auto px-4 py-8">
          <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
              <div>
                <h3 class="text-lg font-medium leading-6 text-gray-900">Détails de la catégorie</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Informations détaillées sur la catégorie</p>
              </div>
              <div class="flex space-x-4">
                <router-link :to="'/EditCategory/' + categoryId">
                  <button
                    type="button"
                    class="px-6 py-2 rounded border-2 border-green-600 text-black text-sm font-medium hover:bg-green-600 hover:text-white transition-all duration-300"
                  >
                    Modifier
                  </button>
                </router-link>
                <button
                  @click="confirmDelete"
                  class="px-6 py-2 rounded border-2 border-orange-600 text-black text-sm font-medium hover:bg-orange-600 hover:text-white transition-all duration-300"
                  :disabled="loading"
                >
                  {{ loading ? "Suppression..." : "Supprimer" }}
                </button>
              </div>
            </div>
            <div class="border-t border-gray-200">
              <dl>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">ID</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ categoryDetails.id || 'N/A' }}</dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Description</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ categoryDetails.description || 'N/A' }}</dd>
                </div>
              </dl>
            </div>
          </div>
        </div>
        <p v-if="errorMessage" class="text-red-500 text-center mt-4">{{ errorMessage }}</p>
      </div>
    </div>
  </div>
</template>

<script>
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import axios from "axios";
import Navbar from "./NavbarOne.vue";
import Sidebar from "./SidebarMenu.vue";

export default {
  data() {
    return {
      categoryDetails: {},
      categoryId: "",
      errorMessage: "",
      loading: false
    };
  },
  components: {
    Navbar,
    Sidebar
  },
  created() {
    this.categoryId = this.$route.params.id;
    this.fetchCategoryDetails();
  },
  methods: {
    async fetchCategoryDetails() {
      try {
        const response = await axios.get(`http://localhost:8000/api/categories/${this.categoryId}`);
        if (response.data) {
          this.categoryDetails = response.data;
        } else {
          this.errorMessage = "Catégorie non trouvée.";
        }
      } catch (error) {
        console.error("Erreur lors de la récupération des détails :", error);
        this.errorMessage = "Impossible de charger les détails de la catégorie.";
      }
    },
    confirmDelete() {
      if (window.confirm("Êtes-vous sûr de vouloir supprimer cette catégorie ?")) {
        this.deleteCategory();
      }
    },
    async deleteCategory() {
      this.loading = true;
      try {
        const response = await axios.delete(`http://localhost:8000/api/categories/${this.categoryId}`);
        if (response.data.success) {
          toast.success("Catégorie supprimée avec succès !", { autoClose: 2000 });
          this.$router.push("/CategoriesList");
        } else {
          this.errorMessage = "Erreur lors de la suppression.";
          toast.error(this.errorMessage, { autoClose: 2000 });
        }
      } catch (error) {
        console.error("Erreur lors de la suppression :", error);
        this.errorMessage = "Une erreur est survenue lors de la suppression.";
        toast.error(this.errorMessage, { autoClose: 2000 });
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>

<style scoped>
.bg-gray-500 {
  margin-right: 250px;
}
</style>

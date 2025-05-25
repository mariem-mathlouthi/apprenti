<template>
  <div>
    <Navbar />
    <div class="grid grid-cols-12 gap-4">
      <div class="col-span-3">
        <Sidebar />
      </div>
      <div class="col-span-9 mt-24 mr-24">
        <router-link
          to="/CategoryList"
          class="px-6 py-2 rounded bg-purple-400 hover:bg-purple-500 text-white ml-4 mt-4 no-underline"
        >
          Retour
        </router-link>

        <div class="container mx-auto px-4 py-8">
          <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
              <div>
                <h3 class="text-lg font-medium leading-6 text-gray-900">Modifier la catégorie</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Mettez à jour ou supprimez la catégorie</p>
              </div>
            </div>

            <div class="border-t border-gray-200">
              <dl>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Description</dt>
                  <dd class="mt-1 sm:col-span-2">
                    <input 
                      type="text" 
                      v-model="description" 
                      class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      :disabled="loading"
                    />
                    <p v-if="errorMessage" class="text-red-500 text-sm mt-2">{{ errorMessage }}</p>
                  </dd>
                </div>
              </dl>
            </div>

            <div class="flex justify-end px-4 py-4 sm:px-6">
              <button
                @click="updateCategory"
                :disabled="loading"
                class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
              >
                {{ loading ? "Enregistrement..." : "Enregistrer" }}
              </button>

              <!-- Nouveau bouton de suppression -->
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
import Navbar from "./NavbarOne.vue";
import Sidebar from "./SidebarMenu.vue";

export default {
  data() {
    return {
      categoryId: "",
      description: "",
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
        const response = await axios.get(`http://localhost:8000/api/categories/${this.categoryId}`,
          {
            headers: {
              Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`
            }
          }
        );
        
        if (response.data && response.data.category) {
          this.description = response.data.category.description;
        } else {
          this.errorMessage = "Catégorie non trouvée.";
        }
      } catch (error) {
        console.error("Erreur lors de la récupération des détails :", error);
        this.errorMessage = "Impossible de charger les détails de la catégorie.";
      }
    },
    
    async updateCategory() {
      if (!this.description.trim()) {
        this.errorMessage = "La description ne peut pas être vide.";
        return;
      }

      this.loading = true;
      this.errorMessage = "";

      try {
        const response = await axios.put(`http://localhost:8000/api/categories/${this.categoryId}`, {
          description: this.description
        },
        {
          headers: {
            Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`
          }
        }
      );

        if (response.data && response.data.data) {
          this.description = response.data.data.description;
          toast.success(response.data.message, { autoClose: 2000 });

          setTimeout(() => {
            this.$router.push("/CategoryList");
          }, 2000);
        } else {
          this.errorMessage = "Erreur lors de la mise à jour.";
          toast.error(this.errorMessage, { autoClose: 2000 });
        }
      } catch (error) {
        console.error("Erreur lors de la mise à jour :", error);
        this.errorMessage = error.response?.data?.message || "Une erreur est survenue.";
        toast.error(this.errorMessage, { autoClose: 2000 });
      } finally {
        this.loading = false;
      }
    },

    confirmDelete() {
      if (window.confirm("Êtes-vous sûr de vouloir supprimer cette catégorie ? Cette action est irréversible !")) {
        this.deleteCategory();
      }
    },

    async deleteCategory() {
      this.loading = true;

      try {
        await axios.delete(`http://localhost:8000/api/categories/${this.categoryId}`,
          {
            headers: {
              Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`
            }
          }
        );
        toast.success("Catégorie supprimée avec succès !", { autoClose: 2000 });

        setTimeout(() => {
          this.$router.push("/CategoryList");
        }, 2000);
      } catch (error) {
        console.error("Erreur lors de la suppression :", error);
        this.errorMessage = error.response?.data?.message || "Une erreur est survenue lors de la suppression.";
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

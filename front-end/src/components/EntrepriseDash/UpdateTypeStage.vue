<template>
  <div>
    <Navbar />
    <div class="grid grid-cols-12 gap-4">
      <div class="col-span-3">
        <Sidebar />
      </div>
      <div class="col-span-9 mt-24 mr-24">
        <router-link
          to="/TypeStageList"
          class="px-6 py-2 rounded bg-purple-400 hover:bg-purple-500 text-white ml-4 mt-4 no-underline"
        >
          Retour
        </router-link>

        <div class="container mx-auto px-4 py-8">
          <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
              <div>
                <h3 class="text-lg font-medium leading-6 text-gray-900">Modifier le type de stage</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Mettez à jour ou supprimez le type de stage</p>
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
                @click="updateTypeStage"
                :disabled="loading"
                class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
              >
                {{ loading ? "Enregistrement..." : "Enregistrer" }}
              </button>

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
import Navbar from "./Navbar.vue";
import Sidebar from "./Sidebar.vue";

export default {
  data() {
    return {
      typeStageId: "",
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
    this.typeStageId = this.$route.params.id;
    this.fetchTypeStageDetails();
  },
  methods: {
    async fetchTypeStageDetails() {
      try {
        this.loading = true;
        const response = await axios.get(
          `http://localhost:8000/api/type-stages/${this.typeStageId}`
        );

        if (response.data?.type_stage?.description) {
          this.description = response.data.type_stage.description;
        } else if (response.data?.typeStage?.description) {
          this.description = response.data.typeStage.description;
        } else if (response.data?.description) {
          this.description = response.data.description;
        } else {
          throw new Error("Structure de réponse non valide");
        }

      } catch (error) {
        this.handleError(error, "Échec du chargement");
      } finally {
        this.loading = false;
      }
    },

    handleError(error, defaultMessage) {
      console.error("Erreur:", error);
      this.errorMessage = error.response?.data?.message 
        || error.message 
        || defaultMessage;
      
      toast.error(this.errorMessage, {
        position: toast.POSITION.TOP_RIGHT,
        autoClose: 3000,
        theme: "colored",
        style: {
          backgroundColor: '#fef2f2',
          color: '#dc2626'
        },
        icon: '❌'
      });
    },

    async updateTypeStage() {
      if (!this.description?.trim()) {
        this.errorMessage = "La description est obligatoire";
        this.handleError(new Error(this.errorMessage));
        return;
      }

      this.loading = true;
      try {
        const response = await axios.put(
          `http://localhost:8000/api/type-stages/${this.typeStageId}`,
          { description: this.description }
        );

        toast.success(response.data?.message || "Modification réussie !", {
          position: toast.POSITION.TOP_RIGHT,
          autoClose: 2000,
          theme: "colored",
          style: {
            backgroundColor: '#f0fdf4',
            color: '#166534'
          },
          icon: '✅',
          onClose: () => this.$router.push("/TypeStageList")
        });

      } catch (error) {
        this.handleError(error, "Échec de la mise à jour");
      } finally {
        this.loading = false;
      }
    },

    confirmDelete() {
      if (confirm("Supprimer définitivement ce type de stage ?")) {
        this.deleteTypeStage();
      }
    },

    async deleteTypeStage() {
      this.loading = true;
      try {
        await axios.delete(
          `http://localhost:8000/api/type-stages/${this.typeStageId}`
        );

        toast.success("Suppression réussie !", {
          position: toast.POSITION.TOP_RIGHT,
          autoClose: 1500,
          theme: "colored",
          style: {
            backgroundColor: '#f0fdf4',
            color: '#166534'
          },
          icon: '✅',
          onClose: () => this.$router.push("/TypeStageList")
        });

      } catch (error) {
        this.handleError(error, "Échec de la suppression");
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

@media (max-width: 640px) {
  .col-span-9 {
    margin-right: 1rem;
  }
  
  .container {
    padding-left: 1rem;
    padding-right: 1rem;
  }

  .text-lg {
    font-size: 1rem;
  }
  
  .text-sm {
    font-size: 0.875rem;
  }
}

/* Styles globaux pour les notifications */
.Toastify__toast--success {
  background: #f0fdf4 !important;
  color: #166534 !important;
  border: 1px solid #bbf7d0;
}

.Toastify__toast--error {
  background: #fef2f2 !important;
  color: #dc2626 !important;
  border: 1px solid #fecaca;
}
</style>
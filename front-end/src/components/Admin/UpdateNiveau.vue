<template>
    <div>
      <Navbar />
      <div class="grid grid-cols-12 gap-4">
        <div class="col-span-3">
          <Sidebar />
        </div>
        <div class="col-span-9 mt-24 mr-24">
          <router-link
            to="/NiveauList"
            class="px-6 py-2 rounded bg-purple-400 hover:bg-purple-500 text-white ml-4 mt-4 no-underline"
          >
            Retour
          </router-link>
  
          <div class="container mx-auto px-4 py-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
              <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                <div>
                  <h3 class="text-lg font-medium leading-6 text-gray-900">Modifier le niveau</h3>
                  <p class="mt-1 max-w-2xl text-sm text-gray-500">Mettez à jour ou supprimez le niveau</p>
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
                  @click="updateNiveau"
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
  import Navbar from "./NavbarOne.vue";
  import Sidebar from "./SidebarMenu.vue";
  
  export default {
    data() {
      return {
        niveauId: "",
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
      this.niveauId = this.$route.params.id;
      this.fetchNiveauDetails();
    },
    methods: {
      async fetchNiveauDetails() {
        try {
          this.loading = true;
          const response = await axios.get(
            `http://localhost:8000/api/niveaux/${this.niveauId}`,
            {
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`
              },
            }
          );
  
          console.log("Réponse API:", response.data);
  
          if (response.data?.success && response.data?.niveau) {
            this.description = response.data.niveau.description;
          } else if (response.data?.description) {
            this.description = response.data.description;
          } else {
            this.errorMessage = "Structure de données API invalide";
            toast.error("Erreur de format des données", { autoClose: 2000 });
          }
        } catch (error) {
          console.error("Erreur détaillée:", {
            message: error.message,
            response: error.response?.data,
            stack: error.stack
          });
  
          this.errorMessage = error.response?.data?.message || 
            "Échec du chargement. Vérifiez la console pour plus de détails.";
          
          toast.error(this.errorMessage, { autoClose: 3000 });
        } finally {
          this.loading = false;
        }
      },
  
      async updateNiveau() {
        if (!this.description?.trim()) {
          this.errorMessage = "La description est obligatoire";
          toast.error(this.errorMessage, { autoClose: 2000 });
          return;
        }
  
        this.loading = true;
        try {
          const response = await axios.put(
            `http://localhost:8000/api/niveaux/${this.niveauId}`,
            { description: this.description },
            {
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`
              },
              validateStatus: (status) => status >= 200 && status < 500
            }
          );
  
          if (response.status === 200) {
            toast.success("Niveau mis à jour avec succès !", { 
              autoClose: 2000,
              onClose: () => this.$router.push("/NiveauList")
            });
          } else {
            throw new Error(response.data?.message || "Réponse serveur inattendue");
          }
        } catch (error) {
          this.errorMessage = error.response?.data?.message || 
            `Erreur mise à jour: ${error.message}`;
          
          toast.error(this.errorMessage, { 
            autoClose: 3000,
            pauseOnFocusLoss: false
          });
        } finally {
          this.loading = false;
        }
      },
  
      confirmDelete() {
        if (confirm("Êtes-vous sûr de vouloir supprimer définitivement ce niveau ?")) {
          this.deleteNiveau();
        }
      },
  
      async deleteNiveau() {
        this.loading = true;
        try {
          const response = await axios.delete(
            `http://localhost:8000/api/niveaux/${this.niveauId}`,
            {
              validateStatus: (status) => status === 200 || status === 404
            },
            {
              headers: {
                Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`
              },
            }
          );
  
          if (response.status === 200) {
            toast.success("Suppression réussie !", {
              autoClose: 1500,
              onClose: () => this.$router.push("/NiveauList")
            });
          } else {
            throw new Error("Niveau introuvable ou déjà supprimé");
          }
        } catch (error) {
          this.errorMessage = error.response?.data?.message || error.message;
          toast.error(`Échec suppression: ${this.errorMessage}`, {
            autoClose: 3000,
            closeOnClick: true
          });
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
  }
  </style>
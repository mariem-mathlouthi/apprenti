<template>
    <div>
      <Navbar />
      <div class="grid grid-cols-12 gap-4">
        <div class="col-span-3">
          <Sidebar />
        </div>
        <div class="col-span-9 mt-24 mr-24">
          <router-link
            to="/SpecialiteList"
            class="px-6 py-2 rounded bg-purple-400 hover:bg-purple-500 text-white ml-4 mt-4 no-underline"
          >
            Retour
          </router-link>
          <div class="container mx-auto px-4 py-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
              <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                <div>
                  <h3 class="text-lg font-medium leading-6 text-gray-900">Détails de la spécialité</h3>
                  <p class="mt-1 max-w-2xl text-sm text-gray-500">Informations complètes</p>
                </div>
                <div class="flex space-x-4">
                  <router-link :to="'/UpdateSpecialite/' + specialiteId">
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
                  >
                    Supprimer
                  </button>
                </div>
              </div>
              <div class="border-t border-gray-200">
                <dl>
                  <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">ID</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ specialiteDetails.id }}</dd>
                  </div>
                  <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ specialiteDetails.description }}</dd>
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
  import axios from "axios";
  import { toast } from "vue3-toastify";
  import "vue3-toastify/dist/index.css";
  import Navbar from "./NavbarOne.vue";
  import Sidebar from "./SidebarMenu.vue";
  
  export default {
    data() {
      return {
        specialiteDetails: {},
        specialiteId: "",
        errorMessage: "",
        loading: false
      };
    },
    components: {
      Navbar,
      Sidebar
    },
    created() {
      this.specialiteId = this.$route.params.id;
      this.fetchSpecialiteDetails();
    },
    methods: {
      async fetchSpecialiteDetails() {
        try {
          const response = await axios.get(`http://localhost:8000/api/specialites/${this.specialiteId}`,
            {
              headers: {
                Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`
              }
            }
          );
          this.specialiteDetails = response.data;
        } catch (error) {
          console.error("Erreur :", error);
          this.errorMessage = "Impossible de charger les détails";
          toast.error(this.errorMessage, { autoClose: 2000 });
        }
      },
      confirmDelete() {
        if (confirm("Confirmez la suppression définitive ?")) {
          this.deleteSpecialite();
        }
      },
      async deleteSpecialite() {
        this.loading = true;
        try {
          await axios.delete(`http://localhost:8000/api/specialites/${this.specialiteId}`,
            {
              headers: {
                Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`
              }
            }
          );
          toast.success("Suppression réussie !", { autoClose: 2000 });
          this.$router.push("/SpecialiteList");
        } catch (error) {
          console.error("Erreur :", error);
          toast.error("Échec de la suppression", { autoClose: 2000 });
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
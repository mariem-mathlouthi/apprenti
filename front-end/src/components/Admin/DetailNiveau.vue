<template>
    <div>
      <Navbar />
      <div class="grid grid-cols-12 gap-4">
        <div class="col-span-3">
          <Sidebar />
        </div>
        <div class="col-span-9 mt-24 mr-24">
          <router-link to="/NiveauList" class="px-6 py-2 rounded bg-purple-400 hover:bg-purple-500 text-white ml-4 mt-4 no-underline">
            Retour
          </router-link>
          <div class="container mx-auto px-4 py-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
              <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                <div>
                  <h3 class="text-lg font-medium leading-6 text-gray-900">Détails du niveau</h3>
                </div>
                <div class="flex space-x-4">
                  <router-link :to="'/UpdateNiveau/' + niveauId">
                    <button class="px-6 py-2 rounded border-2 border-green-600 text-black hover:bg-green-600 hover:text-white">
                      Modifier
                    </button>
                  </router-link>
                  <button @click="confirmDelete" class="px-6 py-2 rounded border-2 border-red-600 text-black hover:bg-red-600 hover:text-white">
                    Supprimer
                  </button>
                </div>
              </div>
              <div class="border-t border-gray-200">
                <dl>
                  <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">ID</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ niveauDetails.id }}</dd>
                  </div>
                  <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ niveauDetails.description }}</dd>
                  </div>
                </dl>
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
  import Navbar from "./NavbarOne.vue";
  import Sidebar from "./SidebarMenu.vue";
  
  export default {
    data() {
      return {
        niveauDetails: {},
        niveauId: ""
      };
    },
    components: { Navbar, Sidebar },
    created() {
      this.niveauId = this.$route.params.id;
      this.fetchNiveauDetails();
    },
    methods: {
      async fetchNiveauDetails() {
        try {
          const response = await axios.get(`http://localhost:8000/api/niveaux/${this.niveauId}`,
            {
              headers: {
                "Content-Type": "application/json",
                Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`
              }
            }
          );
          this.niveauDetails = response.data;
        } catch (error) {
          toast.error("Erreur de chargement", { autoClose: 2000 });
        }
      },
      async confirmDelete() {
        if (confirm("Confirmer la suppression ?")) {
          try {
            await axios.delete(`http://localhost:8000/api/niveaux/${this.niveauId}`,
              {
                headers: {
                  "Content-Type": "application/json",
                  Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`
                }
              }
            );
            this.$router.push("/NiveauList");
          } catch (error) {
            toast.error("Erreur de suppression", { autoClose: 2000 });
          }
        }
      }
    }
  };
  </script>
<template>
    <div>
      <Navbar />
      <div class="grid grid-cols-12">
        <div class="col-span-3">
          <Sidebar />
        </div>
  
        <div class="col-span-9 mt-24 mr-12">
          <h2 class="text-2xl font-bold mb-8 mt-8">Liste des types de stage</h2>
  
          <div class="flex justify-end mb-4">
            <router-link to="/AddTypeStage">
              <button
                type="button"
                class="px-6 py-2 rounded-full text-black text-sm tracking-wider font-medium outline-none border-2 border-purple-600 hover:bg-purple-300 hover:text-white transition-all duration-300 flex items-center gap-2"
              >
                <i class="fas fa-plus"></i>
                <span>Ajouter un nouveau type</span>
              </button>
            </router-link>
          </div>
  
          <div class="overflow-x-auto">
            <table v-if="!loading && typeStages.length > 0" class="min-w-full divide-y divide-gray-200 font-[sans-serif]">
              <thead class="bg-gray-100 whitespace-nowrap">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Détails</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200 whitespace-nowrap">
                <tr v-for="(typeStage, index) in typeStages" :key="typeStage.id">
                  <td class="px-6 py-4 text-sm text-[#333]">{{ index + 1 }}</td>
                  <td class="px-6 py-4 text-sm text-[#333]">{{ typeStage.description }}</td>
                  <td class="px-6 py-4 text-sm text-[#333]">
                    <router-link
                      :to="'/UpdateTypeStage/' + typeStage.id"
                      class="text-green-500 hover:text-green-700"
                    >
                      Détails
                    </router-link>
                  </td>
                </tr>
              </tbody>
            </table>
  
            <p v-else-if="!loading" class="text-center text-gray-500 text-lg mt-6">
              Aucun type de stage trouvé.
            </p>
            <p v-if="loading" class="text-center text-gray-500 text-lg mt-6">
              Chargement...
            </p>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import { toast } from "vue3-toastify";
  import "vue3-toastify/dist/index.css";
  import axios from "axios";
  import Navbar from "./Navbar.vue";
  import Sidebar from "./Sidebar.vue";
  
  export default {
    data() {
      return {
        typeStages: [],
        loading: false
      };
    },
    components: {
      Navbar,
      Sidebar
    },
    methods: {
      async getAllTypeStages() {
        this.loading = true;
        try {
          const response = await axios.get("http://localhost:8000/api/type-stages",
            {
              headers: {
                Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`
              }
            }
          );
          this.typeStages = response.data;
        } catch (error) {
          console.error("Erreur:", error);
          toast.error("Impossible de charger les types de stage.", { autoClose: 2000 });
        } finally {
          this.loading = false;
        }
      }
    },
    mounted() {
      this.getAllTypeStages();
    }
  };
  </script>
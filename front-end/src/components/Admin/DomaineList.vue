<template>
    <div>
      <Navbar />
      <div class="grid grid-cols-12">
        <div class="col-span-3">
          <Sidebar />
        </div>
  
        <div class="col-span-9 mt-24 mr-12">
          <h2 class="text-2xl font-bold mb-8 mt-8">Liste des domaines</h2>
  
          <div class="flex justify-end mb-4">
            <router-link to="/AddDomaine">
              <button
                type="button"
                class="px-6 py-2 rounded-full text-black text-sm tracking-wider font-medium outline-none border-2 border-purple-600 hover:bg-purple-300 hover:text-white transition-all duration-300 flex items-center gap-2"
              >
                <i class="fas fa-plus"></i>
                <span>Ajouter un nouveau domaine</span>
              </button>
            </router-link>
          </div>
  
          <div class="overflow-x-auto">
            <table v-if="!loading && domaines.length > 0" class="min-w-full divide-y divide-gray-200 font-[sans-serif]">
              <thead class="bg-gray-100 whitespace-nowrap">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Détails</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200 whitespace-nowrap">
                <tr v-for="(domaine, index) in domaines" :key="domaine.id">
                  <td class="px-6 py-4 text-sm text-[#333]">{{ index + 1 }}</td>
                  <td class="px-6 py-4 text-sm text-[#333]">{{ domaine.description }}</td>
                  <td class="px-6 py-4 text-sm text-[#333]">
                    <router-link
                      :to="'/UpdateDomaine/' + domaine.id"
                      class="text-green-500 hover:text-green-700"
                    >
                      Détails
                    </router-link>
                  </td>
                </tr>
              </tbody>
            </table>
  
            <p v-else-if="!loading" class="text-center text-gray-500 text-lg mt-6">
              Aucun domaine trouvé.
            </p>
            <p v-if="loading" class="text-center text-gray-500 text-lg mt-6">
              Chargement des domaines...
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
  import Navbar from "./NavbarOne.vue";
  import Sidebar from "./SidebarMenu.vue";
  
  export default {
    data() {
      return {
        domaines: [],
        loading: false
      };
    },
    components: {
      Navbar,
      Sidebar
    },
    methods: {
      async getAllDomaines() {
        this.loading = true;
        try {
          const response = await axios.get("http://localhost:8000/api/domaines");
          this.domaines = response.data;
        } catch (error) {
          console.error("Erreur:", error);
          toast.error("Impossible de charger les domaines.", { autoClose: 2000 });
        } finally {
          this.loading = false;
        }
      }
    },
    mounted() {
      this.getAllDomaines();
    }
  };
  </script>
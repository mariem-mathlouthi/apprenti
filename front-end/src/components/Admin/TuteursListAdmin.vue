<template>
    <div id="app">
      <!-- SIDEBAR -->
      <SidebarMenu></SidebarMenu>
  
      <!-- CONTENT -->
      <section id="content">
        <!-- NAVBAR -->
        <NavbarOne></NavbarOne>
        <div class="col-span-9 mt-24 mr-24 ml-24">
          <h1 class="text-center font-weight-bold my-4">Liste des tuteurs</h1>
          <table class="min-w-full divide-y divide-gray-200 font-[sans-serif]">
            <thead class="bg-gray-100 whitespace-nowrap">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Id</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Spécialité</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expérience</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Téléphone</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Détails</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200 whitespace-nowrap">
              <tr v-for="(tuteur, index) in tuteurs" :key="index">
                <td class="px-6 py-4 text-sm text-[#333]">{{ index+1 }}</td>
                <td class="px-6 py-4 text-sm text-[#333]">{{ tuteur.status }}</td>
                <td class="px-6 py-4 text-sm text-[#333]">{{ tuteur.fullname }}</td>
                <td class="px-6 py-4 text-sm text-[#333]">{{ tuteur.specialite }}</td>
                <td class="px-6 py-4 text-sm text-[#333]">{{ tuteur.experience }} ans</td>
                <td class="px-6 py-4 text-sm text-[#333]">{{ tuteur.phone }}</td>
                <td class="px-6 py-4 text-sm text-[#333]">
                  <button class="text-green-500 hover:text-green-700 mr-4">
                    <router-link :to="'/DetailTuteurAdmin/' + tuteur.id">Détails</router-link>

                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
      <!-- CONTENT -->
    </div>
  </template>
  
  <script>
  import { toast } from "vue3-toastify";
  import "vue3-toastify/dist/index.css";
  import axios from "axios";
  import NavbarOne from "../Admin/NavbarOne.vue";
  import SidebarMenu from "../Admin/SidebarMenu.vue";
  import DashboardContent from "../Admin/DashboardContent.vue";
  
  export default {
    data() {
      return {
        tuteurs: [],
      };
    },
    components: {
      NavbarOne,
      SidebarMenu,
      DashboardContent,
    },
    methods: {
      async getAllTuteurs() {
        try {
          const response = await axios.get(`http://localhost:8000/api/tuteurs`, {
            headers: {
              Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`,
            }
          });
          if (response.data.tuteurs) {
            this.tuteurs = response.data.tuteurs;
            console.table(this.tuteurs);
          } else {
            toast.error("Erreur lors de la récupération des tuteurs", {
              autoClose: 2000,
            });
          }
        } catch (error) {
          console.error("Erreur lors de la récupération des tuteurs:", error);
        }
      },
    },
    mounted() {
      this.getAllTuteurs();
    },
  };
  </script>
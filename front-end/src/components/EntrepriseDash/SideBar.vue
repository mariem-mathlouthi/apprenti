<template>
  <nav class="bg-blue-50 shadow-xl h-screen fixed top-0 left-0 min-w-[250px] py-6 font-[sans-serif] overflow-auto" style="margin-top:72px">
    <div class="relative flex flex-col h-full">
      <ul class="space-y-1 my-10 flex-1">
        <!-- Tableau de bord -->
        <li>
          <router-link to="/EntrepriseDash" class="text-gray-600 text-sm flex items-center hover:text-blue-600 hover:border-r-4 border-blue-500 hover:bg-blue-50 px-8 py-4 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <span>Tableau de bord</span>
          </router-link>
        </li>

        <!-- Type Stage -->
        <li>
          <router-link to="/TypeStageList" class="text-gray-600 text-sm flex items-center hover:text-blue-600 hover:border-r-4 border-blue-500 hover:bg-blue-50 px-8 py-4 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span>Type Stage</span>
          </router-link>
        </li>

        <!-- Liste des offres -->
        <li>
          <router-link to="/OffersList" class="text-gray-600 text-sm flex items-center hover:text-blue-600 hover:border-r-4 border-blue-500 hover:bg-blue-50 px-8 py-4 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <span>Liste des offres</span>
          </router-link>
        </li>

        <!-- Liste des stages -->
        <li>
          <router-link to="/TreatedRequest" class="text-gray-600 text-sm flex items-center hover:text-blue-600 hover:border-r-4 border-blue-500 hover:bg-blue-50 px-8 py-4 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            <span>Liste des stages</span>
          </router-link>
        </li>

        <!-- Liste des étudiants -->
        <li>
          <router-link to="/StudentsList" class="text-gray-600 text-sm flex items-center hover:text-blue-600 hover:border-r-4 border-blue-500 hover:bg-blue-50 px-8 py-4 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span>Liste des étudiants</span>
          </router-link>
        </li>

        <!-- Compte -->
        <li>
          <router-link to="/EntrepriseAccount" class="text-gray-600 text-sm flex items-center hover:text-blue-600 hover:border-r-4 border-blue-500 hover:bg-blue-50 px-8 py-4 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span>Compte</span>
          </router-link>
        </li>

        <!-- Déconnexion -->
        <li>
          <router-link to="/SignIn" class="text-gray-600 text-sm flex items-center hover:text-blue-600 hover:border-r-4 border-blue-500 hover:bg-blue-50 px-8 py-4 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span>Déconnexion</span>
          </router-link>
        </li>
      </ul>

      <!-- Profile Section -->
      <div class="flex items-center cursor-pointer border-t border-gray-200 px-8 py-4">
        <img :src="logoURL" class="w-9 h-9 rounded-full border-2 border-white" />
        <div class="ml-4">
          <p class="text-sm text-gray-600">Entreprise</p>
          <p class="text-xs text-gray-400 mt-1">Compte actif</p>
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
import axios from "axios";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

export default {
  name: 'Sidebar',
  data() {
    return {
      idEntreprise: "",
      logoURL: "https://i.postimg.cc/mDWkzGDv/istockphoto-1200064810-170667a.jpg",
    }
  },
  methods: {
    async getLogo() {
      let storedData = localStorage.getItem("EntrepriseAccountInfo"); 
      this.idEntreprise = JSON.parse(storedData).id;
      try {
        const response = await axios.get(
          `http://localhost:8000/api/getEntreprise/${this.idEntreprise}`,
          {
            headers: {
              Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`,
            },
          }
        );
        if (response.data.check) {
          this.logoURL = "http://localhost:8000" + response.data.entreprise.logo;
        } else {
          toast.error("Erreur de chargement du logo", { autoClose: 2000 });
        }
      } catch (error) {
        console.error("Error:", error);
      }
    }
  },
  mounted() {
    this.getLogo();
  }
}
</script>
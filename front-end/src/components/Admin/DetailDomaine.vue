<template>
    <div>
      <Navbar />
      <div class="grid grid-cols-12 gap-4">
        <div class="col-span-3">
          <Sidebar />
        </div>
        <div class="col-span-9 mt-24 mr-24">
          <router-link to="/DomaineList" class="px-6 py-2 rounded bg-purple-400 hover:bg-purple-500 text-white ml-4 mt-4">
            Retour
          </router-link>
          <div class="container mx-auto px-4 py-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
              <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                <div>
                  <h3 class="text-lg font-medium leading-6 text-gray-900">Détails du domaine</h3>
                </div>
              </div>
              <div class="border-t border-gray-200">
                <dl>
                  <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">ID</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ domaineDetails.id }}</dd>
                  </div>
                  <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ domaineDetails.description }}</dd>
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
  import Navbar from "./NavbarOne.vue";
  import Sidebar from "./SidebarMenu.vue";
  
  export default {
    data() {
      return {
        domaineDetails: {}
      };
    },
    components: { Navbar, Sidebar },
    created() {
      this.fetchDomaineDetails();
    },
    methods: {
      async fetchDomaineDetails() {
        try {
          const response = await axios.get(`http://localhost:8000/api/domaines/${this.$route.params.id}`,
            {
              headers: {
                Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`
              }
            }
          );
          this.domaineDetails = response.data;
        } catch (error) {
          console.error("Erreur de chargement", error);
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
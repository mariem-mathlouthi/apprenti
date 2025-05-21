<template>
  <div id="app">
    <!-- Sidebar -->
    <SidebarMenu />
    
    <!-- Content -->
    <div id="content">
      <!-- Navbar -->
      <NavbarOne />
      
      <!-- Main Content Area -->
      <div class="col-span-9 mt-24 mr-24 ml-24">
        <div class="container mx-auto">
          <h3 class="text-gray-700 text-3xl font-medium mb-6">Demandes de Tuteurs</h3>

          <!-- Loading State -->
          <div v-if="loading" class="flex justify-center items-center h-64">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
          </div>

          <!-- Error Message -->
          <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            {{ error }}
          </div>

          <!-- Tutor Requests List -->
          <div v-if="!loading && tuteurRequests.length > 0" class="bg-white shadow-lg rounded-lg overflow-hidden mt-6">
            <table class="min-w-full leading-normal">
              <thead>
                <tr>
                  <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Nom Complet
                  </th>
                  <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Email
                  </th>
                  <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Experience
                  </th>
                  <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Phone
                  </th>
                  <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="request in tuteurRequests" :key="request.id" class="hover:bg-gray-50">
                  <td class="px-5 py-5 border-b border-gray-200 text-sm">
                    <div class="flex items-center">
                      <div class="ml-3">
                        <p class="text-gray-900 whitespace-no-wrap">
                          {{ request.fullname }}
                        </p>
                      </div>
                    </div>
                  </td>
                  <td class="px-5 py-5 border-b border-gray-200 text-sm">
                    <p class="text-gray-900 whitespace-no-wrap">{{ request.email }}</p>
                  </td>
                  <td class="px-5 py-5 border-b border-gray-200 text-sm">
                    <p class="text-gray-900 whitespace-no-wrap">{{ request.experience }}</p>
                  </td>
                  <td class="px-5 py-5 border-b border-gray-200 text-sm">
                    <p class="text-gray-900 whitespace-no-wrap">{{ request.phone }}</p>
                  </td>
                  <td class="px-5 py-5 border-b border-gray-200 text-sm">
                    <div class="flex space-x-3">
                      <button 
                        @click="acceptRequest(request.id)"
                        class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg text-sm font-medium transition-colors duration-200"
                        :disabled="processing"
                      >
                        Accepter
                      </button>
                      <button 
                        @click="rejectRequest(request.id)"
                        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm font-medium transition-colors duration-200"
                        :disabled="processing"
                      >
                        Rejeter
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- No Requests Message -->
          <div v-if="!loading && tuteurRequests.length === 0" class="text-center py-8">
            <p class="text-gray-500 text-lg">Aucune demande de tuteur en attente</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import SidebarMenu from "./SidebarMenu.vue";
import NavbarOne from "./NavbarOne.vue";
import axios from "axios";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

export default {
  name: "TuteurRequests",
  components: {
    SidebarMenu,
    NavbarOne,
  },
  data() {
    return {
      tuteurRequests: [],
      loading: true,
      error: null,
      processing: false,
    };
  },
  mounted() {
    this.fetchTuteurRequests();
  },
  methods: {
    async fetchTuteurRequests() {
      try {
        const response = await axios.get('http://localhost:8000/api/pending-tuteurs', {
        //   headers: {
        //     Authorization: `Bearer ${JSON.parse(localStorage.getItem('admin_token'))}`
        //   }
        });
        this.tuteurRequests = response.data.tuteurs;
      } catch (error) {
        this.error = 'Erreur lors du chargement des demandes de tuteurs';
        console.error('Error fetching tutor requests:', error);
      } finally {
        this.loading = false;
      }
    },

    async acceptRequest(requestId) {
      if (this.processing) return;
      this.processing = true;
      try {
        await axios.put(`http://localhost:8000/api/tuteur/${requestId}/status`, 
            { status: 'accepté' }
        );
        toast.success('Demande de tuteur acceptée avec succès');
        this.tuteurRequests = this.tuteurRequests.filter(request => request.id !== requestId);
      } catch (error) {
        toast.error('Erreur lors de l\'acceptation de la demande');
        console.error('Error accepting tutor request:', error);
      } finally {
        this.processing = false;
      }
    },

    async rejectRequest(requestId) {
      if (this.processing) return;
      this.processing = true;
      try {
        await axios.put(`http://localhost:8000/api/tuteur/${requestId}/status`, 
            { status: 'refusé' }
        );
        toast.success('Demande de tuteur rejetée');
        this.tuteurRequests = this.tuteurRequests.filter(request => request.id !== requestId);
      } catch (error) {
        toast.error('Erreur lors du rejet de la demande');
        console.error('Error rejecting tutor request:', error);
      } finally {
        this.processing = false;
      }
    }
  }
};
</script>
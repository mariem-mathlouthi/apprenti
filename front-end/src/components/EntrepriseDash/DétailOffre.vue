<template>
  <div>
    <Navbar/>
    <div class="grid grid-cols-12 gap-4">
      <div class="col-span-3">
        <Sidebar />
      </div>
      <div class="col-span-9 mt-24 mr-24">
        <router-link to="/OffersList" class="px-6 py-2 rounded bg-purple-400 hover:bg-purple-400 text-white ml-4 mt-4 no-underline">Back</router-link>
        <div class="container mx-auto px-4 py-8">
          <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
              <div>
                <h3 class="text-lg font-medium leading-6 text-gray-900">Détails de l'offre</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Informations détaillées sur l'offre</p>
              </div>
              <div class="flex space-x-4">
                <router-link :to="'/EditOffre/'+idEntreprise+'/'+offerId">
                  <button type="button"
                    class="px-6 py-2 rounded text-black text-sm tracking-wider font-medium outline-none border-2 border-green-600 hover:bg-green-600  hover:text-white transition-all duration-300">Edit</button>
                </router-link>
                <form @submit.prevent="confirmDelete">
                  <button type="submit"
                    class="px-6 py-2 rounded text-black text-sm tracking-wider font-medium outline-none border-2 border-orange-600 hover:bg-orange-600 hover:text-white transition-all duration-300">Delete</button>
                </form>
              </div>
            </div>

            <div class="border-t border-gray-200">
              <dl>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">ID</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ offerDetails.id }}</dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">ID de l'entreprise</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ offerDetails.idEntreprise }}</dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Statut</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ offerDetails.status }}</dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Titre</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ offerDetails.titre }}</dd>
                </div>
                <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Description</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ offerDetails.description }}</dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Domaine</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ offerDetails.domaine }}</dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">dateDebut</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ offerDetails.dateDebut }}</dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">dateFin</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ offerDetails.dateFin }}</dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">typeOffre</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ offerDetails.typeOffre }}</dd>
                </div>
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Cahier de charge</dt>
                  <dd class="mt-1 text-sm text-gray-900 sm:col-span-2">{{ offerDetails.cahierCharge }}</dd>
                </div>
                <!-- Ajoutez plus de paires de termes et de définitions pour afficher d'autres détails de l'offre -->
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import axios from "axios";
import Navbar from './Navbar.vue';
import Sidebar from './SideBar.vue';

export default {
  data() {
    return {
      offerDetails: {},
      idEntreprise: "",
      offerId: "",
    };
  },
  components: {
    Navbar,
    Sidebar
  },
  created() {
    // Fetch offer details based on route parameter (offer ID)
    this.fetchOfferDetails(this.$route.params.id);
    this.offerId = this.$route.params.id;
  },
  methods: {
    async fetchOfferDetails(id) {
      let storedData = localStorage.getItem("EntrepriseAccountInfo");
      this.idEntreprise = JSON.parse(storedData).id;
      try {
        const response = await axios.get(`http://localhost:8000/api/offreDetail/${this.idEntreprise}/${id}`,
          {
            headers: {
              Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`,
            }
          }
        );
        if (response.data.check) {
          this.offerDetails = response.data.offre;
        } else {
          console.error("Error fetching offer details:", response.data.message);
        }
      } catch (error) {
        console.error("Error fetching offer details:", error);
      }
    },
    confirmDelete() {
      if (window.confirm("Êtes-vous sûr de vouloir supprimer cette offre ?")) {
        this.deleteOffre();
      }
    },
    async deleteOffre() {
      let myjson2 = {
        id: this.offerId,
      };
      try {
        const response = await axios.post("http://localhost:8000/api/deleteOffre", myjson2,
          {
            headers: {
              Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`,
            }
          }
        );
        if (response.data.delete === true) {
          this.$router.push('/OffersList');
        } else {
          toast.error("Something went wrong !", {
            autoClose: 2000,
          });
        }
      } catch (error) {
        console.error("Error", error);
      }
    }
  }
};
</script>

<style>
  .bg-gray-500 {
    margin-right: 250px; /* Assurez-vous que ce style est utilisé correctement pour votre mise en page */
  }
</style>

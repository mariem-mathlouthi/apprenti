<template>
    <div id="app">
      <SidebarMenu />
      <section id="content">
        <NavbarOne />
        <div class="col-span-9 mt-24 mr-24">
          <router-link to="/TuteursListAdmin" class="px-6 py-2 rounded bg-purple-400 hover:bg-purple-500 text-white ml-4 mt-4 no-underline">
            Retour
          </router-link>
          <div class="container mx-auto px-4 py-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
              <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
                <div>
                  <h3 class="text-lg font-medium leading-6 text-gray-900">Détails du Tuteur</h3>
                  <p class="mt-1 max-w-2xl text-sm text-gray-500">Informations détaillées sur le tuteur.</p>
                </div>
                <div class="flex space-x-4">
                  <button @click="updateTuteurStatus('accepté')" class="btn-status border-green-600 hover:bg-green-600">Accepter</button>
                  <button @click="updateTuteurStatus('en attente')" class="btn-status border-blue-600 hover:bg-blue-600">Mettre En Attente</button>
                  <button @click="updateTuteurStatus('refusé')" class="btn-status border-orange-600 hover:bg-orange-600">Refuser</button>
                  <button @click="deleteTuteur" class="btn-status border-red-600 hover:bg-red-600">Supprimer</button>
                </div>
              </div>
              <div class="border-t border-gray-200" v-if="tuteur">
                <dl>
                  <div class="detail-row"><dt>Nom Complet :</dt><dd>{{ tuteur.fullname }}</dd></div>
                  <div class="detail-row"><dt>Email :</dt><dd>{{ tuteur.email }}</dd></div>
                  <div class="detail-row"><dt>Spécialité :</dt><dd>{{ tuteur.specialite }}</dd></div>
                  <div class="detail-row"><dt>Expérience :</dt><dd>{{ tuteur.experience }} ans</dd></div>
                  <div class="detail-row"><dt>Téléphone :</dt><dd>{{ tuteur.phone }}</dd></div>
                  <div class="detail-row"><dt>Statut :</dt>
                    <dd>
                      <span class="px-3 py-1 rounded-full text-white text-xs font-semibold" :class="statusClass">
                        {{ tuteur.status }}
                      </span>
                    </dd>
                  </div>
                  <div v-if="tuteur.image" class="detail-row">
                    <dt>Photo :</dt>
                    <dd><img :src="tuteur.image" alt="Photo du tuteur" class="w-32 h-32 rounded-lg shadow"></dd>
                  </div>
                </dl>
              </div>
              <div v-else class="p-6 text-center text-red-500">Aucune information trouvée pour ce tuteur.</div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </template>
  
  <script>
  import { toast } from "vue3-toastify";
  import "vue3-toastify/dist/index.css";
  import axios from "axios";
  import NavbarOne from "./NavbarOne.vue";
  import SidebarMenu from "./SidebarMenu.vue";
  
  export default {
    name: "DetailTuteurAdmin",
    components: {
      NavbarOne,
      SidebarMenu,
    },
    data() {
      return {
        tuteur: null,
        tuteurId: this.$route.params.id,
      };
    },
    computed: {
      statusClass() {
        return {
          "bg-green-500": this.tuteur?.status === "accepté",
          "bg-blue-500": this.tuteur?.status === "en attente",
          "bg-orange-500": this.tuteur?.status === "refusé",
        };
      },
    },
    created() {
      this.fetchTuteurDetails();
    },
    methods: {
      async fetchTuteurDetails() {
        try {
          const token = JSON.parse(sessionStorage.getItem("token"));
          const response = await axios.get(`http://localhost:8000/api/tuteurs/${this.tuteurId}`, {
            headers: { Authorization: `Bearer ${token}` }
          });
          this.tuteur = response.data.tuteur || null;
          console.log("Données tuteur :", this.tuteur);
        } catch (error) {
          console.error("Erreur lors de la récupération des détails du tuteur:", error);
          toast.error("Impossible de charger les informations du tuteur.");
        }
      },
      async updateTuteurStatus(status) {
        try {
          await axios.put(`http://localhost:8000/api/tuteurs/${this.tuteurId}/status`, { status },
            {
              headers: {
                Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`,
              },
            }
          );
          this.tuteur.status = status;
          toast.success("Statut mis à jour avec succès");
        } catch (error) {
          console.error("Erreur mise à jour du statut :", error);
          toast.error("Échec de la mise à jour");
        }
      },
      async deleteTuteur() {
        if (confirm("Voulez-vous vraiment supprimer ce tuteur ?")) {
          try {
            await axios.delete(`http://localhost:8000/api/tuteurs/${this.tuteurId}`,
              {
                headers: {
                  Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`,
                },
              }
            );
            toast.success("Tuteur supprimé avec succès");
            this.$router.push("/TuteursListAdmin");
          } catch (error) {
            console.error("Erreur suppression :", error);
            toast.error("Impossible de supprimer ce tuteur");
          }
        }
      },
    },
  };
  </script>
  
  <style scoped>
  .btn-status {
    @apply px-6 py-2 rounded text-black text-sm tracking-wider font-medium outline-none border-2 transition-all duration-300;
  }
  .detail-row {
    @apply bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6;
  }
  .detail-row dt {
    @apply text-sm font-medium text-gray-500;
  }
  .detail-row dd {
    @apply mt-1 text-sm text-gray-900 sm:col-span-2;
  }
  </style>
  
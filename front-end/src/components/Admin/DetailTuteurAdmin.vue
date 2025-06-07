<template>
  <div id="app">
    <SidebarMenu />
    <section id="content">
      <NavbarOne />
      <div class="col-span-9 mt-24 mr-24">
        <router-link to="/TuteursListAdmin" class="px-6 py-2 rounded bg-purple-400 hover:bg-purple-500 text-white ml-4 mt-4 no-underline">
          Retour
        </router-link>
        
        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center h-64">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
        </div>

        <!-- Error Message -->
        <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
          {{ error }}
        </div>

        <div class="container mx-auto px-4 py-8" v-if="!loading">
          <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
              <div>
                <h3 class="text-lg font-medium leading-6 text-gray-900">Détails du Tuteur</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Informations détaillées sur le tuteur.</p>
              </div>
              <div class="flex space-x-4">
                <button 
                  @click="updateTuteurStatus('accepté')" 
                  :disabled="processing"
                  class="btn-status border-green-600 hover:bg-green-600 disabled:opacity-50"
                >
                  Accepter
                </button>
                <button 
                  @click="updateTuteurStatus('en attente')" 
                  :disabled="processing"
                  class="btn-status border-blue-600 hover:bg-blue-600 disabled:opacity-50"
                >
                  Mettre En Attente
                </button>
                <button 
                  @click="updateTuteurStatus('refusé')" 
                  :disabled="processing"
                  class="btn-status border-orange-600 hover:bg-orange-600 disabled:opacity-50"
                >
                  Refuser
                </button>
                <button 
                  @click="deleteTuteur" 
                  :disabled="processing"
                  class="btn-status border-red-600 hover:bg-red-600 disabled:opacity-50"
                >
                  Supprimer
                </button>
              </div>
            </div>
            
            <div v-if="tuteur">
              <div class="border-t border-gray-200">
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
                  <div class="detail-row">
                    <dt>CV :</dt>
                    <dd>
                      <div v-if="tuteur.cv" class="space-y-2">
                        <div class="flex items-center space-x-4">
                          <button 
                            @click="downloadCV"
                            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors"
                          >
                            <i class="fas fa-download mr-2"></i>Télécharger le CV
                          </button>
                          <span class="text-sm text-gray-600">
                            {{ getFileName(tuteur.cv) }}
                          </span>
                        </div>
                        <div v-if="isPDF(tuteur.cv)" class="mt-4">
                          <iframe 
                            :src="tuteur.cv + '#view=fitH'" 
                            class="w-full h-96 border rounded-lg"
                            frameborder="0"
                          ></iframe>
                        </div>
                      </div>
                      <div v-else class="text-sm text-gray-500 italic">
                        Aucun CV disponible
                      </div>
                    </dd>
                  </div>
                </dl>
              </div>
            </div>
            <div v-else-if="!loading" class="p-6 text-center text-red-500">
              Aucune information trouvée pour ce tuteur.
            </div>
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
      loading: true,
      error: null,
      processing: false,
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
  this.loading = true;
  this.error = null;
  try {
    const token = JSON.parse(sessionStorage.getItem("token"));
    const response = await axios.get(`http://localhost:8000/api/tuteurs/${this.tuteurId}`, {
      headers: { Authorization: `Bearer ${token}` }
    });
    
    console.log("Réponse complète de l'API:", response.data); // Debug
    
    if (response.data.tuteur) {
      this.tuteur = {
        ...response.data.tuteur,
        image: this.formatUrl(response.data.tuteur.image),
        cv: this.formatUrl(response.data.tuteur.cv) // Assurez-vous que cv existe
      };
      console.log("Données du tuteur après traitement:", this.tuteur); // Debug
    } else {
      this.tuteur = null;
    }
  } catch (error) {
    console.error("Erreur détaillée:", {
      message: error.message,
      response: error.response?.data,
      stack: error.stack
    });
    this.error = "Impossible de charger les informations du tuteur";
    toast.error(this.error);
  } finally {
    this.loading = false;
  }
},

    formatUrl(path) {
      if (!path) return null;
      return path.startsWith('http') ? path : `${window.location.origin}/${path.replace(/^\/+/g, '')}`;
    },

    async downloadCV() {
      if (!this.tuteur?.cv) return;
      
      try {
        const token = JSON.parse(sessionStorage.getItem("token"));
        const response = await axios.get(
          `http://localhost:8000/api/tuteurs/${this.tuteurId}/cv`,
          {
            headers: { Authorization: `Bearer ${token}` },
            responseType: 'blob'
          }
        );
        
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', this.getFileName(this.tuteur.cv));
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(url);
        
      } catch (error) {
        console.error("Erreur téléchargement CV:", error);
        toast.error("Échec du téléchargement du CV");
      }
    },

    getFileName(path) {
      if (!path) return 'document.pdf';
      const decoded = decodeURIComponent(path);
      return decoded.split('/').pop().split('#')[0].split('?')[0];
    },

    isPDF(path) {
      if (!path) return false;
      return path.toLowerCase().includes('.pdf');
    },

    async updateTuteurStatus(status) {
      if (this.processing) return;
      this.processing = true;
      
      try {
        await axios.put(
          `http://localhost:8000/api/tuteurs/${this.tuteurId}/status`, 
          { status },
          {
            headers: {
              Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`,
            },
          }
        );
        
        this.tuteur.status = status;
        toast.success(`Statut mis à jour: ${status}`);
      } catch (error) {
        console.error("Erreur:", error.response?.data || error.message);
        toast.error("Échec de la mise à jour");
      } finally {
        this.processing = false;
      }
    },

    async deleteTuteur() {
      if (this.processing) return;
      
      if (confirm("Voulez-vous vraiment supprimer ce tuteur ?")) {
        this.processing = true;
        try {
          await axios.delete(
            `http://localhost:8000/api/tuteurs/${this.tuteurId}`,
            {
              headers: {
                Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`,
              },
            }
          );
          toast.success("Tuteur supprimé avec succès");
          this.$router.push("/TuteursListAdmin");
        } catch (error) {
          console.error("Erreur:", error.response?.data || error.message);
          toast.error("Impossible de supprimer ce tuteur");
        } finally {
          this.processing = false;
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
<template>
  <Navbar />
  <div class="grid grid-cols-12 gap-4">
    <div class="col-span-3">
      <Sidebar />
    </div>
    <div class="col-span-9 mt-24 mr-24">
      <div class="font-[sans-serif] mt-12">
        <div class="-mt-16 mb-6 px-4">
          <div class="mx-auto max-w-6xl shadow-lg py-8 px-6 relative bg-white rounded">
            <h2 class="text-2xl text-blue-900 font-bold">Entrer les informations de l'offre</h2>
            
            <form class="mt-8 grid sm:grid-cols-2 gap-6" @submit.prevent="addOffre">
              <!-- Titre -->
              <div>
                <label class="font-semibold text-sm">Titre de l'offre</label>
                <input 
                  type="text" 
                  v-model="titre"
                  required
                  placeholder="Entrez le titre de l'offre"
                  class="w-full rounded py-2.5 px-4 mt-2 border-2 text-sm outline-[#007bff]"
                />
              </div>

              <!-- Email -->
              <div>
                <label class="font-semibold text-sm">Email de l'entreprise</label>
                <input 
                  type="email" 
                  v-model="email"
                  disabled
                  class="w-full rounded py-2.5 px-4 border-2 mt-2 text-sm outline-[#007bff] bg-gray-100"
                />
              </div>

              <!-- Domaine -->
              <div>
                <label class="font-semibold text-sm">Domaine</label>
                <select
                  v-model="domaine_id"
                  required
                  class="w-full rounded py-2.5 px-4 border-2 mt-2 text-sm outline-[#007bff]"
                >
                  <option disabled value="">Sélectionnez un domaine</option>
                  <option 
                    v-for="domaine in domaines" 
                    :key="domaine.id"
                    :value="domaine.id"
                  >
                    {{ domaine.description }}
                  </option>
                </select>
              </div>

              <!-- Type de stage -->
              <div>
                <label class="font-semibold text-sm">Type de stage</label>
                <select
                  v-model="type_stage_id"
                  required
                  class="w-full rounded py-2.5 px-4 border-2 mt-2 text-sm outline-[#007bff]"
                >
                  <option disabled value="">Sélectionnez un type</option>
                  <option 
                    v-for="typeStage in typeStages" 
                    :key="typeStage.id"
                    :value="typeStage.id"
                  >
                    {{ typeStage.description }}
                  </option>
                </select>
              </div>

              <!-- Dates -->
              <div>
                <label class="font-semibold text-sm">Date de début</label>
                <input 
                  type="date" 
                  v-model="dateDebut"
                  required
                  class="w-full rounded py-2.5 px-4 border-2 mt-2 text-sm outline-[#007bff]"
                />
              </div>

              <div>
                <label class="font-semibold text-sm">Date de fin</label>
                <input 
                  type="date" 
                  v-model="dateFin"
                  required
                  class="w-full rounded py-2.5 px-4 border-2 mt-2 text-sm outline-[#007bff]"
                />
              </div>

              <!-- Description -->
              <div class="sm:col-span-2">
                <label class="font-semibold text-sm">Description</label>
                <textarea
                  v-model="description"
                  required 
                  placeholder="Description détaillée de l'offre"
                  class="w-full rounded py-2.5 px-4 mt-2 border-2 text-sm outline-[#007bff] h-32"
                ></textarea>
              </div>

              <!-- Fichier -->
              <div class="sm:col-span-2">
                <label class="font-semibold text-sm">Cahier des charges</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                  <div class="space-y-1 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                      <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <div class="flex text-sm text-gray-600">
                      <label class="relative cursor-pointer bg-white rounded-md font-medium text-[#007bff] hover:text-blue-600">
                        <span>Télécharger un fichier</span>
                        <input 
                          type="file" 
                          class="sr-only" 
                          @change="handleFileUpload"
                          accept=".pdf,.doc,.docx,.png,.jpg,.jpeg"
                        />
                      </label>
                      <p class="pl-1">ou glisser-déposer</p>
                    </div>
                    <p class="text-xs text-gray-500">PDF, DOC, PNG, JPG jusqu'à 10MB</p>
                  </div>
                </div>
              </div>

              <!-- Boutons -->
              <div class="sm:col-span-2 flex justify-end space-x-4 mt-8">
                <button
                  type="submit"
                  class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md transition-colors"
                >
                  Publier l'offre
                </button>
                <router-link 
                  to="/OffersList"
                  class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-6 rounded-md transition-colors"
                >
                  Annuler
                </router-link>
              </div>
            </form>
          </div>
        </div>
    </div>
    </div>
  </div>
</template>
  
  <script>
  import { toast } from "vue3-toastify"
  import "vue3-toastify/dist/index.css"
  import axios from "axios"
  import Navbar from './Navbar.vue'
  import Sidebar from './SideBar.vue'
  
  export default {
    components: { Navbar, Sidebar },
  
    data() {
      return {
        idEntreprise: "",
        status: "en attente",
        titre: "",
        description: "",
        domaine_id: null,
        dateDebut: "",
        dateFin: "",
        type_stage_id: null,
        typeStages: [],
        domaines: [],
        cahierCharge: null,
        email: "",
        entrepriseName: "",
      }
    },
  
    mounted() {
      this.loadEntrepriseData()
      this.fetchTypeStages()
      this.fetchDomaines()
    },
  
    methods: {
      loadEntrepriseData() {
        const entrepriseData = localStorage.getItem("EntrepriseAccountInfo")
        if (entrepriseData) {
          const parsedData = JSON.parse(entrepriseData)
          this.idEntreprise = parsedData.id
          this.email = parsedData.email
          this.entrepriseName = parsedData.name
        } else {
          toast.error("Veuillez vous reconnecter")
          this.$router.push("/login")
        }
      },

      async fetchTypeStages() {
        try {
          const response = await axios.get("http://localhost:8000/api/type-stages",
            {
              headers: {
                Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`,
              },
            }
          )
          this.typeStages = response.data
        } catch (error) {
          toast.error("Erreur de chargement des types de stage")
          console.error("Erreur:", error)
        }
      },

      async fetchDomaines() {
        try {
          const response = await axios.get("http://localhost:8000/api/domaines",
            {
              headers: {
                Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`,
              },
            }
          )
          this.domaines = response.data
        } catch (error) {
          toast.error("Erreur de chargement des domaines")
          console.error("Erreur:", error)
        }
      },
  
      handleFileUpload(event) {
        this.cahierCharge = event.target.files[0]
      },
      // ${this.entrepriseName} a ajouté une nouvelle offre : ${this.titre}
      async sendNotification(idEtudiant, idEntreprise, idTuteur ,message, destination, type, date, appointmentId) {
        const notificationData = {
          idEtudiant: idEtudiant,
          idEntreprise: idEntreprise,
          idTuteur: idTuteur,
          message: message,
          destination: destination,
          type: type,
          visibility: "shown",
          date: date,
          appointmentId: appointmentId,
        }

        await axios.post(
          "http://localhost:8000/api/notification",
          notificationData,
          {
            headers: {
              Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`,
            },
          }
        )

      },
  
      async addOffre() {
        if (!this.idEntreprise) {
          toast.error("Identifiant entreprise non trouvé")
          return
        }
  
        const formData = new FormData()
        formData.append("idEntreprise", this.idEntreprise)
        formData.append("status", this.status)
        formData.append("titre", this.titre)
        formData.append("description", this.description)
        formData.append("domaine_id", this.domaine_id)
        formData.append("dateDebut", this.dateDebut)
        formData.append("dateFin", this.dateFin)
        formData.append("type_stage_id", this.type_stage_id)
        if (this.cahierCharge) {
          formData.append("cahierCharge", this.cahierCharge)
        }
  
        try {
          // Création de l'offre
          const response = await axios.post(
            "http://localhost:8000/api/addOffre",
            formData,
            {
              headers: {
                "Content-Type": "multipart/form-data",
                Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`,
              }
            }
          )
  
          if (response.data.check) {
            // Création de la notification
            const notificationMessage = `${this.entrepriseName} a ajouté une nouvelle offre : ${this.titre}`
            await this.sendNotification(0, this.idEntreprise, null, notificationMessage, "Etudiant", "offre", new Date(), null);
  
            toast.success("Offre publiée avec succès !")
            this.$router.push("/OffersList")
          }
        } catch (error) {
          console.error("Erreur:", error)
          const errorMessage = error.response?.data?.error || "Échec de la publication"
          toast.error(`Erreur : ${errorMessage}`)
        }
      }
    }
  }
  </script>
  
  <style scoped>
  /* Styles cohérents avec AddCours */
  .input-field {
    @apply w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-colors;
  }
  
  .btn-primary {
    @apply bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 px-6 rounded-lg transition-colors;
  }
  
  .btn-secondary {
    @apply bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2.5 px-6 rounded-lg transition-colors;
  }
  
  .file-upload {
    @apply border-dashed hover:border-indigo-200 transition-colors;
  }
  </style>
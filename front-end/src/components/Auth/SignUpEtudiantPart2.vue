<template>
  <main class="w-full flex">
    <div class="flex-1 hidden lg:block">
      <img src="./std.jpg" class="w-full h-screen object-cover" />
    </div>
    <div class="flex-1 flex items-center justify-center h-screen">
      <div class="w-full max-w-md space-y-8 px-4 bg-white text-gray-600 sm:px-0">
        <div class="mt-5 space-y-2">
          <h3 class="text-gray-800 text-2xl font-bold sm:text-3xl">
            Finalisation du profil
          </h3>
          <p class="">
            Déjà inscrit ?
            <router-link
              to="/signin"
              class="font-medium text-indigo-600 hover:text-indigo-500"
            >
              Connectez-vous
            </router-link>
          </p>
        </div>

        <form @submit.prevent="signUp" class="space-y-5">
          <div class="grid grid-cols-2 gap-x-3">
            <div>
              <label class="font-medium">Niveau d'études</label>
              <select
                v-model="selectedNiveauId"
                required
                class="w-full mt-2 px-3 py-2 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg"
              >
                <option disabled value="">Sélectionnez un niveau</option>
                <option 
                  v-for="niveau in niveaux" 
                  :key="niveau.id"
                  :value="niveau.id"
                >
                  {{ niveau.description }}
                </option>
              </select>
            </div>

            <div>
              <label class="font-medium">Domaine d'études</label>
              <select
                v-model="selectedDomaineId"
                required
                class="w-full mt-2 px-3 py-2 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg"
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
          </div>

          <div class="grid grid-cols-2 gap-x-3">
            <div>
              <label class="font-medium">Type de stage</label>
              <select
                v-model="selectedTypeStageId"
                required
                class="w-full mt-2 px-3 py-2 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg"
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
            
            <div>
              <label class="font-medium">Spécialité</label>
              <select
                v-model="selectedSpecialiteId"
                required
                class="w-full mt-2 px-3 py-2 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg"
              >
                <option disabled value="">Sélectionnez une spécialité</option>
                <option 
                  v-for="specialite in specialites" 
                  :key="specialite.id"
                  :value="specialite.id"
                >
                  {{ specialite.description }}
                </option>
              </select>
            </div>
          </div>

          <div>
            <label class="font-medium">Établissement</label>
            <input
              v-model="etablissement"
              type="text"
              required
              class="w-full mt-2 px-3 py-2 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg"
            />
          </div>

          <button
            :disabled="loading"
            class="w-full px-4 py-2 text-white font-medium bg-indigo-600 hover:bg-indigo-500 disabled:bg-gray-400 rounded-lg duration-150"
          >
            {{ loading ? 'Création en cours...' : 'Créer mon compte' }}
          </button>
        </form>
      </div>
    </div>
  </main>
</template>

<script>
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import axios from "axios";

export default {
  data() {
    return {
      etablissement: "",
      selectedNiveauId: null,
      selectedDomaineId: null,
      selectedTypeStageId: null,
      selectedSpecialiteId: null,
      image: "default.jpg",
      niveaux: [],
      domaines: [],
      specialites: [],
      typeStages: [],
      loading: false
    };
  },

  async mounted() {
    await Promise.all([
      this.fetchNiveaux(),
      this.fetchDomaines(),
      this.fetchSpecialites(),
      this.fetchTypeStages()
    ]);
  },

  methods: {
    async fetchNiveaux() {
      try {
        const response = await axios.get("http://localhost:8000/api/niveaux");
        this.niveaux = response.data;
      } catch (error) {
        toast.error("Erreur de chargement des niveaux", { autoClose: 2000 });
        console.error("Erreur:", error);
      }
    },

    async fetchDomaines() {
      try {
        const response = await axios.get("http://localhost:8000/api/domaines");
        this.domaines = response.data;
      } catch (error) {
        toast.error("Erreur de chargement des domaines", { autoClose: 2000 });
        console.error("Erreur:", error);
      }
    },

    async fetchSpecialites() {
      try {
        const response = await axios.get("http://localhost:8000/api/specialites");
        this.specialites = response.data;
      } catch (error) {
        toast.error("Erreur de chargement des spécialités", { autoClose: 2000 });
        console.error("Erreur:", error);
      }
    },

    async fetchTypeStages() {
      try {
        const response = await axios.get("http://localhost:8000/api/type-stages");
        this.typeStages = response.data;
      } catch (error) {
        toast.error("Erreur de chargement des types de stage", { autoClose: 2000 });
        console.error("Erreur:", error);
      }
    },

    async signUp() {
      this.loading = true;
      const storedData = JSON.parse(localStorage.getItem("Etudiant"));
      
      const payload = {
        fullname: storedData.fullname,
        niveau_id: this.selectedNiveauId,
        cin: storedData.cin,
        email: storedData.email,
        password: storedData.password,
        domaine_id: this.selectedDomaineId,
        type_stage_id: this.selectedTypeStageId,
        specialite_id: this.selectedSpecialiteId,
        etablissement: this.etablissement,
        image: this.image
      };

      try {
        const response = await axios.post(
          "http://localhost:8000/api/singupEtudiant",
          payload
        );

        if (response.data.check) {
          toast.success("Compte créé avec succès !", { 
            autoClose: 2000,
            onClose: () => {
              localStorage.removeItem("Etudiant");
              this.$router.push("/signin");
            }
          });
        } else {
          toast.error(response.data.message || "Erreur de création", { autoClose: 2000 });
        }
      } catch (error) {
        const errorMessage = error.response?.data?.message || "Erreur de connexion au serveur";
        toast.error(errorMessage, { autoClose: 2000 });
        console.error("Erreur détaillée:", error);
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>
<template>
  <main class="w-full flex">
    <div class="flex-1 hidden lg:block">
      <img src="https://images.unsplash.com/photo-1697135807547-5fa9fd22d9ec?auto=format&fit=crop&q=80&w=3387&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" 
           class="w-full h-screen object-cover" />
    </div>
    <div class="flex-1 flex items-center justify-center h-screen">
      <div class="w-full max-w-md space-y-8 px-4 bg-white text-gray-600 sm:px-0">
        <div class="mt-5 space-y-2">
          <h3 class="text-gray-800 text-2xl font-bold sm:text-3xl">
            Inscription Entreprise
          </h3>
          <p class="">
            Déjà un compte ?
            <router-link
              to="/signin"
              class="font-medium text-indigo-600 hover:text-indigo-500"
            >Connectez-vous</router-link>
          </p>
        </div>

        <form @submit.prevent="signUp" class="space-y-5">
          <div class="grid grid-cols-2 gap-x-3">
            <div>
              <label class="font-medium">Nom de l'entreprise*</label>
              <input
                v-model="name"
                type="text"
                required
                class="w-full mt-2 px-3 py-2 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg"
              />
            </div>
            
            <div>
              <label class="font-medium">Secteur d'activité*</label>
              <select
                v-model="secteur_id"
                required
                class="w-full mt-2 px-3 py-2 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg"
              >
                <option value="" disabled>Sélectionnez un secteur</option>
                <option 
                  v-for="secteur in sectors" 
                  :key="secteur.id" 
                  :value="secteur.id"
                >
                  {{ secteur.description }}
                </option>
              </select>
            </div>
          </div>

          <div>
            <label class="font-medium">Site web*</label>
            <input
              v-model="link"
              type="url"
              required
              placeholder="https://"
              class="w-full mt-2 px-3 py-2 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg"
            />
          </div>
          
          <div>
            <label class="font-medium">Description*</label>
            <textarea
              v-model="description"
              required
              rows="3"
              placeholder="Décrivez votre entreprise en quelques mots..."
              class="w-full mt-2 px-3 py-2 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg"
            ></textarea>
          </div>

          <!-- Champ d'upload de logo -->
          <div class="pt-4 border-t border-gray-200">
            <label class="block font-medium mb-3">Logo (Optionnel)</label>
            <div class="flex flex-col items-center justify-center p-6 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
              <template v-if="logoPreview">
                <img :src="logoPreview" class="h-32 w-32 rounded-full object-cover mb-4 shadow-md" />
                <button
                  @click.prevent="removeLogo"
                  class="text-sm text-red-600 hover:text-red-800 font-medium"
                >
                  Supprimer
                </button>
              </template>
              <template v-else>
                <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <p class="text-sm text-gray-500 mb-2">Ajoutez un logo</p>
                <label class="cursor-pointer">
                  <span class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 transition-colors">
                    Choisir une image
                  </span>
                  <input 
                    type="file" 
                    @change="handleLogoUpload"
                    accept="image/*"
                    class="hidden"
                  />
                </label>
              </template>
            </div>
            <p class="mt-2 text-xs text-gray-500">Formats acceptés : JPG, PNG (max 2MB)</p>
          </div>

          <div class="max-w-lg mx-auto px-4 sm:px-0">
            <ul aria-label="Steps" class="flex items-center">
              <li v-for="(item, idx) in stepsCount" :key="idx" 
                  :aria-current="currentStep == idx + 1 ? 'step' : false" 
                  class="flex-1 last:flex-none flex items-center">
                <div :class="{
                  'w-8 h-8 rounded-full border-2 flex-none flex items-center justify-center': true, 
                  'bg-indigo-600 border-indigo-600': currentStep > idx + 1, 
                  'border-indigo-600': currentStep == idx + 1
                }">
                  <span :class="{
                    'w-2.5 h-2.5 rounded-full bg-indigo-600': true, 
                    'hidden': currentStep != idx + 1
                  }"></span>
                  <template v-if="currentStep > idx + 1">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" 
                         stroke="currentColor" class="w-5 h-5 text-white">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                  </template>
                </div>
                <hr :class="{
                  'w-full border': true, 
                  'hidden': idx + 1 == stepsCount.length, 
                  'border-indigo-600': currentStep > idx + 1
                }" />
              </li>
            </ul>
          </div>

          <button
            :disabled="loading"
            class="w-full px-4 py-2 text-white font-medium bg-indigo-600 hover:bg-indigo-500 disabled:bg-gray-400 rounded-lg duration-150"
          >
            <span v-if="loading">Création en cours...</span>
            <span v-else>Créer mon compte</span>
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
      stepsCount: [1, 2],
      currentStep: 2,
      numeroSIRET: "",
      email: "",
      password: "",
      name: "",
      secteur_id: null,
      sectors: [],
      logo: null,
      logoPreview: null,
      logoFile: null,
      description: "",
      link: "",
      loading: false
    };
  },
  methods: {
    handleLogoUpload(event) {
      const file = event.target.files[0];
      
      // Vérification de la taille (max 2MB)
      if (file.size > 2 * 1024 * 1024) {
        toast.error("Image trop lourde! Maximum 2MB autorisé.", { autoClose: 2000 });
        return;
      }

      const reader = new FileReader();
      reader.onload = (e) => {
        this.logoPreview = e.target.result;
        this.logo = e.target.result.split(',')[1]; // Stocke seulement la partie base64
      };
      reader.readAsDataURL(file);
      this.logoFile = file;
    },
    removeLogo() {
      this.logoPreview = null;
      this.logo = null;
      this.logoFile = null;
    },
    async signUp() {
      this.loading = true;
      
      // Récupération des données depuis le localStorage
      let storedData = JSON.parse(localStorage.getItem("Entreprise"));
      this.numeroSIRET = storedData.numeroSIRET;
      this.email = storedData.email;
      this.password = storedData.password;  

      // Préparation des données à envoyer
      let payload = {
        numeroSIRET: this.numeroSIRET,
        email: this.email,
        password: this.password,
        name: this.name,
        secteur_id: this.secteur_id,
        logo: this.logo, // Base64 ou null
        description: this.description,
        link: this.link
      };

      try {
        const response = await axios.post(
          "http://localhost:8000/api/signupEntreprise",
          payload
        );
        
        if (response.data.check === true) {
          toast.success("Compte créé avec succès!", { 
            autoClose: 2000,
            onClose: () => {
              localStorage.removeItem("Entreprise");
              this.$router.push("/signin");
            }
          });
        } else {
          toast.error(response.data.message || "Erreur lors de la création", { 
            autoClose: 2000 
          });
        }
      } catch (error) {
        const errorMessage = error.response?.data?.message || "Erreur de connexion au serveur";
        toast.error(errorMessage, { autoClose: 2000 });
        console.error("Détail de l'erreur:", error);
      } finally {
        this.loading = false;
      }
    },
    async fetchSectors() {
      try {
        const response = await axios.get("http://localhost:8000/api/secteurs");
        this.sectors = response.data;
      } catch (error) {
        console.error("Erreur lors du chargement des secteurs:", error);
        toast.error("Échec du chargement des secteurs", { autoClose: 2000 });
      }
    }
  },
  mounted() {
    this.fetchSectors();
  },
  watch: {
    description(value) {
      if (value !== "") {
        this.currentStep = 2;
      } else {
        this.currentStep = 1;
      }
    }
  }
};
</script>
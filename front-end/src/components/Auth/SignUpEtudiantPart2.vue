<template>
  <main class="w-full flex">
    <div class="flex-1 hidden lg:block">
      <img src="./std.jpg" class="w-full h-screen object-cover" />
    </div>
    <div class="flex-1 flex items-center justify-center h-screen">
      <div class="w-full max-w-md space-y-8 px-4 bg-white text-gray-600 sm:px-0">
        <div class="mt-5 space-y-2">
          <h3 class="text-gray-800 text-2xl font-bold sm:text-3xl">
            Complete your profile
          </h3>
          <p class="">
            Already registered?
            <router-link
              to="/signin"
              class="font-medium text-indigo-600 hover:text-indigo-500"
            >
              Sign in
            </router-link>
          </p>
        </div>

        <form @submit.prevent="signUp" class="space-y-5">
          <div>
            <label class="font-medium">Domaine</label>
            <select
              v-model="selectedDomaineId"
              required
              class="w-full mt-2 px-3 py-2 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg"
            >
              <option disabled value="">Sélectionner votre domaine</option>
              <option 
                v-for="domaine in domaines" 
                :key="domaine.id"
                :value="domaine.id"
              >
                {{ domaine.description }}
              </option>
            </select>
          </div>

          <div>
            <label class="font-medium">Specialité</label>
            <select
              v-model="selectedSpecialiteId"
              required
              class="w-full mt-2 px-3 py-2 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg"
            >
              <option disabled value="">Sélectionner une specialité</option>
              <option 
                v-for="specialite in specialites" 
                :key="specialite.id"
                :value="specialite.id"
              >
                {{ specialite.description }}
              </option>
            </select>
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

          <!-- Champ d'image en bas du formulaire -->
          <div class="pt-4 border-t border-gray-200">
            <label class="block font-medium mb-3">Profile Image (Optional)</label>
            <div class="flex flex-col items-center justify-center p-6 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
              <template v-if="imagePreview">
                <img :src="imagePreview" class="h-32 w-32 rounded-full object-cover mb-4 shadow-md" />
                <button
                  @click.prevent="removeImage"
                  class="text-sm text-red-600 hover:text-red-800 font-medium"
                >
                  Remove Image
                </button>
              </template>
              <template v-else>
                <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <p class="text-sm text-gray-500 mb-2">Upload a profile picture</p>
                <label class="cursor-pointer">
                  <span class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                    Select Image
                  </span>
                  <input 
                    type="file" 
                    @change="handleImageUpload"
                    accept="image/*"
                    class="hidden"
                  />
                </label>
              </template>
            </div>
            <p class="mt-2 text-xs text-gray-500">We accept JPG, PNG formats (max 2MB)</p>
          </div>

          <button
            :disabled="loading"
            class="w-full px-4 py-2 text-white font-medium bg-indigo-600 hover:bg-indigo-500 disabled:bg-gray-400 rounded-lg duration-150"
          >
            {{ loading ? 'Creating account...' : 'Create my account' }}
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
      selectedDomaineId: null,
      selectedSpecialiteId: null,
      image: null,
      imagePreview: null,
      domaines: [],
      specialites: [],
      loading: false
    };
  },

  async mounted() {
    await Promise.all([
      this.fetchDomaines(),
      this.fetchSpecialites()
    ]);
  },

  methods: {
    handleImageUpload(event) {
      const file = event.target.files[0];
      if (file) {
        // Vérification de la taille du fichier (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
          toast.error("Image size should be less than 2MB", { autoClose: 2000 });
          return;
        }
        
        // Convertir l'image en base64 string
        const reader = new FileReader();
        reader.onload = (e) => {
          this.imagePreview = e.target.result;
          this.image = e.target.result.split(',')[1]; // Stocke seulement la partie base64
        };
        reader.readAsDataURL(file);
      }
    },

    removeImage() {
      this.imagePreview = null;
      this.image = null;
    },

    async fetchDomaines() {
      try {
        const response = await axios.get("http://localhost:8000/api/domaines");
        this.domaines = response.data;
      } catch (error) {
        toast.error("Error loading fields", { autoClose: 2000 });
        console.error("Error:", error);
      }
    },

    async fetchSpecialites() {
      try {
        const response = await axios.get("http://localhost:8000/api/specialites");
        this.specialites = response.data;
      } catch (error) {
        toast.error("Error loading specialties", { autoClose: 2000 });
        console.error("Error:", error);
      }
    },

    async signUp() {
      this.loading = true;
      const storedData = JSON.parse(localStorage.getItem("Etudiant"));
      
      const payload = {
        fullname: storedData.fullname,
        niveau_id: storedData.niveau_id,
        cin: storedData.cin,
        email: storedData.email,
        password: storedData.password,
        domaine_id: this.selectedDomaineId,
        specialite_id: this.selectedSpecialiteId,
        etablissement: this.etablissement,
        image: this.image // Envoie la string base64
      };

      try {
        const response = await axios.post(
          "http://localhost:8000/api/singupEtudiant",
          payload
        );

        if (response.data.check) {
          toast.success("Account created successfully!", { 
            autoClose: 2000,
            onClose: () => {
              localStorage.removeItem("Etudiant");
              this.$router.push("/signin");
            }
          });
        } else {
          toast.error(response.data.message || "Creation error", { autoClose: 2000 });
        }
      } catch (error) {
        const errorMessage = error.response?.data?.message || "Server connection error";
        toast.error(errorMessage, { autoClose: 2000 });
        console.error("Detailed error:", error);
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>
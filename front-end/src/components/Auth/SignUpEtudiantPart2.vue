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
          <div class="grid grid-cols-2 gap-x-3">
            <div>
              <label class="font-medium">Field of study</label>
              <select
                v-model="selectedDomaineId"
                required
                class="w-full mt-2 px-3 py-2 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg"
              >
                <option disabled value="">Select a field</option>
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
              <label class="font-medium">Internship type</label>
              <select
                v-model="selectedTypeStageId"
                required
                class="w-full mt-2 px-3 py-2 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg"
              >
                <option disabled value="">Select a type</option>
                <option 
                  v-for="typeStage in typeStages" 
                  :key="typeStage.id"
                  :value="typeStage.id"
                >
                  {{ typeStage.description }}
                </option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-x-3">
            <div>
              <label class="font-medium">Specialty</label>
              <select
                v-model="selectedSpecialiteId"
                required
                class="w-full mt-2 px-3 py-2 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg"
              >
                <option disabled value="">Select a specialty</option>
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
            <label class="font-medium">Institution</label>
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
      selectedTypeStageId: null,
      selectedSpecialiteId: null,
      image: "default.jpg",
      domaines: [],
      specialites: [],
      typeStages: [],
      loading: false
    };
  },

  async mounted() {
    await Promise.all([
      this.fetchDomaines(),
      this.fetchSpecialites(),
      this.fetchTypeStages()
    ]);
  },

  methods: {
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

    async fetchTypeStages() {
      try {
        const response = await axios.get("http://localhost:8000/api/type-stages");
        this.typeStages = response.data;
      } catch (error) {
        toast.error("Error loading internship types", { autoClose: 2000 });
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
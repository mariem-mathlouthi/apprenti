<template>
  <main class="w-full flex">
    <div class="flex-1 hidden lg:block">
      <img src="https://images.unsplash.com/photo-1697135807547-5fa9fd22d9ec?auto=format&fit=crop&q=80&w=3387&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" class="w-full h-screen object-cover" />
    </div>
    <div class="flex-1 flex items-center justify-center h-screen">
      <div class="w-full max-w-md space-y-8 px-4 bg-white text-gray-600 sm:px-0">
        <div class="mt-5 space-y-2">
          <h3 class="text-gray-800 text-2xl font-bold sm:text-3xl">
            Finalisation du profil
          </h3>
        </div>

        <form @submit.prevent="signUp" class="space-y-5">
          <div>
            <label class="font-medium">Nom complet</label>
            <input
              type="text"
              v-model="fullname"
              required
              class="w-full mt-2 px-3 py-2 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg"
            />
          </div>
          
          <div>
            <label class="font-medium">Spécialité</label>
            <input
              type="text"
              v-model="specialite"
              required
              class="w-full mt-2 px-3 py-2 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg"
            />
          </div>

          <div>
            <label class="font-medium">Années d'expérience</label>
            <input
              type="number"
              v-model="experience"
              required
              class="w-full mt-2 px-3 py-2 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg"
            />
          </div>

          

          <div class="max-w-lg mx-auto px-4 sm:px-0">
            <ul aria-label="Steps" class="flex items-center">
              <li v-for="(item, idx) in stepsCount" :key="idx" :aria-current="currentStep == idx + 1 ? 'step' : false" class="flex-1 last:flex-none flex items-center">
                <div :class="{'w-8 h-8 rounded-full border-2 flex-none flex items-center justify-center': true, 'bg-indigo-600 border-indigo-600': currentStep > idx + 1, 'border-indigo-600': currentStep == idx + 1}">
                  <span :class="{'w-2.5 h-2.5 rounded-full bg-indigo-600': true, 'hidden': currentStep != idx + 1}"></span>
                  <template v-if="currentStep > idx + 1">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-white">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                  </template>
                </div>
                <hr :class="{'w-full border': true, 'hidden': idx + 1 == stepsCount.length, 'border-indigo-600': currentStep > idx + 1}" />
              </li>
            </ul>
          </div>

          <button class="w-full px-4 py-2 text-white font-medium bg-gray-800 hover:bg-gray-700 active:bg-gray-700 rounded-lg duration-150">
            Créer mon compte
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
      fullname: "",
      specialite: "",
      experience: 0,
    
    };
  },
  methods: {
    async signUp() {
      const storedData = JSON.parse(localStorage.getItem("Tuteur"));
      
      const tuteurData = {
        ...storedData,
        fullname: this.fullname,
        specialite: this.specialite,
        experience: this.experience,
       
      };

      try {
        const response = await axios.post(
          "http://localhost:8000/api/signupTuteur",
          tuteurData
        );

        if (response.data.check) {
          toast.success("Compte créé avec succès !", { autoClose: 2000 });
          localStorage.removeItem("Tuteur");
          this.$router.push("/signin");
        } else {
          toast.error(response.data.message || "Erreur lors de la création", { autoClose: 2000 });
        }
      } catch (error) {
        toast.error("Erreur de connexion au serveur", { autoClose: 2000 });
        console.error(error);
      }
    }
  },
  watch: {
    fullname(val) {
      this.currentStep = val ? 2 : 1;
    }
  }
};
</script>
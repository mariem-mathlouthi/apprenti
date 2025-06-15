<template>
  <main class="w-full flex">
    <div class="flex-1 hidden lg:block">
      <img src="./std.jpg" class="w-full h-screen object-cover" />
    </div>
    <div class="flex-1 flex items-center justify-center h-screen">
      <div class="w-full max-w-md space-y-8 px-4 bg-white text-gray-600 sm:px-0">
        <div class="mt-5 space-y-2">
          <h3 class="text-gray-800 text-2xl font-bold sm:text-3xl">Inscription</h3>
          <p class="">
            Vous avez déjà un compte ?
            <router-link
              to="/signin"
              class="font-medium text-indigo-600 hover:text-indigo-500"
            >
              Connectez-vous
            </router-link>
          </p>
        </div>

        <form @submit.prevent="next" class="space-y-5">
          <div class="grid grid-cols-2 gap-x-3">
            <div>
              <label class="font-medium">Nom Complet</label>
              <input
                v-model="fullname"
                type="text"
                required
                @blur="validateFullName"
                class="w-full mt-2 px-3 py-2 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg"
              />
              <p v-if="errors.fullname" class="text-red-500 text-sm mt-1">{{ errors.fullname }}</p>
            </div>
            <div>
              <label class="font-medium">Niveau</label>
              <select
                v-model="selectedNiveauId"
                required
                @change="validateNiveau"
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
              <p v-if="errors.selectedNiveauId" class="text-red-500 text-sm mt-1">{{ errors.selectedNiveauId }}</p>
            </div>
          </div>

          <div>
            <label class="font-medium">CIN</label>
            <input
              v-model="cin"
              type="text"
              required
              @blur="validateCin"
              class="w-full mt-2 px-3 py-2 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg"
            />
            <p v-if="errors.cin" class="text-red-500 text-sm mt-1">{{ errors.cin }}</p>
          </div>

          <div>
            <label class="font-medium">Email</label>
            <input
              v-model="email"
              type="email"
              required
              @blur="validateEmail"
              class="w-full mt-2 px-3 py-2 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg"
            />
            <p v-if="errors.email" class="text-red-500 text-sm mt-1">{{ errors.email }}</p>
          </div>

          <div class="grid grid-cols-2 gap-x-3">
            <div>
              <label class="font-medium">Mot de passe</label>
              <input
                v-model="password"
                type="password"
                required
                @blur="validatePassword"
                class="w-full mt-2 px-3 py-2 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg"
              />
              <p v-if="errors.password" class="text-red-500 text-sm mt-1">{{ errors.password }}</p>
            </div>
            <div>
              <label class="font-medium">Confirmer le mot de passe</label>
              <input
                v-model="confirmPassword"
                type="password"
                required
                @blur="validateConfirmPassword"
                class="w-full mt-2 px-3 py-2 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg"
              />
              <p v-if="errors.confirmPassword" class="text-red-500 text-sm mt-1">{{ errors.confirmPassword }}</p>
            </div>
          </div>

          <div class="max-w-lg mx-auto px-4 sm:px-0">
            <ul aria-label="Steps" class="flex items-center">
              <li 
                v-for="(item, idx) in stepsCount" 
                :key="idx" 
                :aria-current="currentStep == idx + 1 ? 'step' : false" 
                class="flex-1 last:flex-none flex items-center"
              >
                <div :class="{
                  'w-8 h-8 rounded-full border-2 flex-none flex items-center justify-center': true, 
                  'bg-indigo-600 border-indigo-600': currentStep > idx + 1, 
                  'border-indigo-600': currentStep == idx + 1
                }">
                  <span 
                    :class="{
                      'w-2.5 h-2.5 rounded-full bg-indigo-600': true, 
                      'hidden': currentStep != idx + 1
                    }"
                  ></span>
                  <template v-if="currentStep > idx + 1">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-white">
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
            class="w-full px-4 py-2 text-white font-medium bg-indigo-600 hover:bg-indigo-500 rounded-lg duration-150"
          >
            Suivant
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
      currentStep: 1,
      fullname: "",
      selectedNiveauId: null,
      cin: "",
      email: "",
      password: "",
      confirmPassword: "",
      niveaux: [],
      errors: {
        fullname: "",
        selectedNiveauId: "",
        cin: "",
        email: "",
        password: "",
        confirmPassword: ""
      }
    };
  },
  methods: {
    validateFullName() {
      if (!this.fullname) {
        this.errors.fullname = "Le nom complet est requis";
      } else if (this.fullname.length < 3) {
        this.errors.fullname = "Le nom doit contenir au moins 3 caractères";
      } else {
        this.errors.fullname = "";
      }
    },
    
    validateNiveau() {
      if (!this.selectedNiveauId) {
        this.errors.selectedNiveauId = "Veuillez sélectionner un niveau";
      } else {
        this.errors.selectedNiveauId = "";
      }
    },
    
    validateCin() {
      if (!this.cin) {
        this.errors.cin = "Le CIN est requis";
      } else if (!/^\d{8}$/.test(this.cin)) {
        this.errors.cin = "Le CIN doit contenir 8 chiffres";
      } else {
        this.errors.cin = "";
      }
    },
    
    validateEmail() {
      if (!this.email) {
        this.errors.email = "L'email est requis";
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email)) {
        this.errors.email = "Veuillez entrer un email valide";
      } else {
        this.errors.email = "";
      }
    },
    
    validatePassword() {
      if (!this.password) {
        this.errors.password = "Le mot de passe est requis";
      } else if (this.password.length < 6) {
        this.errors.password = "Le mot de passe doit contenir au moins 6 caractères";
      } else {
        this.errors.password = "";
      }
    },
    
    validateConfirmPassword() {
      if (!this.confirmPassword) {
        this.errors.confirmPassword = "Veuillez confirmer votre mot de passe";
      } else if (this.password !== this.confirmPassword) {
        this.errors.confirmPassword = "Les mots de passe ne correspondent pas";
      } else {
        this.errors.confirmPassword = "";
      }
    },
    
    validateForm() {
      this.validateFullName();
      this.validateNiveau();
      this.validateCin();
      this.validateEmail();
      this.validatePassword();
      this.validateConfirmPassword();
      
      return !Object.values(this.errors).some(error => error !== "");
    },
    
    async next() {
      if (!this.validateForm()) {
        toast.error("Veuillez corriger les erreurs dans le formulaire", { autoClose: 2000 });
        return;
      }

      const etudiant = {
        fullname: this.fullname,
        niveau_id: this.selectedNiveauId,
        cin: this.cin,
        email: this.email,
        password: this.password
      };

      try {
        localStorage.setItem("Etudiant", JSON.stringify(etudiant));
        this.$router.push("/signupEtudiantPart2");
      } catch (error) {
        toast.error("Erreur lors de l'enregistrement des données !", { autoClose: 2000 });
      }
    },
    
    async fetchNiveaux() {
      try {
        const response = await axios.get("http://localhost:8000/api/niveaux");
        this.niveaux = response.data;
      } catch (error) {
        toast.error("Échec du chargement des niveaux", { autoClose: 2000 });
        console.error("Erreur:", error);
      }
    }
  },
  mounted() {
    this.fetchNiveaux();
  },
  watch: {
    confirmPassword(value) {
      this.currentStep = value === this.password ? 2 : 1;
    }
  }
};
</script>
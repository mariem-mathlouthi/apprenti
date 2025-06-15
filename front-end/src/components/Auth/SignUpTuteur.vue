<template>
  <main class="w-full flex">
    <div class="flex-1 hidden lg:block">
      <img src="https://images.unsplash.com/photo-1697135807547-5fa9fd22d9ec?auto=format&fit=crop&q=80&w=3387&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" class="w-full h-screen object-cover" />
    </div>
    <div class="flex-1 flex items-center justify-center h-screen">
      <div class="w-full max-w-md space-y-8 px-4 bg-white text-gray-600 sm:px-0">
        <div class="">
          <img src="https://floatui.com/logo.svg" width="150" class="lg:hidden" />
          <div class="mt-5 space-y-2">
            <h3 class="text-gray-800 text-2xl font-bold sm:text-3xl">
              Inscription Tuteur
            </h3>
            <p class="">
              Vous avez déjà un compte ?
              <router-link
                to="/signin"
                class="font-medium text-indigo-600 hover:text-indigo-500"
                >Se connecter</router-link>
            </p>
          </div>
        </div>

        <form @submit.prevent="next" class="space-y-5">
          <div>
            <label class="font-medium">Email*</label>
            <input
              type="email"
              v-model="email"
              @input="clearError('email')"
              @blur="validateEmail"
              required
              class="w-full mt-2 px-3 py-2 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg"
              :class="{ 'border-red-500': errors.email }"
            />
            <p v-if="errors.email" class="text-red-500 text-sm mt-1">{{ errors.email }}</p>
          </div>
          
          <div>
            <label class="font-medium">Téléphone*</label>
            <input
              type="tel"
              v-model="phone"
              @input="clearError('phone')"
              @blur="validatePhone"
              required
              class="w-full mt-2 px-3 py-2 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg"
              :class="{ 'border-red-500': errors.phone }"
            />
            <p v-if="errors.phone" class="text-red-500 text-sm mt-1">{{ errors.phone }}</p>
          </div>
          
          <div>
            <label class="font-medium">Mot de passe*</label>
            <div class="relative">
              <input
                :type="showPassword ? 'text' : 'password'"
                v-model="password"
                @input="clearError('password')"
                @blur="validatePassword"
                required
                class="w-full mt-2 px-3 py-2 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg"
                :class="{ 'border-red-500': errors.password }"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500"
              >
                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
              </button>
            </div>
            <p v-if="errors.password" class="text-red-500 text-sm mt-1">{{ errors.password }}</p>
            <div v-if="password && errors.password" class="mt-2 text-xs">
              <p :class="{'text-red-500': !hasMinLength}">• 8 caractères minimum</p>
              <p :class="{'text-red-500': !hasLetter}">• Au moins une lettre</p>
              <p :class="{'text-red-500': !hasNumber}">• Au moins un chiffre</p>
            </div>
          </div>
          
          <div>
            <label class="font-medium">Confirmer le mot de passe*</label>
            <div class="relative">
              <input
                :type="showConfirmPassword ? 'text' : 'password'"
                v-model="confirmPassword"
                @input="clearError('confirmPassword')"
                @blur="validateConfirmPassword"
                required
                class="w-full mt-2 px-3 py-2 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg"
                :class="{ 'border-red-500': errors.confirmPassword }"
              />
              <button
                type="button"
                @click="showConfirmPassword = !showConfirmPassword"
                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500"
              >
                <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
              </button>
            </div>
            <p v-if="errors.confirmPassword" class="text-red-500 text-sm mt-1">{{ errors.confirmPassword }}</p>
          </div>

          <div class="max-w-lg mx-auto px-4 sm:px-0">
            <ul aria-label="Steps" class="flex items-center">
              <li v-for="(item, idx) in stepsCount" :key="idx" :aria-current="currentStep == idx + 1 ? 'step' : false" class="flex-1 last:flex-none flex items-center">
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
            class="w-full px-4 py-2 text-white font-medium bg-indigo-600 hover:bg-indigo-500 rounded-lg duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="!isFormValid"
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

export default {
  data() {
    return {
      stepsCount: [1, 2],
      currentStep: 1,
      email: "",
      phone: "",
      password: "",
      confirmPassword: "",
      showPassword: false,
      showConfirmPassword: false,
      errors: {
        email: "",
        phone: "",
        password: "",
        confirmPassword: ""
      },
      passwordCriteria: {
        hasMinLength: false,
        hasLetter: false,
        hasNumber: false
      }
    };
  },
  computed: {
    isFormValid() {
      return (
        !this.errors.email &&
        !this.errors.phone &&
        !this.errors.password &&
        !this.errors.confirmPassword &&
        this.email &&
        this.phone &&
        this.password &&
        this.confirmPassword &&
        this.passwordCriteria.hasMinLength &&
        this.passwordCriteria.hasLetter &&
        this.passwordCriteria.hasNumber
      );
    },
    hasMinLength() {
      return this.password.length >= 8;
    },
    hasLetter() {
      return /[a-zA-Z]/.test(this.password);
    },
    hasNumber() {
      return /\d/.test(this.password);
    }
  },
  methods: {
    clearError(field) {
      this.errors[field] = "";
    },
    validateEmail() {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!this.email) {
        this.errors.email = "L'email est requis";
      } else if (!emailRegex.test(this.email)) {
        this.errors.email = "Veuillez entrer un email valide";
      }
    },
    validatePhone() {
      const phoneRegex = /^[925]\d{7}$/;
      if (!this.phone) {
        this.errors.phone = "Le téléphone est requis";
      } else if (!phoneRegex.test(this.phone)) {
        this.errors.phone = "8 chiffres commençant par 9, 2 ou 5";
      }
    },
    validatePassword() {
      this.passwordCriteria = {
        hasMinLength: this.password.length >= 8,
        hasLetter: /[a-zA-Z]/.test(this.password),
        hasNumber: /\d/.test(this.password)
      };

      if (!this.password) {
        this.errors.password = "Le mot de passe est requis";
      } else if (!this.passwordCriteria.hasMinLength || 
                 !this.passwordCriteria.hasLetter || 
                 !this.passwordCriteria.hasNumber) {
        this.errors.password = "Le mot de passe ne respecte pas les critères";
      }
    },
    validateConfirmPassword() {
      if (!this.confirmPassword) {
        this.errors.confirmPassword = "Confirmation requise";
      } else if (this.password !== this.confirmPassword) {
        this.errors.confirmPassword = "Les mots de passe ne correspondent pas";
      }
    },
    validateForm() {
      this.validateEmail();
      this.validatePhone();
      this.validatePassword();
      this.validateConfirmPassword();
      return this.isFormValid;
    },
    next() {
      if (!this.validateForm()) {
        toast.error("Veuillez corriger les erreurs avant de continuer", {
          autoClose: 2000,
          position: toast.POSITION.BOTTOM_RIGHT
        });
        return;
      }

      const tuteurData = {
        email: this.email,
        phone: this.phone,
        password: this.password
      };
      
      localStorage.setItem("Tuteur", JSON.stringify(tuteurData));
      this.$router.push("/signupTuteurPart2");
    }
  },
  watch: {
    password() {
      this.validatePassword();
      this.validateConfirmPassword();
    },
    confirmPassword() {
      this.validateConfirmPassword();
    }
  }
};
</script>
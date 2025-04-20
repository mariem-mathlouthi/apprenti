<template>
  <main class="w-full flex">
    <div class="flex-1 hidden lg:block">
      <img src="./std.jpg" class="w-full h-screen object-cover" />
    </div>
    <div class="flex-1 flex items-center justify-center h-screen">
      <div class="w-full max-w-md space-y-8 px-4 bg-white text-gray-600 sm:px-0">
        <div class="mt-5 space-y-2">
          <h3 class="text-gray-800 text-2xl font-bold sm:text-3xl">Sign up</h3>
          <p class="">
            Already have an account?
            <router-link
              to="/signin"
              class="font-medium text-indigo-600 hover:text-indigo-500"
            >
              Log in
            </router-link>
          </p>
        </div>

        <form @submit.prevent="next" class="space-y-5">
          <div class="grid grid-cols-2 gap-x-3">
            <div>
              <label class="font-medium">Full Name</label>
              <input
                v-model="fullname"
                type="text"
                required
                class="w-full mt-2 px-3 py-2 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg"
              />
            </div>
            <div>
              <label class="font-medium">Niveau</label>
              <select
                v-model="selectedNiveauId"
                required
                class="w-full mt-2 px-3 py-2 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg"
              >
                <option disabled value="">Select a level</option>
                <option 
                  v-for="niveau in niveaux" 
                  :key="niveau.id"
                  :value="niveau.id"
                >
                  {{ niveau.description }}
                </option>
              </select>
            </div>
          </div>

          <div>
            <label class="font-medium">Cin</label>
            <input
              v-model="cin"
              type="text"
              required
              class="w-full mt-2 px-3 py-2 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg"
            />
          </div>

          <div>
            <label class="font-medium">Email</label>
            <input
              v-model="email"
              type="email"
              required
              class="w-full mt-2 px-3 py-2 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg"
            />
          </div>

          <div class="grid grid-cols-2 gap-x-3">
            <div>
              <label class="font-medium">Password</label>
              <input
                v-model="password"
                type="password"
                required
                class="w-full mt-2 px-3 py-2 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg"
              />
            </div>
            <div>
              <label class="font-medium">Confirm Password</label>
              <input
                v-model="confirmPassword"
                type="password"
                required
                class="w-full mt-2 px-3 py-2 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg"
              />
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
            class="w-full px-4 py-2 text-white font-medium bg-gray-800 hover:bg-gray-700 active:bg-gray-700 rounded-lg duration-150"
          >
            Next
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
      niveaux: []
    };
  },
  methods: {
    async next() {
      if (this.password !== this.confirmPassword) {
        toast.error("Password confirmation does not match!", { autoClose: 2000 });
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
        window.location.href = "/signupEtudiantPart2";
      } catch (error) {
        toast.error("Error saving data!", { autoClose: 2000 });
      }
    },
    async fetchNiveaux() {
      try {
        const response = await axios.get("http://localhost:8000/api/niveaux");
        this.niveaux = response.data;
      } catch (error) {
        toast.error("Failed to load levels", { autoClose: 2000 });
        console.error("Error loading niveaux:", error);
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
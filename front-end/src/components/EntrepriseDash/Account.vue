<template>
  <div>
    <Navbar/>
    <div class="grid grid-cols-12 gap-4">
      <div class="col-span-3">
        <Sidebar />
      </div>
      <div class="col-span-9 mt-24 mr-24">
        <form class="font-sans text-[#333] max-w-4xl mx-auto px-6 my-6" @submit.prevent="handleSubmit">
          <div class="grid sm:grid-cols-2 gap-10">
            <div class="relative flex items-center">
              <label class="text-[13px] absolute top-[-25px] left-0 font-semibold">Numero SIRET</label>
              <input type="text" placeholder="SIRET number"
                v-model="numeroSIRET"
                class="px-2 pt-3 pb-2 bg-white w-full text-sm border-b-2 border-gray-100 rounded outline-none" />
            </div>
            <div class="relative flex items-center">
              <label class="text-[13px] absolute top-[-25px] left-0 font-semibold">Name</label>
              <input type="text" placeholder="company name"
                v-model="name"
                class="px-2 pt-3 pb-2 bg-white w-full text-sm border-b-2 border-gray-100 rounded outline-none" />
            </div>
            <div class="relative flex items-center">
              <label class="text-[13px] absolute top-[-25px] left-0 font-semibold">Email</label>
              <input type="email" placeholder="email"
                v-model="email"
                class="px-2 pt-3 pb-2 bg-white w-full text-sm border-b-2 border-gray-100 rounded outline-none" />
            </div>
            <div class="relative flex items-center">
              <label class="text-[13px] absolute top-[-25px] left-0 font-semibold">Secteur</label>
              <select v-model="secteur_id"
                class="px-2 pt-3 pb-2 bg-white w-full text-sm border-b-2 border-gray-100 rounded outline-none">
                <option v-for="secteur in secteurs" 
                        :key="secteur.id" 
                        :value="secteur.id">
                  {{ secteur.description }}
                </option>
              </select>
            </div>
            
            <div class="relative flex items-center sm:col-span-2">
              <label class="text-[13px] absolute top-[-25px] left-0 font-semibold">Description</label>
              <input type="text" placeholder="description"
                v-model="description"
                class="px-2 pt-3 pb-20 bg-white w-full text-sm border-b-2 border-gray-100 rounded outline-none" />
            </div>
            <div class="relative flex items-center sm:col-span-2">
              <label class="text-[13px] absolute top-[-10px] left-0 font-semibold">Logo</label>
        
              <input type="file" accept="image/*"
              @change="handleLogoChange"
                class="px-2 pt-5 pb-2 bg-white w-full text-sm border-b-2 border-gray-100 focus:border-[#333]  outline-none" />
            </div>
          </div>
          <button type="submit" class="mt-10 px-2 py-2.5 w-full rounded text-sm font-semibold bg-[#333] text-white hover:bg-[#222]">
            Submit
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import Navbar from './Navbar.vue';
import Sidebar from './SideBar.vue';
import "aos/dist/aos.css";
import AOS from "aos";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import axios from "axios";

export default {
  name: 'EntrepriseDashboard',
  data() {
    return {
      numeroSIRET: "",
      name: "",
      email: "",
      secteur_id: null,
      description: "",
      logo: null,
      secteurs: [],
      originalData: {}
    };
  },
  components: {
    Navbar,
    Sidebar,
  },
  methods: {
    async fetchSecteurs() {
      try {
        const response = await axios.get("http://localhost:8000/api/secteurs",
          {
            headers: {
              Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`,
            },
          }
        );
        this.secteurs = response.data;
      } catch (error) {
        console.error("Error fetching secteurs:", error);
        toast.error("Failed to load sectors list", { autoClose: 2000 });
      }
    },
    loadInitialData() {
      const storedData = localStorage.getItem("EntrepriseAccountInfo");
      if (storedData) {
        const data = JSON.parse(storedData);
        this.originalData = data;
        this.numeroSIRET = data.numeroSIRET;
        this.name = data.name;
        this.email = data.email;
        this.secteur_id = data.secteur_id;
        this.description = data.description;
      }
    },
    handleLogoChange(event) {
      this.logo = event.target.files[0];
    },
    async handleSubmit() {
      const formData = new FormData();
      formData.append('numeroSIRET', this.numeroSIRET);
      formData.append('name', this.name);
      formData.append('email', this.email);
      formData.append('secteur_id', this.secteur_id);
      formData.append('description', this.description);
      
      if (this.logo) {
        formData.append('logo', this.logo);
      }

      try {
        const response = await axios.post(
          "http://localhost:8000/api/modifyEntreprise",
          formData,
          {
            headers: {
              'Content-Type': 'multipart/form-data',
              Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`,
            }
          }
        );

        if (response.data.update) {
          this.handleSuccess(response.data.entreprise);
        } else {
          toast.error("Update failed", { autoClose: 2000 });
        }
      } catch (error) {
        this.handleError(error);
      }
    },
    handleSuccess(updatedData) {
      toast.success("Account updated successfully!", { autoClose: 2000 });
      
      // Merge and update local storage
      const mergedData = { ...this.originalData, ...updatedData };
      localStorage.setItem('EntrepriseAccountInfo', JSON.stringify(mergedData));
      
      // Update original data reference
      this.originalData = mergedData;
    },
    handleError(error) {
      if (error.response) {
        const status = error.response.status;
        const message = error.response.data?.message || 'Unknown error';
        
        if (status === 404) {
          toast.error("Company not found", { autoClose: 2000 });
        } else if (status === 422) {
          toast.error("Invalid data: " + message, { autoClose: 2000 });
        } else {
          toast.error(`Server error: ${status}`, { autoClose: 2000 });
        }
      } else {
        toast.error("Network error", { autoClose: 2000 });
      }
      console.error("Error details:", error);
    }
  },
  async mounted() {
    await this.fetchSecteurs();
    this.loadInitialData();
    this.$nextTick(() => {
      AOS.init({
        duration: 2500,
        easing: "ease-in-out",
        once: true,
        mirror: false,
      });
    });
  },
  watch: {
    secteur_id(newVal) {
      if (newVal && !this.secteurs.some(s => s.id === newVal)) {
        console.warn("Selected secteur_id not found in available secteurs");
      }
    }
  }
};
</script>
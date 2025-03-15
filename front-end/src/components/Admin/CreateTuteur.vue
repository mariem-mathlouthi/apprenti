<template>
  <div id="app" class="flex flex-col h-screen">
    <!-- SIDEBAR AND NAVBAR -->
    <NavbarOne />
    <SidebarMenu />

    <!-- CONTENT -->
    <section id="content" class="flex-1 flex justify-center items-center py-6">
      <!-- MAIN CONTENT (Formulaire pour Créer un Tuteur) -->
      <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-indigo-700 text-center mb-6">
          Créer un Tuteur
        </h1>
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-4xl mx-auto">
          <form @submit.prevent="createTuteur" class="space-y-6">
            <!-- Grid Layout for Form Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Nom complet -->
              <div class="form-group">
                <label class="label">Nom complet</label>
                <input
                  type="text"
                  v-model="fullname"
                  required
                  class="input"
                  placeholder="Entrez le nom complet"
                />
                <p v-if="errors.fullname" class="text-red-500 text-sm mt-1">
                  {{ errors.fullname }}
                </p>
              </div>

              <!-- Email -->
              <div class="form-group">
                <label class="label">Email</label>
                <input
                  type="email"
                  v-model="email"
                  required
                  class="input"
                  placeholder="Entrez l'email"
                />
                <p v-if="errors.email" class="text-red-500 text-sm mt-1">
                  {{ errors.email }}
                </p>
              </div>

              <!-- Domaine -->
              <div class="form-group">
                <label class="label">Domaine</label>
                <input
                  type="text"
                  v-model="domaine"
                  required
                  class="input"
                  placeholder="Entrez le domaine"
                />
                <p v-if="errors.domaine" class="text-red-500 text-sm mt-1">
                  {{ errors.domaine }}
                </p>
              </div>

              <!-- Spécialité -->
              <div class="form-group">
                <label class="label">Spécialité</label>
                <input
                  type="text"
                  v-model="specialite"
                  required
                  class="input"
                  placeholder="Entrez la spécialité"
                />
                <p v-if="errors.specialite" class="text-red-500 text-sm mt-1">
                  {{ errors.specialite }}
                </p>
              </div>

              <!-- Expérience -->
              <div class="form-group">
                <label class="label">Expérience (en années)</label>
                <input
                  type="number"
                  v-model="experience"
                  required
                  class="input"
                  placeholder="Expérience en années"
                  min="0"
                />
                <p v-if="errors.experience" class="text-red-500 text-sm mt-1">
                  {{ errors.experience }}
                </p>
              </div>

              <!-- Téléphone -->
              <div class="form-group">
                <label class="label">Téléphone</label>
                <input
                  type="text"
                  v-model="phone"
                  required
                  class="input"
                  placeholder="Entrez le téléphone"
                />
                <p v-if="errors.phone" class="text-red-500 text-sm mt-1">
                  {{ errors.phone }}
                </p>
              </div>

              <!-- Mot de passe -->
              <div class="form-group">
                <label class="label">Mot de passe</label>
                <input
                  type="password"
                  v-model="password"
                  required
                  class="input"
                  placeholder="Entrez le mot de passe"
                />
                <p v-if="errors.password" class="text-red-500 text-sm mt-1">
                  {{ errors.password }}
                </p>
              </div>
            </div>

            <!-- Bouton d'ajout -->
            <div class="mt-6 flex justify-center">
              <button type="submit" class="btn-submit">
                Ajouter Tuteur
              </button>
            </div>
          </form>
        </div>
      </div>
    </section>
    <!-- END CONTENT -->
  </div>
</template>

<script>
import SidebarMenu from "./SidebarMenu.vue";
import NavbarOne from "./NavbarOne.vue";
import axios from "axios";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

export default {
  name: "CreateTuteur",
  components: {
    SidebarMenu,
    NavbarOne,
  },
  data() {
    return {
      fullname: "",
      email: "",
      domaine: "",
      specialite: "",
      experience: "",
      phone: "",
      password: "",
      errors: {}, // Pour stocker les erreurs de validation
    };
  },
  methods: {
    validateForm() {
      this.errors = {};

      // Validation rules
      if (!this.fullname) this.errors.fullname = "Le nom complet est requis.";
      if (!this.email || !this.email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
        this.errors.email = "Veuillez entrer un email valide.";
      }
      if (!this.domaine) this.errors.domaine = "Le domaine est requis.";
      if (!this.specialite) this.errors.specialite = "La spécialité est requise.";
      if (!this.experience || this.experience < 0) {
        this.errors.experience = "L'expérience doit être un nombre positif.";
      }
      if (!this.phone || !this.phone.match(/^\d{10}$/)) {
        this.errors.phone = "Le téléphone doit contenir 10 chiffres.";
      }
      if (!this.password || this.password.length < 6) {
        this.errors.password = "Le mot de passe doit contenir au moins 6 caractères.";
      }

      // Return true if no errors
      return Object.keys(this.errors).length === 0;
    },
    async createTuteur() {
      if (!this.validateForm()) return; // Stop if validation fails

      let newTuteur = {
        fullname: this.fullname,
        email: this.email,
        domaine: this.domaine,
        specialite: this.specialite,
        experience: parseInt(this.experience),
        phone: this.phone,
        password: this.password,
      };

      try {
        const response = await axios.post(
          "http://localhost:8000/api/admin/tuteur",
          newTuteur,
          {
            headers: {
              "Content-Type": "application/json",
            },
          }
        );
        toast.success("Tuteur ajouté avec succès !");
        this.$router.push("/Admin");
      } catch (error) {
        if (error.response && error.response.data.message) {
          toast.error(`Erreur: ${error.response.data.message}`);

        } else {
          console.error("Erreur inconnue:", error);
        }
      }
    },
  },
};
</script>

<style scoped>
.input {
  width: 100%;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
}
.btn-submit {
  background-color: #4f46e5;
  color: white;
  padding: 10px 15px;
  border-radius: 4px;
  cursor: pointer;
}
.btn-submit:hover {
  background-color: #4338ca;
}
</style>
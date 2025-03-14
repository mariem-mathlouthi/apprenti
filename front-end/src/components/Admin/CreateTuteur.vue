<<<<<<< HEAD
<!-- <template>
  <div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-4">Créer un Tuteur</h2>
    <form @submit.prevent="createTuteur" class="space-y-4">
      <div>
        <label class="font-medium">Nom complet</label>
        <input
          type="text"
          v-model="fullname"
          required
          class="w-full px-3 py-2 border rounded-lg"
        />
      </div>

      <div>
        <label class="font-medium">Email</label>
        <input
          type="email"
          v-model="email"
          required
          class="w-full px-3 py-2 border rounded-lg"
        />
      </div>

      <div>
        <label class="font-medium">Domaine</label>
        <input
          type="text"
          v-model="domaine"
          required
          class="w-full px-3 py-2 border rounded-lg"
        />
      </div>

      <div>
        <label class="font-medium">Spécialité</label>
        <input
          type="text"
          v-model="specialite"
          required
          class="w-full px-3 py-2 border rounded-lg"
        />
      </div>

      <div>
        <label class="font-medium">Expérience (années)</label>
        <input
          type="number"
          v-model="experience"
          required
          class="w-full px-3 py-2 border rounded-lg"
        />
      </div>

      <div>
        <label class="font-medium">Téléphone</label>
        <input
          type="text"
          v-model="phone"
          required
          class="w-full px-3 py-2 border rounded-lg"
        />
      </div>

      <div>
        <label class="font-medium">Mot de passe</label>
        <input
          type="password"
          v-model="password"
          required
          class="w-full px-3 py-2 border rounded-lg"
        />
      </div>

      <button
        type="submit"
        class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-500"
      >
        Ajouter Tuteur
      </button>
    </form>
=======
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
        <div class="bg-white p-8 rounded-lg shadow-lg w-full sm:w-96 max-w-4xl mx-auto">
          <form @submit.prevent="createTuteur" class="space-y-6">
            <!-- Nom complet -->
            <div class="form-group">
              <label class="label">Nom complet</label>
              <input type="text" v-model="fullname" required class="input" placeholder="Entrez le nom complet" />
            </div>

            <!-- Email -->
            <div class="form-group">
              <label class="label">Email</label>
              <input type="email" v-model="email" required class="input" placeholder="Entrez l'email" />
            </div>

            <!-- Domaine -->
            <div class="form-group">
              <label class="label">Domaine</label>
              <input type="text" v-model="domaine" required class="input" placeholder="Entrez le domaine" />
            </div>

            <!-- Spécialité -->
            <div class="form-group">
              <label class="label">Spécialité</label>
              <input type="text" v-model="specialite" required class="input" placeholder="Entrez la spécialité" />
            </div>

            <!-- Expérience -->
            <div class="form-group">
              <label class="label">Expérience (en années)</label>
              <input type="number" v-model="experience" required class="input" placeholder="Expérience en années" />
            </div>

            <!-- Téléphone -->
            <div class="form-group">
              <label class="label">Téléphone</label>
              <input type="text" v-model="phone" required class="input" placeholder="Entrez le téléphone" />
            </div>

            <!-- Mot de passe -->
            <div class="form-group">
              <label class="label">Mot de passe</label>
              <input type="password" v-model="password" required class="input" placeholder="Entrez le mot de passe" />
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
>>>>>>> sauvegarde-commits
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
    NavbarOne
  },
  data() {
    return {
      fullname: "",
      email: "",
      domaine: "",
      specialite: "",
      experience: "",
      phone: "",
      password: ""
    };
  },
  methods: {
    async createTuteur() {
      let newTuteur = {
        fullname: this.fullname,
        email: this.email,
        domaine: this.domaine,
        specialite: this.specialite,
        experience: parseInt(this.experience), // Convertir en nombre
        phone: this.phone,
        password: this.password
      };

      try {
        const response = await axios.post(
          "http://localhost:8000/api/admin/tuteur",
          newTuteur,
          {
            headers: {
<<<<<<< HEAD
              "Content-Type": "application/json",
            },
=======
              "Content-Type": "application/json"
            }
>>>>>>> sauvegarde-commits
          }
        );
        toast.success("Tuteur ajouté avec succès !");
        this.$router.push("/Admin");
      } catch (error) {
<<<<<<< HEAD
        if (error.response) {
          console.error("Erreur Laravel:", error.response.data);
          toast.error(
            `Erreur: ${
              error.response.data.message || "Vérifiez les champs saisis"
            }`
          );
=======
        if (error.response && error.response.data.message) {
          toast.error(`Erreur: ${error.response.data.message}`);
>>>>>>> sauvegarde-commits
        } else {
          console.error("Erreur inconnue:", error);
        }
      }
    }
  }
};
</script> -->
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
        <div class="bg-white p-8 rounded-lg shadow-lg w-full sm:w-96 max-w-4xl mx-auto">
          <form @submit.prevent="createTuteur" class="space-y-6">
            <!-- Nom complet -->
            <div class="form-group">
              <label class="label">Nom complet</label>
              <input type="text" v-model="fullname" required class="input" placeholder="Entrez le nom complet" />
            </div>

            <!-- Email -->
            <div class="form-group">
              <label class="label">Email</label>
              <input type="email" v-model="email" required class="input" placeholder="Entrez l'email" />
            </div>

            <!-- Domaine -->
            <div class="form-group">
              <label class="label">Domaine</label>
              <input type="text" v-model="domaine" required class="input" placeholder="Entrez le domaine" />
            </div>

            <!-- Spécialité -->
            <div class="form-group">
              <label class="label">Spécialité</label>
              <input type="text" v-model="specialite" required class="input" placeholder="Entrez la spécialité" />
            </div>

            <!-- Expérience -->
            <div class="form-group">
              <label class="label">Expérience (en années)</label>
              <input type="number" v-model="experience" required class="input" placeholder="Expérience en années" />
            </div>

            <!-- Téléphone -->
            <div class="form-group">
              <label class="label">Téléphone</label>
              <input type="text" v-model="phone" required class="input" placeholder="Entrez le téléphone" />
            </div>

            <!-- Mot de passe -->
            <div class="form-group">
              <label class="label">Mot de passe</label>
              <input type="password" v-model="password" required class="input" placeholder="Entrez le mot de passe" />
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
    NavbarOne
  },
  data() {
    return {
      fullname: "",
      email: "",
      domaine: "",
      specialite: "",
      experience: "",
      phone: "",
      password: ""
    };
  },
  methods: {
    async createTuteur() {
      let newTuteur = {
        fullname: this.fullname,
        email: this.email,
        domaine: this.domaine,
        specialite: this.specialite,
        experience: parseInt(this.experience), // Convertir en nombre
        phone: this.phone,
        password: this.password
      };

      try {
        const response = await axios.post(
          "http://localhost:8000/api/admin/tuteur",
          newTuteur,
          {
            headers: {
              "Content-Type": "application/json"
            }
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
    }
  }
};
</script>

<style scoped>
/* Structure principale */
#app {
  display: flex;
  flex-direction: column;
  height: 100vh;
}

section#content {
  display: flex;
  justify-content: center;
  align-items: center;
  flex: 1;
  padding-top: 20px;
  padding-bottom: 20px;
}

/* Style du formulaire */
.container {
  max-width: 700px;
}

.label {
  font-weight: 600;
  font-size: 1.1rem;
  color: #4a4a4a;
  margin-bottom: 4px;
}

.input {
  width: 100%;
  padding: 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  transition: all 0.3s ease-in-out;
}

.input:focus {
  outline: none;
  border-color: #4c51bf;
  box-shadow: 0 0 6px rgba(76, 81, 191, 0.5);
}

.btn-submit {
  width: 100%;
  padding: 12px;
  background-color: #4c51bf;
  color: white;
  font-weight: bold;
  border-radius: 8px;
  font-size: 16px;
  text-transform: uppercase;
  transition: transform 0.2s, background-color 0.3s;
}

.btn-submit:hover {
  background-color: #4338ca;
  transform: scale(1.05);
}

/* Resize for small screens */
@media (max-width: 640px) {
  .container {
    width: 100%;
    padding: 10px;
  }

  .input {
    font-size: 14px;
  }
}
</style>

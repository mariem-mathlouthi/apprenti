<template>
  <div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-4">Créer un Tuteur</h2>
    <form @submit.prevent="createTuteur" class="space-y-4">
      <div>
        <label class="font-medium">Nom complet</label>
        <input type="text" v-model="fullname" required class="w-full px-3 py-2 border rounded-lg" />
      </div>

      <div>
        <label class="font-medium">Email</label>
        <input type="email" v-model="email" required class="w-full px-3 py-2 border rounded-lg" />
      </div>

      <div>
        <label class="font-medium">Domaine</label>
        <input type="text" v-model="domaine" required class="w-full px-3 py-2 border rounded-lg" />
      </div>

      <div>
        <label class="font-medium">Spécialité</label>
        <input type="text" v-model="specialite" required class="w-full px-3 py-2 border rounded-lg" />
      </div>

      <div>
        <label class="font-medium">Expérience (années)</label>
        <input type="number" v-model="experience" required class="w-full px-3 py-2 border rounded-lg" />
      </div>

      <div>
        <label class="font-medium">Téléphone</label>
        <input type="text" v-model="phone" required class="w-full px-3 py-2 border rounded-lg" />
      </div>

      <div>
        <label class="font-medium">Mot de passe</label>
        <input type="password" v-model="password" required class="w-full px-3 py-2 border rounded-lg" />
      </div>

      <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-500">
        Ajouter Tuteur
      </button>
    </form>
  </div>
</template>

<script>
import axios from "axios";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

export default {
  data() {
    return {
      fullname: "",
      email: "",
      domaine: "",
      specialite: "",
      experience: "",
      phone: "",
      password: "",
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
        password: this.password,
      };

      console.log("Données envoyées :", newTuteur); // Debugging

      try {
        const response = await axios.post("http://localhost:8000/api/admin/tuteur", newTuteur, {
          headers: {
            "Content-Type": "application/json",
          },
        });
        toast.success("Tuteur ajouté avec succès !");
        this.$router.push("/Admin");
      } catch (error) {
        if (error.response) {
          console.error("Erreur Laravel:", error.response.data);
          toast.error(`Erreur: ${error.response.data.message || "Vérifiez les champs saisis"}`);
        } else {
          console.error("Erreur inconnue:", error);
        }
      }
    },
  },
};
</script>

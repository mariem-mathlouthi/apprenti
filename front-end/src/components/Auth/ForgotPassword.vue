<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-50 to-indigo-50 flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8">
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mt-4">Mot de passe oublié</h1>
        <p class="mt-2 text-gray-600">Entrez votre email pour réinitialiser votre mot de passe</p>
      </div>

      <form @submit.prevent="handleSubmit" class="space-y-6">
        <div>
          <input
            v-model="localEmail"
            type="email"
            required
            placeholder="votre@email.com"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
          />
        </div>

        <button
          type="submit"
          class="w-full bg-green-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-green-700"
        >
          Envoyer le code
        </button>

        <router-link 
          to="/login" 
          class="text-indigo-600 hover:text-indigo-800 block text-center"
        >
          Retour à la connexion
        </router-link>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { toast } from 'vue3-toastify';

export default {
  data() {
    return {
      localEmail: '',
    };
  },
  methods: {
    async handleSubmit() {
      try {
        const response = await axios.post('http://localhost:8000/api/forgot-password', {
          email: this.localEmail
        });

        if (response.data.check) {
          toast.success('Code envoyé par email !');
          this.$router.push({
            path: '/change-password',
            query: { email: this.localEmail }
          });
        }
      } catch (error) {
        toast.error(error.response?.data?.message || 'Erreur');
      }
    }
  }
};
</script>
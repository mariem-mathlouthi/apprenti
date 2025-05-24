<template>
  <div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-8">
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold">Nouveau mot de passe</h1>
      </div>

      <form @submit.prevent="handleSubmit" class="space-y-6">
        <div>
          <label class="block text-sm font-medium">Code de vérification</label>
          <input
            v-model="code"
            type="text"
            required
            class="w-full mt-1 p-2 border rounded-lg"
          />
        </div>

        <div>
          <label class="block text-sm font-medium">Nouveau mot de passe</label>
          <input
            v-model="password"
            type="password"
            required
            class="w-full mt-1 p-2 border rounded-lg"
          />
        </div>

        <div>
          <label class="block text-sm font-medium">Confirmer le mot de passe</label>
          <input
            v-model="passwordConfirmation"
            type="password"
            required
            class="w-full mt-1 p-2 border rounded-lg"
          />
        </div>

        <button
          type="submit"
          class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700"
        >
          Réinitialiser
        </button>
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
      email: this.$route.query.email || '',
      code: this.$route.query.code || '',
      password: '',
      passwordConfirmation: ''
    };
  },
  methods: {
    async handleSubmit() {
      if (this.password !== this.passwordConfirmation) {
        toast.error('Les mots de passe ne correspondent pas');
        return;
      }

      try {
        const response = await axios.post('http://localhost:8000/api/reset-password', {
          email: this.email,
          code: this.code,
          password: this.password,
          password_confirmation: this.passwordConfirmation
        });

        if (response.data.check) {
          toast.success('Mot de passe modifié !');
          this.$router.push('/signin');
        }
      } catch (error) {
        toast.error(error.response?.data?.message || 'Erreur');
      }
    }
  }
};
</script>
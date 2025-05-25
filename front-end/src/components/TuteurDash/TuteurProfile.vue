<template>
    <div>
      <NavBarTut class="w-full fixed z-50" />
      <div class="grid grid-cols-12 gap-4">
        <div class="col-span-3">
          <SidebarTut />
        </div>
        <div class="col-span-9 mt-24 mr-24">
          <div class="relative md:left-10 md:w-3/4 my-4 border px-4 shadow-xl sm:rounded-xl sm:px-4 sm:py-4">
            <div class="flex flex-col border-b py-4 sm:flex-row sm:items-start">
              <div class="shrink-0 mr-auto sm:py-3">
                <p class="font-medium">Profil Tuteur</p>
                <p class="text-sm text-gray-600">Modifiez vos informations professionnelles</p>
              </div>
            </div>
  
            <form @submit.prevent="saveChanges" class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Colonne gauche -->
                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Nom complet</label>
                    <input
                      v-model="form.fullname"
                      class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                    >
                  </div>
  
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input
                      v-model="form.email"
                      type="email"
                      class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                    >
                  </div>
  
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Téléphone</label>
                    <input
                      v-model="form.phone"
                      class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                    >
                  </div>
                </div>
  
                <!-- Colonne droite -->
                <div class="space-y-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Expérience (années)</label>
                    <input
                      v-model.number="form.experience"
                      type="number"
                      min="0"
                      class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                    >
                  </div>
  
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Spécialité</label>
                    <select
                      v-model="form.specialite_id"
                      class="mt-1 block w-full rounded-md border border-gray-300 bg-white py-2 px-3 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    >
                      <option v-for="specialite in specialites" :key="specialite.id" :value="specialite.id">
                        {{ specialite.description }}
                      </option>
                    </select>
                  </div>
  
                  <div>
                    <label class="block text-sm font-medium text-gray-700">Photo de profil</label>
                    <div class="mt-1 flex items-center">
                      <img 
                        :src="imagePreview || defaultAvatar" 
                        class="h-16 w-16 rounded-full object-cover border-2 border-gray-200"
                      >
                      <input
                        type="file"
                        @change="handleImageChange"
                        accept="image/*"
                        class="ml-4 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                      >
                    </div>
                  </div>
                </div>
              </div>
  
              <div class="border-t pt-4">
                <button
                  type="submit"
                  class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                  Sauvegarder les modifications
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios'
  import { toast } from 'vue3-toastify'
  import NavBarTut from './NavBarTut.vue'
  import SidebarTut from './SidebarTut.vue'
  
  export default {
    components: {
      NavBarTut,
      SidebarTut
    },
    data() {
      return {
        form: {
          id: '',
          fullname: '',
          email: '',
          phone: '',
          experience: 0,
          specialite_id: null,
          image: null
        },
        specialites: [],
        imagePreview: null,
        defaultAvatar: 'https://i.postimg.cc/mDWkzGDv/istockphoto-1200064810-170667a.jpg'
      }
    },
    async mounted() {
      await this.fetchSpecialites()
      await this.loadTuteurData()
    },
    methods: {
      async fetchSpecialites() {
        try {
          const response = await axios.get('http://localhost:8000/api/specialites',
            {
              headers: {
                Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`
              }
            }
          )
          this.specialites = response.data
        } catch (error) {
          toast.error('Erreur de chargement des spécialités', { autoClose: 2000 })
        }
      },
  
      async loadTuteurData() {
        try {
          const storedData = JSON.parse(localStorage.getItem('TuteurAccountInfo'))
          if (storedData?.id) {
            const response = await axios.get(`http://localhost:8000/api/getTuteurDetail/${storedData.id}`,
              {
                headers: {
                  Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`
                }
              }
            )
            const tuteur = response.data.tuteur
            
            this.form = {
              id: tuteur.id,
              fullname: tuteur.fullname,
              email: tuteur.email,
              phone: tuteur.phone,
              experience: tuteur.experience,
              specialite_id: tuteur.specialite_id
            }
  
            this.imagePreview = tuteur.image || this.defaultAvatar
          }
        } catch (error) {
          toast.error('Erreur de chargement du profil', { autoClose: 2000 })
        }
      },
  
      handleImageChange(event) {
        const file = event.target.files[0]
        if (file) {
          this.form.image = file
          this.imagePreview = URL.createObjectURL(file)
        }
      },
  
      async saveChanges() {
        const formData = new FormData()
        
        Object.entries(this.form).forEach(([key, value]) => {
          if (value !== null && value !== undefined) {
            formData.append(key, value)
          }
        })
  
        try {
          const { data } = await axios.post('http://localhost:8000/api/modifyTuteur', formData, {
            headers: { 'Content-Type': 'multipart/form-data', Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}` }
          })
  
          if (data.update) {
            localStorage.setItem('TuteurAccountInfo', JSON.stringify({
              ...this.form,
              image: data.tuteur.image || this.imagePreview
            }))
            toast.success('Profil mis à jour avec succès', { autoClose: 2000 })
          }
        } catch (error) {
          const errorMessage = error.response?.data?.message || 'Erreur lors de la mise à jour'
          toast.error(errorMessage, { autoClose: 2000 })
          console.error('Détails de l\'erreur:', error)
        }
      }
    }
  }
  </script>
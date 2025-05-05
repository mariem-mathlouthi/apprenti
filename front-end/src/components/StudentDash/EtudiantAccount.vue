<template>
  <div>
    <NavBarStd class="w-full fixed z-50" />
    <div class="grid grid-cols-12 gap-4">
      <div class="col-span-3">
        <Sidebar />
      </div>
      <div class="col-span-9 mt-24 mr-24">
        <div class="relative md:left-10 md:w-3/4 my-4 border px-4 shadow-xl sm:rounded-xl sm:px-4 sm:py-4">
          <div class="flex flex-col border-b py-4 sm:flex-row sm:items-start">
            <div class="shrink-0 mr-auto sm:py-3">
              <p class="font-medium">Détails du compte</p>
              <p class="text-sm text-gray-600">Modifiez vos informations</p>
            </div>
          </div>

          <form @submit.prevent="saveChanges" class="space-y-5">
            <div class="grid grid-cols-2 gap-x-3">
              <div>
                <label class="font-medium">Nom complet</label>
                <input
                  v-model="form.fullname"
                  class="w-full mt-2 px-3 py-2 border rounded-lg"
                />
              </div>

              <div>
                <label class="font-medium">Email</label>
                <input
                  v-model="form.email"
                  type="email"
                  class="w-full mt-2 px-3 py-2 border rounded-lg"
                />
              </div>
            </div>

            <div class="grid grid-cols-2 gap-x-3">
              <div>
                <label class="font-medium">Niveau</label>
                <select
                  v-model="form.niveau_id"
                  class="w-full mt-2 px-3 py-2 border rounded-lg"
                >
                  <option disabled value="">Sélectionner un niveau</option>
                  <option 
                    v-for="niveau in niveaux" 
                    :key="niveau.id"
                    :value="niveau.id"
                  >
                    {{ niveau.description }}
                  </option>
                </select>
              </div>

              <div>
                <label class="font-medium">Domaine</label>
                <select
                  v-model="form.domaine_id"
                  class="w-full mt-2 px-3 py-2 border rounded-lg"
                >
                  <option disabled value="">Sélectionner un domaine</option>
                  <option 
                    v-for="domaine in domaines" 
                    :key="domaine.id"
                    :value="domaine.id"
                  >
                    {{ domaine.description }}
                  </option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-x-3">
              <div>
                <label class="font-medium">Spécialité</label>
                <select
                  v-model="form.specialite_id"
                  class="w-full mt-2 px-3 py-2 border rounded-lg"
                >
                  <option disabled value="">Sélectionner une spécialité</option>
                  <option 
                    v-for="specialite in specialites" 
                    :key="specialite.id"
                    :value="specialite.id"
                  >
                    {{ specialite.description }}
                  </option>
                </select>
              </div>

              <div>
                <label class="font-medium">Établissement</label>
                <input
                  v-model="form.etablissement"
                  class="w-full mt-2 px-3 py-2 border rounded-lg"
                />
              </div>
            </div>

            <div class="mt-6">
              <label class="font-medium">Photo de profil</label>
              <div class="flex items-center gap-4 mt-2">
                <img 
                  :src="imagePreview || defaultAvatar"
                  class="h-24 w-24 rounded-full object-cover border-2"
                />
                <input
                  type="file"
                  accept="image/*"
                  @change="handleImageChange"
                  class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                />
              </div>
            </div>

            <button
              type="submit"
              class="w-full px-4 py-2 text-white font-medium bg-indigo-600 hover:bg-indigo-500 rounded-lg duration-150"
            >
              Enregistrer les modifications
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { toast } from 'vue3-toastify'
import NavBarStd from './NavBarStd.vue'
import Sidebar from './Sidebar.vue'

export default {
  components: { NavBarStd, Sidebar },
  data() {
    return {
      form: {
        id: '',
        fullname: '',
        email: '',
        niveau_id: null,
        domaine_id: null,
        specialite_id: null,
        etablissement: '',
        image: null
      },
      niveaux: [],
      domaines: [],
      specialites: [],
      imagePreview: null,
      defaultAvatar: 'https://i.postimg.cc/mDWkzGDv/istockphoto-1200064810-170667a.jpg'
    }
  },
  async mounted() {
    await Promise.all([
      this.fetchNiveaux(),
      this.fetchDomaines(),
      this.fetchSpecialites()
    ])
    this.loadStudentData()
  },
  methods: {
    async fetchNiveaux() {
      try {
        const response = await axios.get("http://localhost:8000/api/niveaux")
        this.niveaux = response.data
      } catch (error) {
        toast.error("Erreur de chargement des niveaux", { autoClose: 2000 })
      }
    },

    async fetchDomaines() {
      try {
        const response = await axios.get("http://localhost:8000/api/domaines")
        this.domaines = response.data
      } catch (error) {
        toast.error("Erreur de chargement des domaines", { autoClose: 2000 })
      }
    },

    async fetchSpecialites() {
      try {
        const response = await axios.get("http://localhost:8000/api/specialites")
        this.specialites = response.data
      } catch (error) {
        toast.error("Erreur de chargement des spécialités", { autoClose: 2000 })
      }
    },

    async loadStudentData() {
      try {
        const storedData = JSON.parse(localStorage.getItem('StudentAccountInfo'))
        if (storedData?.id) {
          const response = await axios.get(`http://localhost:8000/api/getStudentDetail/${storedData.id}`)
          const student = response.data.student
          
          this.form = {
            id: student.id,
            fullname: student.fullname,
            email: student.email,
            niveau_id: student.niveau_id,
            domaine_id: student.domaine_id,
            specialite_id: student.specialite_id,
            etablissement: student.etablissement
          }

          this.imagePreview = student.image 
            ? `http://localhost:8000${student.image}`
            : this.defaultAvatar
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
        const { data } = await axios.post('http://localhost:8000/api/modifyStudent', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        })

        if (data.update) {
          localStorage.setItem('StudentAccountInfo', JSON.stringify({
            ...this.form,
            image: data.etudiant.image || this.imagePreview
          }))
          toast.success('Modifications enregistrées !', { autoClose: 2000 })
        }
      } catch (error) {
        const errorMessage = error.response?.data?.message || 'Erreur de mise à jour'
        toast.error(errorMessage, { autoClose: 2000 })
      }
    }
  }
}
</script>
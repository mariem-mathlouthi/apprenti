<template>
    <div id="app">
      <!-- SIDEBAR -->
      <SidebarMenu></SidebarMenu>
  
      <!-- CONTENT -->
      <section id="content">
        <!-- NAVBAR -->
        <NavbarOne></NavbarOne>
  
        <div class="col-span-9 mt-24 mr-24">
          <div class="container mx-auto px-4 py-8">
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
              <!-- En-tête avec filtres -->
              <div class="px-6 py-4 border-b flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <h2 class="text-xl font-semibold text-gray-800">Gestion des Feedbacks</h2>
                <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                  <select 
                    v-model="filters.cours_id" 
                    class="px-3 py-2 border rounded-md text-sm w-full md:w-48"
                  >
                    <option value="">Tous les cours</option>
                    <option 
                      v-for="cours in allCours" 
                      :key="cours.id" 
                      :value="cours.id"
                    >
                      {{ cours.titre }}
                    </option>
                  </select>
                  <select 
                    v-model="filters.note" 
                    class="px-3 py-2 border rounded-md text-sm w-full md:w-36"
                  >
                    <option value="">Toutes notes</option>
                    <option v-for="n in 5" :key="n" :value="n">
                      {{ n }} ★
                    </option>
                  </select>
                </div>
              </div>
  
              <!-- Tableau des feedbacks -->
              <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                  <thead class="bg-gray-50">
                    <tr>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Étudiant</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cours</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commentaire</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                      <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="feedback in paginatedFeedbacks" :key="feedback.id">
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ feedback.id }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                          <div class="flex-shrink-0 h-10 w-10 bg-blue-500 rounded-full flex items-center justify-center text-white">
                            {{ feedback.etudiant?.fullname?.charAt(0) || '?' }}
                          </div>
                          <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">
                              {{ feedback.etudiant?.fullname || 'Inconnu' }}
                            </div>
                            <div class="text-sm text-gray-500">
                              ID: {{ feedback.etudiant_id }}
                            </div>
                          </div>
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                          {{ feedback.cours?.titre || 'Cours supprimé' }}
                        </div>
                        <div class="text-sm text-gray-500">
                          ID: {{ feedback.cours_id }}
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex">
                          <span 
                            v-for="i in 5" 
                            :key="i" 
                            :class="{
                              'text-yellow-400': i <= feedback.note, 
                              'text-gray-300': i > feedback.note
                            }"
                            class="text-lg"
                          >
                            ★
                          </span>
                        </div>
                      </td>
                      <td class="px-6 py-4">
                        <div class="text-sm text-gray-500 max-w-xs truncate hover:max-w-none hover:whitespace-normal">
                          {{ feedback.commentaire }}
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ formatDate(feedback.created_at) }}
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button 
                          @click="confirmDelete(feedback.id)" 
                          class="text-red-600 hover:text-red-900"
                        >
                          Supprimer
                        </button>
                      </td>
                    </tr>
                    <tr v-if="filteredFeedbacks.length === 0">
                      <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                        Aucun feedback trouvé
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
  
              <!-- Pagination -->
              <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                  <button 
                    @click="currentPage = Math.max(1, currentPage - 1)"
                    :disabled="currentPage === 1"
                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
                  >
                    Précédent
                  </button>
                  <button 
                    @click="currentPage = Math.min(totalPages, currentPage + 1)"
                    :disabled="currentPage === totalPages"
                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50"
                  >
                    Suivant
                  </button>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                  <div>
                    <p class="text-sm text-gray-700">
                      Affichage de <span class="font-medium">{{ (currentPage - 1) * perPage + 1 }}</span>
                      à <span class="font-medium">{{ Math.min(currentPage * perPage, totalFeedbacks) }}</span>
                      sur <span class="font-medium">{{ totalFeedbacks }}</span> résultats
                    </p>
                  </div>
                  <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                      <button 
                        @click="currentPage = Math.max(1, currentPage - 1)"
                        :disabled="currentPage === 1"
                        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
                      >
                        <span class="sr-only">Précédent</span>
                        &lt;
                      </button>
                      <button 
                        v-for="page in visiblePages" 
                        :key="page"
                        @click="currentPage = page"
                        :class="{
                          'bg-blue-50 border-blue-500 text-blue-600 z-10': currentPage === page, 
                          'bg-white border-gray-300 text-gray-500 hover:bg-gray-50': currentPage !== page
                        }"
                        class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                      >
                        {{ page }}
                      </button>
                      <button 
                        @click="currentPage = Math.min(totalPages, currentPage + 1)"
                        :disabled="currentPage === totalPages"
                        class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50"
                      >
                        <span class="sr-only">Suivant</span>
                        &gt;
                      </button>
                    </nav>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </template>
  
  <script>
  import axios from 'axios'
  import NavbarOne from './NavbarOne.vue'
  import SidebarMenu from './SidebarMenu.vue'
  
  export default {
    name: 'AdminFeedbacks',
    components: {
      NavbarOne,
      SidebarMenu
    },
    data() {
      return {
        feedbacks: [],
        allCours: [],
        filters: {
          cours_id: '',
          note: ''
        },
        currentPage: 1,
        perPage: 10,
        totalFeedbacks: 0,
        maxVisiblePages: 5
      }
    },
    computed: {
      filteredFeedbacks() {
        return this.feedbacks.filter(feedback => {
          const matchesCourse = !this.filters.cours_id || feedback.cours_id == this.filters.cours_id
          const matchesRating = !this.filters.note || feedback.note == this.filters.note
          return matchesCourse && matchesRating
        })
      },
      totalPages() {
        return Math.ceil(this.filteredFeedbacks.length / this.perPage)
      },
      paginatedFeedbacks() {
        const start = (this.currentPage - 1) * this.perPage
        const end = start + this.perPage
        return this.filteredFeedbacks.slice(start, end)
      },
      visiblePages() {
        const range = []
        const half = Math.floor(this.maxVisiblePages / 2)
        let start = Math.max(1, this.currentPage - half)
        let end = Math.min(this.totalPages, start + this.maxVisiblePages - 1)
        
        if (end - start < this.maxVisiblePages - 1) {
          start = Math.max(1, end - this.maxVisiblePages + 1)
        }
        
        for (let i = start; i <= end; i++) {
          range.push(i)
        }
        
        return range
      }
    },
    async created() {
      await this.fetchFeedbacks()
      await this.fetchAllCours()
    },
    methods: {
      async fetchFeedbacks() {
        try {
          const response = await axios.get('/api/admin/feedbacks', {
            headers: {
              Authorization: `Bearer ${localStorage.getItem('token')}`
            }
          })
          this.feedbacks = response.data
          this.totalFeedbacks = this.feedbacks.length
        } catch (error) {
          console.error('Erreur lors de la récupération des feedbacks:', error)
        }
      },
      async fetchAllCours() {
        try {
          const response = await axios.get('/api/cours')
          this.allCours = response.data
        } catch (error) {
          console.error('Erreur lors de la récupération des cours:', error)
        }
      },
      formatDate(dateString) {
        if (!dateString) return ''
        const options = { 
          year: 'numeric', 
          month: 'short', 
          day: 'numeric', 
          hour: '2-digit', 
          minute: '2-digit' 
        }
        return new Date(dateString).toLocaleDateString('fr-FR', options)
      },
      confirmDelete(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce feedback ?')) {
          this.deleteFeedback(id)
        }
      },
      async deleteFeedback(id) {
        try {
          await axios.delete(`/api/feedbacks/${id}`, {
            headers: {
              Authorization: `Bearer ${localStorage.getItem('token')}`
            }
          })
          this.feedbacks = this.feedbacks.filter(f => f.id !== id)
          this.totalFeedbacks = this.feedbacks.length
        } catch (error) {
          console.error('Erreur lors de la suppression:', error)
          alert('Une erreur est survenue lors de la suppression')
        }
      }
    },
    watch: {
      filters: {
        deep: true,
        handler() {
          this.currentPage = 1
        }
      }
    }
  }
  </script>
  
  <style scoped>
  .truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  
  .hover\:max-w-none:hover {
    max-width: none;
    white-space: normal;
    word-break: break-word;
  }
  
  /* Style pour les flèches de pagination */
  button:disabled {
    cursor: not-allowed;
    opacity: 0.5;
  }
  </style>
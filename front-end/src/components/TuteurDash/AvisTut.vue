<template>
  <div class="flex flex-col h-screen bg-gray-100">
    <NavBarTuteur />
    <SidebarTuteur class="w-64" />

    <main class="flex-1 p-8 overflow-auto mt-16 ml-64">
      <!-- Bouton de retour -->
      <router-link
        :to="`/cours`"
        class="inline-flex items-center px-4 py-2 mb-4 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
      >
        <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Retour à la liste des cours
      </router-link>

      <!-- Boutons Avis -->
      <!-- <div class="flex justify-start space-x-4">
        <button
          @click="toggleAvis"
          class="feedback-btn"
          :class="{ 'feedback-btn-primary': !showAvis, 'feedback-btn-secondary': showAvis }"
        >
          <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
          </svg>
          {{ showAvis ? 'Masquer les Avis' : 'Afficher les Avis' }}
        </button>
      </div> -->

      <!-- Section Avis -->
      <div class="mt-6 bg-gray-100 rounded-lg p-6 -mx-8">
        <div class="max-w-6xl mx-auto">
          <h1 class="text-2xl font-bold text-gray-800 mb-6">Avis des Étudiants</h1>

          <!-- Barre de recherche et filtre -->
          <div class="flex flex-col sm:flex-row gap-4 mb-6">
            <div class="flex-1 relative">
              <input
                type="text"
                v-model="searchQuery"
                placeholder="Rechercher des avis..."
                class="w-full pl-10 pr-10 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
              <svg
                class="absolute left-3 top-2.5 h-5 w-5 text-gray-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                ></path>
              </svg>
              <button
                v-if="searchQuery"
                @click="clearSearch"
                class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="h-5 w-5"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                  />
                </svg>
              </button>
            </div>

            <div class="w-full sm:w-64">
              <select
                v-model="selectedRating"
                class="w-full pl-3 pr-10 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              >
                <option value="0">Toutes les notes</option>
                <option value="1">1 étoile</option>
                <option value="2">2 étoiles</option>
                <option value="3">3 étoiles</option>
                <option value="4">4 étoiles</option>
                <option value="5">5 étoiles</option>
              </select>
            </div>
          </div>

          <!-- État de chargement -->
          <div v-if="isLoadingFeedbacks" class="text-center py-10">
            <i class="fas fa-spinner fa-spin text-blue-500 text-2xl"></i>
            <p class="mt-2 text-gray-600">Chargement des avis...</p>
          </div>

          <!-- Message d'erreur -->
          <div v-else-if="feedbackError" class="text-center py-10 text-red-500">
            {{ feedbackError }}
          </div>

          <!-- Liste des avis -->
          <div v-else class="space-y-6 bg-white rounded-lg p-6 shadow-sm">
            <div v-for="feedback in filteredAvis" :key="feedback.id" class="border-b pb-6 last:border-b-0">
              <div class="flex justify-between items-start">
                <div>
                  <h2 class="font-bold text-lg text-gray-800">
                    {{ feedback.etudiant.fullname }}
                  </h2>
                  <p class="text-sm text-gray-500">
                    {{ feedback.etudiant.specialite }} - {{ feedback.etudiant.niveau }}
                  </p>
                </div>
                <div class="flex items-center">
                  <div class="flex mr-2">
                    <span v-for="i in 5" :key="i" class="text-yellow-400 text-xl">
                      <span v-if="i <= feedback.note">★</span>
                      <span v-else>☆</span>
                    </span>
                  </div>
                  <span class="text-gray-500 text-sm">{{ formatDate(feedback.created_at) }}</span>
                </div>
              </div>
              
              <p class="mt-4 text-gray-700">{{ feedback.commentaire }}</p>

              <!-- Bouton Répondre -->
              <div v-if="!feedback.reponse" class="mt-4">
                <button
                  @click="repondreAuFeedback(feedback)"
                  class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                  </svg>
                  Répondre
                </button>
              </div>

              <!-- Section des réponses -->
              <div class="mt-4 space-y-4">
              <!-- État de chargement des réponses -->
              <div v-if="loadingReponses[feedback.id]" class="ml-8 text-gray-500">
                <i class="fas fa-spinner fa-spin"></i> Chargement des réponses...
              </div>

              <!-- Liste des réponses -->
              <div v-else-if="feedbackReponses[feedback.id] && feedbackReponses[feedback.id].length > 0" class="space-y-2">
                <div v-for="reponse in feedbackReponses[feedback.id]" :key="reponse.id" 
                  class="ml-6 p-3 bg-white rounded-md shadow-sm border border-gray-100 hover:shadow-sm transition-shadow duration-200"
                >
                  <div class="flex items-start space-x-2">
                    <!-- Icône du rôle -->
                    <div class="flex-shrink-0">
                      <div class="w-8 h-8 rounded-full flex items-center justify-center" 
                        :class="reponse.user_role === 'tuteur' ? 'bg-purple-50' : 'bg-blue-50'"
                      >
                        <svg v-if="reponse.user_role === 'tuteur'" class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15"/>
                        </svg>
                        <svg v-else class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                      </div>
                    </div>
                    
                    <!-- Contenu de la réponse -->
                    <div class="flex-grow">
                      <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium" :class="reponse.user_role === 'tuteur' ? 'text-purple-700' : 'text-blue-700'">
                          {{ reponse.user_role === 'tuteur' ? 'Réponse de l\'enseignant' : 'Réponse de l\'étudiant' }}
                        </span>
                        <div class="flex items-center">
                          <span class="text-xs text-gray-500 mr-2">{{ formatDate(reponse.created_at) }}</span>
                          
                          <!-- Menu pour les actions (modifier/supprimer) -->
                          <div v-if="isMyReponse(reponse)" class="relative">
                            <button 
                              @click.stop="toggleReponseMenu(reponse.id)"
                              class="text-gray-500 hover:text-gray-700 focus:outline-none p-1 rounded-full hover:bg-gray-200"
                            >
                              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                              </svg>
                            </button>
                            
                            <!-- Menu déroulant pour les réponses -->
                            <transition name="menu">
                              <div 
                                v-if="activeReponseMenu === reponse.id"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 border border-gray-200"
                                v-click-outside="closeReponseMenu"
                              >
                                <!-- Option Modifier -->
                                <button
                                  @click="editReponse(reponse, feedback.id)"
                                  class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center menu-option"
                                >
                                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                  </svg>
                                  Modifier
                                </button>
                                
                                <!-- Option Supprimer -->
                                <button
                                  @click="confirmDeleteReponse(reponse.id, feedback.id)"
                                  class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center menu-option"
                                >
                                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                  </svg>
                                  Supprimer
                                </button>
                              </div>
                            </transition>
                          </div>
                        </div>
                      </div>
                      <p class="text-sm text-gray-700">{{ reponse.contenu }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Message si aucune réponse -->
              <div v-else-if="!loadingReponses[feedback.id]" class="ml-8 text-gray-500">
                Aucune réponse pour cet avis
              </div>
            </div>
              <!-- <div class="mt-4 space-y-4">
                État de chargement des réponses
                <div v-if="loadingReponses[feedback.id]" class="ml-8 text-gray-500">
                  <i class="fas fa-spinner fa-spin"></i> Chargement des réponses...
                </div>

                Liste des réponses
                <div v-else-if="feedbackReponses[feedback.id] && feedbackReponses[feedback.id].length > 0" class="space-y-4">
                  <div v-for="reponse in feedbackReponses[feedback.id]" :key="reponse.id" class="ml-8 p-4 bg-gray-50 rounded-lg border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                      <div>
                        <span class="font-semibold text-blue-600">{{ reponse.user_role === 'tuteur' ? 'Réponse de l\'enseignant' : 'Réponse de l\'étudiant' }}</span>
                        <p class="mt-2 text-gray-600">{{ reponse.contenu }}</p>
                      </div>
                      <span class="text-sm text-gray-500">{{ formatDate(reponse.created_at) }}</span>
                    </div>
                  </div>
                </div>

                Message si aucune réponse
                <div v-else-if="!loadingReponses[feedback.id]" class="ml-8 text-gray-500">
                  Aucune réponse pour cet avis
                </div>
              </div> -->
            </div>


            <div v-if="!isLoadingFeedbacks && filteredAvis.length === 0" class="text-center py-10">
              <p class="text-gray-500">Aucun avis disponible pour ce cours.</p>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <!-- Modal de réponse -->
  <div v-if="showReponseModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg max-w-lg w-full p-6">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Répondre à l'avis</h3>
        <button @click="showReponseModal = false" class="text-gray-400 hover:text-gray-500">
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div class="mb-4">
        <p class="text-sm text-gray-500 mb-2">Avis de l'étudiant :</p>
        <p class="text-gray-700 bg-gray-50 p-3 rounded">{{ selectedFeedback?.commentaire }}</p>
      </div>

      <div class="mb-4">
        <label for="reponse" class="block text-sm font-medium text-gray-700 mb-2">Votre réponse</label>
        <textarea
          id="reponse"
          v-model="reponseText"
          rows="4"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          placeholder="Écrivez votre réponse ici..."
        ></textarea>
      </div>

      <div class="flex justify-end space-x-3">
        <button
          @click="showReponseModal = false"
          class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
        >
          Annuler
        </button>
        <button
          @click="submitReponse"
          class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          Envoyer la réponse
        </button>
      </div>
    </div>
  </div>

  <!-- Modal de modification de réponse -->
  <div v-if="showEditReponseModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg max-w-lg w-full p-6">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Modifier votre réponse</h3>
        <button @click="showEditReponseModal = false" class="text-gray-400 hover:text-gray-500">
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div class="mb-4">
        <label for="editReponse" class="block text-sm font-medium text-gray-700 mb-2">Votre réponse</label>
        <textarea
          id="editReponse"
          v-model="editReponseText"
          rows="4"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          placeholder="Modifiez votre réponse ici..."
        ></textarea>
      </div>

      <div class="flex justify-end space-x-3">
        <button
          @click="showEditReponseModal = false"
          class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
        >
          Annuler
        </button>
        <button
          @click="submitEditReponse"
          class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          Enregistrer les modifications
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import NavBarTuteur from "./NavBarTut.vue";
import SidebarTuteur from "./SidebarTut.vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

import { nextTick } from 'vue';

export default {
  name: "AvisTuteur",
  components: { NavBarTuteur, SidebarTuteur },
  data() {
    return {
      badWords: [
        "bhim",
        "yaayef"
      ],
      idCours: this.$route.params.id,
      showAvis: false,
      listeAvis: [],
      selectedRating: 0,
      searchQuery: "",
      isLoadingFeedbacks: false,
      feedbackError: null,
      activeReponseMenu: null,
      showReponseModal: false,
      selectedFeedback: null,
      selectedFeedbackId: null, // To know which feedback's responses to update
      reponseText: "",
      feedbackReponses: {},
      loadingReponses: {},
      studentInfo: null,
      tuteurInfo: null,

      // New properties for editing responses
      showEditReponseModal: false,
      selectedReponse: null, // The response object being edited
      editReponseText: "", // Text for the edit modal's textarea
    };
  },
  computed: {
    filteredAvis() {
      let filtered = this.listeAvis;
      
      if (this.selectedRating > 0) {
        filtered = filtered.filter(avis => avis.note == this.selectedRating);
      }
      
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase();
        filtered = filtered.filter(avis => 
          (avis.etudiant.fullname && avis.etudiant.fullname.toLowerCase().includes(query)) ||
          (avis.commentaire && avis.commentaire.toLowerCase().includes(query))
        );
      }
      
      return filtered;
    }
  },
  methods: {
    created() {
      this.loadUserInfos();
    },

    async checkComment(comment) {
      this.warning = "";

      const lower = comment.toLowerCase();
      if (this.badWords.some((word) => lower.includes(word))) {
        return false;
      }

      const apiKey = "AIzaSyD_Q97rsq5y0y-kWFuSQCtcMdu6kEsATHA"; 
      const endpoint = `https://commentanalyzer.googleapis.com/v1alpha1/comments:analyze?key=${apiKey}`;

      const body = {
        comment: { text: comment },
        languages: ["fr"],
        requestedAttributes: { TOXICITY: {} },
      };

      try {
        const response = await fetch(endpoint, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(body),
        });

        const result = await response.json();
        const score = result.attributeScores.TOXICITY.summaryScore.value;

        if (score > 0.7) {
          return false;
        } else {
          return true;
        }
      } catch (err) {
        console.error("Perspective API error:", err);
        return false;
      }
    },

    async fetchAvis() {
      this.isLoadingFeedbacks = true;
      this.feedbackError = null;
      
      try {
        const response = await axios.get(
          `http://localhost:8000/api/feedbacks/course/${this.idCours}`,
          {
            headers: {
              Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`
            }
          }
        );
        
        if (response.data.success) {
          this.listeAvis = response.data.feedbacks.map(feedback => {
            return {
              ...feedback,
              etudiant: {
                ...feedback.etudiant,
                fullname: feedback.etudiant.fullname || 'Étudiant'
              }
            };
          }).sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

          // Charger les réponses pour chaque avis
          this.listeAvis.forEach(feedback => {
            if (!this.feedbackReponses[feedback.id]) { // Fetch only if not already loaded or to refresh
                this.fetchReponses(feedback.id);
            }
          });
        } else {
          throw new Error(response.data.message || 'Erreur inconnue');
        }
      } catch (error) {
        console.error("Erreur lors du chargement des avis :", error);
        this.feedbackError = error.response?.data?.message || "Impossible de charger les avis";
        toast.error(this.feedbackError);
      } finally {
        this.isLoadingFeedbacks = false;
      }
    },

    loadUserInfos() {
      const studentData = localStorage.getItem('StudentAccountInfo');
      if (studentData) {
        this.studentInfo = JSON.parse(studentData);
      }
      const tuteurData = localStorage.getItem('TuteurAccountInfo');
      if (tuteurData) {
        this.tuteurInfo = JSON.parse(tuteurData);
      }
      // console.log("Infos utilisateur chargées:", this.studentInfo, this.tuteurInfo);
    },

    isMyReponse(reponse) {
      const studentInfo = JSON.parse(localStorage.getItem('StudentAccountInfo'));
      const tuteurInfo = JSON.parse(localStorage.getItem('TuteurAccountInfo'));
      
      const currentUser = JSON.parse(sessionStorage.getItem('CurrentUser'));

      return reponse.user_role === currentUser && (
        (currentUser === 'etudiant' && studentInfo && studentInfo.id === reponse.user_id) ||
        (currentUser === 'tuteur' && tuteurInfo && tuteurInfo.id === reponse.user_id)
      );
      // if (reponse.user_role === currentUser && studentInfo) {
      //   return studentInfo.id === reponse.user_id;
      // } else if (reponse.user_role === currentUser && tuteurInfo) {
      //   return tuteurInfo.id === reponse.user_id;
      // }
      // return false;
    },

    toggleReponseMenu(reponseId) {
      this.activeReponseMenu = this.activeReponseMenu === reponseId ? null : reponseId;
    },

    closeReponseMenu() {
      this.activeReponseMenu = null;
    },

    async fetchReponses(feedbackId) {
      this.loadingReponses[feedbackId] = true;
      try {
        const response = await axios.get(`http://localhost:8000/api/feedback/${feedbackId}/reponses`,
          {
            headers: {
              Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`
            }
          }
        );
        this.feedbackReponses[feedbackId] = response.data.reponses.map(reponse => ({
          ...reponse,
          contenu: reponse.reponse, // Ensure 'contenu' is used consistently
          user_role: reponse.user_role // Ensure user_role is part of the reponse object
        })).sort((a,b) => new Date(a.created_at) - new Date(b.created_at)); // Sort responses by creation time
      } catch (error) {
        console.error(`Erreur lors du chargement des réponses pour l'avis ${feedbackId}:`, error);
      } finally {
        this.loadingReponses[feedbackId] = false;
      }
    },

    // toggleAvis() {
    //   this.showAvis = !this.showAvis;
    //   if (this.showAvis && this.listeAvis.length === 0) {
    //     this.fetchAvis();
    //   }
    // },

    editReponse(reponse, feedbackId) {
      this.selectedReponse = { ...reponse }; // Clone to avoid direct mutation if modal is cancelled
      this.selectedFeedbackId = feedbackId; // Store feedbackId to know which list of reponses to update
      this.editReponseText = reponse.contenu;
      this.showEditReponseModal = true;
      this.activeReponseMenu = null; // Close the three-dot menu
    },

    async submitEditReponse() {
      if (!this.editReponseText.trim()) {
        toast.warn("La réponse ne peut pas être vide.");
        return;
      }
      const tuteurInfo = JSON.parse(localStorage.getItem("TuteurAccountInfo"));
      if (!tuteurInfo || !this.selectedReponse) {
        toast.error("Erreur : Informations utilisateur ou réponse non trouvées.");
        this.showEditReponseModal = false;
        return;
      }

      if (!await this.checkComment(this.editReponseText)) {
        toast.error('Votre réponse contient des mots inappropriés');
        return;
      } else {
        try {
          const token = JSON.parse(localStorage.getItem('TuteurAccountInfo')).token;
          const userRole = this.selectedReponse.user_role;
          const response = await axios.put(
            `http://localhost:8000/api/reponse/${this.selectedReponse.id}`,
            { 
              reponse: this.editReponseText,
              user_role: userRole
            },
            {
              headers: {
                Authorization: `Bearer ${token}`
              }
            }
          );
  
          if (response.status === 200) {
            const updatedReponseFromServer = {
               ...response.data.reponse, 
               contenu: response.data.reponse.reponse || this.editReponseText, // Ensure 'contenu' is correctly mapped
               user_role: response.data.reponse.user_role || 'tuteur' // ensure user_role is present
              };
  
            const feedbackId = this.selectedFeedbackId;
            if (this.feedbackReponses[feedbackId]) {
              const index = this.feedbackReponses[feedbackId].findIndex(r => r.id === updatedReponseFromServer.id);
              if (index !== -1) {
                this.feedbackReponses[feedbackId].splice(index, 1, updatedReponseFromServer);
                // Use nextTick to ensure DOM updates if direct splice isn't reactive enough for complex objects/arrays
                await nextTick(); 
              }
            }
            toast.success("Réponse modifiée avec succès !");
            this.showEditReponseModal = false;
            this.selectedReponse = null;
            this.editReponseText = "";
          } else {
            toast.error(response.data.message || "Erreur lors de la modification de la réponse.");
          }
        } catch (error) {
          console.error("Erreur lors de la modification de la réponse:", error);
          toast.error(error.response?.data?.message || "Impossible de modifier la réponse.");
        }
      }
    },

    confirmDeleteReponse(reponseId, feedbackId) {
      this.activeReponseMenu = null; // Close menu first
      if (window.confirm("Êtes-vous sûr de vouloir supprimer cette réponse ?")) {
        this.deleteReponse(reponseId, feedbackId);
      }
    },

    async deleteReponse(reponseId, feedbackId) {
      const token = JSON.parse(localStorage.getItem("TuteurAccountInfo")).token;

      try {
        const response = await axios.delete(
          `http://localhost:8000/api/reponse/${reponseId}`,
          {
            headers: {
              Authorization: `Bearer ${token}`
            },
            data: { 
              user_role: 'tuteur' 
            }
          }
        );

        if (response.status === 200) {
          if (this.feedbackReponses[feedbackId]) {
            this.feedbackReponses[feedbackId] = this.feedbackReponses[feedbackId].filter(r => r.id !== reponseId);
            await nextTick(); // Ensure DOM updates
          }
          toast.success("Réponse supprimée avec succès !");
        } else {
          toast.error(response.data.message || "Erreur lors de la suppression de la réponse.");
        }
      } catch (error) {
        console.error("Erreur lors de la suppression de la réponse:", error);
        toast.error(error.response?.data?.message || "Impossible de supprimer la réponse.");
      }
    },

    formatDate(dateString) {
      const date = new Date(dateString);
      const now = new Date();
      const diff = now - date;
      
      const seconds = Math.floor(diff / 1000);
      const minutes = Math.floor(seconds / 60);
      const hours = Math.floor(minutes / 60);
      const days = Math.floor(hours / 24);
      const months = Math.floor(days / 30);
      const years = Math.floor(months / 12);
      
      if (years > 0) return `${years} an${years > 1 ? 's' : ''}`;
      if (months > 0) return `${months} mois`;
      if (days > 0) return `${days} jour${days > 1 ? 's' : ''}`;
      if (hours > 0) return `${hours} heure${hours > 1 ? 's' : ''}`;
      if (minutes > 0) return `${minutes} minute${minutes > 1 ? 's' : ''}`;
      return `${seconds} seconde${seconds > 1 ? 's' : ''}`;
    },

    clearSearch() {
      this.searchQuery = "";
    },

    repondreAuFeedback(feedback) {
      this.selectedFeedback = feedback;
      this.showReponseModal = true;
      this.reponseText = "";
    },

    async submitReponse() {
      if (!this.reponseText.trim()) {
        toast.error("Veuillez écrire une réponse avant d'envoyer");
        return;
      }

      if (!await this.checkComment(this.reponseText)) {
        toast.error('Votre réponse contient des mots inappropriés');
        return;
      }else {

        try {
          const token = JSON.parse(sessionStorage.getItem("token"));
          const tuteurId = JSON.parse(localStorage.getItem('TuteurAccountInfo')).id;
          const response = await axios.post(
            `http://localhost:8000/api/feedbacks/${this.selectedFeedback.id}/reponse`,
            {
              reponse: this.reponseText,
              user_id: tuteurId,
              user_role: 'tuteur'
            },
            {
              headers: {
                'Authorization': `Bearer ${token}`
              }
            }
          );
  
          if (response.status === 200) {
            toast.success('Votre réponse a été envoyée avec succès');
            this.showReponseModal = false;
            this.fetchReponses(this.selectedFeedback.id);
          } else {
            throw new Error(response.data.message || 'Erreur lors de l\'envoi de la réponse');
          }
        } catch (error) {
          console.error('Erreur lors de l\'envoi de la réponse:', error);
          toast.error(error.response?.data?.message || 'Erreur lors de l\'envoi de la réponse');
        }
      }

    }
  },
  mounted() {
    this.fetchAvis();
    this.studentInfo = JSON.parse(localStorage.getItem('StudentAccountInfo'));
    this.tuteurInfo = JSON.parse(localStorage.getItem('TuteurAccountInfo'));
  }
};
</script>

<style scoped>
.feedback-btn {
  @apply inline-flex items-center px-4 py-2 rounded-md text-sm font-medium transition-colors duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2;
}

.feedback-btn-primary {
  @apply text-white bg-blue-600 hover:bg-blue-700 focus:ring-blue-500;
}

.feedback-btn-secondary {
  @apply text-blue-700 bg-blue-100 hover:bg-blue-200 focus:ring-blue-500;
}

.btn-icon {
  @apply h-5 w-5 mr-2;
}

.menu-enter-active,
.menu-leave-active {
  transition: opacity 0.2s, transform 0.2s;
}

.menu-enter-from,
.menu-leave-to {
  opacity: 0;
  transform: scale(0.95);
}
</style>
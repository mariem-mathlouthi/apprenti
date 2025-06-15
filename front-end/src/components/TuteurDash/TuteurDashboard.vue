<template>
  <div class="flex flex-col min-h-screen bg-gradient-to-br from-white to-indigo-50">
    <Navbar class="z-50" />
    <div class="flex-grow grid grid-cols-12 gap-4 pt-16">
      <div class="col-span-3">
        <Sidebar />
      </div>
      <div class="col-span-9 mr-10 mb-6 overflow-auto">
        <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg p-8">
          <!-- Header -->
          <div class="mb-10">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Bienvenue, {{ fullname || 'Tuteur' }}!</h1>
            <p class="text-gray-600 max-w-2xl">Votre espace pour gérer vos cours, quiz et rendez-vous.</p>
          </div>

          <!-- Stats Cards -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
            <!-- Cours Card -->
            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold text-gray-700">Cours</h3>
                <div class="bg-indigo-100 p-3 rounded-lg">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                  </svg>
                </div>
              </div>
              <p class="text-4xl font-bold text-indigo-600 mb-3">{{ coursesCount }}</p>
              <router-link to="/cours" class="text-indigo-600 text-sm hover:text-indigo-700 font-medium flex items-center">
                Gérer les cours
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </router-link>
            </div>

            <!-- Quiz Card -->
            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold text-gray-700">Quiz</h3>
                <div class="bg-purple-100 p-3 rounded-lg">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 12h.01M7 7h10a2 2 0 012 2v8a2 2 0 01-2 2H7a2 2 0 01-2-2V9a2 2 0 012-2z" />
                  </svg>
                </div>
              </div>
              <p class="text-4xl font-bold text-purple-600 mb-3">{{ quizzesCount }}</p>
              <router-link to="/QuizzList" class="text-purple-600 text-sm hover:text-purple-700 font-medium flex items-center">
                Créer un quiz
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </router-link>
            </div>

            <!-- Appointments Card -->
            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold text-gray-700">Rendez-vous</h3>
                <div class="bg-pink-100 p-3 rounded-lg">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-pink-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                </div>
              </div>
              <p class="text-4xl font-bold text-pink-600 mb-3">{{ appointmentsCount }}</p>
              <router-link to="/AppointVideoCall" class="text-pink-600 text-sm hover:text-pink-700 font-medium flex items-center">
                Voir le calendrier
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </router-link>
            </div>

            <!-- Students Card -->
            <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold text-gray-700">Étudiants</h3>
                <div class="bg-green-100 p-3 rounded-lg">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                  </svg>
                </div>
              </div>
              <p class="text-4xl font-bold text-green-600 mb-3">{{ studentsCount }}</p>
              <router-link to="/tutor-students" class="text-green-600 text-sm hover:text-green-700 font-medium flex items-center">
                Voir les étudiants
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
              </router-link>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="mb-10">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
              Actions Rapides
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
              <router-link to="/create-course" class="bg-gray-50 hover:bg-gray-100 p-6 rounded-xl flex items-center transition-colors duration-300 border border-gray-200 shadow-sm hover:shadow-md">
                <div class="bg-indigo-100 p-3 rounded-lg mr-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                  </svg>
                </div>
                <div>
                  <h3 class="font-semibold text-gray-700">Créer un cours</h3>
                  <p class="text-sm text-gray-500">Ajouter un nouveau cours</p>
                </div>
              </router-link>

              <router-link to="/create-quiz" class="bg-gray-50 hover:bg-gray-100 p-6 rounded-xl flex items-center transition-colors duration-300 border border-gray-200 shadow-sm hover:shadow-md">
                <div class="bg-purple-100 p-3 rounded-lg mr-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 12h.01M7 7h10a2 2 0 012 2v8a2 2 0 01-2 2H7a2 2 0 01-2-2V9a2 2 0 012-2z" />
                  </svg>
                </div>
                <div>
                  <h3 class="font-semibold text-gray-700">Créer un quiz</h3>
                  <p class="text-sm text-gray-500">Évaluer vos étudiants</p>
                </div>
              </router-link>

              <router-link to="/schedule-appointment" class="bg-gray-50 hover:bg-gray-100 p-6 rounded-xl flex items-center transition-colors duration-300 border border-gray-200 shadow-sm hover:shadow-md">
                <div class="bg-pink-100 p-3 rounded-lg mr-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-pink-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                </div>
                <div>
                  <h3 class="font-semibold text-gray-700">Planifier RDV</h3>
                  <p class="text-sm text-gray-500">Créer un nouveau rendez-vous</p>
                </div>
              </router-link>

              <router-link to="/tutor-ressources" class="bg-gray-50 hover:bg-gray-100 p-6 rounded-xl flex items-center transition-colors duration-300 border border-gray-200 shadow-sm hover:shadow-md">
                <div class="bg-blue-100 p-3 rounded-lg mr-4">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                </div>
                <div>
                  <h3 class="font-semibold text-gray-700">Ressources</h3>
                  <p class="text-sm text-gray-500">Gérer les documents</p>
                </div>
              </router-link>
            </div>
          </div>

          <!-- Recent Appointments -->
          <div class="mb-10">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              Rendez-vous à venir
            </h2>
            <div class="bg-white rounded-xl p-6 shadow-md border border-gray-100">
              <div v-if="upcomingAppointments.length > 0" class="space-y-4">
                <div v-for="(appointment, index) in upcomingAppointments" :key="index"
                     class="border-b border-gray-200 pb-4 last:border-0 last:pb-0">
                  <div class="flex items-start">
                    <div class="bg-blue-100 p-3 rounded-lg mr-4">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                    </div>
                    <div class="flex-1">
                      <h4 class="font-medium text-gray-800">{{ appointment.title }}</h4>
                      <p class="text-gray-600 text-sm mt-1">Étudiants: {{ appointment.student_ids?.length || 0 }}</p>
                      <p class="text-gray-600 text-sm mt-1">Salle: {{ appointment.roomId || 'Non spécifiée' }}</p>
                      <p class="text-gray-500 text-xs mt-2">{{ formatDate(appointment.date) }}</p>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-8 text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p>Aucun rendez-vous à venir</p>
              </div>
            </div>
          </div>

          <!-- Recent Feedback -->
          <div>
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
              </svg>
              Feedback Récent
            </h2>
            <div class="bg-white rounded-xl p-6 shadow-md border border-gray-100">
              <div v-if="recentFeedback.length > 0" class="space-y-4">
                <div v-for="(feedback, index) in recentFeedback" :key="index"
                     class="border-b border-gray-200 pb-4 last:border-0 last:pb-0">
                  <div class="flex items-start">
                    <div class="bg-yellow-100 p-3 rounded-lg mr-4">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                      </svg>
                    </div>
                    <div class="flex-1">
                      <h4 class="font-medium text-gray-800">{{ feedback.course_title || 'Cours sans titre' }}</h4>
                      <div class="flex items-center mt-1">
                        <span class="text-yellow-500 mr-1">★</span>
                        <span class="text-gray-600 text-sm">{{ feedback.rating || 0 }}/5</span>
                      </div>
                      <p class="text-gray-600 text-sm mt-2">{{ feedback.comment || 'Aucun commentaire' }}</p>
                      <p class="text-gray-500 text-xs mt-2">{{ formatDate(feedback.created_at) }}</p>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-8 text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                </svg>
                <p>Aucun feedback récent</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import "aos/dist/aos.css";
import AOS from "aos";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import Sidebar from "./SidebarTut.vue";
import Navbar from "./NavBarTut.vue";
import axios from 'axios';

export default {
  data() {
    return {
      fullname: "",
      coursesCount: 0,
      quizzesCount: 0,
      appointmentsCount: 0,
      studentsCount: 0,
      upcomingAppointments: [],
      recentFeedback: [],
      idTuteur: null,
      loading: false,
      error: null
    };
  },
  components: {
    Sidebar,
    Navbar,
  },
  methods: {
    formatDate(dateString) {
      if (!dateString) return 'Date non spécifiée';
      try {
        const date = new Date(dateString);
        return new Intl.DateTimeFormat('fr-FR', {
          day: '2-digit',
          month: 'short',
          year: 'numeric',
          hour: '2-digit',
          minute: '2-digit'
        }).format(date);
      } catch (e) {
        console.error('Erreur de formatage de date:', e);
        return dateString;
      }
    },
    async fetchTutorInfo() {
      try {
        const storedData = localStorage.getItem("TutorAccountInfo");
        if (storedData) {
          const tutorInfo = JSON.parse(storedData);
          this.fullname = tutorInfo.fullname || "Tuteur";
          this.idTuteur = tutorInfo.id;
        } else {
          console.warn("Informations du tuteur non trouvées dans localStorage");
          this.error = "Informations du tuteur non trouvées";
        }
      } catch (error) {
        console.error('Erreur lors de la récupération des informations du tuteur:', error);
        this.error = "Erreur lors du chargement des informations";
      }
    },
    async fetchDashboardData() {
      if (!this.idTuteur) {
        this.error = "ID tuteur non disponible";
        return;
      }

      this.loading = true;
      this.error = null;

      try {
        const token = JSON.parse(sessionStorage.getItem('token'));
        if (!token) {
          throw new Error('Token non trouvé');
        }

        const headers = {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        };

        // Utilisation de Promise.all pour les requêtes parallèles
        const [coursesRes, quizzesRes, appointmentsRes, feedbackRes] = await Promise.all([
          axios.get('http://localhost:8000/api/cours-by-tuteur', { headers })
            .catch(err => {
              console.error('Erreur cours:', err);
              return { data: { cours: [] } };
            }),
          axios.get('http://localhost:8000/api/quizz-by-tuteur', { headers })
            .catch(err => {
              console.error('Erreur quiz:', err);
              return { data: { quizz: [] } };
            }),
          axios.get(`http://localhost:8000/api/appointByTuteur/${this.idTuteur}`, { headers })
            .catch(err => {
              console.error('Erreur rendez-vous:', err);
              return { data: { appointments: [] } };
            }),
          axios.get(`http://localhost:8000/api/feedbacks/course/${this.idTuteur}`, { headers })
            .catch(err => {
              console.error('Erreur feedback:', err);
              return { data: [] };
            })
        ]);

        // Mise à jour des données
        this.coursesCount = coursesRes.data.cours?.length || 0;
        this.quizzesCount = quizzesRes.data.quizz?.length || 0;
        this.appointmentsCount = appointmentsRes.data.appointments?.length || 0;
        this.recentFeedback = feedbackRes.data?.slice(0, 3) || []; // Limite à 3 feedbacks

        // Gestion des rendez-vous à venir
        const now = new Date();
        this.upcomingAppointments = (appointmentsRes.data.appointments || [])
          .filter(app => {
            try {
              const appDate = new Date(app.date);
              return appDate >= now;
            } catch (e) {
              console.error('Date de rendez-vous invalide:', app.date);
              return false;
            }
          })
          .sort((a, b) => new Date(a.date) - new Date(b.date))
          .slice(0, 3);

        // Récupération du nombre d'étudiants
        try {
          const studentsRes = await axios.get(`http://localhost:8000/api/subcsriptWithTueur`, { headers });
          this.studentsCount = studentsRes.data.length || 0;
        } catch (err) {
          console.error('Erreur étudiants:', err);
          this.studentsCount = 0;
        }

      } catch (error) {
        console.error('Erreur globale:', error);
        this.error = "Erreur lors du chargement des données";
        toast.error(this.error, { autoClose: 5000 });
      } finally {
        this.loading = false;
      }
    }
  },
  async mounted() {
    await this.fetchTutorInfo();
    
    if (this.idTuteur) {
      toast.success(`Bienvenue, ${this.fullname || 'Tuteur'}!`, { autoClose: 2000 });
      await this.fetchDashboardData();
    } else {
      toast.warn("Veuillez vous connecter", { autoClose: 3000 });
    }

    AOS.init({
      duration: 1000,
      easing: "ease-out-cubic",
      once: true,
      mirror: false,
    });
  },
};
</script>
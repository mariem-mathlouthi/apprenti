<template>
  <div class="flex flex-col min-h-screen bg-gradient-to-br from-white to-indigo-50">
    <Navbar class="z-50" /> <!-- Ensure Navbar is on top -->
    <div class="flex-grow grid grid-cols-12 gap-4 pt-16"> <!-- Add pt-16 for Navbar height -->
      <div class="col-span-3">
        <Sidebar />
      </div>
      <div class="col-span-9 mr-10 mb-6 overflow-auto"> <!-- Add overflow-auto for scrolling -->
        <!-- Main Dashboard Content -->
        <div class="bg-white/80 backdrop-blur-sm rounded-xl shadow-lg p-8">
          <!-- Header Section with Welcome Message -->
        <div class="mb-10">
          <h1 class="text-4xl font-bold text-gray-800 mb-2">Bienvenue, {{ fullname || 'Étudiant' }}!</h1>
          <p class="text-gray-600 max-w-2xl">Votre espace personnalisé pour suivre vos cours, stages et notifications.</p>
        </div>

        <!-- Stats Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
          <!-- Courses Card -->
          <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-xl font-semibold text-gray-700">Cours Disponibles</h3>
              <div class="bg-indigo-100 p-3 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
              </div>
            </div>
            <p class="text-4xl font-bold text-indigo-600 mb-3">{{ coursCount }}</p>
            <router-link to="/ConsultListCours" class="text-indigo-600 text-sm hover:text-indigo-700 font-medium flex items-center">
              Voir tous les cours
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </router-link>
          </div>

          <!-- Internships Card -->
          <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-xl font-semibold text-gray-700">Offres de Stage</h3>
              <div class="bg-purple-100 p-3 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
              </div>
            </div>
            <p class="text-4xl font-bold text-purple-600 mb-3">{{ offresCount }}</p>
            <router-link to="/OffersListStd" class="text-purple-600 text-sm hover:text-purple-700 font-medium flex items-center">
              Voir toutes les offres
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </router-link>
          </div>

          <!-- Notifications Card -->
          <div class="bg-white p-6 rounded-xl shadow-md border border-gray-100 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-xl font-semibold text-gray-700">Notifications</h3>
              <div class="bg-pink-100 p-3 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-pink-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
              </div>
            </div>
            <p class="text-4xl font-bold text-pink-600 mb-3">{{ visibleNotificationsCount }}</p>
            <router-link to="/Consulternotif" class="text-pink-600 text-sm hover:text-pink-700 font-medium flex items-center">
              Voir toutes les notifications
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </router-link>
          </div>
        </div>

        <!-- Quick Actions Section -->
        <div class="mb-10">
          <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            Actions Rapides
          </h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <router-link to="/OffersListStd" class="bg-gray-50 hover:bg-gray-100 p-6 rounded-xl flex items-center transition-colors duration-300 border border-gray-200 shadow-sm hover:shadow-md">
              <div class="bg-purple-100 p-3 rounded-lg mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
              </div>
              <div>
                <h3 class="font-semibold text-gray-700">Postuler à un stage</h3>
                <p class="text-sm text-gray-500">Explorez les offres disponibles</p>
              </div>
            </router-link>

            <router-link to="/ConsultListCours" class="bg-gray-50 hover:bg-gray-100 p-6 rounded-xl flex items-center transition-colors duration-300 border border-gray-200 shadow-sm hover:shadow-md">
              <div class="bg-indigo-100 p-3 rounded-lg mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
              </div>
              <div>
                <h3 class="font-semibold text-gray-700">Accéder aux cours</h3>
                <p class="text-sm text-gray-500">Consultez vos ressources</p>
              </div>
            </router-link>

            <router-link to="/EtudiantAccount" class="bg-gray-50 hover:bg-gray-100 p-6 rounded-xl flex items-center transition-colors duration-300 border border-gray-200 shadow-sm hover:shadow-md">
              <div class="bg-pink-100 p-3 rounded-lg mr-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-pink-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <div>
                <h3 class="font-semibold text-gray-700">Profil étudiant</h3>
                <p class="text-sm text-gray-500">Gérez vos informations</p>
              </div>
            </router-link>
          </div>
        </div>

        <!-- Recent Activity Section -->
        <div>
          <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-3 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Activité Récente
          </h2>
          <div class="bg-white rounded-xl p-6 shadow-md border border-gray-100">
            <div v-if="visibleNotificationsCount > 0" class="space-y-4">
              <div v-for="(notification, index) in recentNotifications" :key="index"
                   class="border-b border-gray-200 pb-4 last:border-0 last:pb-0">
                <div class="flex items-start">
                  <div :class="getNotificationColorClass(notification.type, 'iconContainer')" class="p-2 rounded-lg mr-4">
                     <svg xmlns="http://www.w3.org/2000/svg" :class="getNotificationColorClass(notification.type, 'icon')" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                  </div>
                  <div class="flex-1">
                    <h4 class="font-medium text-gray-800">{{ notification.title }}</h4>
                    <p class="text-gray-600 text-sm mt-1">{{ notification.message }}</p>
                    <p class="text-gray-500 text-xs mt-2">{{ formatDate(notification.date) }}</p>
                  </div>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-8 text-gray-500">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>
              <p>Aucune notification récente</p>
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
import Sidebar from "./Sidebar.vue";
import Navbar from "./NavBarStd.vue";
import axios from 'axios';

export default {
  data: function () {
    return {
      fullname: "",
      coursCount: 0,
      offresCount: 0,
      notifications: [], // Initialize with some dummy data for styling
      idEtudiant: null, // Will be fetched from localStorage
    };
  },
  components: {
    Sidebar,
    Navbar,
  },
  computed: {
    visibleNotificationsCount() {
      return this.notifications.filter(notif => notif.visibility === 'shown').length;
    },
    recentNotifications() {
      // Filter for 'shown', sort by date descending, and return the 3 most recent
      return this.notifications
        .filter(notif => notif.visibility === 'shown')
        .sort((a, b) => new Date(b.date) - new Date(a.date))
        .slice(0, 3);
    },
  },
  methods: {
    requestNotificationPermission() {
      Notification.requestPermission().then((permission) => {
        if (permission === "granted") {
          console.log("Permission pour les notifications accordée.");
        } else {
          console.log("Permission pour les notifications refusée.");
        }
      });
    },
    formatDate(dateString) {
      if (!dateString) return '';
      const date = new Date(dateString);
      return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      }).format(date);
    },
    getNotificationColorClass(type, element) {
      const colors = {
        offre: { iconContainer: 'bg-purple-100', icon: 'text-purple-600' },
        cours: { iconContainer: 'bg-indigo-100', icon: 'text-indigo-600' },
        attestation: { iconContainer: 'bg-amber-100', icon: 'text-amber-600' },
        demande: { iconContainer: 'bg-green-100', icon: 'text-green-600' },
        appointment: { iconContainer: 'bg-red-100', icon: 'text-red-600' },
        default: { iconContainer: 'bg-gray-100', icon: 'text-gray-600' },
      };
      return (colors[type] || colors.default)[element];
    },
    async fetchStudentInfo() {
      const storedData = localStorage.getItem("StudentAccountInfo");
      if (storedData) {
        const studentInfo = JSON.parse(storedData);
        this.fullname = studentInfo.fullname || "";
        this.idEtudiant = studentInfo.id;
      } else {
        console.warn("Informations de l'étudiant non trouvées dans localStorage.");
        // Potentially redirect to login or show an error
      }
    },
    async fetchDashboardData() {
      if (!this.idEtudiant) {
        toast.error("ID étudiant non disponible. Impossible de charger les données.", { autoClose: 3000 });
        return;
      }
      try {
        const headers = {
          'Authorization': `Bearer ${JSON.parse(sessionStorage.getItem('token'))}`
        };
        const [coursResponse, offresResponse, notificationsResponse] = await Promise.all([
          axios.get('http://localhost:8000/api/cours', { headers: headers }),
          axios.get('http://localhost:8000/api/allOffres', { headers: headers }),
          axios.get(`http://localhost:8000/api/getAllNotifications/`, { headers: headers }) // Assuming this endpoint exists
        ]);
        localStorage.setItem("notifications", JSON.stringify(notificationsResponse.data));

        this.coursCount = coursResponse.data.cours.length || 0;
        this.offresCount = offresResponse.data.offres.length || 0;
        this.notifications = notificationsResponse.data.notifications || [];
        
      } catch (error) {
        console.error('Erreur lors de la récupération des données du tableau de bord:', error);
        toast.error("Erreur lors du chargement des données.", { autoClose: 3000 });
        // Fallback for notifications if API fails
        // const storedNotifications = localStorage.getItem("notifications");
        // if (storedNotifications) {
        //   this.notifications = JSON.parse(storedNotifications).notifications || [];
        // }
      }
    },
  },
  async mounted() {
    this.requestNotificationPermission();
    await this.fetchStudentInfo(); // Fetch student info first
    
    if (this.idEtudiant) {
        toast.success(`Bienvenue, ${this.fullname || 'Étudiant'}!`, {
        autoClose: 2000,
      });
      await this.fetchDashboardData(); // Then fetch dashboard data
    } else {
       toast.warn("Veuillez vous connecter pour voir votre tableau de bord.", { autoClose: 3000 });
    }
  },
  created() {
    this.$nextTick(() => {
      AOS.init({
        duration: 1000, // Shorter duration for dashboard elements
        easing: "ease-out-cubic",
        once: true,
        mirror: false,
      });
    });
  },
};
</script>
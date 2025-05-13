<template>
  <div id="app" class="flex flex-col h-screen">
    <!-- Navbar & Sidebar -->
    <NavBarStd />
    <Sidebar />
    
    <!-- Main Content -->
    <div class="main-content">
      <!-- Video call container - Positioned to not cover navigation -->
      <div class="calendar-container">
        <div class="header">
          <h1 class="title">Mes rendez-vous</h1>
          <p class="subtitle">Consultez vos sessions de vidéoconférence planifiées</p>
        </div>

        <div class="calendar-view">
          <!-- For demonstration, we'll show a list of appointments -->
          <div class="appointments-list">
            <h2 class="section-title">Rendez-vous à venir</h2>

            <div v-if="appointments.length === 0" class="no-appointments">
              Aucun rendez-vous planifié pour le moment.
            </div>

            <div v-else class="appointment-cards">
              <div v-for="(appointment, index) in appointments" :key="index" class="appointment-card">
                <div class="appointment-date">
                  <div class="date-day">{{ formatDay(appointment.date) }}</div>
                  <div class="date-month">{{ formatMonth(appointment.date) }}</div>
                </div>

                <div class="appointment-details">
                  <h3 class="appointment-title">{{ appointment.title }}</h3>
                  <div class="appointment-time">
                    <i class="fas fa-clock"></i> {{ formatTime(appointment.date) }}
                  </div>
                  <div class="appointment-tutor">
                    <i class="fas fa-user-tie"></i> {{ appointment.tutorName || 'Tuteur' }}
                  </div>
                  <div class="appointment-description">{{ appointment.description }}</div>
                </div>

                <div class="appointment-actions">
                  <button 
                    @click="joinVideoCall(appointment)" 
                    class="join-call-btn"
                    :disabled="!appointment.isCallStarted"
                    :class="{ 'disabled': !appointment.isCallStarted }"
                  >
                    <i class="fas fa-video"></i>
                    <span class="tooltip">{{ appointment.isCallStarted ? 'Rejoindre' : 'Pas encore commencé' }}</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
  
<script>
  import Sidebar from "./Sidebar.vue";
  import NavBarStd from "./NavBarStd.vue";
  import { ZegoUIKitPrebuilt } from '@zegocloud/zego-uikit-prebuilt';
  import { ref, onMounted, computed } from 'vue';
  import { useRoute } from 'vue-router';
  import axios from 'axios';
  import Pusher from "pusher-js";
  
  export default {
    name: 'AppointmentView',
    components: { Sidebar, NavBarStd },
    setup() {
      const API_BASE_URL = 'http://localhost:8000';
      const route = useRoute();
      const appointments = ref([]);
      const isLoading = ref(false);
      const  pusher = ref(null);
      const  channel = ref(null);
      const  idEtudiant = ref(null);
      const  activeCallAppointment = ref(null);
      const isVideoCallActive = ref(false);
      
      // Load appointments on component mount
      onMounted( async () => {
        initializePusher();
        await fetchAppointments();
        const roomID = route.query.roomID;
        const appointmentId = route.query.appointmentId;
        
        if (roomID && appointmentId) {
          // Auto-join the call if roomID is provided in URL
          setTimeout(() => {
            const appointment = {
              id: appointmentId,
              roomId: roomID
            };
            joinVideoCall(appointment);
          }, 1000);
        }

        checkForActiveCalls();
      });

      const showNotification = (title, options) => {
        if (Notification.permission === "granted") {
          new Notification(title, options);
        } else if (Notification.permission !== "denied") {
          // requestNotificationPermission();
          console.log("Notification permission denied");
        }
      };

      const initializePusher = () => {
        Pusher.logToConsole = true;

        pusher.value = new Pusher("d46657216dc865de1519", {
          cluster: "eu",
        });
        idEtudiant.value = JSON.parse(localStorage.getItem("StudentAccountInfo")).id;

        channel.value = pusher.value.subscribe(`appointement.${idEtudiant.value}`);
        channel.value.bind("notification-event", (data) => {
          // if (data && data.appointmentId) {
            // const appointment = appointments.value.find(app => app.id === data.appointmentId);
            // if (appointment) {
              appointment.isCallStarted = true;
              isVideoCallActive.value = true;
              showNotification("Rendez-vous", {
                body: `Le rendez-vous est prêt à être rejoint`,
                icon: "./logo.png",
              });
            // }
          // }
        });
      };

      const fetchAppointments = async () => {
          isLoading.value = true;
          try {
            const response = await axios.get(`${API_BASE_URL}/api/appointsCall`, {
              headers: { 
                Authorization: `Bearer ${JSON.parse(localStorage.getItem('StudentAccountInfo')).token}` 
              }
            });
            
            appointments.value = response.data.map(appointment => ({
              ...appointment,
              date: new Date(appointment.date),
              isCallStarted: false, // Default value
            }));
          } catch (error) {
            console.error('Error fetching appointments:', error);
            // showNotification('Erreur lors du chargement des rendez-vous', 'error');
          } finally {
            isLoading.value = false;
          }
        };
      
      // Format functions for displaying dates
      const formatDay = (date) => {
        return new Date(date).getDate();
      };
      
      const formatMonth = (date) => {
        return new Date(date).toLocaleString('default', { month: 'short' });
      };
      
      const formatTime = (date) => {
        return new Date(date).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
      };
      
      // Check if an appointment is currently active (tutor has started the call)
      // const isAppointmentActive = (appointment) => {
      //   // In a real app, this would check with the backend if the call is active
      //   // For demonstration, we'll just return the active property
      //   return appointment.active;
      // };
      
      // Check for active calls (in a real app, this would use websockets)
      const checkForActiveCalls = () => {
        // Simulate a tutor starting a call for the first appointment
        setTimeout(() => {
          if (appointments.value.length > 0) {
            appointments.value[0].active = true;
          }
        }, 5000);
      };
      
      // Join a video call for an appointment
      const joinVideoCall = (appointment) => {
        window.open(`/video-call?roomID=${appointment.roomId}&userName=${JSON.parse(localStorage.getItem('StudentAccountInfo')).fullname}`, '_blank');
      };
      
      
      return {
        appointments,
        formatDay,
        formatMonth,
        formatTime,
        // isAppointmentActive,
        joinVideoCall,
      };
    }
  };
</script>
  
<style scoped>
  .main-content {
    margin-left: 250px; /* Width of the sidebar */
    margin-top: 72px; /* Height of the navbar */
    padding: 20px;
    width: calc(100% - 250px);
    min-height: calc(100vh - 72px);
    position: relative;
  }
  
  .video-container {
    position: fixed;
    top: 72px; /* Position below navbar */
    left: 250px; /* Position to the right of sidebar */
    width: calc(100% - 250px); /* Adjust width to not cover sidebar */
    height: calc(100vh - 72px); /* Adjust height to not cover navbar */
    z-index: 100; /* Lower z-index to ensure navigation remains accessible */
    background-color: #f8f9fa;
    display: none;
  }
  
  .video-call-controls {
    position: absolute;
    top: 20px;
    right: 20px;
    display: flex;
    gap: 10px;
    z-index: 101; /* Ensure controls are above video container but don't interfere with navigation */
  }
  
  .close-video-call-btn {
    background-color: rgba(239, 68, 68, 0.9);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    font-weight: 500;
    display: flex;
    align-items: center;
    cursor: pointer;
    transition: background-color 0.2s;
  }
  
  .close-video-call-btn:hover {
    background-color: rgba(220, 38, 38, 1);
  }
  
  .close-video-call-btn i {
    margin-right: 8px;
  }
  
  .calendar-container {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
  }
  
  .header {
    margin-bottom: 24px;
  }
  
  .title {
    font-size: 1.75rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
  }
  
  .subtitle {
    font-size: 1rem;
    color: #666;
  }
  
  .section-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 16px;
  }
  
  .no-appointments {
    padding: 24px;
    background-color: #f9fafb;
    border-radius: 8px;
    text-align: center;
    color: #6b7280;
  }
  
  .appointment-cards {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 16px;
  }
  
  .appointment-card {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    padding: 16px;
    display: flex;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
  }
  
  .appointment-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }
  
  .appointment-date {
    background-color: #4f46e5;
    color: white;
    padding: 8px;
    border-radius: 6px;
    text-align: center;
    min-width: 60px;
    margin-right: 16px;
  }
  
  .date-day {
    font-size: 1.5rem;
    font-weight: 700;
  }
  
  .date-month {
    font-size: 0.875rem;
    text-transform: uppercase;
  }
  
  .appointment-details {
    flex: 1;
  }
  
  .appointment-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
  }
  
  .appointment-time,
  .appointment-tutor {
    font-size: 0.875rem;
    color: #6b7280;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
  }
  
  .appointment-time i,
  .appointment-tutor i {
    margin-right: 6px;
    width: 16px;
  }
  
  .appointment-description {
    font-size: 0.875rem;
    color: #4b5563;
    margin-top: 8px;
    line-height: 1.4;
  }
  
  .appointment-actions {
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 8px;
    margin-left: 16px;
  }
  
  .join-call-btn {
    background-color: #10b981;
    color: white;
    border: none;
    border-radius: 4px;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background-color 0.2s;
    position: relative;
  }
  
  .join-call-btn:hover {
    background-color: #059669;
  }
  
  .join-call-btn.disabled {
    background-color: #9ca3af;
    cursor: not-allowed;
  }
  
  .join-call-btn .tooltip {
    visibility: hidden;
    width: 120px;
    background-color: #333;
    color: #fff;
    text-align: center;
    border-radius: 6px;
    padding: 5px;
    position: absolute;
    z-index: 1;
    bottom: 125%;
    left: 50%;
    margin-left: -60px;
    opacity: 0;
    transition: opacity 0.3s;
    font-size: 0.75rem;
  }
  
  .join-call-btn:hover .tooltip {
    visibility: visible;
    opacity: 1;
  }
</style>
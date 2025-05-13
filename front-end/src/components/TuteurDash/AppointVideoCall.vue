<template>
  <div id="app" class="flex flex-col h-screen">
    <!-- Navbar & Sidebar - Always visible -->
    <NavbarTuteur />
    <SidebarTuteur />

    <!-- Main Content -->
    <div class="main-content">
      <!-- Video call container - Positioned to not cover navigation -->
      <div ref="videoContainer" class="video-container"></div>
      <div class="calendar-container">
        <div class="header">
          <h1 class="title">Calendrier</h1>
          <p class="subtitle">
            Planifiez vos sessions de vidéoconférence avec vos étudiants
          </p>
        </div>

        <div class="calendar-actions">
          <button
            @click="showAppointmentModal = true"
            class="create-appointment-btn"
          >
            <i class="fas fa-plus"></i> Créer un rendez-vous
          </button>
        </div>

        <div class="calendar-view">
          <!-- Calendar grid would be implemented here -->
          <!-- For demonstration, we'll show a list of appointments -->
          <div class="appointments-list">
            <h2 class="section-title">Rendez-vous à venir</h2>

            <div v-if="appointments.length === 0" class="no-appointments">
              Aucun rendez-vous planifié. Créez-en un nouveau en cliquant sur le
              bouton ci-dessus.
            </div>

            <div v-else class="appointment-cards">
              <div
                v-for="(appointment, index) in appointments"
                :key="index"
                class="appointment-card"
              >
                <div class="appointment-date">
                  <div class="date-day">{{ formatDay(appointment.date) }}</div>
                  <div class="date-month">
                    {{ formatMonth(appointment.date) }}
                  </div>
                </div>

                <div class="appointment-details">
                  <h3 class="appointment-title">{{ appointment.title }}</h3>
                  <div class="appointment-time">
                    <i class="fas fa-clock"></i>
                    {{ formatTime(appointment.date) }}
                  </div>
                  <!-- <div class="appointment-students">
                    <i class="fas fa-users"></i>
                    {{ appointment.students.length }} étudiants
                  </div> -->
                  <div class="appointment-description">
                    {{ appointment.description }}
                  </div>
                </div>

                <div class="appointment-actions">
                  <button
                    @click="startVideoCall(appointment)"
                    class="start-call-btn"
                  >
                    <i class="fas fa-video"></i>
                  </button>
                  <button
                    @click="editAppointment(appointment)"
                    class="edit-btn"
                  >
                    <i class="fas fa-edit"></i>
                  </button>
                  <button
                    @click="deleteAppointment(appointment)"
                    class="delete-btn"
                  >
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Create/Edit Appointment Modal -->
        <div v-if="showAppointmentModal" class="modal-overlay">
          <div class="modal-container">
            <div class="modal-header">
              <h2>
                {{
                  editingAppointment
                    ? "Modifier le rendez-vous"
                    : "Créer un nouveau rendez-vous"
                }}
              </h2>
              <button @click="closeModal" class="close-btn">
                <i class="fas fa-times"></i>
              </button>
            </div>
            <div class="modal-body">
              <form @submit.prevent="saveAppointment">
                <!-- Steps indicator -->
                <div class="steps-indicator">
                  <div :class="['step', formStep === 1 ? 'active' : '']">
                    1. Sélection du cours
                  </div>
                  <div :class="['step', formStep === 2 ? 'active' : '']">
                    2. Détails du rendez-vous
                  </div>
                </div>

                <!-- Step 1: Course and Title -->
                <div v-if="formStep === 1">
                  <div class="form-group">
                    <label for="course">Cours</label>
                    <select
                      id="course"
                      v-model="appointmentForm.selectedCourse"
                      required
                      class="w-full px-4 py-2 border rounded-md"
                    >
                      <option value="" disabled>Sélectionnez un cours</option>
                      <option
                        v-for="course in courses"
                        :key="course.id"
                        :value="course.id"
                      >
                        {{ course.titre }}
                      </option>
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="title">Titre</label>
                    <input
                      type="text"
                      id="title"
                      v-model="appointmentForm.title"
                      required
                      placeholder="Ex: Session de révision"
                    />
                  </div>

                  <div class="form-actions">
                    <button
                      type="button"
                      @click="closeModal"
                      class="cancel-btn"
                    >
                      Annuler
                    </button>
                    <button
                      type="button"
                      @click="nextStep"
                      class="save-btn"
                      :disabled="
                        !appointmentForm.selectedCourse ||
                        !appointmentForm.title
                      "
                    >
                      Suivant
                    </button>
                  </div>
                </div>

                <!-- Step 2: Appointment Details -->
                <div v-if="formStep === 2">
                  <div class="form-group">
                    <label for="date">Date et heure</label>
                    <input
                      type="datetime-local"
                      id="date"
                      v-model="appointmentForm.date"
                      required
                    />
                  </div>

                  <div class="form-group">
                    <label for="description">Description</label>
                    <textarea
                      id="description"
                      v-model="appointmentForm.description"
                      placeholder="Décrivez l'objectif de cette session"
                      rows="3"
                    ></textarea>
                  </div>

                  <div class="form-group">
                    <label>Étudiants</label>
                    <div class="students-selection">
                      <div
                        v-for="student in availableStudents"
                        :key="student.id"
                        class="student-checkbox"
                      >
                        <input
                          type="checkbox"
                          :id="`student-${student.etudiant.id}`"
                          :value="student.etudiant.id"
                          v-model="appointmentForm.selectedStudents"
                        />
                        <label :for="`student-${student.etudiant.id}`">{{
                          student.etudiant.fullname
                        }}</label>
                      </div>
                    </div>
                  </div>

                  <div class="form-actions">
                    <button
                      type="button"
                      @click="previousStep"
                      class="cancel-btn"
                    >
                      Retour
                    </button>
                    <button type="submit" class="save-btn">Enregistrer</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import SidebarTuteur from "./SidebarTut.vue";
import NavbarTuteur from "./NavBarTut.vue";
import { ref, reactive, onMounted } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

function randomID(len) {
  let result = '';
  if (result) return result;
  var chars = '12345qwertyuiopasdfgh67890jklmnbvcxzMNBVCZXASDQWERTYHGFUIOLKJP',
    maxPos = chars.length,
    i;
  len = len || 5;
  for (i = 0; i < len; i++) {
    result += chars.charAt(Math.floor(Math.random() * maxPos));
  }
  return result;
}

export default {
  name: "CalendarComponent",
  components: { SidebarTuteur, NavbarTuteur },
  setup() {
    const router = useRouter();
    const API_BASE_URL = "http://localhost:8000"; // Change this to your actual API URL
    const appointments = ref([]);
    const showAppointmentModal = ref(false);
    const editingAppointment = ref(null);
    const isLoading = ref(false);
    const isSaving = ref(false);
    const videoContainer = ref(null);
    const courses = ref([]);
    const formStep = ref(1);
    const nextStep = async () => {
      if (appointmentForm.selectedCourse && appointmentForm.title) {
        formStep.value = 2;
        // Fetch students for the selected course
        const Tut_token = JSON.parse(localStorage.getItem("TuteurAccountInfo")).token;
        console.log(`tutuer token ${Tut_token}`)
        try {
          const response = await axios.get(
            `${API_BASE_URL}/api/etudiants`,
            {
              params: {
              cours_id: appointmentForm.selectedCourse,
              tuteur_id: JSON.parse(
                localStorage.getItem("TuteurAccountInfo")
              ).id,
              },
              headers: {
                Authorization: `Bearer ${Tut_token}`,
                'Content-Type': 'application/json'
              },
            }

          );
          // create for each student a new object with the courseId
          availableStudents.value = response.data.students;
          console.log(availableStudents.value);
        } catch (error) {
          console.error("Error fetching students:", error);
          showNotification("Erreur lors du chargement des étudiants", "error");
        }
      }
    };

    const previousStep = () => {
      formStep.value = 1;
    };

    const notification = reactive({
      show: false,
      message: "",
      type: "success", // success, error
      icon: "fas fa-check-circle",
      timeout: null,
    });

    const appointmentForm = reactive({
      title: "",
      date: "",
      description: "",
      selectedStudents: [],
      selectedCourse: "", // Added for course selection
    });

    const availableStudents = ref([]);

    // Load appointments and students on component mount
    onMounted(async () => {
      await fetchAppointments();
      // await fetchStudents();
      await fetchCourses();
    });
    const token = JSON.parse(localStorage.getItem("TuteurAccountInfo")).token;
    // API calls
    const fetchAppointments = async () => {
      isLoading.value = true;
      try {
        const response = await axios.get(`${API_BASE_URL}/api/appointsCall`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });

        appointments.value = response.data.map((appointment) => ({
          ...appointment,
          date: new Date(appointment.date), // Convert date string to Date object
        }));
      } catch (error) {
        console.error("Error fetching appointments:", error);
        showNotification("Erreur lors du chargement des rendez-vous", "error");
      } finally {
        isLoading.value = false;
      }
    };

    // const fetchStudents = async () => {
    //   try {
    //     const response = await axios.get(`${API_BASE_URL}/api/etudiants`, {
    //       headers: {
    //         Authorization: `Bearer ${token}`,
    //       },
    //     });
    //     availableStudents.value = response.data.students[0];
    //     // console.log(availableStudents.value);
    //     console.log(availableStudents.value);
    //   } catch (error) {
    //     console.error("Error fetching students:", error);
    //     showNotification("Erreur lors du chargement des étudiants", "error");
    //   }
    // };

    const fetchCourses = async () => {
      try {
        const tuteurId = JSON.parse(
          localStorage.getItem("TuteurAccountInfo")
        ).id;
        const response = await axios.get(
          `${API_BASE_URL}/api/cours-by-tuteur?tuteurId=${tuteurId}`
        );
        if (response.data.success) {
          courses.value = response.data.cours;
        }
      } catch (error) {
        console.error("Error fetching courses:", error);
        showNotification("Erreur lors du chargement des cours", "error");
      }
    };

    async function sendNotification(message, userId, appointmentId) {
        try {
          await axios.post(
            `${API_BASE_URL}/api/send-notification`,
            {
                message: message,
                userId: userId,
                appointmentId: appointmentId
            }
          );
        }catch (error) {
          console.error("Error sending notification:", error);
        }
    };

    const createAppointment = async (appointmentData) => {
      appointmentData = {
        ...appointmentData,
        student_ids: appointmentForm.selectedStudents,
        tuteur_id: JSON.parse(localStorage.getItem("TuteurAccountInfo")).id,
        cours_id: appointmentForm.selectedCourse,
      };
      console.log(appointmentData.date);
      try {
        const response = await axios.post(
          `${API_BASE_URL}/api/appointCall`,
          appointmentData,
          {
            headers: {
              Authorization: `Bearer ${token}`,
              "Content-Type": "application/json",
            },
          }
        );
        for (const student in appointmentForm.selectedStudents) {
          await sendNotification(
            `Un rendez-vous a été créé pour vous le ${new Date(
              appointmentData.date
            ).toLocaleString()}`,
            Number(student)+1,
          );
        }
        window.location.reload();
        return response.data;
      } catch (error) {
        console.error("Error creating appointment:", error);
        throw error;
      }
    };

    const updateAppointment = async (id, appointmentData) => {
      appointmentData = {
        ...appointmentData,
        student_ids: appointmentForm.selectedStudents,
        tuteur_id: JSON.parse(localStorage.getItem("TuteurAccountInfo")).id,
        cours_id: appointmentForm.selectedCourse,
      };
      try {
        const response = await axios.put(
          `${API_BASE_URL}/api/appointsCall/${id}`,
          appointmentData,
          {
            headers: {
              Authorization: `Bearer ${token}`,
              "Content-Type": "application/json",
            },
          }
        );
        window.location.reload();
        return response.data;
      } catch (error) {
        console.error("Error updating appointment:", error);
        throw error;
      }
    };

    const deleteAppointmentAPI = async (id) => {
      try {
        await axios.delete(`${API_BASE_URL}/api/appointsCall/${id}`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        });
        window.location.reload();
      } catch (error) {
        console.error("Error deleting appointment:", error);
        throw error;
      }
    };

    // Format functions for displaying dates
    const formatDay = (date) => {
      return new Date(date).getDate();
    };

    const formatMonth = (date) => {
      return new Date(date).toLocaleString("default", { month: "short" });
    };

    const formatTime = (date) => {
      return new Date(date).toLocaleTimeString([], {
        hour: "2-digit",
        minute: "2-digit",
      });
    };

    // Toggle student selection in the form
    const toggleStudentSelection = (student) => {
      const index = appointmentForm.selectedStudents.indexOf(student.id);
      if (index === -1) {
        appointmentForm.selectedStudents.push(student.id);
      } else {
        appointmentForm.selectedStudents.splice(index, 1);
      }
    };

    // Open modal for creating a new appointment
    const openCreateModal = () => {
      editingAppointment.value = null;
      appointmentForm.title = "";
      appointmentForm.date = "";
      appointmentForm.description = "";
      appointmentForm.selectedStudents = [];
      appointmentForm.selectedCourse = ""; // Reset selected course
      showAppointmentModal.value = true;
    };

    // Open modal for editing an existing appointment
    const editAppointment = (appointment) => {
      editingAppointment.value = appointment;
      appointmentForm.title = appointment.title;
      appointmentForm.date = new Date(appointment.date)
        .toISOString()
        .slice(0, 16);
      appointmentForm.description = appointment.description;
      appointmentForm.selectedCourse = appointment.courseId || ""; // Set selected course
      appointmentForm.selectedStudents = appointment.students
        ? appointment.students.map((student) => student.id)
        : [];
      showAppointmentModal.value = true;
    }; // Close the modal
    const closeModal = () => {
      showAppointmentModal.value = false;
      appointmentForm.selectedCourse = ""; // Reset selected course
    };

    // Save the appointment (create or update)
    const saveAppointment = async () => {
      isSaving.value = true;

      const appointmentData = {
        title: appointmentForm.title,
        date: new Date(appointmentForm.date).toISOString(),
        description: appointmentForm.description,
        students: appointmentForm.selectedStudents,
        courseId: appointmentForm.selectedCourse,
        roomId: randomID(5), // Generate a random room ID for new appointments
      };

      try {
        if (editingAppointment.value) {
          // Update existing appointment
          const updatedAppointment = await updateAppointment(
            editingAppointment.value.id,
            appointmentData
          );

          // Update local state
          const index = appointments.value.findIndex(
            (a) => a.id === editingAppointment.value.id
          );
          if (index !== -1) {
            appointments.value[index] = {
              ...updatedAppointment,
              date: new Date(updatedAppointment.date),
            };
          }

          showNotification("Rendez-vous mis à jour avec succès", "success");
        } else {
          // Create new appointment
          const newAppointment = await createAppointment(appointmentData);

          // Add to local state
          appointments.value.push({
            ...newAppointment,
            date: new Date(newAppointment.date),
          });

          showNotification("Rendez-vous créé avec succès", "success");
        }

        closeModal();
      } catch (error) {
        showNotification(
          `Erreur: ${
            error.response?.data?.message || "Une erreur est survenue"
          }`,
          "error"
        );
      } finally {
        isSaving.value = false;
      }
    };

    // Delete an appointment
    const deleteAppointment = async (appointment) => {
      if (confirm("Êtes-vous sûr de vouloir supprimer ce rendez-vous ?")) {
        try {
          await deleteAppointmentAPI(appointment.id);

          // Update local state
          appointments.value = appointments.value.filter(
            (a) => a.id !== appointment.id
          );

          showNotification("Rendez-vous supprimé avec succès", "success");
        } catch (error) {
          showNotification(
            "Erreur lors de la suppression du rendez-vous",
            "error"
          );
        }
      }
    };

    // Start a video call for an appointment
    const startVideoCall = async (appointment) => {
      window.open(`/video-call/?roomID=${appointment.roomId}&userName=${JSON.parse(localStorage.getItem('TuteurAccountInfo')).fullname}`, '_blank');
      for (const student in appointment.student_ids) {
        await sendNotification(
          `Un appel vidéo a été démarré pour le rendez-vous "${appointment.title}"`,
          Number(student)+1,
          appointment.id
        );
      }
    };

    // Close the video call completely
    const closeVideoCall = () => {
      if (videoContainer.value) {
        videoContainer.value.style.display = "none";
      } else {
        document.querySelector(".video-container").style.display = "none";
      }
      // Clean up any resources if needed
      sessionStorage.removeItem("currentCallAppointment");
    };

    // Return to the appointments list while keeping the call active in background
    const returnToAppointments = () => {
      if (videoContainer.value) {
        videoContainer.value.style.display = "none";
      } else {
        document.querySelector(".video-container").style.display = "none";
      }
      // We don't remove the session storage item so the call state is preserved
    };

    // Show notification toast
    const showNotification = (message, type = "success") => {
      // Clear any existing timeout
      if (notification.timeout) {
        clearTimeout(notification.timeout);
      }

      // Set notification properties
      notification.show = true;
      notification.message = message;
      notification.type = type;
      notification.icon =
        type === "success"
          ? "fas fa-check-circle"
          : "fas fa-exclamation-circle";

      // Auto-hide after 5 seconds
      notification.timeout = setTimeout(() => {
        notification.show = false;
      }, 5000);
    };
    return {
      appointments,
      showAppointmentModal,
      editingAppointment,
      appointmentForm,
      availableStudents,
      isLoading,
      isSaving,
      notification,
      videoContainer,
      courses,
      formStep,
      nextStep,
      previousStep,
      formatDay,
      formatMonth,
      formatTime,
      toggleStudentSelection,
      openCreateModal,
      editAppointment,
      closeModal,
      saveAppointment,
      deleteAppointment,
      startVideoCall,
      closeVideoCall,
      returnToAppointments,
    };
  },
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

.close-video-call-btn,
.return-to-appointments-btn {
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

.return-to-appointments-btn {
  background-color: rgba(59, 130, 246, 0.9);
}

.return-to-appointments-btn:hover {
  background-color: rgba(37, 99, 235, 1);
}

.close-video-call-btn i,
.return-to-appointments-btn i {
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

.calendar-actions {
  margin-bottom: 24px;
  display: flex;
  justify-content: flex-end;
}

.create-appointment-btn {
  background-color: #4f46e5;
  color: white;
  border: none;
  padding: 10px 16px;
  border-radius: 6px;
  font-weight: 500;
  display: flex;
  align-items: center;
  cursor: pointer;
  transition: background-color 0.2s;
}

.create-appointment-btn:hover {
  background-color: #4338ca;
}

.create-appointment-btn i {
  margin-right: 8px;
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
.appointment-students {
  font-size: 0.875rem;
  color: #6b7280;
  margin-bottom: 4px;
  display: flex;
  align-items: center;
}

.appointment-time i,
.appointment-students i {
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

.start-call-btn,
.edit-btn,
.delete-btn {
  border: none;
  border-radius: 4px;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background-color 0.2s;
}

.start-call-btn {
  background-color: #10b981;
  color: white;
}

.start-call-btn:hover {
  background-color: #059669;
}

.edit-btn {
  background-color: #f59e0b;
  color: white;
}

.edit-btn:hover {
  background-color: #d97706;
}

.delete-btn {
  background-color: #ef4444;
  color: white;
}

.delete-btn:hover {
  background-color: #dc2626;
}

/* Modal styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.steps-indicator {
  display: flex;
  margin-bottom: 24px;
  border-bottom: 2px solid #e5e7eb;
}

.step {
  flex: 1;
  padding: 12px;
  text-align: center;
  color: #6b7280;
  font-weight: 500;
  position: relative;
}

.step.active {
  color: #4f46e5;
}

.step.active::after {
  content: "";
  position: absolute;
  bottom: -2px;
  left: 0;
  width: 100%;
  height: 2px;
  background-color: #4f46e5;
}

/* Rest of modal styles */
.modal-container {
  background-color: white;
  border-radius: 8px;
  width: 90%;
  max-width: 600px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.modal-header {
  padding: 16px 24px;
  border-bottom: 1px solid #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.modal-header h2 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #333;
  margin: 0;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.25rem;
  color: #6b7280;
  cursor: pointer;
}

.modal-body {
  padding: 24px;
}

.form-group {
  margin-bottom: 16px;
}

.form-group label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  color: #4b5563;
  margin-bottom: 6px;
}

input[type="text"],
input[type="datetime-local"],
textarea {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 0.875rem;
  transition: border-color 0.2s;
}

input[type="text"]:focus,
input[type="datetime-local"]:focus,
textarea:focus {
  border-color: #4f46e5;
  outline: none;
}

.students-selection {
  max-height: 200px;
  overflow-y: auto;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  padding: 12px;
}

.student-checkbox {
  margin-bottom: 8px;
  display: flex;
  align-items: center;
}

.student-checkbox input[type="checkbox"] {
  margin-right: 8px;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 24px;
}

.cancel-btn,
.save-btn {
  padding: 10px 16px;
  border-radius: 6px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s;
}

.cancel-btn {
  background-color: #f3f4f6;
  color: #4b5563;
  border: 1px solid #d1d5db;
}

.cancel-btn:hover {
  background-color: #e5e7eb;
}

.save-btn {
  background-color: #4f46e5;
  color: white;
  border: none;
}

.save-btn:hover {
  background-color: #4338ca;
}

select {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 0.875rem;
  transition: border-color 0.2s;
}

select:focus {
  border-color: #4f46e5;
  outline: none;
}
</style>

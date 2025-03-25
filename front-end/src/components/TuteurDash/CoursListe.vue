<template>
  <div id="app" class="flex flex-col h-screen">
    <!-- Navbar & Sidebar -->
    <NavbarTuteur />
    <SidebarTuteur />

    <!-- Main Content -->
    <section id="content" class="flex-1 flex justify-center items-center py-6">
      <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-gray-800 text-center mb-6">
          Liste des Cours
        </h1>

        <!-- Add Course Button -->
        <div class="text-right mb-6">
          <router-link to="/ajouter-cours" class="btn-submit">
            Ajouter un Cours
          </router-link>
        </div>

        <!-- Course Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <!-- Course Card -->
          <div
            v-for="cours in coursListe"
            :key="cours.id"
            class="card"
          >
            <!-- Course Image -->
            <div class="relative h-32 overflow-hidden">
              <img
                :src="getImageUrl(cours.file)"
                alt="Image du cours"
                class="w-full h-full object-cover"
                @error="handleImageError"
              />
            </div>

            <!-- Course Content -->
            <div class="text">
              <!-- Course Title -->
              <h2 class="title">{{ cours.titre }}</h2>
              <!-- Course Subtitle -->
              <p class="subtitle">{{ cours.description || "No description available." }}</p>
            </div>

            <!-- Action Buttons -->
            <div class="icons">
              <!-- Details Button -->
              <router-link
                :to="`/cours/${cours.id}`"
                class="btn"
              >
                <svg
                  class="svg-icon"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M10 12a2 2 0 100-4 2 2 0 000 4z"
                  />
                  <path
                    fill-rule="evenodd"
                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                    clip-rule="evenodd"
                  />
                </svg>
              </router-link>

              <!-- Edit Button -->
              <router-link
                :to="`/modifier-cours/${cours.id}`"
                class="btn"
              >
                <svg
                  class="svg-icon"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                  />
                </svg>
              </router-link>

              <!-- Delete Button -->
              <button
                @click="deleteCours(cours.id)"
                class="btn"
              >
                <svg
                  class="svg-icon"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import NavbarTuteur from "./NavbarTut.vue";
import SidebarTuteur from "./SidebarTut.vue";
import axios from "axios";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

export default {
  name: "CoursListe",
  components: {
    NavbarTuteur,
    SidebarTuteur,
  },
  data() {
    return {
      coursListe: [], // List of courses
      defaultImage: "/placeholder-course.jpg", // Default image
    };
  },
  methods: {
    // Fetch courses for the tutor
    async fetchCours() {
      try {
        const tuteurId = JSON.parse(localStorage.getItem("TuteurAccountInfo")).id;
        const response = await axios.get(
          `http://localhost:8000/api/cours-by-tuteur?tuteurId=${tuteurId}`
        );
        this.coursListe = response.data.cours.map((cours) => ({
          ...cours,
          file: cours.file || this.defaultImage, // Use default image if file is missing
        }));
      } catch (error) {
        console.error("Erreur lors de la récupération des cours :", error);
        toast.error("Impossible de récupérer les cours.");
      }
    },

    // Delete a course
    async deleteCours(id) {
      try {
        await axios.delete(`http://localhost:8000/api/cours/${id}`);
        toast.success("Cours supprimé avec succès !");
        this.fetchCours(); // Refresh the list
      } catch (error) {
        console.error("Erreur lors de la suppression du cours :", error);
        toast.error("Erreur lors de la suppression du cours.");
      }
    },

    // Handle image errors
    handleImageError(event) {
      event.target.src = this.defaultImage; // Replace with default image
    },

    // Build image URL
    getImageUrl(filePath) {
      if (!filePath || filePath === this.defaultImage) {
        return this.defaultImage; // Return default image if path is empty or already default
      }
      return `http://localhost:8000${filePath}`; // Return full image URL
    },
  },
  mounted() {
    this.fetchCours(); // Load courses on component mount
  },
};
</script>

<style scoped>
/* Card Styles */
.card {
  width: 250px;
  height: 300px; /* Increased height to accommodate image */
  border-radius: 15px;
  background: rgba(105, 13, 197, 0.103);
  display: flex;
  flex-direction: column;
  position: relative;
  overflow: hidden;
}

.card::before {
  content: "";
  height: 100px;
  width: 100px;
  position: absolute;
  top: -40%;
  left: -20%;
  border-radius: 50%;
  border: 35px solid rgba(255, 255, 255, 0.102);
  transition: all 0.8s ease;
  filter: blur(0.5rem);
}

.card:hover::before {
  width: 140px;
  height: 140px;
  top: -30%;
  left: 50%;
  filter: blur(0rem);
}

/* Image Section */
.relative {
  position: relative;
}

.h-32 {
  height: 8rem; /* Adjust image height */
}

/* Text Section */
.text {
  flex-grow: 1;
  padding: 15px;
  display: flex;
  flex-direction: column;
  color: aliceblue;
  font-weight: 900;
  font-size: 1.2em;
}

.subtitle {
  font-size: 0.6em;
  font-weight: 300;
  color: rgba(240, 248, 255, 0.691);
}

/* Action Buttons */
.icons {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 250px;
  border-radius: 0px 0px 15px 15px;
  overflow: hidden;
}

.btn {
  border: none;
  width: 84px;
  height: 35px;
  background-color: rgba(247, 234, 234, 0.589);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

.svg-icon {
  width: 25px;
  height: 25px;
  stroke: rgb(38, 59, 126);
}

.btn:hover {
  background-color: rgb(247, 234, 234);
}

/* Add Course Button */
.btn-submit {
  background-color: #4f46e5;
  color: white;
  padding: 8px 12px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: bold;
  font-size: 14px;
}

.btn-submit:hover {
  background-color: #4338ca;
}
</style>
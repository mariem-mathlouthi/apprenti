<template>
  <div
    id="app"
    class="flex flex-col h-screen bg-gradient-to-br from-gray-50 to-gray-100"
  >
    <!-- Navbar -->
    <NavBarStd />

    <!-- Sidebar + Main Content -->
    <div class="flex flex-1">
      <!-- Sidebar -->
      <Sidebar class="w-64" />

      <!-- Main Content -->
      <section class="flex-1 p-8 overflow-auto mt-16 ml-64">
        <div class="max-w-6xl mx-auto">
          <!-- Header -->
          <div class="flex justify-between items-center mb-6">
            <!-- Titre centré -->
            <div class="flex-1 text-center">
              <h1 class="text-2xl font-bold text-gray-800">
                Ressources du Cours
              </h1>
            </div>
            <!-- Nouveau bouton Quizz -->
            <router-link
              :to="{ name: 'ListeQuizz', params: { idCours: idCours } }"
              class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md shadow-sm text-sm font-medium transition-colors"
            >
              Voir les Quizz
            </router-link>
          </div>

          <!-- Liste des ressources -->
          <div
            v-if="ressources.length"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"
          >
            <div
              v-for="ressource in ressources"
              :key="ressource.id"
              class="card transform transition-all hover:scale-105"
            >
              <div class="p-4">
                <!-- Contenu de la carte -->
                <h3 class="font-semibold text-base text-gray-800 mb-1">
                  {{ ressource.titre }}
                </h3>
                <p class="text-gray-600 text-xs">{{ ressource.description }}</p>

                <!-- Affichage de la vidéo ou du fichier -->
                <div v-if="ressource.file" class="mt-2">
                  <!-- Si c'est une vidéo -->
                  <video
                    v-if="isVideo(ressource.file)"
                    controls
                    class="w-full rounded-lg"
                  >
                    <source :src="ressource.file" type="video/mp4" />
                    Votre navigateur ne supporte pas la lecture de vidéos.
                  </video>

                  <!-- Si c'est un fichier (PDF, DOC, etc.) -->
                  <a
                    v-else
                    :href="ressource.file"
                    target="_blank"
                    class="btn-link flex items-center space-x-1 text-xs"
                  >
                    <span>Voir le fichier</span>
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Message si aucune ressource -->
          <div v-else class="text-center py-10">
            <p class="text-gray-500 text-sm">
              Aucune ressource disponible pour ce cours.
            </p>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import NavBarStd from "./NavBarStd.vue";
import Sidebar from "./Sidebar.vue";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

export default {
  name: "ConsultRessource",
  components: { NavBarStd, Sidebar },
  data() {
    return {
      ressources: [], // Liste des ressources
      idCours: this.$route.params.id, // ID du cours récupéré depuis l'URL
    };
  },
  methods: {
    // Récupérer les ressources du cours
    async fetchRessources() {
      try {
        const response = await axios.get(
          "http://localhost:8000/api/ressources"
        );
        // Filtrer les ressources pour ce cours
        this.ressources = response.data.filter(
          (ressource) => ressource.idCours === parseInt(this.idCours)
        );
      } catch (error) {
        console.error("Erreur lors du chargement des ressources :", error);
        toast.error("Erreur lors du chargement des ressources");
      }
    },

    // Vérifie si le fichier est une vidéo
    isVideo(file) {
      const videoExtensions = [".mp4", ".webm", ".ogg"];
      return videoExtensions.some((ext) => file.toLowerCase().endsWith(ext));
    },
  },
  mounted() {
    this.fetchRessources(); // Charger les ressources au montage du composant
  },
};
</script>

<style scoped>
/* Fond dégradé */
.bg-gradient-to-br {
  background: linear-gradient(135deg, #f9fafb, #f3f4f6);
}

/* Espacement pour éviter que le contenu touche le navbar */
.mt-16 {
  margin-top: 64px;
}

/* Lien Voir fichier */
.btn-link {
  display: inline-flex;
  align-items: center;
  color: #4f46e5;
  font-weight: bold;
  text-decoration: none;
  transition: 0.3s;
}
.btn-link:hover {
  color: #4338ca;
  text-decoration: underline;
}

/* Style des cartes */
.card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s, box-shadow 0.3s;
}
.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}
</style>

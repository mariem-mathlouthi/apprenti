<template>
  <div id="app" class="flex flex-col h-screen bg-gray-100">
    <!-- Navbar -->
    <NavbarTuteur />

    <!-- Sidebar + Main Content -->
    <div class="flex flex-1">
      <SidebarTuteur class="w-64" />

      <!-- Main Content -->
      <section class="flex-1 p-6 overflow-auto mt-20">
        <div class="max-w-5xl mx-auto bg-white shadow-lg rounded-lg p-6">
          <!-- Header -->
          <div class="flex flex-col items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 text-center">📚 Ressources du Cours</h1>
            <p class="text-gray-600 text-center max-w-2xl mt-2">
              Gérer et organiser les ressources pédagogiques pour ce cours. Ajoutez, modifiez ou supprimez des ressources en un clic.
            </p>
            <router-link 
              :to="`/cours/${idCours}/ajouter-ressource`"
              class="btn-primary mt-4"
            >
              ➕ Ajouter une Ressource
            </router-link>
          </div>

          <!-- Liste des ressources -->
          <div v-if="ressources.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div 
              v-for="ressource in ressources" 
              :key="ressource.id" 
              class="card transform transition-all hover:scale-105"
            >
              <div class="p-6">
                <h3 class="font-bold text-lg text-gray-800 mb-2">{{ ressource.titre }}</h3>
                <p class="text-gray-600 text-sm">{{ ressource.description }}</p>

                <!-- Affichage de la vidéo ou du fichier -->
                <div v-if="ressource.file" class="mt-4">
                  <!-- Si c'est une vidéo -->
                  <video 
                    v-if="isVideo(ressource.file)"
                    controls
                    class="w-full rounded-lg"
                  >
                    <source :src="ressource.file" type="video/mp4">
                    Votre navigateur ne supporte pas la lecture de vidéos.
                  </video>

                  <!-- Si c'est un fichier (PDF, DOC, etc.) -->
                  <a 
                    v-else
                    :href="ressource.file" 
                    target="_blank" 
                    class="btn-link flex items-center space-x-2"
                  >
                    <span>📂</span>
                    <span>Voir le fichier</span>
                  </a>
                </div>

                <!-- Actions -->
                <div class="mt-6 flex justify-end space-x-3">
                  <button 
                    @click="editRessource(ressource)" 
                    class="btn-edit flex items-center space-x-2"
                  >
                    <span>✏️</span>
                    <span>Modifier</span>
                  </button>
                  <button 
                    @click="deleteRessource(ressource.id)" 
                    class="btn-delete flex items-center space-x-2"
                  >
                    <span>🗑️</span>
                    <span>Supprimer</span>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Message si aucune ressource -->
          <div v-else class="text-center py-10">
            <p class="text-gray-500">📭 Aucune ressource disponible pour ce cours.</p>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import NavbarTuteur from './NavbarTut.vue';
import SidebarTuteur from './SidebarTut.vue';
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

export default {
  name: "RessourceList",
  components: { NavbarTuteur, SidebarTuteur },
  data() {
    return {
      ressources: [],
      idCours: this.$route.params.id
    };
  },
  methods: {
    async fetchRessources() {
      try {
        const response = await axios.get("http://localhost:8000/api/ressources");
        this.ressources = response.data.filter(
          ressource => ressource.idCours === parseInt(this.idCours)
        );
      } catch (error) {
        console.error("Erreur lors du chargement des ressources :", error);
        toast.error("Erreur lors du chargement des ressources");
      }
    },
    async deleteRessource(id) {
      try {
        await axios.delete(`http://localhost:8000/api/ressources/${id}`);
        this.ressources = this.ressources.filter((r) => r.id !== id);
        toast.success("Ressource supprimée avec succès !");
      } catch (error) {
        console.error("Erreur lors de la suppression de la ressource :", error);
        toast.error("Échec de la suppression de la ressource.");
      }
    },
    editRessource(ressource) {
      this.$router.push({ name: "RessourceEdit", params: { id: ressource.id } });
    },
    // Vérifie si le fichier est une vidéo
    isVideo(file) {
      const videoExtensions = ['.mp4', '.webm', '.ogg'];
      return videoExtensions.some(ext => file.toLowerCase().endsWith(ext));
    },
  },
  mounted() {
    this.fetchRessources();
  }
};
</script>

<style scoped>
/* Espacement pour éviter que le contenu touche le navbar */
.mt-20 {
  margin-top: 80px;
}

/* Ajustement global */
.max-w-5xl {
  max-width: 900px;
}

/* Bouton Ajouter */
.btn-primary {
  background-color: #4f46e5;
  color: white;
  padding: 12px 20px;
  border-radius: 8px;
  font-weight: bold;
  text-decoration: none;
  transition: 0.3s;
}
.btn-primary:hover {
  background-color: #4338ca;
}

/* Lien Voir fichier */
.btn-link {
  display: inline-block;
  color: #4f46e5;
  font-weight: bold;
  margin-top: 10px;
  text-decoration: underline;
}
.btn-link:hover {
  color: #4338ca;
}

/* Style des cartes */
.card {
  background: white;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s, box-shadow 0.3s;
  text-align: center;
}
.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

/* Boutons Modifier et Supprimer */
.btn-edit {
  background-color: #3b82f6;
  color: white;
  padding: 8px 16px;
  border-radius: 6px;
  font-weight: bold;
  cursor: pointer;
  transition: 0.3s;
}
.btn-edit:hover {
  background-color: #2563eb;
  transform: translateY(-2px);
}

.btn-delete {
  background-color: #ef4444;
  color: white;
  padding: 8px 16px;
  border-radius: 6px;
  font-weight: bold;
  cursor: pointer;
  transition: 0.3s;
}
.btn-delete:hover {
  background-color: #dc2626;
  transform: translateY(-2px);
}
</style>

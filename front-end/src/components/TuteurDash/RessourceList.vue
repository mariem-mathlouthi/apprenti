<template>
  <div id="app" class="flex flex-col h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- Navbar -->
    <NavbarTuteur />

    <!-- Sidebar + Main Content -->
    <div class="flex flex-1">
      <!-- Sidebar -->
      <SidebarTuteur class="w-64" />

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

            <!-- Bouton Ajouter (+) en haut à droite -->
            <router-link 
              :to="`/cours/${idCours}/ajouter-ressource`"
              class="btn-add"
            >
              +
            </router-link>
          </div>

          <!-- Liste des ressources -->
          <div v-if="ressources.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div 
              v-for="ressource in ressources" 
              :key="ressource.id" 
              class="card transform transition-all hover:scale-105"
            >
              <div class="p-4">
                <!-- Symbole ":" en haut à droite -->
                <div class="flex justify-end">
                  <button 
                    @click.stop="toggleActions(ressource.id)" 
                    class="text-gray-600 hover:text-gray-800 text-xl focus:outline-none"
                  >
                    :
                  </button>
                </div>

                <!-- Menu contextuel (affiché uniquement si la carte est active) -->
                <div 
                  v-if="activeCardId === ressource.id" 
                  class="absolute bg-white rounded-lg shadow-md p-2 mt-2 right-4 z-10"
                >
                  <button 
                    @click.stop="editRessource(ressource)" 
                    class="btn-menu"
                  >
                    Modifier
                  </button>
                  <button 
                    @click.stop="deleteRessource(ressource.id)" 
                    class="btn-menu"
                  >
                    Supprimer
                  </button>
                </div>

                <!-- Contenu de la carte -->
                <h3 class="font-semibold text-base text-gray-800 mb-1">{{ ressource.titre }}</h3>
                <p class="text-gray-600 text-xs">{{ ressource.description }}</p>

                <!-- Affichage de la vidéo ou du fichier -->
                <div v-if="ressource.file" class="mt-2">
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
            <p class="text-gray-500 text-sm">Aucune ressource disponible pour ce cours.</p>
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
      idCours: this.$route.params.id,
      activeCardId: null, // ID de la carte active (pour afficher les boutons)
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
    // Afficher/Masquer les boutons d'actions
    toggleActions(cardId) {
      this.activeCardId = this.activeCardId === cardId ? null : cardId;
    },
  },
  mounted() {
    this.fetchRessources();
  }
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

/* Bouton Ajouter (+) en haut à droite */
.btn-add {
  background-color: #4f46e5;
  color: white;
  padding: 8px 16px;
  border-radius: 50%;
  font-weight: bold;
  text-decoration: none;
  transition: 0.3s;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
}
.btn-add:hover {
  background-color: #4338ca;
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
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
  position: relative; /* Pour positionner le menu contextuel */
}
.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

/* Boutons du menu contextuel */
.btn-menu {
  display: block;
  width: 100%;
  padding: 6px 12px;
  text-align: left;
  color: #4b5563;
  font-weight: bold;
  transition: 0.3s;
}
.btn-menu:hover {
  background-color: #f3f4f6;
  color: #1e40af;
}
</style>
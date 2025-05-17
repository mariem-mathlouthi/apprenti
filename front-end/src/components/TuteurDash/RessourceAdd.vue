<template>
  <div id="app" class="flex flex-col h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <!-- Navbar & Sidebar -->
    <NavbarTuteur />
    <SidebarTuteur />

    <!-- Main Content -->
    <section id="content" class="flex-1 flex flex-col items-center py-8">
      <div class="container mx-auto p-6 w-full max-w-4xl">
        <!-- Title -->
        <h1 class="text-3xl font-bold text-gray-800 text-center mb-8">Ajouter une Ressource</h1>

        <!-- Formulaire d'ajout de ressource -->
        <form @submit.prevent="createRessource" class="bg-white p-8 rounded-xl shadow-lg space-y-6">
          <!-- Champ Titre -->
          <div>
            <label for="titre" class="block text-sm font-medium text-gray-700 mb-2">Titre</label>
            <input
              type="text"
              id="titre"
              v-model="form.titre"
              required
              class="input"
              placeholder="Titre de la ressource"
            />
            <span v-if="errors.titre" class="text-red-500 text-sm mt-1">{{ errors.titre }}</span>
          </div>

          <!-- Champ Description -->
          <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea
              id="description"
              v-model="form.description"
              required
              class="input"
              rows="4"
              placeholder="Description de la ressource"
            ></textarea>
            <span v-if="errors.description" class="text-red-500 text-sm mt-1">{{ errors.description }}</span>
          </div>

          <!-- Champ Fichier -->
          <div>
            <label for="file" class="block text-sm font-medium text-gray-700 mb-2">Fichier</label>
            <div class="relative">
              <input
                type="file"
                id="file"
                @change="handleFileUpload"
                class="input"
                accept=".pdf,.doc,.docx,.txt,.mp4,.mov,.avi"
                required
              />
              <span v-if="fileToUpload" class="text-sm text-gray-600 mt-2 block">
                Fichier sélectionné : {{ fileToUpload.name }}
              </span>
            </div>
            <span v-if="errors.file" class="text-red-500 text-sm mt-1">{{ errors.file }}</span>
          </div>

          <!-- Bouton Soumettre -->
          <button type="submit" class="btn-submit">Ajouter la Ressource</button>
        </form>
      </div>
    </section>
  </div>
</template>

<script>
import SidebarTuteur from "./SidebarTut.vue";
import NavbarTuteur from "./NavbarTut.vue";
import axios from "axios";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

export default {
  name: "RessourceAdd",
  components: { SidebarTuteur, NavbarTuteur },
  data() {
    return {
      form: {
        titre: "",
        description: "",
        file: null, // Ce sera le chemin du fichier après l'upload
        idCours: this.$route.params.id, // ID du cours depuis l'URL
      },
      fileToUpload: null, // Fichier sélectionné par l'utilisateur
      errors: {
        titre: "",
        description: "",
        file: "",
      },
    };
  },
  methods: {
    // Gérer l'upload du fichier
    handleFileUpload(event) {
      this.fileToUpload = event.target.files[0]; // Stocker le fichier sélectionné
      this.errors.file = ""; // Réinitialiser l'erreur du fichier
    },

    // Valider les champs
    validateForm() {
      this.errors = { titre: "", description: "", file: "" }; // Réinitialiser les erreurs

      let isValid = true;

      if (!this.form.titre) {
        this.errors.titre = "Le titre est obligatoire.";
        isValid = false;
      }

      if (!this.form.description) {
        this.errors.description = "La description est obligatoire.";
        isValid = false;
      }

      if (!this.fileToUpload) {
        this.errors.file = "Le fichier est obligatoire.";
        isValid = false;
      }

      return isValid;
    },

    // Uploader le fichier et obtenir son chemin
    async uploadFile() {
      const formData = new FormData();
      formData.append("file", this.fileToUpload); // Ajouter le fichier à FormData

      try {
        const response = await axios.post("http://localhost:8000/api/upload", formData, {
          headers: {
            "Content-Type": "multipart/form-data", // Définir le bon type de contenu
          },
        });
        return response.data.filePath; // Supposons que l'API renvoie le chemin du fichier
      } catch (error) {
        console.error("Erreur lors de l'upload du fichier :", error);
        toast.error("Échec de l'upload du fichier.");
        return null;
      }
    },

    async sendNotification(idEtudiant, idEntreprise, idTuteur ,message, destination, type, date, appointmentId) {
      const notificationData = {
        idEtudiant: idEtudiant,
        idEntreprise: idEntreprise,
        idTuteur: idTuteur,
        message: message,
        destination: destination,
        type: type,
        visibility: "shown",
        date: date,
        appointmentId: appointmentId,
      }

      await axios.post(
        "http://localhost:8000/api/notification",
        notificationData
      )

    },

    // Ajouter une ressource
    async createRessource() {
      // Valider le formulaire avant de soumettre
      if (!this.validateForm()) {
        return; // Arrêter la soumission si le formulaire n'est pas valide
      }

      // Uploader le fichier et obtenir son chemin
      const filePath = await this.uploadFile();

      // Préparer les données pour l'ajout de la ressource
      const requestData = {
        titre: this.form.titre,
        description: this.form.description,
        idCours: this.form.idCours,
        file: filePath, // Utiliser le chemin du fichier uploadé
      };

      try {
        const response = await axios.post("http://localhost:8000/api/ressources", requestData);
        toast.success("Ressource ajoutée avec succès !");

        // Envoyer une notification
        const idEtudiantsSubscripted = await axios.get("http://localhost:8000/api/etudiants",
          {
            params: {
              tuteur_id: JSON.parse(localStorage.getItem('TuteurAccountInfo')).id,
              cours_id: this.form.idCours 
            }
          },
          {
            headers: {
              "Content-Type": "application/json",
              "Authorization": "Bearer " + JSON.parse(localStorage.getItem('TuteurAccountInfo')).token
            }
          }
        );

        idEtudiantsSubscripted.data.students.forEach(etudiant => {
          console.log(etudiant.etudiant_id);
          this.sendNotification(etudiant.etudiant_id, null, JSON.parse(localStorage.getItem('TuteurAccountInfo')).id, "Un nouveau fichier a été ajouté à votre cours", "Etudiant", "ressource", new Date(), null);
        });
        // Rediriger vers la liste des ressources
        this.$router.push({ name: "RessourceList", params: { id: this.form.idCours } });
      } catch (error) {
        console.error("Erreur lors de l'ajout de la ressource :", error);
        if (error.response && error.response.data) {
          console.error("Détails de l'erreur :", error.response.data);
        }
        toast.error("Échec de l'ajout de la ressource.");
      }
    },
  },
};
</script>

<style scoped>
.input {
  width: 100%;
  padding: 12px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  margin-top: 4px;
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

.input:focus {
  border-color: #4f46e5;
  box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
  outline: none;
}

.btn-submit {
  background-color: #4f46e5;
  color: white;
  padding: 12px 24px;
  border-radius: 8px;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.3s ease;
  width: 100%;
}

.btn-submit:hover {
  background-color: #4338ca;
  transform: translateY(-2px);
}

.text-red-500 {
  color: #ef4444;
}
</style>
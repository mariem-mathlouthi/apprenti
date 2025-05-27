<template>
  <div id="app" class="flex flex-col h-screen">
    <!-- Navbar fixe en haut -->
    <NavbarTuteur class="fixed top-0 w-full z-50 h-16" />

    <!-- Sidebar fixe -->
    <SidebarTuteur class="fixed left-0 top-16 h-[calc(100vh-4rem)] z-40" />

    <!-- Contenu principal -->
    <section 
      id="content" 
      class="flex-1 mt-16 pb-6 overflow-y-auto z-10"
      :style="{ height: 'calc(100vh - 4rem)' }"
    >
      <div class="container mx-auto p-6 flex justify-center">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full md:w-3/4 lg:w-2/3 max-w-4xl">
          <h1 class="text-3xl font-bold text-black text-center mb-6">
            Ajouter un Cours
          </h1>

          <form @submit.prevent="createCours" class="space-y-6">
            <!-- Titre -->
            <div>
              <label class="label">Titre</label>
              <input type="text" v-model="titre" required class="input" placeholder="Entrez le titre" />
            </div>

            <!-- Description -->
            <div>
              <label class="label">Description</label>
              <textarea v-model="description" required class="input" placeholder="Entrez la description"></textarea>
            </div>

            <!-- Catégorie -->
            <div>
              <label class="label">Catégorie</label>
              <select v-model="selectedCategory" required class="input">
                <option disabled value="">Sélectionnez une catégorie</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                  {{ category.description }}
                </option>
              </select>
            </div>

            <!-- Prix -->
            <div>
              <label class="label">Prix (dt)</label>
              <input type="number" v-model="prix" required class="input" placeholder="Entrez le prix" />
            </div>

            <!-- Durée -->
            <div>
              <label class="label">Durée (heures)</label>
              <input type="number" v-model="duration" required class="input" placeholder="Durée du cours" />
            </div>

            <!-- Fichier -->
            <div>
              <label class="label">Fichier</label>
              <input type="file" @change="handleFileUpload" class="input" />
            </div>

            <!-- Bouton -->
            <div class="mt-6 flex justify-center">
              <button type="submit" class="btn-submit">
                Ajouter Cours
              </button>
            </div>
          </form>
        </div>
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
  name: "Cours",
  components: {
    SidebarTuteur,
    NavbarTuteur,
  },
  data() {
    return {
      titre: "",
      description: "",
      selectedCategory: "",
      categories: [],
      prix: "",
      duration: "",
      file: null,
      idTuteur: null,
      createdBy: null,
      tuteurName: "",
    };
  },
  methods: {
    async fetchCategories() {
      try {
        const response = await axios.get("http://localhost:8000/api/categories",
          {
            headers: {
              Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`,
            },
          }
        );
        this.categories = response.data || [];
      } catch (error) {
        console.error("Erreur lors de la récupération des catégories :", error);
        toast.error("Impossible de récupérer les catégories.");
      }
    },

    handleFileUpload(event) {
      this.file = event.target.files[0];
    },

    loadTuteurData() {
      const tuteurData = localStorage.getItem("TuteurAccountInfo");
      if (tuteurData) {
        const parsedData = JSON.parse(tuteurData);
        this.idTuteur = parsedData.id;
        this.createdBy = parsedData.id;
        this.tuteurName = parsedData.fullname;
      } else {
        toast.error("Veuillez vous reconnecter");
        this.$router.push("/login");
      }
    },

    showNotification(title, options) {
      if (Notification.permission === "granted") {
        new Notification(title, options);
      } else if (Notification.permission !== "denied") {
        this.requestNotificationPermission();
      }
    },
    
    requestNotificationPermission() {
      if ("Notification" in window) {
        Notification.requestPermission().then((permission) => {
          if (permission === "granted") {
            console.log("Notification permission granted");
          }
        });
      }
    },

    async sendNotification(idEtudiant, idEntreprise, idTuteur, message, destination, type, date, appointmentId) {
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
      };

      await axios.post(
        "http://localhost:8000/api/notification",
        notificationData,
        {
          headers: {
            Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`,
          },
        }
      );
    },

    async createCours() {
      if (!this.idTuteur) {
        toast.error("Identifiant tuteur non trouvé");
        return;
      }

      let formData = new FormData();
      formData.append("titre", this.titre);
      formData.append("description", this.description);
      formData.append("category_id", Number(this.selectedCategory));
      formData.append("prix", Number(this.prix));
      formData.append("duration", Number(this.duration));
      formData.append("idTuteur", this.idTuteur);
      formData.append("createdBy", this.createdBy);

      if (this.file) {
        formData.append("file", this.file);
      }

      try {
        const response = await axios.post("http://localhost:8000/api/cours", formData, {
          headers: {
            "Content-Type": "multipart/form-data",
            Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`,
          },
        });

        if (response.data.success) {
          const notificationMessage = `${this.tuteurName} a ajouté un nouveau cours : ${this.titre}`;
          this.sendNotification(0, null, this.idTuteur, notificationMessage, "Etudiant", "cours", new Date().toISOString().split('T')[0], null);
          this.showNotification("Nouveau cours ajouté", {
            body: notificationMessage,
          });

          toast.success("Cours ajouté avec succès !");
          this.$router.push("/cours");
        }
      } catch (error) {
        console.error("Erreur complète :", error);
        const errorMessage = error.response?.data?.error || "Erreur inconnue";
        toast.error(`Échec de l'ajout : ${errorMessage}`);
      }
    },
  },
  mounted() {
    this.loadTuteurData();
    this.fetchCategories();
  },
};
</script>

<style scoped>
.input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 6px;
  transition: border-color 0.2s;
}

.input:focus {
  border-color: #4f46e5;
  outline: none;
}

.btn-submit {
  background-color: #4f46e5;
  color: white;
  padding: 10px 15px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: bold;
  transition: background-color 0.2s;
}

.btn-submit:hover {
  background-color: #4338ca;
}

.label {
  display: block;
  margin-bottom: 8px;
  font-weight: 500;
  color: #374151;
}

@media (max-width: 640px) {
  #content {
    margin-top: 3.5rem;
    height: calc(100vh - 3.5rem) !important;
  }
  
  .fixed.left-0 {
    top: 3.5rem;
    height: calc(100vh - 3.5rem);
  }
}
</style>
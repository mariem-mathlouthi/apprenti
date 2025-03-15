<!-- <template>
    <Navbar />
    <div class="grid grid-cols-12 gap-4">
      <div class="col-span-3">
        <Sidebar />
      </div>
      <div class="col-span-9 mt-16 mr-16">
        <div class="bg-gray-100 px-8 py-12 font-[sans-serif]">
          <div class="container mx-auto p-6 bg-white rounded-lg shadow-lg">
            <h2 class="text-3xl font-bold text-purple-700 mb-6">Ajouter un nouveau cours</h2>
            <form @submit.prevent="submitCourse" class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-gray-700 font-medium">Titre du cours</label>
                <input v-model="course.title" type="text" class="w-full p-3 border rounded-lg mt-1" required />
              </div>
              <div>
                <label class="block text-gray-700 font-medium">Créé par</label>
                <input v-model="course.createdBy" type="text" class="w-full p-3 border rounded-lg mt-1" required />
              </div>
              <div>
                <label class="block text-gray-700 font-medium">Durée (en heures)</label>
                <input v-model="course.duration" type="number" class="w-full p-3 border rounded-lg mt-1" required />
              </div>
              <div>
                <label class="block text-gray-700 font-medium">Prix (€)</label>
                <input v-model="course.price" type="number" class="w-full p-3 border rounded-lg mt-1" required />
              </div>
              <div class="col-span-2">
                <label class="block text-gray-700 font-medium">Description</label>
                <textarea v-model="course.description" class="w-full p-3 border rounded-lg mt-1" rows="4" required></textarea>
              </div>
              <div>
                <label class="block text-gray-700 font-medium">Niveau</label>
                <select v-model="course.level" class="w-full p-3 border rounded-lg mt-1">
                  <option value="faible">Faible</option>
                  <option value="intermediaire">Intermédiaire</option>
                  <option value="avance">Avancé</option>
                </select>
              </div>
              <div>
                <label class="block text-gray-700 font-medium">Fichier du cours</label>
                <input type="file" @change="handleFileUpload" class="w-full p-3 border rounded-lg mt-1" />
              </div>
              <div class="col-span-2 text-right">
                <button type="submit" class="bg-purple-700 text-white px-6 py-3 rounded-lg hover:bg-purple-800">Ajouter le cours</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import Sidebar from "./SidebarTut.vue";
  import Navbar from "./NavBarTut.vue";
  import { toast } from "vue3-toastify";
  import "vue3-toastify/dist/index.css";
  
  export default {
    components: { Sidebar, Navbar },
    data() {
      return {
        course: {
          title: "",
          description: "",
          createdBy: "",
          duration: "",
          price: "",
          level: "faible",
          file: null,
        },
      };
    },
    methods: {
      handleFileUpload(event) {
        this.course.file = event.target.files[0];
      },
      submitCourse() {
        if (!this.course.title || !this.course.description || !this.course.createdBy || !this.course.duration || !this.course.price) {
          toast.error("Veuillez remplir tous les champs.", { autoClose: 2000 });
          return;
        }
        toast.success("Cours ajouté avec succès !", { autoClose: 2000 });
        console.log("Données du cours :", this.course);
      },
    },
  };
  </script>
   -->
   <template>
    <div id="app" class="flex flex-col h-screen">
      <!-- SIDEBAR & NAVBAR -->
      <NavbarTuteur />
      <SidebarTuteur />
  
      <!-- CONTENU -->
      <section id="content" class="flex-1 flex justify-center items-center py-6">
        <div class="container mx-auto p-6">
          <h1 class="text-3xl font-bold text-indigo-700 text-center mb-6">
            Ajouter un Cours
          </h1>
          <div class="bg-white p-8 rounded-lg shadow-lg w-full sm:w-96 max-w-4xl mx-auto">
            <form @submit.prevent="createCours" class="space-y-6">
              <!-- Titre -->
              <div class="form-group">
                <label class="label">Titre</label>
                <input type="text" v-model="titre" required class="input" placeholder="Entrez le titre" />
              </div>
  
              <!-- Description -->
              <div class="form-group">
                <label class="label">Description</label>
                <textarea v-model="description" required class="input" placeholder="Entrez la description"></textarea>
              </div>
  
              <!-- Catégorie -->
              <div class="form-group">
                <label class="label">Catégorie</label>
                <select v-model="selectedCategory" required class="input">
                  <option disabled value="">Sélectionnez une catégorie</option>
                  <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                  </option>
                </select>
              </div>
  
              <!-- Prix -->
              <div class="form-group">
                <label class="label">Prix (€)</label>
                <input type="number" v-model="prix" required class="input" placeholder="Entrez le prix" />
              </div>
  
              <!-- Durée -->
              <div class="form-group">
                <label class="label">Durée (heures)</label>
                <input type="number" v-model="duree" required class="input" placeholder="Durée du cours" />
              </div>
  
              <!-- Fichier (PDF ou autre) -->
              <div class="form-group">
                <label class="label">Fichier</label>
                <input type="file" @change="handleFileUpload" required class="input" />
              </div>
  
              <!-- Bouton d'ajout -->
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
  import NavbarTuteur from "./NavbarTut.vue"; // Assurez-vous d'importer NavbarTuteur
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
        duree: "",
        file: null,
      };
    },
    methods: {
      async fetchCategories() {
        try {
          const response = await axios.get("http://localhost:8000/api/categories");
          this.categories = response.data;
        } catch (error) {
          console.error("Erreur lors de la récupération des catégories :", error);
          toast.error("Impossible de récupérer les catégories.");
        }
      },
      handleFileUpload(event) {
        this.file = event.target.files[0];
      },
      async createCours() {
        let formData = new FormData();
        formData.append("titre", this.titre);
        formData.append("description", this.description);
        formData.append("category_id", this.selectedCategory);
        formData.append("prix", this.prix); // Correspond au backend
        formData.append("duree", this.duree); // Correspond au backend
        formData.append("file", this.file);
        formData.append("createdBy", "1"); // Remplacez par l'ID du tuteur connecté
  
        try {
          await axios.post("http://localhost:8000/api/cours", formData, {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          });
          toast.success("Cours ajouté avec succès !");
          this.$router.push("/tuteur/dashboard"); // Rediriger après l'ajout
        } catch (error) {
          if (error.response && error.response.data.message) {
            toast.error(`Erreur: ${error.response.data.message}`);
          } else {
            console.error("Erreur inconnue:", error);
          }
        }
      },
    },
    mounted() {
      this.fetchCategories();
    },
  };
  </script>
  
  <style scoped>
  .input {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
  }
  .btn-submit {
    background-color: #4f46e5;
    color: white;
    padding: 10px 15px;
    border-radius: 4px;
    cursor: pointer;
  }
  .btn-submit:hover {
    background-color: #4338ca;
  }
  </style>
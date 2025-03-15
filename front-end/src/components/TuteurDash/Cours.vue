<template>
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
  
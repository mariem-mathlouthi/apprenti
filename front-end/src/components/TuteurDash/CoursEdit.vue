<template>
  <div>
    <NavbarTuteur />
    <div class="grid grid-cols-12 gap-4">
      <div class="col-span-3">
        <SidebarTuteur />
      </div>
      <div class="col-span-9 mt-24 mr-24">
        <router-link
          to="/cours"
          class="px-6 py-2 rounded bg-purple-400 hover:bg-purple-500 text-white ml-4 mt-4 no-underline"
        >
          Retour
        </router-link>

        <div class="container mx-auto px-4 py-8">
          <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
              <div>
                <h3 class="text-lg font-medium leading-6 text-gray-900">Modifier le cours</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Mettez à jour ou supprimez le cours</p>
              </div>
            </div>

            <div class="border-t border-gray-200">
              <dl>
                <!-- Titre -->
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Titre</dt>
                  <dd class="mt-1 sm:col-span-2">
                    <input
                      type="text"
                      v-model="form.titre"
                      class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      :disabled="loading"
                    />
                  </dd>
                </div>

                <!-- Description -->
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Description</dt>
                  <dd class="mt-1 sm:col-span-2">
                    <textarea
                      v-model="form.description"
                      class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      :disabled="loading"
                    ></textarea>
                  </dd>
                </div>

                <!-- Catégorie -->
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Catégorie</dt>
                  <dd class="mt-1 sm:col-span-2">
                    <select
                      v-model="form.category_id"
                      class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      :disabled="loading"
                    >
                      <option disabled value="">Sélectionnez une catégorie</option>
                      <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.description }}
                      </option>
                    </select>
                  </dd>
                </div>

                <!-- Prix -->
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Prix (€)</dt>
                  <dd class="mt-1 sm:col-span-2">
                    <input
                      type="number"
                      v-model="form.prix"
                      class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      :disabled="loading"
                    />
                  </dd>
                </div>

                <!-- Durée -->
                <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                  <dt class="text-sm font-medium text-gray-500">Durée (heures)</dt>
                  <dd class="mt-1 sm:col-span-2">
                    <input
                      type="number"
                      v-model="form.duration"
                      class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                      :disabled="loading"
                    />
                  </dd>
                </div>
              </dl>
            </div>

            <div class="flex justify-end px-4 py-4 sm:px-6">
              <button
                @click="updateCours"
                :disabled="loading"
                class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
              >
                {{ loading ? "Enregistrement..." : "Enregistrer" }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import NavbarTuteur from "./NavbarTut.vue";
import SidebarTuteur from "./SidebarTut.vue";

export default {
  data() {
    return {
      form: {
        titre: "",
        description: "",
        category_id: "",
        prix: "",
        duration: ""
      },
      categories: [],
      coursId: null,
      loading: false
    };
  },
  components: {
    NavbarTuteur,
    SidebarTuteur
  },
  created() {
    this.coursId = this.$route.params.id;
    this.fetchCategories();
    this.fetchCoursDetails();
  },
  methods: {
    async fetchCategories() {
      try {
        const response = await axios.get("http://localhost:8000/api/categories",
          {
            headers: {
              Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`
            }
          }
        );
        this.categories = response.data || [];
      } catch (error) {
        console.error("Erreur lors de la récupération des catégories :", error);
      }
    },
    async fetchCoursDetails() {
      try {
        const response = await axios.get(`http://localhost:8000/api/cours/${this.coursId}`,
          {
            headers: {
              Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`
            }
          }
        );
        this.form = response.data.cours;
      } catch (error) {
        console.error("Erreur lors de la récupération des détails du cours :", error);
      }
    },
    async updateCours() {
      this.loading = true;
      try {
        const response = await axios.put(`http://localhost:8000/api/cours/${this.coursId}`, this.form,
          {
            headers: {
              Authorization: `Bearer ${JSON.parse(sessionStorage.getItem("token"))}`
            }
          }
        );
        if (response.data.success) {
          toast.success("Cours mis à jour avec succès !");
          this.form = response.data.cours;
        }
      } catch (error) {
        console.error("Erreur lors de la mise à jour :", error);
        toast.error("Échec de la mise à jour.");
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>
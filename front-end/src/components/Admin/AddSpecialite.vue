<template>
  <div class="dashboard-container">
    <!-- Sidebar -->
    <Sidebar />

    <div class="main-content">
      <!-- Navbar -->
    

      <div class="add-specialite-container">
        <div class="form-card">
          <div class="header">
            <h2 class="title">Nouvelle Spécialité</h2>
            <p class="subtitle">Remplissez les informations de la spécialité</p>
          </div>

          <form @submit.prevent="addSpecialite" class="specialite-form">
            <div class="form-group">
              <label for="description" class="input-label">Description</label>
              <div class="input-wrapper">
                <input 
                  type="text" 
                  id="description" 
                  v-model="specialite.description" 
                  class="form-input"
                  placeholder="Entrez la description de la spécialité"
                  required
                />
                
              </div>
            </div>

            <button type="submit" :disabled="loading" class="submit-btn">
              <span v-if="!loading">Ajouter</span>
              <span v-else>Enregistrement...</span>
              <i class="fas" :class="loading ? 'fa-spinner fa-spin' : 'fa-arrow-right'"></i>
            </button>
          </form>

          <transition name="fade">
            <div v-if="message" class="success-message">
              <i class="fas fa-check-circle"></i>
              {{ message }}
            </div>
          </transition>

          <transition name="fade">
            <div v-if="errorMessage" class="error-message">
              <i class="fas fa-exclamation-triangle"></i>
              {{ errorMessage }}
            </div>
          </transition>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Sidebar from "./SidebarMenu.vue";
import Navbar from "./NavbarOne.vue";
import axios from "axios";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

export default {
  components: { Sidebar, Navbar },
  data() {
    return {
      specialite: { description: "" },
      message: "",
      errorMessage: "",
      loading: false
    };
  },
  methods: {
    async addSpecialite() {
      this.loading = true;
      try {
        const response = await axios.post("http://127.0.0.1:8000/api/specialites", this.specialite);
        
        this.message = "Spécialité créée avec succès!";
        toast.success(this.message, { autoClose: 2000 });

        // Redirection après 2 secondes
        setTimeout(() => {
          this.$router.push('/SpecialiteList');
        }, 2000);

      } catch (error) {
        this.errorMessage = error.response?.data?.message || "Erreur lors de la création";
        toast.error(this.errorMessage, { autoClose: 3000 });
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>

<style scoped>
.dashboard-container {
  display: flex;
  min-height: 100vh;
  background: #f8f9fa;
}

.main-content {
  flex: 1;
  padding: 30px;
  margin-left: 280px;
}

.add-specialite-container {
  max-width: 600px;
  margin: 40px auto;
}

.form-card {
  background: white;
  border-radius: 16px;
  box-shadow: 0 8px 30px rgba(0,0,0,0.06);
  padding: 40px;
  position: relative;
  overflow: hidden;
}

.header {
  text-align: center;
  margin-bottom: 40px;
}

.title {
  font-size: 2rem;
  color: #2d3748;
  margin-bottom: 8px;
  font-weight: 700;
}

.subtitle {
  color: #718096;
  font-size: 0.95rem;
}

.specialite-form {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.form-group {
  position: relative;
}

.input-label {
  display: block;
  margin-bottom: 8px;
  color: #4a5568;
  font-weight: 500;
}

.input-wrapper {
  position: relative;
}

.form-input {
  width: 100%;
  padding: 14px 48px 14px 16px;
  border: 2px solid #e2e8f0;
  border-radius: 10px;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.form-input:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
  outline: none;
}

.input-icon {
  position: absolute;
  right: 16px;
  top: 50%;
  transform: translateY(-50%);
  color: #a0aec0;
}

.submit-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 16px;
  border: none;
  border-radius: 10px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  transition: transform 0.2s, box-shadow 0.2s;
}

.submit-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

.submit-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.success-message {
  margin-top: 20px;
  padding: 15px;
  background: #48bb78;
  color: white;
  border-radius: 8px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.error-message {
  margin-top: 20px;
  padding: 15px;
  background: #f56565;
  color: white;
  border-radius: 8px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to {
  opacity: 0;
}

@media (max-width: 768px) {
  .main-content {
    margin-left: 0;
    padding: 20px;
  }
  
  .form-card {
    padding: 25px;
  }
}
</style>
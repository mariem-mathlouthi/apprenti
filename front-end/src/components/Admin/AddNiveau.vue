<template>
  <div class="dashboard-container">
    <Sidebar />
    <div class="main-content">
      <Navbar />
      <div class="add-niveau">
        <h2 class="animated-title">Ajouter un Nouveau Niveau</h2>
        
        <form @submit.prevent="addNiveau" class="form-container">
          <div class="form-group floating-label">
            <input 
              type="text" 
              id="description" 
              v-model="niveau.description" 
              required 
              placeholder=" "
              class="styled-input"
            />
            <label for="description">Description du niveau</label>
            <div class="underline"></div>
          </div>

          <button 
            type="submit" 
            :disabled="loading"
            class="submit-button"
          >
            <span v-if="!loading">Créer le Niveau</span>
            <div v-else class="loader"></div>
          </button>

          <transition name="fade">
            <p v-if="message" class="message success-message">
              <i class="fas fa-check-circle"></i> {{ message }}
            </p>
          </transition>
          
          <transition name="fade">
            <p v-if="errorMessage" class="message error-message">
              <i class="fas fa-exclamation-triangle"></i> {{ errorMessage }}
            </p>
          </transition>
        </form>
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
      niveau: { description: "" },
      message: "",
      errorMessage: "",
      loading: false
    };
  },
  methods: {
    async addNiveau() {
      this.loading = true;
      try {
        await axios.post("http://127.0.0.1:8000/api/niveaux", this.niveau);
        this.message = "Niveau ajouté avec succès !";
        toast.success(this.message, { autoClose: 2000 });
        this.niveau.description = "";
      } catch (error) {
        this.errorMessage = "Erreur lors de l'ajout du niveau.";
        toast.error(this.errorMessage, { autoClose: 2000 });
      } finally {
        this.loading = false;
      }
      setTimeout(() => {
        this.$router.push('/NiveauList');
      }, 2000);
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
  flex-grow: 1;
  padding: 2rem;
  transition: margin-left 0.3s;
}

.add-niveau {
  max-width: 600px;
  margin: 2rem auto;
  padding: 2.5rem;
  background: white;
  border-radius: 15px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.animated-title {
  text-align: center;
  font-size: 2.2rem;
  margin-bottom: 2.5rem;
  color: #2c3e50;
  position: relative;
  display: inline-block;
  width: 100%;
}

.animated-title::after {
  content: '';
  display: block;
  width: 60px;
  height: 4px;
  background: linear-gradient(45deg, #6a11cb, #2575fc);
  margin: 0.5rem auto;
  border-radius: 2px;
}

.form-container {
  position: relative;
}

.form-group {
  margin-bottom: 2rem;
  position: relative;
}

.floating-label {
  position: relative;
}

.styled-input {
  width: 100%;
  padding: 1.2rem 1rem;
  font-size: 1rem;
  border: none;
  background: transparent;
  border-radius: 4px;
  transition: all 0.3s ease;
  box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.styled-input:focus {
  outline: none;
  box-shadow: 0 4px 10px rgba(106, 17, 203, 0.1);
}

.styled-input:focus + label,
.styled-input:not(:placeholder-shown) + label {
  transform: translateY(-2rem);
  font-size: 0.8rem;
  color: #6a11cb;
}

.floating-label label {
  position: absolute;
  left: 1rem;
  top: 1.2rem;
  color: #666;
  pointer-events: none;
  transition: all 0.3s ease;
  font-size: 1rem;
}

.underline {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 2px;
  background: #e0e0e0;
  transform-origin: bottom right;
  transition: transform 0.3s ease;
}

.styled-input:focus ~ .underline {
  transform: scaleX(1);
  background: linear-gradient(45deg, #6a11cb, #2575fc);
}

.submit-button {
  width: 100%;
  padding: 1.2rem;
  background: linear-gradient(45deg, #6a11cb, #2575fc);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1.1rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.submit-button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(106, 17, 203, 0.3);
}

.submit-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.loader {
  border: 3px solid #f3f3f3;
  border-top: 3px solid #6a11cb;
  border-radius: 50%;
  width: 24px;
  height: 24px;
  animation: spin 1s linear infinite;
  margin: 0 auto;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.message {
  text-align: center;
  padding: 1rem;
  border-radius: 8px;
  margin-top: 1.5rem;
  font-weight: 500;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
}

.success-message {
  background: #e8f5e9;
  color: #2e7d32;
  border: 1px solid #a5d6a7;
}

.error-message {
  background: #ffebee;
  color: #c62828;
  border: 1px solid #ef9a9a;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to {
  opacity: 0;
}

@media (max-width: 768px) {
  .add-niveau {
    margin: 1rem;
    padding: 1.5rem;
  }
  
  .animated-title {
    font-size: 1.8rem;
  }
}
</style>
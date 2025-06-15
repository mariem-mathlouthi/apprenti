<template>
    <main class="w-full h-screen flex flex-col items-center justify-center px-4 bg-gray-50">
        <div class="max-w-sm w-full text-gray-600 bg-white p-8 rounded-lg shadow-md">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-indigo-600 mb-2">Connexion</h1>
                <div class="mt-5 space-y-2">
                    <p class="text-gray-600">
                        Vous n'avez pas de compte ?
                        <router-link
                          to="/register"
                          class="font-medium text-indigo-600 hover:text-indigo-500"
                          >S'inscrire</router-link>
                    </p>
                </div>
            </div>
            <form
                @submit.prevent="seConnecter"
                class="mt-8 space-y-5"
            >
                <!-- Champ Email -->
                <div>
                    <label class="font-medium block mb-1">
                        Adresse email*
                    </label>
                    <input
                        type="email"
                        v-model="email"
                        @blur="validerEmail"
                        required
                        class="w-full mt-1 px-3 py-2 text-gray-700 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg transition-colors"
                        :class="{ 'border-red-500': emailError }"
                    />
                    <p v-if="emailError" class="text-red-500 text-sm mt-1">{{ emailError }}</p>
                </div>

                <!-- Champ Mot de passe -->
                <div>
                    <label class="font-medium block mb-1">
                        Mot de passe*
                    </label>
                    <div class="relative">
                        <input
                            :type="montrerMotDePasse ? 'text' : 'password'"
                            v-model="motDePasse"
                            @input="validerMotDePasse"
                            required
                            class="w-full mt-1 px-3 py-2 text-gray-700 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg transition-colors"
                            :class="{ 'border-red-500': motDePasseError }"
                        />
                        <button
                            type="button"
                            @click="montrerMotDePasse = !montrerMotDePasse"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-indigo-600"
                        >
                            <i :class="montrerMotDePasse ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>
                    <p v-if="motDePasseError" class="text-red-500 text-sm mt-1">{{ motDePasseError }}</p>
                </div>

                <!-- Bouton de connexion -->
                <button
                    :disabled="chargement"
                    class="w-full px-4 py-2 text-white font-medium bg-indigo-600 hover:bg-indigo-500 disabled:bg-indigo-400 rounded-lg duration-150 transition-colors flex items-center justify-center"
                >
                    <span v-if="chargement">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Connexion...
                    </span>
                    <span v-else>Se connecter</span>
                </button>

                <!-- Lien mot de passe oublié -->
                <div class="text-center pt-2">
                   <router-link 
                     to="/forgot-password" 
                     class="text-indigo-600 hover:text-indigo-500 text-sm"
                   >
                        Mot de passe oublié ?
                    </router-link>
                </div>
            </form>
        </div>
    </main>
</template>

<script>
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import axios from "axios";

export default {
  data() {
    return {
      email: "",
      motDePasse: "",
      chargement: false,
      montrerMotDePasse: false,
      // Gestion des erreurs
      emailError: "",
      motDePasseError: ""
    };
  },
  methods: {
    validerEmail() {
      const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!this.email) {
        this.emailError = "L'email est requis";
      } else if (!regexEmail.test(this.email)) {
        this.emailError = "Veuillez entrer un email valide";
      } else {
        this.emailError = "";
      }
      return !this.emailError;
    },
    
    validerMotDePasse() {
      if (!this.motDePasse) {
        this.motDePasseError = "Le mot de passe est requis";
      } else if (this.motDePasse.length < 8) {
        this.motDePasseError = "Minimum 8 caractères";
      } else {
        this.motDePasseError = "";
      }
      return !this.motDePasseError;
    },
    
    async seConnecter() {
      // Validation des champs avant soumission
      const isEmailValid = this.validerEmail();
      const isPasswordValid = this.validerMotDePasse();
      
      if (!isEmailValid || !isPasswordValid) {
        toast.error("Veuillez corriger les erreurs avant de continuer", {
          autoClose: 2000,
        });
        return;
      }
      
      this.chargement = true;
      
      const donneesConnexion = {
        email: this.email,
        password: this.motDePasse,
      };

      try {
        const response = await axios.post(
          "http://localhost:8000/api/login",
          donneesConnexion
        );
        
        if (response.data.check === true) {
          // Gestion des différents types de comptes
          switch(response.data.role) {
            case "entreprise":
              this.gererEntreprise(response);
              break;
              
            case "student":
              this.gererEtudiant(response);
              break;
              
            case "tuteur":
              this.gererTuteur(response);
              break;
              
            case "admin":
              this.gererAdmin(response);
              break;
              
            default:
              toast.error("Type de compte non reconnu", { autoClose: 2000 });
          }
        } else {
          toast.error(response.data.message || "Échec de la connexion", {
            autoClose: 2000, 
          });
        }
      } catch (error) {
        let messageErreur = "Erreur de connexion au serveur";
        if (error.response?.data?.message) {
          messageErreur = error.response.data.message;
        }
        toast.error(messageErreur, { autoClose: 2000 });
        console.error("Erreur détaillée:", error);
      } finally {
        this.chargement = false;
      }
    },
    
    gererEntreprise(response) {
      const compteEntreprise = {
        id: response.data.user.id,
        numeroSIRET: response.data.user.numeroSIRET,
        email: response.data.user.email,
        name: response.data.user.name,
        secteur: response.data.user.secteur,
        description: response.data.user.description,
        token: response.data.token,
        role: response.data.role,
      };
      
      localStorage.setItem("EntrepriseAccountInfo", JSON.stringify(compteEntreprise));
      sessionStorage.setItem("CurrentUser", JSON.stringify('entreprise'));
      sessionStorage.setItem('token', JSON.stringify(response.data.token));
      
      toast.success("Connexion réussie!", { autoClose: 1500 });
      this.$router.push("/EntrepriseDash");
    },
    
    gererEtudiant(response) {
      const compteEtudiant = {
        id: response.data.user.id,
        fullname: response.data.user.fullname,
        email: response.data.user.email,
        niveau: response.data.user.niveau,
        domaine: response.data.user.domaine,
        specialite: response.data.user.specialite,
        typeStage: response.data.user.typeStage,
        etablissement: response.data.user.etablissement,
        token: response.data.token,
        role: response.data.role,
      };
      
      localStorage.setItem("StudentAccountInfo", JSON.stringify(compteEtudiant));
      sessionStorage.setItem("CurrentUser", JSON.stringify('etudiant'));
      sessionStorage.setItem('token', JSON.stringify(response.data.token));
      
      toast.success("Connexion réussie!", { autoClose: 1500 });
      this.$router.push("/StudentDash");
    },
    
    gererTuteur(response) {
      const compteTuteur = {
        id: response.data.user.id,
        fullname: response.data.user.fullname,
        email: response.data.user.email,
        domaine: response.data.user.domaine,
        specialite: response.data.user.specialite,
        experience: response.data.user.experience,
        token: response.data.token,
        role: response.data.role,
      };
      
      localStorage.setItem("TuteurAccountInfo", JSON.stringify(compteTuteur));
      sessionStorage.setItem("CurrentUser", JSON.stringify('tuteur'));
      sessionStorage.setItem('token', JSON.stringify(response.data.token));
      
      toast.success("Connexion réussie!", { autoClose: 1500 });
      this.$router.push("/TuteurDashboard");
    },
    
    gererAdmin(response) {
      sessionStorage.setItem("CurrentUser", JSON.stringify('admin'));
      sessionStorage.setItem('token', JSON.stringify(response.data.token));
      
      toast.success("Connexion administrateur réussie!", { autoClose: 1500 });
      this.$router.push("/Admin");
    }
  }
};
</script>
<template>
    <main class="w-full h-screen flex flex-col items-center justify-center px-4">
        <div class="max-w-sm w-full text-gray-600">
            <div class="text-center">
                
                <div class="mt-5 space-y-2">
                    <h3 class="text-gray-800 text-2xl font-bold sm:text-3xl">Log in to your account</h3>
                    <p class="">
                Dont't you have an account?
                <router-link
                  to="/"
                  class="font-medium text-indigo-600 hover:text-indigo-500"
                  >Sign up </router-link>
                
              </p>
                    
                </div>
            </div>
            <form
            @submit.prevent="Signin"
                class="mt-8 space-y-5"
            >
                <div>
                    <label class="font-medium">
                        Email
                    </label>
                    <input
                        type="email"
                        v-model="email"
                        required
                        class="w-full mt-2 px-3 py-2 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg"
                    />
                </div>
                <div>
                    <label class="font-medium">
                        Password
                    </label>
                    <input
                        type="password"
                        v-model="password"
                        required
                        class="w-full mt-2 px-3 py-2 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg"
                    />
                </div>
                <button
                    class="w-full px-4 py-2 text-white font-medium bg-gray-800 hover:bg-gray-700 active:bg-gray-700 rounded-lg duration-150"
                >
                    Sign in
                </button>
                <div class="text-center">
                   <router-link to="/forgot-password" class="text-indigo-600 hover:text-indigo-500">
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
import { computed } from "vue";
// import authStore from "../../plugins/authStore";

export default {
  data() {
    return {
      email: "",
      password: "",
      isAuthenticated : false,
    };
  },
  methods: {

    async Signin() {
    let myjson = {
        email:this.email,
        password:this.password,
      }
      console.log(myjson);
        try {
          const response = await axios.post(
            "http://localhost:8000/api/login",
            myjson,
            
          );
          if (response.data.check === true) {
            // authStore.logedIn();

            if(response.data.role === "entreprise"){
              let EntrepriseAccount = {
                id:response.data.user.id,
                numeroSIRET:response.data.user.numeroSIRET,
                email:response.data.user.email,
                name:response.data.user.name,
                secteur:response.data.user.secteur,
                description:response.data.user.description,
                token:response.data.token,
                role:response.data.role,
              }
            localStorage.setItem("EntrepriseAccountInfo",JSON.stringify(EntrepriseAccount));
            sessionStorage.setItem("CurrentUser", JSON.stringify('entreprise'));
            sessionStorage.setItem('token', JSON.stringify(response.data.token));

            // this.$router.push('/EntrepriseDash'); // Redirection vers le tableau de bord de l'entreprise
            // window.location.pathname = "/EntrepriseDash";
            window.location.href = "/EntrepriseDash";
             
            }
            else if(response.data.role === "student"){
              let StudentAccount = {
                id:response.data.user.id,
                fullname:response.data.user.fullname,
                email:response.data.user.email,
                niveau:response.data.user.niveau,
                domaine:response.data.user.domaine,
                specialite:response.data.user.specialite,
                typeStage:response.data.user.typeStage,
                etablissement:response.data.user.etablissement,
                token:response.data.token,
                role:response.data.role,
                
              }
            localStorage.setItem("StudentAccountInfo",JSON.stringify(StudentAccount));
            sessionStorage.setItem("CurrentUser", JSON.stringify('etudiant'));
            sessionStorage.setItem('token', JSON.stringify(response.data.token));

            // this.$router.push('/StudentDash');
            window.location.href = "/StudentDash";
            }
            else if(response.data.role === "tuteur"){
              let TuteurAccount = {
                id:response.data.user.id,
                fullname:response.data.user.fullname,
                email:response.data.user.email,
                domaine:response.data.user.domaine,
                specialite:response.data.user.specialite,
                experience:response.data.user.experience,
                token:response.data.token,
                role:response.data.role,

              }
              localStorage.setItem("TuteurAccountInfo", JSON.stringify(TuteurAccount));
              sessionStorage.setItem("CurrentUser", JSON.stringify('tuteur'));
              sessionStorage.setItem('token', JSON.stringify(response.data.token));
              // this.$router.push('/TuteurDashboard');
              window.location.href = "/TuteurDashboard";
          }
           else if(response.data.role === "admin"){
              // this.$router.push('/Admin');
              sessionStorage.setItem("CurrentUser", JSON.stringify('admin'));
              sessionStorage.setItem('token', JSON.stringify(response.data.token));
              
              window.location.href = "/Admin";
              toast.success("Admin Account Exist !", {
              autoClose: 2000, 
            });

            }
        
          } else {
            toast.error(response.data.message, {
              autoClose: 2000, 
            });
          }
        } catch (error) {
          console.error("Error:", error);
        }
        
    

    }
    
  },
  mounted() {
   
  },
};
</script>
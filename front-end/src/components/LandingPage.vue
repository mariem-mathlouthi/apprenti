<template>
  <div class="flex flex-col min-h-screen bg-gradient-to-br from-white to-indigo-50 relative overflow-hidden">
    <!-- Élément décoratif dynamique -->
    <div class="absolute inset-0 bg-[radial-gradient(at_top_right,_var(--tw-gradient-stops))] from-purple-100/50 to-transparent"></div>

    <header class="bg-white/80 backdrop-blur-sm border-b border-gray-100 sticky top-0 z-50">
      <nav class="flex items-center justify-between px-4 py-3 mx-auto max-w-screen-xl sm:px-8">
        <a href="/" class="hover:scale-[1.02] transition-transform duration-300">
          <img src="../assets/logo.png" width="140" height="60" alt="Logo" class="drop-shadow-sm" />
        </a>
        <ul class="flex items-center space-x-4 sm:space-x-8">
          <li v-for="link in navigation" :key="link.id" class="group relative">
            <a :href="link.router" 
               class="px-3 py-2 text-gray-600 hover:text-indigo-700 font-medium transition-colors duration-300
                      before:absolute before:-bottom-1 before:h-0.5 before:w-0 before:bg-indigo-500 
                      before:transition-all before:duration-300 hover:before:w-full">
              {{ link.title }}
            </a>
          </li>
          <li>
            <router-link to="/signin" 
                         class="flex items-center space-x-2 bg-indigo-600 text-white px-6 py-2.5 rounded-lg
                                shadow-sm hover:bg-indigo-700 transition-all duration-300
                                hover:shadow-md hover:-translate-y-0.5">
              <span>Connexion</span>
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3 3a1 1 0 011 1v12a1 1 0 11-2 0V4a1 1 0 011-1zm7.707 3.293a1 1 0 010 1.414L9.414 9H17a1 1 0 110 2H9.414l1.293 1.293a1 1 0 01-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0z" clip-rule="evenodd"/>
              </svg>
            </router-link>
          </li>
        </ul>
      </nav>
    </header>

    <main class="flex-1">
      <!-- Section Héro -->
      <section class="relative pt-20 pb-32 sm:py-40">
        <div class="max-w-screen-xl mx-auto px-4 sm:px-8 grid lg:grid-cols-2 gap-16 items-center">
          <div class="text-center lg:text-left space-y-8">
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold text-gray-900 leading-tight 
                       animate-slide-up">
              <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                Transformez votre gestion pédagogique
              </span>
            </h1>
            
            <p class="text-xl text-gray-600 max-w-2xl mx-auto lg:mx-0 leading-relaxed">
              Une plateforme unifiée pour les formations académiques, stages professionnels et collaborations inter-entreprises
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
              <router-link 
                v-for="(btn, index) in buttons" 
                :key="index"
                :to="btn.link"
                class="flex items-center justify-center gap-x-3 px-8 py-3.5 text-lg font-semibold rounded-xl 
                       transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
                :class="[
                  index === 0 ? 'bg-indigo-600 text-white shadow-md hover:bg-indigo-700' : 
                  'bg-white text-gray-700 border-2 border-gray-200 hover:border-indigo-200 hover:bg-gray-50'
                ]">
                <component :is="btn.icon" class="w-6 h-6" />
                {{ btn.label }}
              </router-link>
            </div>
          </div>

          <!-- Illustration centrée avec effet de profondeur -->
          <div class="relative mt-16 lg:mt-0 flex justify-center items-center">
            <div class="relative w-full max-w-2xl aspect-square bg-indigo-50 rounded-[2rem] 
                        shadow-xl overflow-hidden transform rotate-1">
              <img src="../assets/education-illustration.png" 
                   alt="Gestion pédagogique"
                   class="absolute inset-0 w-full h-full object-cover scale-[1.01] 
                          hover:scale-100 transition-transform duration-500" />
            </div>
          </div>
        </div>
      </section>

      <!-- Section Fonctionnalités -->
      <section class="py-24 bg-white/50 backdrop-blur-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid md:grid-cols-3 gap-12">
          <div v-for="(feature, index) in features" :key="index"
               class="group p-8 rounded-3xl bg-white hover:bg-indigo-50 transition-colors duration-300 
                      border border-gray-100 hover:border-indigo-100 shadow-sm hover:shadow-md">
            <div class="flex items-center justify-center h-24 w-24 mx-auto mb-6 bg-indigo-100 rounded-2xl
                        group-hover:bg-indigo-200 transition-colors duration-300">
              <component :is="feature.icon" class="w-12 h-12 text-indigo-600 group-hover:text-indigo-700" />
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-3 text-center">{{ feature.title }}</h3>
            <p class="text-gray-600 text-center">{{ feature.description }}</p>
          </div>
        </div>
      </section>
    </main>

    <footer class="border-t bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 py-12 text-center text-gray-600">
        <p class="mb-2">© 2024 EduManager. Tous droits réservés.</p>
        <p class="text-sm">Une solution innovante pour l'éducation moderne</p>
      </div>
    </footer>
  </div>
</template>

<script>
import { BookOpenIcon, UserGroupIcon, BriefcaseIcon, AcademicCapIcon, ChartBarIcon, ClockIcon } from '@heroicons/vue/24/outline'

export default {
  components: { BookOpenIcon, UserGroupIcon, BriefcaseIcon, AcademicCapIcon, ChartBarIcon, ClockIcon },
  data() {
    return {
      navigation: [
        { title: "Équipe", router: "/team" },
        { title: "Contact", router: "/contact" },
      ],
      buttons: [
        { 
          label: "Espace Entreprise",
          link: "/signupEntreprise",
          icon: 'BriefcaseIcon'
        },
        { 
          label: "Espace Étudiant",
          link: "/signupEtudiant",
          icon: 'BookOpenIcon'
        },
        { 
          label: "Espace Tuteur",
          link: "/SignUpTuteur",
          icon: 'UserGroupIcon'
        }
      ],
      features: [
        {
          icon: 'AcademicCapIcon',
          title: "Programmes Académiques",
          description: "Gestion centralisée des cursus et ressources pédagogiques"
        },
        {
          icon: 'ChartBarIcon',
          title: "Suivi Personnalisé",
          description: "Tableaux de bord interactifs pour un monitoring en temps réel"
        },
        {
          icon: 'ClockIcon',
          title: "Planification Intelligente",
          description: "Optimisation des emplois du temps et échéances"
        }
      ]
    }
  }
}
</script>

<style scoped>
@keyframes slide-up {
  0% {
    opacity: 0;
    transform: translateY(30px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-slide-up {
  animation: slide-up 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) both;
}

@media (prefers-reduced-motion: reduce) {
  .animate-slide-up {
    animation: none;
  }
}
</style>
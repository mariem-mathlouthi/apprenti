
   <template>
    <nav
      class="bg-blue-100 shadow-xl h-screen fixed top-0 left-0 min-w-[250px] py-6 font-[sans-serif] overflow-auto"
      style="margin-top:72px"
    >
      <div class="relative flex flex-col h-full">
        <ul class="space-y-3 my-10 flex-1">
          <!-- Tableau de bord -->
          <li key="dashboard">
            <router-link
              to="/TuteurDashboard"
              class="text-[#333] text-sm flex items-center hover:text-[#007bff] hover:border-r-[5px] border-[#077bff] hover:bg-gray-100 px-8 py-4 transition-all"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="currentColor"
                class="w-[18px] h-[18px] mr-4"
                viewBox="0 0 512 512"
              >
                <path
                  d="M197.332 170.668h-160C16.746 170.668 0 153.922 0 133.332v-96C0 16.746 16.746 0 37.332 0h160c20.59 0 37.336 16.746 37.336 37.332v96c0 20.59-16.746 37.336-37.336 37.336zM37.332 32A5.336 5.336 0 0 0 32 37.332v96a5.337 5.337 0 0 0 5.332 5.336h160a5.338 5.338 0 0 0 5.336-5.336v-96A5.337 5.337 0 0 0 197.332 32z"
                  data-original="#000000"
                />
              </svg>
              <span>Tableau de bord</span>
            </router-link>
          </li>
  
          <!-- Cours -->
          <li key="cours">
            <router-link
              to="/cours"
              class="text-[#333] text-sm flex items-center hover:text-[#007bff] hover:border-r-[5px] border-[#077bff] hover:bg-gray-100 px-8 py-4 transition-all"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="currentColor"
                class="w-[18px] h-[18px] mr-4"
                viewBox="0 0 512 512"
              >
                <!-- Replace "M..." with a valid SVG path -->
                <path
                  d="M256 0C114.6 0 0 114.6 0 256s114.6 256 256 256 256-114.6 256-256S397.4 0 256 0zm0 464c-114.7 0-208-93.3-208-208S141.3 48 256 48s208 93.3 208 208-93.3 208-208 208zm96-280h-64v-64c0-8.8-7.2-16-16-16s-16 7.2-16 16v64h-64c-8.8 0-16 7.2-16 16s7.2 16 16 16h64v64c0 8.8 7.2 16 16 16s16-7.2 16-16v-64h64c8.8 0 16-7.2 16-16s-7.2-16-16-16z"
                  data-original="#000000"
                />
              </svg>
              <span>Cours</span>
            </router-link>
          </li>
          <li key="QuizzList">
            <router-link
              to="/QuizzList"
              class="text-[#333] text-sm flex items-center hover:text-[#007bff] hover:border-r-[5px] border-[#077bff] hover:bg-gray-100 px-8 py-4 transition-all"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="currentColor"
                class="w-[18px] h-[18px] mr-4"
                viewBox="0 0 512 512"
              >
                <!-- Replace "M..." with a valid SVG path -->
                <path
                  d="M256 0C114.6 0 0 114.6 0 256s114.6 256 256 256 256-114.6 256-256S397.4 0 256 0zm0 464c-114.7 0-208-93.3-208-208S141.3 48 256 48s208 93.3 208 208-93.3 208-208 208zm96-280h-64v-64c0-8.8-7.2-16-16-16s-16 7.2-16 16v64h-64c-8.8 0-16 7.2-16 16s7.2 16 16 16h64v64c0 8.8 7.2 16 16 16s16-7.2 16-16v-64h64c8.8 0 16-7.2 16-16s-7.2-16-16-16z"
                  data-original="#000000"
                />
              </svg>
              <span>Quizz</span>
            </router-link>
          </li>
          
  <li>
      <router-link to="/TuteurProfile" class="text-[#333] text-sm flex items-center hover:text-[#007bff] hover:border-r-[5px] border-[#077bff] hover:bg-gray-100 px-8 py-4 transition-all">
        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" stroke='currentColor'
                  class="w-[18px] h-[18px] mr-4" viewBox="0 0 682.667 682.667">
                  <defs>
                    <clipPath id="a" clipPathUnits="userSpaceOnUse">
                      <path d="M0 512h512V0H0Z" data-original="#000000" />
                    </clipPath>
                  </defs>
                  <g clip-path="url(#a)" transform="matrix(1.33 0 0 -1.33 0 682.667)">
                    <path fill="none" stroke-miterlimit="10" stroke-width="40"
                      d="M452 444H60c-22.091 0-40-17.909-40-40v-39.446l212.127-157.782c14.17-10.54 33.576-10.54 47.746 0L492 364.554V404c0 22.091-17.909 40-40 40Z"
                      data-original="#000000" />
                    <path
                      d="M472 274.9V107.999c0-11.027-8.972-20-20-20H60c-11.028 0-20 8.973-20 20V274.9L0 304.652V107.999c0-33.084 26.916-60 60-60h392c33.084 0 60 26.916 60 60v196.653Z"
                      data-original="#000000" />
                  </g>
                </svg>
          <span> Compte</span>
      </router-link>
  </li>

          <!-- Déconnexion -->
        <li>
          <router-link to="/SignIn"
            class="text-[#333] text-sm flex items-center hover:text-[#007bff] hover:border-r-[5px] border-[#077bff] hover:bg-gray-100 px-8 py-4 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Déconnexion</span>
          </router-link>
        </li>
  
          <!-- Autres liens existants -->
          <!-- Ajoutez ici les autres liens de votre sidebar sans les supprimer -->
        </ul>
      </div>
    </nav>
  </template>
  
  <script>
  export default {
    name: "SidebarTuteur",
  };
  </script>
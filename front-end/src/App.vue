
<template>
  <div id="app">
    <router-view></router-view>
    <NotifPopup v-if="isUserLoggedIn"/>
    <GeminiChatbot />
  </div>
</template>

<script>
import NotifPopup from './components/NotifPopup.vue'
import GeminiChatbot from './components/GeminiChatbot.vue'
import { computed, ref } from 'vue'

export default{
  components:{
    NotifPopup,
    GeminiChatbot
  },
  
  setup() {
    const isUserLoggedIn = computed(() => {
      return localStorage.getItem("StudentAccountInfo") || 
             localStorage.getItem("TuteurAccountInfo") || 
             localStorage.getItem("EntrepriseAccountInfo")
    })
    
    return {
      isUserLoggedIn
    }
  }
}
</script>


<style scoped>

</style>


<template>
  <div id="app">
    <router-view></router-view>
    <NotifPopup v-if="isUserLoggedIn"/>
  </div>
</template>

<script>
import NotifPopup from './components/NotifPopup.vue'
import { computed, ref } from 'vue'

export default{
  components:{
    NotifPopup
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

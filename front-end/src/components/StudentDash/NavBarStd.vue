<template>
  <header class="fixed w-full shadow-sm bg-white font-[sans-serif]">
    <div class="flex justify-between items-start sm:px-6 px-4 py-1 h-16">
      <!-- Logo -->
      <a href="#" class="ml-8 flex-shrink-0 -mt-4">
  <img src="./logo.png" alt="logo" class="w-56 object-contain" />
</a>



      <!-- Notification icon -->
      <div class="flex items-start mr-4 relative cursor-pointer" @click="goToNotifications">
        <svg
          class="fill-[#333] hover:fill-[#077bff]"
          xmlns="http://www.w3.org/2000/svg"
          width="24px"
          viewBox="0 0 371.263 371.263"
        >
          <path d="M305.402 234.794v-70.54c0-52.396-33.533-98.085-79.702-115.151.539-2.695.838-5.449.838-8.204C226.539 18.324 208.215 0 185.64 0s-40.899 18.324-40.899 40.899c0 2.695.299 5.389.778 7.964-15.868 5.629-30.539 14.551-43.054 26.647-23.593 22.755-36.587 53.354-36.587 86.169v73.115c0 2.575-2.096 4.731-4.731 4.731-22.096 0-40.959 16.647-42.995 37.845-1.138 11.797 2.755 23.533 10.719 32.276 7.904 8.683 19.222 13.713 31.018 13.713h72.217c2.994 26.887 25.869 47.905 53.534 47.905s50.54-21.018 53.534-47.905h72.217c11.797 0 23.114-5.03 31.018-13.713 7.904-8.743 11.797-20.479 10.719-32.276-2.036-21.198-20.958-37.845-42.995-37.845a4.704 4.704 0 0 1-4.731-4.731zM185.64 23.952c9.341 0 16.946 7.605 16.946 16.946 0 .778-.12 1.497-.24 2.275-4.072-.599-8.204-1.018-12.336-1.138-7.126-.24-14.132.24-21.078 1.198-.12-.778-.24-1.497-.24-2.275.002-9.401 7.607-17.006 16.948-17.006zm0 323.358c-14.431 0-26.527-10.3-29.342-23.952h58.683c-2.813 13.653-14.909 23.952-29.341 23.952zm143.655-67.665c.479 5.15-1.138 10.12-4.551 13.892-3.533 3.773-8.204 5.868-13.353 5.868H59.89c-5.15 0-9.82-2.096-13.294-5.868-3.473-3.772-5.09-8.743-4.611-13.892.838-9.042 9.282-16.168 19.162-16.168 15.809 0 28.683-12.874 28.683-28.683v-73.115c0-26.228 10.419-50.719 29.282-68.923 18.024-17.425 41.498-26.887 66.528-26.887 1.198 0 2.335 0 3.533.06 50.839 1.796 92.277 45.929 92.277 98.325v70.54c0 15.809 12.874 28.683 28.683 28.683 9.88 0 18.264 7.126 19.162 16.168z" />
        </svg>

        <span
          v-if="visibleNotificationsCount > 0"
          class="absolute -top-1 -right-1 flex items-center justify-center h-4 w-4 bg-red-600 rounded-full text-white text-[0.65rem] font-bold"
        >
          {{ visibleNotificationsCount }}
        </span>
      </div>
    </div>

    <!-- Reste de ton code -->
    <div id="collapseMenu" class="hidden before:fixed before:bg-black before:opacity-40 before:inset-0 max-lg:before:z-50">
      <button id="toggleClose" class="fixed top-1.5 right-3 z-[100] rounded-full bg-white p-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 fill-black" viewBox="0 0 320.591 320.591">
          <path
            d="M30.391 318.583a30.37 30.37 0 0 1-21.56-7.288c-11.774-11.844-11.774-30.973 0-42.817L266.643 10.665c12.246-11.459 31.462-10.822 42.921 1.424 10.362 11.074 10.966 28.095 1.414 39.875L51.647 311.295a30.366 30.366 0 0 1-21.256 7.288z"
          />
          <path
            d="M287.9 318.583a30.37 30.37 0 0 1-21.257-8.806L8.83 51.963C-2.078 39.225-.595 20.055 12.143 9.146c11.369-9.736 28.136-9.736 39.504 0l259.331 257.813c12.243 11.462 12.876 30.679 1.414 42.922-.456.487-.927.958-1.414 1.414a30.368 30.368 0 0 1-23.078 7.288z"
          />
        </svg>
      </button>

      <ul
        class="block fixed bg-white w-1/2 min-w-[250px] top-0 left-0 p-3 h-full shadow-md overflow-auto z-50"
      >
        <li class="pb-3 px-2">
          <a href="#">
            <img src="./logo.png" alt="logo" class="w-32" /> <!-- Logo mobile -->
          </a>
        </li>
      </ul>
    </div>
  </header>
</template>


<script>
import axios from 'axios';

export default {
  data() {
    return {
      notifications: [],
    };
  },
  computed: {
    visibleNotificationsCount() {
      return this.notifications.filter(notif => notif.visibility === 'shown').length;
    }
  },
  methods: {
    getNotifications() {
      const storedData = localStorage.getItem("notifications");
      if (storedData) {
        this.notifications = JSON.parse(storedData).notifications;
      }
    },
    async goToNotifications() {
      try {
        // Get the authorization token
        const token = JSON.parse(sessionStorage.getItem('token'));
        const headers = {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        };

        // Update visibility for each shown notification
        const shownNotifications = this.notifications.filter(n => n.visibility === 'shown');
        await Promise.all(shownNotifications.map(notification => 
          axios.put(
            `http://localhost:8000/notification/${notification.id}/visibility`,
            { visibility: 'hidden' },
            { headers }
          )
        ));

        // Update local state
        const updatedNotifications = this.notifications.map(n => ({ ...n, visibility: 'hidden' }));
        localStorage.setItem("notifications", JSON.stringify({ notifications: updatedNotifications }));
        this.notifications = updatedNotifications;
        
        // Emit event for other components
        this.$root.$emit('notifications-cleared');

        // Navigate to notifications page
        this.$router.push('/Consulternotif');
      } catch (error) {
        console.error('Error updating notification visibility:', error);
        // Still navigate even if the update fails
        this.$router.push('/Consulternotif');
      }
    }
  },
  mounted() {
    this.getNotifications();
  }
}
</script>

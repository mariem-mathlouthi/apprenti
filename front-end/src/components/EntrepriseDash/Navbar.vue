<template>
  <header class="fixed w-full shadow-sm bg-white font-[sans-serif]">
    <div class="flex justify-between items-start sm:px-6 px-4 py-1 h-16">
      <!-- Logo -->
      <a href="#" class="ml-8 flex-shrink-0 -mt-4">
  <img src="./logo.png" alt="logo" class="w-56 object-contain" />
</a>



     
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
export default {
  data() {
    return {
      notifications: [],
    };
  },
  methods: {
    getNotifications() {
      const storedData = localStorage.getItem("notifications");
      if (storedData) {
        this.notifications = JSON.parse(storedData).notifications;
      }
    },
    goToNotifications() {
      this.$router.push('/Consulternotif');
      this.notifications = [];
      localStorage.setItem("notifications", JSON.stringify({ notifications: [] }));
      this.$root.$emit('notifications-cleared');
    }
  },
  mounted() {
    this.getNotifications();
  }
}
</script>

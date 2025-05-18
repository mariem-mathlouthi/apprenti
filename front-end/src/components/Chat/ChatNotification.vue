<template>
  <div>
    <!-- Message Notification -->
    <transition name="notification-popup">
      <div v-if="show" class="fixed top-5 right-5 max-w-md w-full bg-white rounded-lg shadow-lg overflow-hidden z-50">
        <div class="bg-purple-600 h-2"></div>
        <div class="p-4">
          <div class="flex items-start">
            <div class="flex-shrink-0 mr-3">
              <div class="w-10 h-10 rounded-full bg-purple-600 flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
              </div>
            </div>
            <div class="flex-1">
              <h3 class="text-lg font-semibold text-gray-800">{{ title }}</h3>
              <p class="text-gray-600 mt-1">{{ message }}</p>
              <div class="mt-2 text-sm text-gray-500">{{ formatTime(timestamp) }}</div>
            </div>
            <button @click="close" class="text-gray-400 hover:text-gray-600">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          <div class="mt-3 flex justify-end space-x-2">
            <router-link 
              :to="chatRoute" 
              class="inline-flex items-center px-3 py-1 rounded-md bg-purple-100 text-purple-700 hover:bg-purple-200 transition-colors duration-200"
              @click="close"
            >
              <span>Voir le message</span>
              <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
              </svg>
            </router-link>
          </div>
        </div>
      </div>
    </transition>
    
    <!-- Error Notification -->
    <transition name="notification-popup">
      <div v-if="errorMessage" class="fixed bottom-5 right-5 max-w-md w-full bg-white rounded-lg shadow-lg overflow-hidden z-50">
        <div class="bg-red-600 h-2"></div>
        <div class="p-4">
          <div class="flex items-start">
            <div class="flex-shrink-0 mr-3">
              <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
              </div>
            </div>
            <div class="flex-1">
              <h3 class="text-lg font-semibold text-gray-800">Erreur</h3>
              <p class="text-gray-600 mt-1">{{ errorMessage }}</p>
            </div>
            <button @click="closeError" class="text-gray-400 hover:text-gray-600">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
export default {
  name: 'ChatNotification',
  props: {
    show: {
      type: Boolean,
      default: false
    },
    title: {
      type: String,
      default: 'Nouveau message'
    },
    message: {
      type: String,
      required: true
    },
    timestamp: {
      type: [Date, String],
      default: () => new Date()
    },
    userRole: {
      type: String,
      required: true
    },
    error: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      errorMessage: ''
    };
  },
  watch: {
    error(newValue) {
      if (newValue) {
        this.errorMessage = newValue;
        // Auto-hide error after 5 seconds
        setTimeout(() => {
          this.errorMessage = '';
        }, 5000);
      }
    }
  },
  computed: {
    chatRoute() {
      return this.userRole === 'student' ? '/student-chat' : '/tutor-chat';
    }
  },
  methods: {
    close() {
      this.$emit('close');
    },
    closeError() {
      this.errorMessage = '';
    },
    formatTime(timestamp) {
      if (!timestamp) return '';
      
      const date = new Date(timestamp);
      return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }
  }
};
</script>

<style scoped>
.notification-popup-enter-active,
.notification-popup-leave-active {
  transition: all 0.3s ease;
}

.notification-popup-enter-from,
.notification-popup-leave-to {
  transform: translateY(-30px);
  opacity: 0;
}
</style>
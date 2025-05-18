<template>
  <div class="chat-list-container bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Header -->
    <div class="chat-list-header bg-purple-600 text-white p-4">
      <h2 class="font-bold text-xl">Conversations</h2>
    </div>

    <!-- Search -->
    <div class="p-3 border-b">
      <div class="relative">
        <input 
          type="text" 
          v-model="searchQuery" 
          placeholder="Rechercher..." 
          class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
        />
        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
      </div>
    </div>

    <!-- Contact List -->
    <div class="chat-contacts overflow-y-auto" style="max-height: 400px;">
      <div v-if="loading" class="flex justify-center items-center py-8">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-700"></div>
      </div>
      
      <div v-else-if="filteredContacts.length === 0" class="text-center text-gray-500 py-8">
        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        <p>Aucun contact disponible</p>
      </div>
      
      <div v-else>
        <div 
          v-for="contact in filteredContacts" 
          :key="contact.id"
          @click="selectContact(contact)"
          class="chat-contact p-3 border-b hover:bg-purple-50 cursor-pointer transition-colors duration-200"
          :class="{'bg-purple-100': selectedContactId === contact.id}"
        >
          <div class="flex items-center">
            <div class="w-10 h-10 rounded-full bg-purple-300 flex items-center justify-center mr-3">
              <span class="text-purple-700 font-bold">{{ getInitials(contact.name) }}</span>
            </div>
            <div class="flex-grow">
              <h3 class="font-medium text-gray-800">{{ contact.name }}</h3>
              <p class="text-sm text-gray-500 truncate">{{ contact.lastMessage || 'Commencer une conversation' }}</p>
            </div>
            <div v-if="contact.unreadCount" class="bg-purple-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
              {{ contact.unreadCount }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import chatApiService from './chatApiService';

export default {
  name: 'ChatList',
  props: {
    currentUserId: {
      type: [String, Number],
      required: true
    },
    currentUserRole: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      contacts: [],
      loading: true,
      searchQuery: '',
      selectedContactId: null
    };
  },
  computed: {
    filteredContacts() {
      if (!this.searchQuery) return this.contacts;
      
      const query = this.searchQuery.toLowerCase();
      return this.contacts.filter(contact => 
        contact.name.toLowerCase().includes(query)
      );
    }
  },
  mounted() {
    this.loadContacts();
  },
  methods: {
    async loadContacts() {
      this.loading = true;
      try {
        const response = await chatApiService.getContacts(
          this.currentUserId,
          this.currentUserRole
        );
        
        if (response.data.success) {
          this.contacts = response.data.contacts;
        }
      } catch (error) {
        console.error('Error loading contacts:', error);
      } finally {
        this.loading = false;
      }
    },
    
    selectContact(contact) {
      this.selectedContactId = contact.id;
      this.$emit('select-contact', contact);
    },
    
    getInitials(name) {
      if (!name) return '';
      
      return name
        .split(' ')
        .map(word => word[0])
        .join('')
        .toUpperCase();
    }
  }
};
</script>

<style scoped>
.chat-list-container {
  width: 100%;
  height: 100%;
}

.chat-contacts {
  max-height: calc(100% - 120px);
}
</style>
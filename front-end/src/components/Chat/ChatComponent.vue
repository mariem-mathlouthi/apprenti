<template>
  <div class="chat-container bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Chat Header -->
    <div class="chat-header bg-purple-600 text-white p-4 flex justify-between items-center">
      <div class="flex items-center">
        <div class="w-10 h-10 rounded-full bg-purple-300 flex items-center justify-center mr-3">
          <span class="text-purple-700 font-bold">{{ getInitials(chatPartner.name) }}</span>
        </div>
        <div>
          <h3 class="font-semibold">{{ chatPartner.name }}</h3>
          <p class="text-xs text-purple-200">{{ chatPartner.status || (chatPartner.role === 'tutor' ? 'Tuteur' : 'Étudiant') }}</p>
        </div>
      </div>
      <button @click="$emit('close')" class="text-white hover:text-purple-200">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>

    <!-- Chat Messages -->
    <div class="chat-messages p-4 h-96 overflow-y-auto" ref="messageContainer">
      <div v-if="loading" class="flex justify-center items-center h-full">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-700"></div>
      </div>
      <div v-else-if="messages.length === 0" class="flex flex-col items-center justify-center h-full text-gray-500">
        <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
        </svg>
        <p>Aucun message. Commencez la conversation!</p>
      </div>
      <template v-else>
        <div v-for="(message, index) in messages" :key="index" 
             :class="[message.sender_type === currentUserRole ? 'receiver' : 'sender', 'message-bubble mb-4']">
          <div :class="[message.sender_type === currentUserRole  ? 'bg-gray-200 text-gray-800' : 'bg-purple-600 text-white', 'rounded-lg p-3 max-w-xs md:max-w-md inline-block']">
            <p>{{ message.content }}</p>
            <span class="text-xs block mt-1 opacity-75">{{ formatTime(message.timestamp) }}</span>
          </div>
        </div>
      </template>
    </div>

    <!-- Chat Input -->
    <div class="chat-input border-t p-4">
      <form @submit.prevent="sendMessage" class="flex">
        <input 
          v-model="newMessage" 
          type="text" 
          placeholder="Tapez votre message..." 
          class="flex-grow border rounded-l-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500"
        />
        <button 
          type="submit" 
          class="bg-purple-600 text-white px-4 py-2 rounded-r-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500"
          :disabled="!newMessage.trim() || sending"
        >
          <svg v-if="!sending" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
          </svg>
          <div v-else class="w-5 h-5 border-t-2 border-white rounded-full animate-spin"></div>
        </button>
      </form>
    </div>
  </div>
</template>

<script>
import chatApiService from './chatApiService';

export default {
  name: 'ChatComponent',
  props: {
    chatPartner: {
      type: Object,
      required: true
    },
    currentUserId: {
      type: [String, Number],
      required: true
    },
    currentUserRole: {
      type: String,
      required: true
    },
    channelName: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      messages: [],
      newMessage: '',
      pusher: null,
      channel: null,
      loading: true,
      sending: false
    };
  },
  mounted() {
    this.initializePusher();
    this.loadChatHistory();
  },
  beforeUnmount() {
    if (this.channel) {
      this.channel.unbind_all();
      this.pusher.unsubscribe(this.channelName);
    }
  },
  watch: {
    messages() {
      this.$nextTick(() => {
        this.scrollToBottom();
      });
    },
    chatPartner() {
      // Reload chat history when chat partner changes
      this.loading = true;
      this.messages = [];
      this.loadChatHistory();
      
      // Reinitialize Pusher with new channel
      if (this.channel) {
        this.channel.unbind_all();
        this.pusher.unsubscribe(this.channelName);
      }
      this.initializePusher();
    }
  },
  methods: {
    initializePusher() {
      const { pusher, channel } = chatApiService.initializePusher(
        this.currentUserId, 
        this.currentUserRole,
        (message) => {
          // Only add the message if it's not from the current user
          // This prevents duplicate messages since we already add them optimistically
          if (message.sender_type !== this.currentUserId) {
            this.messages.push({
              content: message.message,
              senderId: message.senderId,
              timestamp: new Date(message.timestamp)
            });
            
            // Emit notification event if the chat is not currently focused
            if (!document.hasFocus()) {
              this.$emit('new-message', {
                title: `Nouveau message de ${this.chatPartner.name}`,
                message: message.message,
                timestamp: message.timestamp
              });
            }
          }
        }
      );
      
      this.pusher = pusher;
      this.channel = channel;
    },
    
    async loadChatHistory() {
      this.loading = true;
      try {
        const response = await chatApiService.getChatHistory(
          this.currentUserId,
          this.currentUserRole,
          this.chatPartner.id,
          this.chatPartner.role
        );
        
        if (response.data.success) {
          this.messages = response.data.messages.map(msg => ({
            content: msg.message,
            senderId: msg.sender_id,
            sender_type: msg.sender_type,
            timestamp: new Date(msg.created_at)
          }));
          console.log("sender::", this.messages)
        }
      } catch (error) {
        console.error('Error loading chat history:', error);
        this.$emit('error', 'Erreur lors du chargement de l\'historique des messages.');
      } finally {
        this.loading = false;
      }
    },
    
    async sendMessage() {
      if (!this.newMessage.trim() || this.sending) return;
      
      const messageContent = this.newMessage.trim();
      this.sending = true;
      
      const messageData = {
        content: messageContent,
        senderId: this.currentUserId,
        senderRole: this.currentUserRole,
        receiverId: this.chatPartner.id,
        receiverRole: this.chatPartner.role,
        timestamp: new Date()
      };
      
      // Optimistically add message to UI
      this.messages.push({
        content: messageContent,
        senderId: this.currentUserId,
        timestamp: new Date()
      });
      
      // Clear input field
      this.newMessage = '';
      
      try {
        // Send message to server using our improved API service
        const response = await chatApiService.sendMessage(messageData);
        
        if (!response.data.success) {
          console.error('Failed to send message:', response.data.message);
          this.$emit('error', 'Erreur lors de l\'envoi du message.');
          
          // Remove the optimistically added message if sending failed
          this.messages.pop();
        }
      } catch (error) {
        console.error('Error sending message:', error);
        this.$emit('error', 'Erreur lors de l\'envoi du message. Veuillez réessayer.');
        
        // Remove the optimistically added message if sending failed
        this.messages.pop();
      } finally {
        this.sending = false;
      }
    },
    
    scrollToBottom() {
      const container = this.$refs.messageContainer;
      if (container) {
        container.scrollTop = container.scrollHeight;
      }
    },
    
    formatTime(timestamp) {
      if (!timestamp) return '';
      
      const date = new Date(timestamp);
      return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
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
.chat-container {
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.chat-messages {
  flex-grow: 1;
}

.message-bubble.sender {
  display: flex;
  justify-content: flex-end;
}

.message-bubble.receiver {
  display: flex;
  justify-content: flex-start;
}
</style>

<style scoped>
.chat-container {
  display: flex;
  flex-direction: column;
  height: 100%;
  max-height: 600px;
}

.chat-messages {
  flex-grow: 1;
  overflow-y: auto;
  scroll-behavior: smooth;
}

.message-bubble.sender {
  text-align: right;
}

.message-bubble.receiver {
  text-align: left;
}
</style>
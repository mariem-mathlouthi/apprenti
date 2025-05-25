<template>
  <div class="chat-container flex flex-col bg-white rounded-xl shadow-lg border border-gray-100 transition-all duration-300 hover:shadow-xl h-full" style="height: calc(100vh - 100px);"> <!-- Adjust 100px based on your header/footer height -->
    <!-- Chat Header -->
    <div class="chat-header bg-white border-b border-gray-100 p-4 flex justify-between items-center sticky top-0 z-10 shadow-sm">
      <div class="flex items-center space-x-3">
        <div class="relative">
          <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center shadow-lg transform transition-transform duration-300 hover:scale-105">
            <span class="text-white font-semibold text-sm">{{ getInitials(chatPartner.name) }}</span>
          </div>
          <!-- Removed green circle indicator -->
        </div>
        <div>
          <h3 class="font-semibold text-gray-900">{{ chatPartner.name }}</h3>
          <p class="text-xs text-gray-500 font-medium">{{ chatPartner.status || (chatPartner.role === 'tutor' ? 'Tuteur' : 'Étudiant') }}</p>
        </div>
      </div>
      <button @click="$emit('close')" class="p-2 rounded-full hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500">
        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>

    <!-- Chat Messages -->
    <div class="chat-messages flex-grow px-4 py-3 space-y-2 overflow-y-auto bg-gray-50 bg-opacity-80 h-0" 
         style="background-image: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgdmlld0JveD0iMCAwIDYwIDYwIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiIG9wYWNpdHk9Ii4xIj48Y2lyY2xlIGN4PSIzMCIgY3k9IjMwIiByPSIxIiBmaWxsPSIjOTk5Ii8+PC9nPjwvc3ZnPg==');"
         ref="messageContainer">
      <div v-if="loading" class="flex justify-center items-center h-full">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-t-2 border-blue-600"></div>
      </div>
      <div v-else-if="messages.length === 0" class="flex flex-col items-center justify-center h-full text-gray-400">
        <div class="bg-blue-100 rounded-full p-6 shadow-sm mb-4 border border-blue-200">
          <svg class="w-12 h-12 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
          </svg>
        </div>
        <p class="text-sm">Aucun message. Commencez la conversation!</p>
      </div>
      <template v-else>
        <template v-for="(message, index) in messages" :key="index">
          <div v-if="index === firstUnreadMessageIndex && firstUnreadMessageIndex !== -1" class="unread-separator my-3">
            <hr class="border-t border-red-500">
            <p class="text-center text-xs text-red-500 font-medium py-1">Messages non lus</p>
            <hr class="border-t border-red-500">
          </div>
          <div :class="[message.sender_type === currentUserRole ? 'receiver' : 'sender', 'message-bubble mb-2']">
            <div :class="[message.sender_type === currentUserRole ? 'bg-blue-600 text-white' : 'bg-white text-gray-800 border border-gray-200', 'rounded-lg py-2 px-3 max-w-xs md:max-w-sm inline-block shadow-sm text-sm']">
              <div v-if="message.file" class="mb-2">
                <div v-if="message.file.type.startsWith('image/')" class="mb-2">
                  <img :src="message.file.url" :alt="message.file.name" class="max-w-full rounded-lg" />
                </div>
                <div v-else-if="message.file.type === 'application/pdf'" class="flex items-center space-x-2 p-2 bg-opacity-10 bg-gray-500 rounded">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                  <a :href="message.file.url" target="_blank" class="underline hover:text-opacity-75">{{ message.file.name }}</a>
                </div>
              </div>
              <p v-if="message.content">{{ message.content }}</p>
              <span class="text-xs block mt-1 opacity-75">{{ formatTime(message.timestamp) }}</span>
          </div>
          </div>
        </template>
      </template>
    </div>

    <!-- Chat Input -->
    <div class="chat-input border-t border-gray-100 p-3">
      <form @submit.prevent="sendMessage" class="flex flex-col space-y-2">
        <div class="flex items-center space-x-2">
          <input 
            v-model="newMessage" 
            type="text" 
            placeholder="Tapez votre message..." 
            class="flex-grow border border-gray-200 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
          />
          <input
            type="file"
            ref="fileInput"
            @change="handleFileSelect"
            accept="image/*,.pdf"
            class="hidden"
          />
          <button
            type="button"
            @click="$refs.fileInput.click()"
            class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
          >
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
            </svg>
          </button>
          <button 
            type="submit" 
            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors"
            :disabled="(!newMessage.trim() && !selectedFile) || sending"
          >
            <svg v-if="!sending" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
            </svg>
            <div v-else class="w-5 h-5 border-t-2 border-white rounded-full animate-spin"></div>
          </button>
        </div>
        <div v-if="selectedFile" class="flex items-center justify-between bg-gray-50 p-2 rounded-lg">
          <div class="flex items-center space-x-2">
            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <span class="text-sm text-gray-600 truncate">{{ selectedFile.name }}</span>
          </div>
          <button 
            @click="removeSelectedFile" 
            type="button"
            class="text-red-500 hover:text-red-600 focus:outline-none"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
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
      sending: false,
      firstUnreadMessageIndex: -1, // Initialize with -1 (no unread messages)
      unreadMessagesCount: 0, // To store the count of unread messages
      selectedFile: null, // To store the selected file for upload
      allowedFileTypes: ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'] // Allowed file types
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
        (pusherData) => {

          // pusherData is the object like: {"id":39,"etudiant_id":2,"tuteur_id":1,"message":"bhi", ... ,"sender_type":"tuteur"}
          if (pusherData && pusherData.sender_type && pusherData.sender_type !== this.currentUserRole) {
            // Determine senderId based on sender_type
            let senderId;
            if (pusherData.sender_type === 'etudiant') {
              senderId = pusherData.etudiant_id;
            } else if (pusherData.sender_type === 'tuteur') {
              senderId = pusherData.tuteur_id;
            }

            this.messages.push({
              id: pusherData.id, // Use the ID from Pusher data
              content: pusherData.message,
              senderId: senderId,
              sender_type: pusherData.sender_type,
              timestamp: new Date(pusherData.created_at),
              read_at: pusherData.read_at ? new Date(pusherData.read_at) : null
            });

            // If the received message is for the currently active chat, mark it as read immediately
            if (this.chatPartner && 
                ((pusherData.sender_type === 'etudiant' && pusherData.etudiant_id === this.chatPartner.id) || 
                 (pusherData.sender_type === 'tuteur' && pusherData.tuteur_id === this.chatPartner.id))) {
                this.markMessagesAsRead(); // This will also reset unreadMessagesCount if applicable
            } else {
                // If it's for another chat, increment unread count for that chat (if needed elsewhere or for notifications)
                // For now, just focus on the current chat. Future enhancement: update global unread counts.
            }

            if (!document.hasFocus()) {
              this.$emit('new-message', {
                title: `Nouveau message de ${pusherData.sender_type === 'tuteur' ? 'Tuteur' : 'Étudiant'}`, // Or fetch sender name if available
                message: pusherData.message,
                timestamp: pusherData.created_at
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
        // Fetch unread messages count
        const token = JSON.parse(sessionStorage.getItem('token')); // Or however you store/retrieve the token
        const unreadCountResponse = await chatApiService.getUnreadCount(token, this.currentUserRole);
        if (unreadCountResponse.data.success && unreadCountResponse.data.unread_by_user) {
          const partnerUnreadInfo = unreadCountResponse.data.unread_by_user.find(
            u => u.user && u.user.id === this.chatPartner.id
          );
          this.unreadMessagesCount = partnerUnreadInfo ? partnerUnreadInfo.unread_count : 0;
        } else {
          console.error('Failed to fetch unread count or unread_by_user is missing:', unreadCountResponse.data.message);
          this.unreadMessagesCount = 0; // Default to 0 on error
        }

        const response = await chatApiService.getChatHistory(
          this.currentUserId,
          this.currentUserRole,
          this.chatPartner.id,
          this.chatPartner.role
        );
        
        if (response.data.success) {
          this.messages = response.data.messages.map(msg => ({
            id: msg.id,
            tuteur_id: msg.tuteur_id,
            etudiant_id: msg.etudiant_id,
            content: msg.message, 
            sender_type: msg.sender_type, 
            timestamp: new Date(msg.created_at),
            read_at: msg.read_at ? new Date(msg.read_at) : null 
          }));
          console.log("sender::", this.messages)
          this.findFirstUnreadMessage();
        }
      } catch (error) {
        console.error('Error loading chat history or unread count:', error);
        this.$emit('error', 'Erreur lors du chargement des messages.');
      } finally {
        this.loading = false;
        this.scrollToBottom();
      }
    },
    
    handleFileSelect(event) {
      const file = event.target.files[0];
      if (file) {
        if (this.allowedFileTypes.includes(file.type)) {
          if (file.size <= 10 * 1024 * 1024) { // 10MB max size
            this.selectedFile = file;
          } else {
            this.$emit('error', 'Le fichier est trop volumineux. Taille maximale : 10MB');
            event.target.value = '';
          }
        } else {
          this.$emit('error', 'Type de fichier non autorisé. Formats acceptés : JPG, PNG, GIF, PDF');
          event.target.value = '';
        }
      }
    },

    removeSelectedFile() {
      this.selectedFile = null;
      if (this.$refs.fileInput) {
        this.$refs.fileInput.value = '';
      }
    },

    async sendMessage() {
      if ((!this.newMessage.trim() && !this.selectedFile) || this.sending) return;
      
      this.sending = true;
      
      try {
        let messageData = {
          senderId: this.currentUserId,
          senderRole: this.currentUserRole,
          receiverId: this.chatPartner.id,
          receiverRole: this.chatPartner.role,
          timestamp: new Date()
        };

        if (this.selectedFile) {
          const formData = new FormData();
          formData.append('file', this.selectedFile);
          Object.keys(messageData).forEach(key => {
            formData.append(key, messageData[key]);
          });
          if (this.newMessage.trim()) {
            formData.append('content', this.newMessage.trim());
          }
          messageData = formData;
        } else {
          messageData.content = this.newMessage.trim();
        }
        
        // Optimistically add message to UI
        const previewMessage = {
          content: this.newMessage.trim(),
          senderId: this.currentUserId,
          sender_type: this.currentUserRole,
          timestamp: new Date(),
          file: this.selectedFile ? {
            name: this.selectedFile.name,
            type: this.selectedFile.type
          } : null
        };
        this.messages.push(previewMessage);
        
        // Clear input fields
        this.newMessage = '';
        this.removeSelectedFile();
        this.scrollToBottom();
        
        const response = await chatApiService.sendMessage(messageData);
        
        if (!response.data.success) {
          console.error('Failed to send message:', response.data.message);
          this.$emit('error', 'Erreur lors de l\'envoi du message.');
          this.messages.pop();
        }
      } catch (error) {
        console.error('Error sending message:', error);
        this.$emit('error', 'Erreur lors de l\'envoi du message. Veuillez réessayer.');
        this.messages.pop();
      } finally {
        this.sending = false;
      }
    },
    
    scrollToBottom() {
      this.$nextTick(() => {
        const container = this.$refs.messageContainer;
        if (container) {
          container.scrollTop = container.scrollHeight;
        }
      });
    },
    
    formatTime(timestamp) {
      if (!timestamp) return '';
      
      const date = new Date(timestamp);
      return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    },
    
    findFirstUnreadMessage() {
      if (this.unreadMessagesCount > 0 && this.messages.length > 0) {
        const unreadBlockStartIndex = this.messages.length - this.unreadMessagesCount;
        
        if (unreadBlockStartIndex >= 0 && unreadBlockStartIndex < this.messages.length) {
            this.firstUnreadMessageIndex = unreadBlockStartIndex;
        } else {
            this.firstUnreadMessageIndex = -1; 
        }

      } else {
        this.firstUnreadMessageIndex = -1; 
      }

      if (this.firstUnreadMessageIndex !== -1) {
        this.$nextTick(() => {
          const unreadSeparator = this.$refs.messageContainer.querySelector('.unread-separator');
          if (unreadSeparator) {
            unreadSeparator.scrollIntoView({ behavior: 'smooth', block: 'center' });
          } else {
            const messageElements = this.$refs.messageContainer.querySelectorAll('.message-bubble');
            if (messageElements[this.firstUnreadMessageIndex]) {
              messageElements[this.firstUnreadMessageIndex].scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
          }
          
          this.markMessagesAsRead();
        });
      }
    },
    
    async markMessagesAsRead() {
      if (this.unreadMessagesCount <= 0 || this.firstUnreadMessageIndex === -1) {
        return; // No unread messages to mark
      }
      
      try {
        // Get the token from localStorage
        const token = JSON.parse(sessionStorage.getItem('token'));
        if (!token) {
          console.error('Authentication token not found for marking messages as read.');
          return;
        }
        
        // Get the IDs of unread messages
        const unreadMessageIds = [];
        for (let i = this.firstUnreadMessageIndex; i < this.messages.length; i++) {
          // Only include messages that don't have a read_at value
          console.log('message::', this.messages[i].read_at)
          
          if (this.messages[i] && this.messages[i].id && !this.messages[i].read_at) {
            unreadMessageIds.push(this.messages[i].id);
          }
        }
        
        if (unreadMessageIds.length === 0) {
          console.log('No unread message IDs found to mark as read.');
          return;
        }
        
        // Mark messages as read one at a time
        for (const messageId of unreadMessageIds) {
          const response = await chatApiService.markMessagesAsRead(token, messageId);
          console.log('mark read:: ', response)
          if (response.data && response.data.success) {
            console.log('Message marked as read successfully:', messageId);
            
            // Update the local message to reflect it's been read
            const messageIndex = this.messages.findIndex(msg => msg.id === messageId);
            if (messageIndex !== -1) {
              this.messages[messageIndex].read_at = new Date();
            }
          } else {
            console.error('Failed to mark message as read:', messageId, response.data.message);
          }
        }
        
        // Reset unread count after processing all messages
        this.unreadMessagesCount = 0;
        // Emit an event to notify parent components that messages have been read
        this.$emit('messages-read', this.chatPartner.id);
        
      } catch (error) {
        console.error('Error in markMessagesAsRead:', error);
      }
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
  scroll-behavior: smooth;
}

.message-bubble.sender {
  display: flex;
  justify-content: flex-start;
}

.message-bubble.receiver {
  display: flex;
  justify-content: flex-end;
}
</style>
<template>
  <div class="gemini-chatbot-container">
    <!-- Welcome Notification -->
    <transition name="slide-in-notification" appear>
      <div
        v-if="showWelcomeNotification && !isOpen && !hasInteracted"
        class="welcome-notification"
        @click="dismissNotification"
      >
        <div class="notification-content">
          <div class="notification-icon">
            <i class="fas fa-robot"></i>
          </div>
          <div class="notification-text">
            <h4>Assistant IA disponible!</h4>
            <p>Cliquez pour obtenir de l'aide</p>
          </div>
          <button class="notification-close" @click.stop="dismissNotification">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <div class="notification-arrow"></div>
      </div>
    </transition>

    <!-- Floating Chat Button -->
    <transition name="bounce" appear>
      <div
        v-if="!isOpen"
        @click="toggleChat"
        @mouseenter="onButtonHover"
        @mouseleave="onButtonLeave"
        class="chat-button"
        :class="{
          'pulse': hasNewMessage,
          'shake': shouldShake,
          'glow': isGlowing,
          'breathing': isBreathing,
          'hover-float': isHovering
        }"
      >
        <!-- Floating Particles -->
        <div class="floating-particles">
          <div v-for="n in 6" :key="n" class="particle" :style="getParticleStyle(n)"></div>
        </div>

        <!-- Magic Circle -->
        <div class="magic-circle" :class="{ 'active': showMagicCircle }"></div>

        <!-- Button Content -->
        <div class="chat-icon" :class="{ 'rotate': isRotating }">
          <i class="fas fa-robot"></i>
        </div>

        <!-- Enhanced Tooltip -->
        <div class="chat-tooltip" :class="{ 'show': showTooltip }">
          <div class="tooltip-content">
            <span class="tooltip-text">Assistant IA - Cliquez pour discuter</span>
            <div class="tooltip-sparkles">
              <span v-for="n in 3" :key="n" class="sparkle" :style="getSparkleStyle(n)">✨</span>
            </div>
          </div>
        </div>

        <!-- Multiple Ripple Effects -->
        <div v-for="(ripple, index) in activeRipples" :key="index"
             class="button-ripple"
             :style="ripple.style"></div>

        <!-- Orbit Rings -->
        <div class="orbit-ring ring-1" :class="{ 'active': showOrbitRings }"></div>
        <div class="orbit-ring ring-2" :class="{ 'active': showOrbitRings }"></div>
        <div class="orbit-ring ring-3" :class="{ 'active': showOrbitRings }"></div>
      </div>
    </transition>

    <!-- Chat Window -->
    <transition name="slide-up" appear>
      <div v-if="isOpen" class="chat-window">
        <!-- Header -->
        <div class="chat-header">
          <!-- Animated Background -->
          <div class="header-bg-animation">
            <div v-for="n in 20" :key="n" class="bg-particle" :style="getBgParticleStyle(n)"></div>
          </div>

          <div class="header-content">
            <div class="ai-avatar" :class="{ 'pulse-avatar': isTyping }">
              <i class="fas fa-robot"></i>
              <div class="avatar-glow"></div>
            </div>
            <div class="header-text">
              <h4>Assistant IA</h4>
              <span class="status" :class="{
                'online': isOnline && networkStatus === 'online',
                'offline': !isOnline || networkStatus === 'offline',
                'checking': networkStatus === 'checking',
                'reconnecting': isReconnecting
              }">
                <span class="status-dot"></span>
                <span v-if="networkStatus === 'checking'">Vérification...</span>
                <span v-else-if="isReconnecting">Reconnexion...</span>
                <span v-else-if="pendingOfflineMessages > 0 && isOnline">
                  En ligne ({{ pendingOfflineMessages }} en attente)
                </span>
                <span v-else>{{ isOnline ? 'En ligne' : 'Hors ligne' }}</span>
                <div v-if="isTyping" class="typing-dots">
                  <span></span><span></span><span></span>
                </div>
              </span>
            </div>
          </div>
          <button @click="toggleChat" class="close-btn" @mouseenter="onCloseHover">
            <i class="fas fa-times"></i>
            <div class="close-ripple" v-if="showCloseRipple"></div>
          </button>
        </div>

        <!-- Offline Indicator -->
        <div v-if="showOfflineIndicator || (!isOnline && networkStatus === 'offline')" class="offline-banner">
          <div class="offline-content">
            <i class="fas fa-wifi-slash"></i>
            <span v-if="pendingOfflineMessages > 0">
              {{ pendingOfflineMessages }} message(s) en attente - Sera traité quand la connexion sera rétablie
            </span>
            <span v-else>
              Mode hors ligne - Vos messages seront traités dès la reconnexion
            </span>
          </div>
        </div>

        <!-- Messages Container -->
        <div class="messages-container" ref="messagesContainer">
          <div class="welcome-message" v-if="messages.length === 0">
            <div class="welcome-avatar">
              <i class="fas fa-robot"></i>
            </div>
            <h5>Bonjour! Je suis votre Assistant IA</h5>
            <p>Je suis là pour vous aider avec vos questions sur Apprenti+, les stages, les cours, et bien plus encore!</p>

            <!-- Quick Suggestions -->
            <div class="quick-suggestions">
              <h6>Questions rapides:</h6>
              <div class="suggestions-grid">
                <button 
                  v-for="(suggestion, index) in quickSuggestions" 
                  :key="index"
                  @click="sendQuickMessage(suggestion)"
                  class="suggestion-btn"
                >
                  {{ suggestion }}
                </button>
              </div>
            </div>
          </div>

          <!-- Chat Messages -->
          <transition-group name="message-slide" tag="div">
            <div
              v-for="(message, index) in messages"
              :key="message.id || index"
              class="message"
              :class="{
                'user-message': message.isUser,
                'ai-message': !message.isUser,
                'message-appear': message.isNew,
                'offline-message': message.isOffline,
                'success-message': message.isSuccess
              }"
              @animationend="onMessageAnimationEnd(message)"
            >
            <div class="message-avatar" v-if="!message.isUser">
              <i class="fas fa-robot"></i>
            </div>
            <div class="message-content">
              <div class="message-bubble">
                <p v-html="formatMessage(message.text)"></p>
                <span class="message-time">{{ formatTime(message.timestamp) }}</span>
              </div>
            </div>
            <div class="message-avatar user-avatar" v-if="message.isUser">
              <i class="fas fa-user"></i>
            </div>
            </div>
          </transition-group>

          <!-- Typing Indicator -->
          <div v-if="isTyping" class="message ai-message">
            <div class="message-avatar">
              <i class="fas fa-robot"></i>
            </div>
            <div class="message-content">
              <div class="typing-indicator">
                <span></span>
                <span></span>
                <span></span>
              </div>
            </div>
          </div>
        </div>

        <!-- Input Area -->
        <div class="input-area">
          <!-- Floating Input Particles -->
          <div class="input-particles">
            <div v-for="n in 8" :key="n" class="input-particle" :style="getInputParticleStyle(n)"></div>
          </div>

          <div class="input-container" :class="{ 'focused': isInputFocused, 'typing': isUserTyping }">
            <div class="input-glow" :class="{ 'active': isInputFocused }"></div>

            <input
              v-model="currentMessage"
              @keypress.enter="sendMessage"
              @input="handleInput"
              @focus="onInputFocus"
              @blur="onInputBlur"
              :placeholder="isOnline ? 'Tapez votre message...' : 'Mode hors ligne - Votre message sera envoyé plus tard'"
              class="message-input"
              :disabled="isTyping"
              maxlength="2000"
              ref="messageInput"
            />

            <!-- Animated Send Button -->
            <button
              @click="sendMessage"
              @mouseenter="onSendHover"
              @mouseleave="onSendLeave"
              class="send-btn"
              :class="{
                'ready': currentMessage.trim() && !isTyping,
                'sending': isSending,
                'hover': isSendHovering,
                'offline': !isOnline
              }"
              :disabled="!currentMessage.trim() || isTyping"
            >
              <div class="send-icon-container">
                <i class="fas fa-paper-plane" :class="{ 'fly': isSending }"></i>
              </div>
              <div class="send-ripple" v-if="showSendRipple"></div>
            </button>
          </div>
          <div class="input-footer">
            <span class="char-count">{{ currentMessage.length }}/2000</span>
            <span class="powered-by">Propulsé par Gemini IA</span>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import geminiService from '../services/geminiService.js';

export default {
  name: 'GeminiChatbot',
  data() {
    return {
      isOpen: false,
      isOnline: true,
      networkStatus: 'online', // 'online', 'offline', 'checking'
      isTyping: false,
      hasNewMessage: false,
      hasInteracted: false,
      showWelcomeNotification: true,
      shouldShake: false,
      isGlowing: false,
      isBreathing: true,
      isHovering: false,
      isRotating: false,
      showMagicCircle: false,
      showOrbitRings: false,
      showTooltip: false,
      showCloseRipple: false,
      isInputFocused: false,
      isUserTyping: false,
      isSending: false,
      isSendHovering: false,
      showSendRipple: false,
      activeRipples: [],
      currentMessage: '',
      messages: [],
      messageIdCounter: 0,
      pendingOfflineMessages: 0,
      showOfflineIndicator: false,
      isReconnecting: false,
      quickSuggestions: [
        "Comment postuler pour un stage?",
        "Quels cours sont disponibles?",
        "Comment contacter mon tuteur?",
        "Comment télécharger mon CV?",
        "Quelles sont les fonctionnalités de la plateforme?",
        "Comment planifier un rendez-vous?",
        "Comment suivre mes candidatures?",
        "Quels documents dois-je fournir?"
      ]
    };
  },
  mounted() {
    this.checkServiceHealth();
    this.loadQuickSuggestions();
    this.setupNetworkListeners();
    this.updatePendingMessagesCount();

    // Welcome notification sequence
    setTimeout(() => {
      this.showWelcomeNotification = true;
    }, 1000);

    // Auto-dismiss notification after 8 seconds
    setTimeout(() => {
      if (!this.hasInteracted) {
        this.showWelcomeNotification = false;
      }
    }, 9000);

    // Add periodic attention-grabbing animations
    this.startPeriodicAnimations();

    // Periodic health checks
    this.startPeriodicHealthChecks();
  },

  beforeUnmount() {
    this.removeNetworkListeners();
  },
  methods: {
    toggleChat() {
      this.isOpen = !this.isOpen;
      this.hasInteracted = true;
      this.showWelcomeNotification = false;

      if (this.isOpen) {
        this.triggerRippleEffect();
        this.$nextTick(() => {
          this.scrollToBottom();
          if (this.$refs.messagesContainer) {
            this.$refs.messagesContainer.querySelector('.message-input')?.focus();
          }
        });
      }
    },

    dismissNotification() {
      this.showWelcomeNotification = false;
      this.hasInteracted = true;
    },

    startPeriodicAnimations() {
      // Glow effect every 15 seconds if not interacted
      setInterval(() => {
        if (!this.hasInteracted && !this.isOpen) {
          this.isGlowing = true;
          setTimeout(() => {
            this.isGlowing = false;
          }, 3000);
        }
      }, 15000);

      // Shake effect every 30 seconds if not interacted
      setInterval(() => {
        if (!this.hasInteracted && !this.isOpen) {
          this.shouldShake = true;
          setTimeout(() => {
            this.shouldShake = false;
          }, 1000);
        }
      }, 30000);
    },

    triggerRippleEffect() {
      const ripple = {
        style: {
          animationDelay: '0s'
        }
      };
      this.activeRipples.push(ripple);
      setTimeout(() => {
        this.activeRipples.shift();
      }, 600);
    },

    // Button hover effects
    onButtonHover() {
      this.isHovering = true;
      this.showTooltip = true;
      this.showMagicCircle = true;
      this.showOrbitRings = true;
      this.isRotating = true;
    },

    onButtonLeave() {
      this.isHovering = false;
      this.showTooltip = false;
      this.showMagicCircle = false;
      this.showOrbitRings = false;
      this.isRotating = false;
    },

    // Input focus effects
    onInputFocus() {
      this.isInputFocused = true;
    },

    onInputBlur() {
      this.isInputFocused = false;
    },

    // Send button effects
    onSendHover() {
      this.isSendHovering = true;
    },

    onSendLeave() {
      this.isSendHovering = false;
    },

    // Close button effects
    onCloseHover() {
      this.showCloseRipple = true;
      setTimeout(() => {
        this.showCloseRipple = false;
      }, 300);
    },

    // Message animation end
    onMessageAnimationEnd(message) {
      if (message.isNew) {
        message.isNew = false;
      }
    },

    // Style generators for particles and effects
    getParticleStyle(index) {
      const angle = (index * 60) + Math.random() * 30;
      const distance = 80 + Math.random() * 20;
      const x = Math.cos(angle * Math.PI / 180) * distance;
      const y = Math.sin(angle * Math.PI / 180) * distance;

      return {
        left: `${x}px`,
        top: `${y}px`,
        animationDelay: `${index * 0.2}s`,
        animationDuration: `${3 + Math.random() * 2}s`
      };
    },

    getSparkleStyle(index) {
      return {
        animationDelay: `${index * 0.3}s`,
        left: `${index * 15}px`
      };
    },

    getBgParticleStyle(index) {
      return {
        left: `${Math.random() * 100}%`,
        animationDelay: `${index * 0.1}s`,
        animationDuration: `${4 + Math.random() * 3}s`
      };
    },

    getInputParticleStyle(index) {
      return {
        left: `${Math.random() * 100}%`,
        animationDelay: `${index * 0.2}s`,
        animationDuration: `${2 + Math.random() * 2}s`
      };
    },



    async sendMessage() {
      if (!this.currentMessage.trim() || this.isTyping) return;

      const validation = geminiService.validateMessage(this.currentMessage);
      if (!validation.isValid) {
        this.showError(validation.error);
        return;
      }

      // Trigger sending animation
      this.isSending = true;
      this.showSendRipple = true;

      const userMessage = {
        id: this.messageIdCounter++,
        text: this.currentMessage.trim(),
        isUser: true,
        timestamp: new Date(),
        isNew: true
      };

      this.messages.push(userMessage);
      const messageToSend = this.currentMessage.trim();
      this.currentMessage = '';

      this.$nextTick(() => {
        this.scrollToBottom();
      });

      // Handle offline scenario
      if (!this.isOnline || this.networkStatus === 'offline') {
        this.handleOfflineMessage(messageToSend);
        return;
      }

      this.isTyping = true;

      try {
        const conversationHistory = geminiService.formatConversationHistory(this.messages.slice(0, -1));
        const response = await geminiService.sendMessage(messageToSend, conversationHistory);

        if (response.success) {
          const aiMessage = {
            id: this.messageIdCounter++,
            text: response.data.message,
            isUser: false,
            timestamp: new Date(),
            isNew: true
          };
          this.messages.push(aiMessage);
        } else {
          this.showError(response.error);
        }
      } catch (error) {
        // If error occurs, treat as offline and store message
        this.handleOfflineMessage(messageToSend);
      } finally {
        this.isTyping = false;
        this.isSending = false;
        this.showSendRipple = false;
        this.$nextTick(() => {
          this.scrollToBottom();
        });
      }
    },

    handleOfflineMessage(messageToSend) {
      // Store message for later processing
      const conversationHistory = geminiService.formatConversationHistory(this.messages.slice(0, -1));
      geminiService.storeOfflineMessage(messageToSend, conversationHistory);

      // Show offline response
      const offlineResponse = {
        id: this.messageIdCounter++,
        text: `🔄 ${geminiService.getOfflineResponse()}`,
        isUser: false,
        timestamp: new Date(),
        isNew: true,
        isOffline: true
      };

      this.messages.push(offlineResponse);
      this.updatePendingMessagesCount();
      this.showOfflineIndicator = true;

      // Auto-hide offline indicator after 5 seconds
      setTimeout(() => {
        this.showOfflineIndicator = false;
      }, 5000);

      this.isSending = false;
      this.showSendRipple = false;
      this.$nextTick(() => {
        this.scrollToBottom();
      });
    },

    sendQuickMessage(suggestion) {
      this.currentMessage = suggestion;
      this.sendMessage();
    },

    async checkServiceHealth() {
      try {
        this.networkStatus = 'checking';
        const response = await geminiService.enhancedHealthCheck();
        this.isOnline = response.success;
        this.networkStatus = response.networkStatus || (response.success ? 'online' : 'offline');

        // If we're back online and have pending messages, try to process them
        if (this.isOnline && this.pendingOfflineMessages > 0) {
          this.processPendingMessages();
        }
      } catch (error) {
        this.isOnline = false;
        this.networkStatus = 'offline';
      }
    },

    setupNetworkListeners() {
      // Listen for online/offline events
      window.addEventListener('online', this.handleNetworkOnline);
      window.addEventListener('offline', this.handleNetworkOffline);
    },

    removeNetworkListeners() {
      window.removeEventListener('online', this.handleNetworkOnline);
      window.removeEventListener('offline', this.handleNetworkOffline);
    },

    handleNetworkOnline() {
      console.log('Network back online');
      this.networkStatus = 'online';
      this.isReconnecting = true;

      // Recheck service health
      setTimeout(() => {
        this.checkServiceHealth();
        this.isReconnecting = false;
      }, 1000);
    },

    handleNetworkOffline() {
      console.log('Network went offline');
      this.isOnline = false;
      this.networkStatus = 'offline';
      this.showOfflineIndicator = true;
    },

    startPeriodicHealthChecks() {
      // Check health every 30 seconds
      setInterval(() => {
        if (this.networkStatus !== 'offline') {
          this.checkServiceHealth();
        }
      }, 30000);
    },

    async processPendingMessages() {
      if (!this.isOnline) return;

      try {
        const result = await geminiService.processPendingMessages();
        if (result.processedCount > 0) {
          this.showSuccessMessage(`${result.processedCount} message(s) en attente traité(s) avec succès !`);
        }
        this.updatePendingMessagesCount();
      } catch (error) {
        console.error('Failed to process pending messages:', error);
      }
    },

    updatePendingMessagesCount() {
      this.pendingOfflineMessages = geminiService.getOfflineMessages().length;
    },

    showSuccessMessage(message) {
      const successMessage = {
        id: this.messageIdCounter++,
        text: `✅ ${message}`,
        isUser: false,
        timestamp: new Date(),
        isNew: true,
        isSuccess: true
      };
      this.messages.push(successMessage);
      this.$nextTick(() => {
        this.scrollToBottom();
      });
    },

    async loadQuickSuggestions() {
      try {
        const response = await geminiService.getQuickSuggestions();
        if (response.success && response.data.suggestions) {
          this.quickSuggestions = response.data.suggestions;
        }
      } catch (error) {
        console.warn('Échec du chargement des suggestions, utilisation des valeurs par défaut');
      }
    },

    showError(message) {
      const errorMessage = {
        text: `❌ ${message}`,
        isUser: false,
        timestamp: new Date()
      };
      this.messages.push(errorMessage);
      this.$nextTick(() => {
        this.scrollToBottom();
      });
    },

    handleInput() {
      // Trigger typing animation
      this.isUserTyping = true;
      clearTimeout(this.typingTimeout);
      this.typingTimeout = setTimeout(() => {
        this.isUserTyping = false;
      }, 1000);
    },

    scrollToBottom() {
      this.$nextTick(() => {
        const container = this.$refs.messagesContainer;
        if (container) {
          container.scrollTop = container.scrollHeight;
        }
      });
    },

    formatMessage(text) {
      // Basic formatting for AI responses
      return text
        .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
        .replace(/\*(.*?)\*/g, '<em>$1</em>')
        .replace(/\n/g, '<br>');
    },

    formatTime(timestamp) {
      return new Date(timestamp).toLocaleTimeString([], { 
        hour: '2-digit', 
        minute: '2-digit' 
      });
    }
  }
};
</script>

<style scoped>
.gemini-chatbot-container {
  position: fixed;
  bottom: 20px;
  right: 20px;
  z-index: 9999;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Welcome Notification */
.welcome-notification {
  position: absolute;
  bottom: 80px;
  right: 0;
  background: white;
  border-radius: 12px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
  padding: 16px;
  max-width: 280px;
  cursor: pointer;
  border: 1px solid #e2e8f0;
  transition: all 0.3s ease;
}

.welcome-notification:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 35px rgba(0, 0, 0, 0.2);
}

.notification-content {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  position: relative;
}

.notification-icon {
  width: 36px;
  height: 36px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 16px;
  flex-shrink: 0;
}

.notification-text h4 {
  margin: 0 0 4px;
  font-size: 14px;
  font-weight: 600;
  color: #1e293b;
}

.notification-text p {
  margin: 0;
  font-size: 12px;
  color: #64748b;
}

.notification-close {
  position: absolute;
  top: -4px;
  right: -4px;
  background: #f1f5f9;
  border: none;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 10px;
  color: #64748b;
  cursor: pointer;
  transition: all 0.3s ease;
}

.notification-close:hover {
  background: #e2e8f0;
  color: #374151;
}

.notification-arrow {
  position: absolute;
  bottom: -8px;
  right: 24px;
  width: 0;
  height: 0;
  border-left: 8px solid transparent;
  border-right: 8px solid transparent;
  border-top: 8px solid white;
}

.notification-arrow::before {
  content: '';
  position: absolute;
  bottom: 1px;
  left: -8px;
  width: 0;
  height: 0;
  border-left: 8px solid transparent;
  border-right: 8px solid transparent;
  border-top: 8px solid #e2e8f0;
}

/* Floating Chat Button */
.chat-button {
  width: 60px;
  height: 60px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.chat-button:hover {
  transform: translateY(-2px) scale(1.05);
  box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
}

.chat-button.pulse {
  animation: pulse 2s infinite;
}

.chat-button.shake {
  animation: shake 0.8s ease-in-out;
}

.chat-button.glow {
  animation: glow 3s ease-in-out;
}

.chat-button.breathing {
  animation: breathing 4s ease-in-out infinite;
}

.chat-button.hover-float {
  animation: hover-float 0.3s ease-out forwards;
}

/* Floating Particles */
.floating-particles {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 1px;
  height: 1px;
  pointer-events: none;
}

.particle {
  position: absolute;
  width: 4px;
  height: 4px;
  background: rgba(255, 255, 255, 0.8);
  border-radius: 50%;
  animation: float-particle infinite ease-in-out;
}

/* Magic Circle */
.magic-circle {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 80px;
  height: 80px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  transform: translate(-50%, -50%) scale(0);
  transition: all 0.3s ease;
}

.magic-circle.active {
  transform: translate(-50%, -50%) scale(1);
  animation: magic-rotate 3s linear infinite;
}

/* Orbit Rings */
.orbit-ring {
  position: absolute;
  top: 50%;
  left: 50%;
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  transform: translate(-50%, -50%);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.orbit-ring.active {
  opacity: 1;
}

.ring-1 {
  width: 90px;
  height: 90px;
  animation: orbit-1 4s linear infinite;
}

.ring-2 {
  width: 110px;
  height: 110px;
  animation: orbit-2 6s linear infinite reverse;
}

.ring-3 {
  width: 130px;
  height: 130px;
  animation: orbit-3 8s linear infinite;
}

.button-ripple {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0;
  height: 0;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.6);
  transform: translate(-50%, -50%);
  animation: ripple 0.6s ease-out;
}

.chat-icon {
  color: white;
  font-size: 24px;
  transition: transform 0.3s ease;
  position: relative;
  z-index: 2;
}

.chat-icon.rotate {
  animation: icon-rotate 0.5s ease-in-out;
}

.chat-button:hover .chat-icon {
  transform: scale(1.1);
}

/* Enhanced Tooltip */
.chat-tooltip {
  position: absolute;
  right: 70px;
  top: 50%;
  transform: translateY(-50%) scale(0.8);
  background: linear-gradient(135deg, rgba(0, 0, 0, 0.9), rgba(30, 30, 30, 0.9));
  color: white;
  padding: 12px 16px;
  border-radius: 12px;
  font-size: 12px;
  white-space: nowrap;
  opacity: 0;
  pointer-events: none;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.chat-tooltip.show {
  opacity: 1;
  transform: translateY(-50%) scale(1);
}

.tooltip-content {
  position: relative;
}

.tooltip-sparkles {
  position: absolute;
  top: -5px;
  right: -5px;
  pointer-events: none;
}

.sparkle {
  position: absolute;
  font-size: 8px;
  animation: sparkle 2s infinite ease-in-out;
}

/* Chat Window */
.chat-window {
  width: 380px;
  height: 500px;
  background: white;
  border-radius: 16px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  border: 1px solid rgba(0, 0, 0, 0.1);
}

/* Offline Banner */
.offline-banner {
  background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
  color: white;
  padding: 8px 16px;
  font-size: 12px;
  text-align: center;
  animation: slide-down 0.3s ease-out;
}

.offline-content {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.offline-content i {
  font-size: 14px;
}

/* Header */
.chat-header {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 16px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: relative;
  overflow: hidden;
}

/* Animated Background */
.header-bg-animation {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
  overflow: hidden;
}

.bg-particle {
  position: absolute;
  width: 2px;
  height: 2px;
  background: rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  animation: bg-float infinite ease-in-out;
}

.header-content {
  display: flex;
  align-items: center;
  gap: 12px;
}

.ai-avatar {
  width: 40px;
  height: 40px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  position: relative;
  transition: all 0.3s ease;
}

.ai-avatar.pulse-avatar {
  animation: avatar-pulse 1.5s ease-in-out infinite;
}

.avatar-glow {
  position: absolute;
  top: -2px;
  left: -2px;
  right: -2px;
  bottom: -2px;
  border-radius: 50%;
  background: linear-gradient(45deg, rgba(255, 255, 255, 0.3), transparent);
  opacity: 0;
  animation: avatar-glow 2s ease-in-out infinite;
}

.header-text h4 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
}

.status {
  font-size: 12px;
  opacity: 0.9;
  display: flex;
  align-items: center;
  gap: 4px;
}

.status-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  display: inline-block;
}

.status.online .status-dot {
  background: #4ade80;
  animation: status-pulse 2s ease-in-out infinite;
}

.status.offline .status-dot {
  background: #f87171;
  animation: pulse-red 2s infinite;
}

.status.checking .status-dot {
  background: #f59e0b;
  animation: pulse-yellow 1.5s infinite;
}

.status.reconnecting .status-dot {
  background: #10b981;
  animation: pulse-green 1s infinite;
}

.typing-dots {
  display: flex;
  gap: 2px;
  margin-left: 8px;
}

.typing-dots span {
  width: 3px;
  height: 3px;
  background: rgba(255, 255, 255, 0.8);
  border-radius: 50%;
  animation: typing-dots 1.4s infinite ease-in-out;
}

.typing-dots span:nth-child(2) {
  animation-delay: 0.2s;
}

.typing-dots span:nth-child(3) {
  animation-delay: 0.4s;
}

.close-btn {
  background: none;
  border: none;
  color: white;
  font-size: 18px;
  cursor: pointer;
  padding: 8px;
  border-radius: 50%;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.close-btn:hover {
  background: rgba(255, 255, 255, 0.2);
  transform: rotate(90deg);
}

.close-ripple {
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0;
  height: 0;
  background: rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  transform: translate(-50%, -50%);
  animation: close-ripple 0.3s ease-out;
}

/* Messages Container */
.messages-container {
  flex: 1;
  overflow-y: auto;
  padding: 20px;
  background: #f8fafc;
}

.welcome-message {
  text-align: center;
  padding: 20px;
}

.welcome-avatar {
  width: 60px;
  height: 60px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 16px;
  color: white;
  font-size: 24px;
}

.welcome-message h5 {
  margin: 0 0 8px;
  color: #1e293b;
  font-weight: 600;
}

.welcome-message p {
  margin: 0 0 20px;
  color: #64748b;
  font-size: 14px;
}

.quick-suggestions h6 {
  margin: 0 0 12px;
  color: #374151;
  font-size: 13px;
  font-weight: 600;
}

.suggestions-grid {
  display: grid;
  gap: 8px;
}

.suggestion-btn {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 10px 12px;
  font-size: 12px;
  color: #475569;
  cursor: pointer;
  transition: all 0.3s ease;
  text-align: left;
}

.suggestion-btn:hover {
  background: #f1f5f9;
  border-color: #667eea;
  color: #667eea;
  transform: translateY(-1px);
}

/* Messages */
.message {
  display: flex;
  margin-bottom: 16px;
  align-items: flex-end;
  gap: 8px;
}

.message-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  flex-shrink: 0;
}

.ai-message .message-avatar {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.user-avatar {
  background: #e2e8f0;
  color: #64748b;
}

.message-content {
  flex: 1;
  max-width: 80%;
}

.user-message .message-content {
  margin-left: auto;
}

.message-bubble {
  padding: 12px 16px;
  border-radius: 16px;
  position: relative;
}

.ai-message .message-bubble {
  background: white;
  border: 1px solid #e2e8f0;
  border-bottom-left-radius: 4px;
}

.user-message .message-bubble {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-bottom-right-radius: 4px;
}

/* Offline Message Styles */
.offline-message .message-bubble {
  background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
  color: white;
  border: 1px solid #f59e0b;
  position: relative;
}

.offline-message .message-bubble::before {
  content: '🔄';
  position: absolute;
  top: -8px;
  right: -8px;
  background: #f59e0b;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 10px;
  animation: spin 2s linear infinite;
}

/* Success Message Styles */
.success-message .message-bubble {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  border: 1px solid #10b981;
}

.success-message .message-avatar {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
}

.message-bubble p {
  margin: 0;
  font-size: 14px;
  line-height: 1.4;
}

.message-time {
  font-size: 11px;
  opacity: 0.7;
  display: block;
  margin-top: 4px;
}

/* Typing Indicator */
.typing-indicator {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  border-bottom-left-radius: 4px;
  padding: 12px 16px;
  display: flex;
  gap: 4px;
  align-items: center;
}

.typing-indicator span {
  width: 6px;
  height: 6px;
  background: #94a3b8;
  border-radius: 50%;
  animation: typing 1.4s infinite ease-in-out;
}

.typing-indicator span:nth-child(2) {
  animation-delay: 0.2s;
}

.typing-indicator span:nth-child(3) {
  animation-delay: 0.4s;
}

/* Input Area */
.input-area {
  background: white;
  border-top: 1px solid #e2e8f0;
  padding: 16px 20px;
}

.input-container {
  display: flex;
  gap: 8px;
  align-items: center;
}

.message-input {
  flex: 1;
  border: 1px solid #e2e8f0;
  border-radius: 20px;
  padding: 10px 16px;
  font-size: 14px;
  outline: none;
  transition: border-color 0.3s ease;
}

.message-input:focus {
  border-color: #667eea;
}

.message-input:disabled {
  background: #f1f5f9;
  color: #94a3b8;
}

.send-btn {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border: none;
  border-radius: 50%;
  color: white;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
}

.send-btn:hover:not(:disabled) {
  transform: scale(1.05);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.send-btn:disabled {
  background: #e2e8f0;
  color: #94a3b8;
  cursor: not-allowed;
}

.send-btn.offline {
  background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
  animation: pulse-orange 2s infinite;
}

.send-btn.offline:hover:not(:disabled) {
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

.input-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 8px;
  font-size: 11px;
  color: #94a3b8;
}

/* Animations */
@keyframes pulse {
  0% {
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
  }
  50% {
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
  }
  100% {
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
  }
}

@keyframes typing {
  0%, 60%, 100% {
    transform: translateY(0);
  }
  30% {
    transform: translateY(-10px);
  }
}

@keyframes shake {
  0%, 100% {
    transform: translateX(0);
  }
  10%, 30%, 50%, 70%, 90% {
    transform: translateX(-3px);
  }
  20%, 40%, 60%, 80% {
    transform: translateX(3px);
  }
}

@keyframes glow {
  0%, 100% {
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
  }
  50% {
    box-shadow: 0 8px 35px rgba(102, 126, 234, 0.8), 0 0 20px rgba(102, 126, 234, 0.5);
  }
}

@keyframes ripple {
  0% {
    width: 0;
    height: 0;
    opacity: 1;
  }
  100% {
    width: 120px;
    height: 120px;
    opacity: 0;
  }
}

/* Transitions */
.bounce-enter-active {
  animation: bounce-in 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.bounce-leave-active {
  animation: bounce-out 0.3s ease-in;
}

@keyframes bounce-in {
  0% {
    transform: scale(0);
    opacity: 0;
  }
  50% {
    transform: scale(1.1);
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

@keyframes bounce-out {
  0% {
    transform: scale(1);
    opacity: 1;
  }
  100% {
    transform: scale(0);
    opacity: 0;
  }
}

.slide-up-enter-active {
  animation: slide-up-in 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.slide-up-leave-active {
  animation: slide-up-out 0.3s cubic-bezier(0.55, 0.055, 0.675, 0.19);
}

.slide-in-notification-enter-active {
  animation: slide-in-notification 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.slide-in-notification-leave-active {
  animation: slide-out-notification 0.3s cubic-bezier(0.55, 0.055, 0.675, 0.19);
}

@keyframes slide-up-in {
  0% {
    transform: translateY(100%) scale(0.8);
    opacity: 0;
  }
  100% {
    transform: translateY(0) scale(1);
    opacity: 1;
  }
}

@keyframes slide-up-out {
  0% {
    transform: translateY(0) scale(1);
    opacity: 1;
  }
  100% {
    transform: translateY(100%) scale(0.8);
    opacity: 0;
  }
}

@keyframes slide-in-notification {
  0% {
    transform: translateX(100%) scale(0.8);
    opacity: 0;
  }
  100% {
    transform: translateX(0) scale(1);
    opacity: 1;
  }
}

@keyframes slide-out-notification {
  0% {
    transform: translateX(0) scale(1);
    opacity: 1;
  }
  100% {
    transform: translateX(100%) scale(0.8);
    opacity: 0;
  }
}

/* New Amazing Animations */
@keyframes breathing {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05);
  }
}

@keyframes hover-float {
  0% {
    transform: translateY(0);
  }
  100% {
    transform: translateY(-5px);
  }
}

@keyframes float-particle {
  0%, 100% {
    transform: translateY(0) rotate(0deg);
    opacity: 0.3;
  }
  50% {
    transform: translateY(-20px) rotate(180deg);
    opacity: 1;
  }
}

@keyframes magic-rotate {
  0% {
    transform: translate(-50%, -50%) scale(1) rotate(0deg);
  }
  100% {
    transform: translate(-50%, -50%) scale(1) rotate(360deg);
  }
}

@keyframes orbit-1 {
  0% {
    transform: translate(-50%, -50%) rotate(0deg);
  }
  100% {
    transform: translate(-50%, -50%) rotate(360deg);
  }
}

@keyframes orbit-2 {
  0% {
    transform: translate(-50%, -50%) rotate(0deg);
  }
  100% {
    transform: translate(-50%, -50%) rotate(-360deg);
  }
}

@keyframes orbit-3 {
  0% {
    transform: translate(-50%, -50%) rotate(0deg);
  }
  100% {
    transform: translate(-50%, -50%) rotate(360deg);
  }
}

@keyframes icon-rotate {
  0% {
    transform: rotate(0deg);
  }
  50% {
    transform: rotate(180deg) scale(1.2);
  }
  100% {
    transform: rotate(360deg);
  }
}

@keyframes sparkle {
  0%, 100% {
    opacity: 0;
    transform: scale(0.5);
  }
  50% {
    opacity: 1;
    transform: scale(1);
  }
}

@keyframes bg-float {
  0%, 100% {
    transform: translateY(0px);
    opacity: 0.3;
  }
  50% {
    transform: translateY(-30px);
    opacity: 1;
  }
}

@keyframes avatar-pulse {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.1);
  }
}

@keyframes avatar-glow {
  0%, 100% {
    opacity: 0;
  }
  50% {
    opacity: 0.8;
  }
}

@keyframes status-pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
}

@keyframes typing-dots {
  0%, 60%, 100% {
    transform: translateY(0);
  }
  30% {
    transform: translateY(-8px);
  }
}

@keyframes close-ripple {
  0% {
    width: 0;
    height: 0;
    opacity: 1;
  }
  100% {
    width: 40px;
    height: 40px;
    opacity: 0;
  }
}

@keyframes message-slide-enter {
  0% {
    opacity: 0;
    transform: translateY(20px) scale(0.9);
  }
  100% {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

@keyframes pulse-red {
  0%, 100% {
    opacity: 1;
    transform: scale(1);
  }
  50% {
    opacity: 0.7;
    transform: scale(1.1);
  }
}

@keyframes pulse-yellow {
  0%, 100% {
    opacity: 1;
    transform: scale(1);
  }
  50% {
    opacity: 0.8;
    transform: scale(1.05);
  }
}

@keyframes pulse-green {
  0%, 100% {
    opacity: 1;
    transform: scale(1);
  }
  50% {
    opacity: 0.8;
    transform: scale(1.05);
  }
}

@keyframes pulse-orange {
  0%, 100% {
    opacity: 1;
    transform: scale(1);
  }
  50% {
    opacity: 0.8;
    transform: scale(1.05);
  }
}

@keyframes slide-down {
  0% {
    transform: translateY(-100%);
    opacity: 0;
  }
  100% {
    transform: translateY(0);
    opacity: 1;
  }
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

/* Responsive Design */
@media (max-width: 480px) {
  .gemini-chatbot-container {
    bottom: 10px;
    right: 10px;
  }

  .chat-window {
    width: calc(100vw - 20px);
    height: calc(100vh - 100px);
    max-width: 380px;
    max-height: 500px;
  }

  .chat-tooltip {
    display: none;
  }
}

/* Scrollbar Styling */
.messages-container::-webkit-scrollbar {
  width: 4px;
}

.messages-container::-webkit-scrollbar-track {
  background: transparent;
}

.messages-container::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 2px;
}

.messages-container::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>

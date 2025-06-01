import axios from 'axios';

// Base URL for the API
const API_BASE_URL = 'http://localhost:8000/api';

// Create axios instance with default config
const apiClient = axios.create({
  baseURL: API_BASE_URL,
  timeout: 30000, // 30 seconds timeout
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  }
});

// Add request interceptor to include auth token
apiClient.interceptors.request.use(
  (config) => {
    // Get token from localStorage (adjust based on your auth implementation)
    const studentInfo = localStorage.getItem('StudentAccountInfo');
    const tuteurInfo = localStorage.getItem('TuteurAccountInfo');
    const entrepriseInfo = localStorage.getItem('EntrepriseAccountInfo');
    
    let token = null;
    if (studentInfo) {
      const student = JSON.parse(studentInfo);
      token = student.token;
    } else if (tuteurInfo) {
      const tuteur = JSON.parse(tuteurInfo);
      token = tuteur.token;
    } else if (entrepriseInfo) {
      const entreprise = JSON.parse(entrepriseInfo);
      token = entreprise.token;
    }
    
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Add response interceptor for error handling
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      // Handle unauthorized access
      console.warn('Unauthorized access - redirecting to login');
      // You might want to redirect to login page here
    }
    return Promise.reject(error);
  }
);

class GeminiService {
  constructor() {
    this.offlineMessages = [];
    this.defaultResponses = [
      "Je suis actuellement hors ligne, mais je serai de retour bientôt ! En attendant, vous pouvez consulter la section d'aide de la plateforme.",
      "Service temporairement indisponible. Vos questions sont importantes pour moi, je vous répondrai dès que possible !",
      "Je ne peux pas vous répondre maintenant car je suis hors ligne. Essayez de redémarrer la conversation dans quelques minutes.",
      "Connexion interrompue ! Pendant ce temps, vous pouvez explorer les fonctionnalités de la plateforme ou contacter votre tuteur directement.",
      "Je rencontre des difficultés de connexion. Votre message a été enregistré et je vous répondrai dès mon retour en ligne !"
    ];
  }

  /**
   * Send a message to Gemini AI and get response
   * @param {string} message - The user's message
   * @param {Array} conversationHistory - Previous conversation messages
   * @returns {Promise} - API response
   */
  async sendMessage(message, conversationHistory = []) {
    try {
      const response = await apiClient.post('/gemini/chat', {
        message: message.trim(),
        conversation_history: conversationHistory
      });

      return {
        success: true,
        data: response.data
      };
    } catch (error) {
      console.error('Gemini chat error:', error);

      let errorMessage = 'Échec de la réponse IA';
      if (error.response?.data?.message) {
        errorMessage = error.response.data.message;
      } else if (error.message) {
        errorMessage = error.message;
      }

      return {
        success: false,
        error: errorMessage
      };
    }
  }

  /**
   * Get a random default offline response
   * @returns {string} - Default response message
   */
  getOfflineResponse() {
    const randomIndex = Math.floor(Math.random() * this.defaultResponses.length);
    return this.defaultResponses[randomIndex];
  }

  /**
   * Store message for offline processing
   * @param {string} message - The user's message
   * @param {Array} conversationHistory - Previous conversation messages
   */
  storeOfflineMessage(message, conversationHistory = []) {
    const offlineMessage = {
      id: Date.now(),
      message: message.trim(),
      conversationHistory,
      timestamp: new Date(),
      status: 'pending'
    };

    this.offlineMessages.push(offlineMessage);
    this.saveOfflineMessagesToStorage();

    return offlineMessage;
  }

  /**
   * Get all pending offline messages
   * @returns {Array} - Array of offline messages
   */
  getOfflineMessages() {
    return this.offlineMessages;
  }

  /**
   * Clear all offline messages
   */
  clearOfflineMessages() {
    this.offlineMessages = [];
    this.saveOfflineMessagesToStorage();
  }

  /**
   * Save offline messages to localStorage
   */
  saveOfflineMessagesToStorage() {
    try {
      localStorage.setItem('gemini_offline_messages', JSON.stringify(this.offlineMessages));
    } catch (error) {
      console.warn('Failed to save offline messages to storage:', error);
    }
  }

  /**
   * Load offline messages from localStorage
   */
  loadOfflineMessagesFromStorage() {
    try {
      const stored = localStorage.getItem('gemini_offline_messages');
      if (stored) {
        this.offlineMessages = JSON.parse(stored);
      }
    } catch (error) {
      console.warn('Failed to load offline messages from storage:', error);
      this.offlineMessages = [];
    }
  }

  /**
   * Process pending offline messages when back online
   * @returns {Promise} - Processing result
   */
  async processPendingMessages() {
    if (this.offlineMessages.length === 0) {
      return { success: true, processedCount: 0 };
    }

    let processedCount = 0;
    const failedMessages = [];

    for (const offlineMessage of this.offlineMessages) {
      try {
        const response = await this.sendMessage(
          offlineMessage.message,
          offlineMessage.conversationHistory
        );

        if (response.success) {
          processedCount++;
        } else {
          failedMessages.push(offlineMessage);
        }
      } catch (error) {
        failedMessages.push(offlineMessage);
      }
    }

    // Keep only failed messages
    this.offlineMessages = failedMessages;
    this.saveOfflineMessagesToStorage();

    return {
      success: true,
      processedCount,
      failedCount: failedMessages.length
    };
  }

  /**
   * Get quick suggestion prompts
   * @returns {Promise} - API response with suggestions
   */
  async getQuickSuggestions() {
    try {
      const response = await apiClient.get('/gemini/suggestions');
      
      return {
        success: true,
        data: response.data
      };
    } catch (error) {
      console.error('Failed to get suggestions:', error);
      
      return {
        success: false,
        error: 'Échec du chargement des suggestions',
        data: {
          suggestions: [
            "Comment postuler pour un stage ?",
            "Quels cours sont disponibles ?",
            "Comment contacter mon tuteur ?",
            "Comment télécharger mon CV ?"
          ]
        }
      };
    }
  }

  /**
   * Check if Gemini service is available
   * @returns {Promise} - Health check response
   */
  async healthCheck() {
    try {
      const response = await apiClient.get('/gemini/health');

      return {
        success: true,
        data: response.data
      };
    } catch (error) {
      console.error('Gemini health check failed:', error);

      return {
        success: false,
        error: 'Service IA indisponible'
      };
    }
  }

  /**
   * Check network connectivity
   * @returns {boolean} - Network status
   */
  isNetworkOnline() {
    return navigator.onLine;
  }

  /**
   * Enhanced health check with network detection
   * @returns {Promise} - Enhanced health check response
   */
  async enhancedHealthCheck() {
    // First check basic network connectivity
    if (!this.isNetworkOnline()) {
      return {
        success: false,
        error: 'Aucune connexion réseau détectée',
        networkStatus: 'offline'
      };
    }

    // Then check service availability
    try {
      const response = await apiClient.get('/gemini/health');

      return {
        success: true,
        data: response.data,
        networkStatus: 'online'
      };
    } catch (error) {
      console.error('Gemini health check failed:', error);

      return {
        success: false,
        error: 'Service IA indisponible',
        networkStatus: 'online' // Network is up but service is down
      };
    }
  }

  /**
   * Format conversation history for API
   * @param {Array} messages - Array of message objects
   * @returns {Array} - Formatted conversation history
   */
  formatConversationHistory(messages) {
    return messages.map(msg => ({
      role: msg.isUser ? 'user' : 'assistant',
      content: msg.text
    }));
  }

  /**
   * Validate message before sending
   * @param {string} message - Message to validate
   * @returns {Object} - Validation result
   */
  validateMessage(message) {
    if (!message || typeof message !== 'string') {
      return {
        isValid: false,
        error: 'Message requis'
      };
    }

    const trimmedMessage = message.trim();
    if (trimmedMessage.length === 0) {
      return {
        isValid: false,
        error: 'Le message ne peut pas être vide'
      };
    }

    if (trimmedMessage.length > 2000) {
      return {
        isValid: false,
        error: 'Message trop long (max 2000 caractères)'
      };
    }
    
    return {
      isValid: true,
      message: trimmedMessage
    };
  }
}

// Create and export service instance
const geminiService = new GeminiService();

// Initialize offline messages from storage
geminiService.loadOfflineMessagesFromStorage();

export default geminiService;

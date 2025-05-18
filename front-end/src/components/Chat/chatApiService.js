/**
 * Mock API Service for Chat Functionality
 * 
 * This file provides mock implementations of the backend API endpoints
 * required for the chat feature. In a production environment, these would
 * be replaced with actual API calls to the backend server.
 */

import axios from 'axios';
import Pusher from 'pusher-js';

// Mock data for demonstration
const MOCK_CONTACTS = {
  student: [
    {
      id: 1,
      name: 'Prof. Ahmed',
      role: 'tutor',
      lastMessage: 'Bonjour, comment puis-je vous aider avec le cours?',
      unreadCount: 2
    },
    {
      id: 2,
      name: 'Prof. Marie',
      role: 'tutor',
      lastMessage: 'N\'oubliez pas de soumettre votre devoir avant vendredi',
      unreadCount: 0
    }
  ],
  tutor: [
    {
      id: 101,
      name: 'Étudiant Mohamed',
      role: 'student',
      lastMessage: 'Merci pour votre aide avec le projet',
      unreadCount: 0
    },
    {
      id: 102,
      name: 'Étudiant Sara',
      role: 'student',
      lastMessage: 'J\'ai une question sur le chapitre 3',
      unreadCount: 1
    },
    {
      id: 103,
      name: 'Étudiant Karim',
      role: 'student',
      lastMessage: null,
      unreadCount: 0
    }
  ]
};

const MOCK_MESSAGES = {
  'chat.1_student.1_tutor': [
    {
      id: 1,
      content: 'Bonjour, j\'ai une question sur le cours',
      sender_id: 1,
      receiver_id: 1,
      created_at: '2023-05-01T10:00:00Z'
    },
    {
      id: 2,
      content: 'Bonjour, comment puis-je vous aider avec le cours?',
      sender_id: 1,
      receiver_id: 1,
      created_at: '2023-05-01T10:05:00Z'
    }
  ],
  'chat.101_student.2_tutor': [
    {
      id: 3,
      content: 'Bonjour professeur, j\'ai besoin d\'aide avec mon projet',
      sender_id: 101,
      receiver_id: 2,
      created_at: '2023-05-02T14:30:00Z'
    },
    {
      id: 4,
      content: 'Bien sûr, dites-moi ce qui vous pose problème',
      sender_id: 2,
      receiver_id: 101,
      created_at: '2023-05-02T14:35:00Z'
    },
    {
      id: 5,
      content: 'Merci pour votre aide avec le projet',
      sender_id: 101,
      receiver_id: 2,
      created_at: '2023-05-02T15:00:00Z'
    }
  ]
};

// Mock API service
const chatApiService = {
  /**
   * Get chat contacts for the current user
   */
  getContacts: async (userId, userRole) => {
    try {
      let response;
      
      if (userRole === 'student') {
        // For students, fetch their tutors
        response = await axios.get('/subcsriptWithTueur', {
          params: { etudiant_id: userId }
        });
        
        if (response.data.check) {
          // Transform the response to match our component's expected format
          return {
            data: {
              success: true,
              contacts: response.data.tutors.map(tutor => ({
                id: tutor.id,
                name: tutor.fullname,
                role: 'tutor',
                lastMessage: null,
                unreadCount: 0
              }))
            }
          };
        }
      } else if (userRole === 'tutor') {
        // For tutors, fetch their students
        response = await axios.get('/getAllStudentsSubscripted', {
          params: { tuteur_id: userId }
        });
        
        if (response.data.check) {
          // Transform the response to match our component's expected format
          return {
            data: {
              success: true,
              contacts: response.data.students.map(student => ({
                id: student.id,
                name: student.fullname,
                role: 'student',
                lastMessage: null,
                unreadCount: 0
              }))
            }
          };
        }
      }
      
      // If we get here, something went wrong
      return {
        data: {
          success: false,
          contacts: [],
          message: 'Failed to fetch contacts'
        }
      };
    } catch (error) {
      console.error('Error fetching contacts:', error);
      return {
        data: {
          success: false,
          contacts: [],
          message: 'Error fetching contacts'
        }
      };
    }
  },

  /**
   * Get chat history between two users
   */
  getChatHistory: async (currentUserId, currentUserRole, partnerId, partnerRole) => {
    // Create channel name using the same logic as in the frontend
    const participants = [
      { id: currentUserId, role: currentUserRole },
      { id: partnerId, role: partnerRole }
    ];
    
    participants.sort((a, b) => {
      if (a.id !== b.id) return a.id - b.id;
      return a.role.localeCompare(b.role);
    });
    
    const channelName = `chat.${participants[0].id}_${participants[0].role}.${participants[1].id}_${participants[1].role}`;
    
    // In a real implementation, this would be an API call
    return new Promise((resolve) => {
      setTimeout(() => {
        resolve({
          data: {
            success: true,
            messages: MOCK_MESSAGES[channelName] || []
          }
        });
      }, 500); // Simulate network delay
    });
  },

  /**
   * Send a new message
   */
  sendMessage: async (messageData) => {
    // In a real implementation, this would be an API call that saves the message
    // and triggers a Pusher event
    
    // Simulate sending the message to the server
    return new Promise((resolve) => {
      setTimeout(() => {
        // Simulate Pusher event broadcast
        // In a real implementation, this would be done by the server
        const pusher = new Pusher('edc2943b2a2068f8b38c', {
          cluster: 'eu'
        });
        
        // Get a reference to the channel
        const channel = pusher.channel(messageData.channelName);
        
        // If the channel exists, trigger an event on it
        if (channel) {
          // In a real implementation, the server would trigger this event
          // This is just for demonstration purposes
          channel.trigger('chat-message', {
            message: {
              content: messageData.content,
              senderId: messageData.senderId,
              timestamp: messageData.timestamp
            }
          });
        }
        
        resolve({
          data: {
            success: true,
            message: 'Message sent successfully'
          }
        });
      }, 300); // Simulate network delay
    });
  },

  /**
   * Initialize Pusher for real-time messaging
   */
  initializePusher: (channelName, onMessageReceived) => {
    const pusher = new Pusher('edc2943b2a2068f8b38c', {
      cluster: 'eu'
    });
    
    const channel = pusher.subscribe(channelName);
    
    channel.bind('chat-message', (data) => {
      if (data && data.message) {
        onMessageReceived(data.message);
      }
    });
    
    return { pusher, channel };
  }
};

export default chatApiService;
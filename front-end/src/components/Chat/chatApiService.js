/**
 * Mock API Service for Chat Functionality
 * 
 * This file provides mock implementations of the backend API endpoints
 * required for the chat feature. In a production environment, these would
 * be replaced with actual API calls to the backend server.
 */

import axios from 'axios';
import Pusher from 'pusher-js';


// Mock API service
const chatApiService = {
  /**
   * Get chat contacts for the current user
   */
  getContacts: async (userId, userRole, token) => {
    try {
      // const token = localStorage.getItem('token');
      // const token = null;
      // if (userRole === 'etudiant') {
      //   token = JSON.parse(localStorage.getItem('StudentAccountInfo')).token
      // } else if (userRole === 'tuteur') {
      //   token = JSON.parse(localStorage.getItem('TuteurAccountInfo')).token
      // }

      // if (!token) {
      //   console.error('Authentication token not found.');
      //   return {
      //     data: {
      //       success: false,
      //       contacts: [],
      //       message: 'Authentication token not found.'
      //     }
      //   };
      // }
      console.log('Requesting contacts for role:', userRole);
      console.log('Using token:', token);

      // Ensure a token is available
      // The calling component should ideally pass the token.
      // This is a fallback, but relying on the passed `token` argument is preferred.
      let authToken = token;
      if (!authToken) {
        console.warn('Token not passed to getContacts, attempting to retrieve from localStorage.');
        if (userRole === 'etudiant') {
          const studentInfo = JSON.parse(localStorage.getItem('StudentAccountInfo'));
          authToken = studentInfo ? studentInfo.token : null;
        } else if (userRole === 'tuteur') {
          const tuteurInfo = JSON.parse(localStorage.getItem('TuteurAccountInfo'));
          authToken = tuteurInfo ? tuteurInfo.token : null;
        }
      }

      if (!authToken) {
        console.error('Authentication token not found.');
        return {
          data: {
            success: false,
            contacts: [],
            message: 'Authentication token not found.'
          }
        };
      }

      const response = await axios.get(`http://localhost:8000/api/chat/contacts/${userRole}`,
        {
          headers: {
            Authorization: `Bearer ${authToken}`
          },
        }
      );

      console.log(response)
      if (response.data && response.data.success) {
        // Transform the response to match our component's expected format
        // The new API response already provides 'fullname' as 'name' and 'id'
        // It also seems to provide 'image', which we can add if needed.
        // The 'role' for contacts will be the opposite of currentUserRole, or determined by API if provided.
        return {
          data: {
            success: true,
            contacts: response.data.contacts.map(contact => ({
              id: contact.id,
              name: contact.fullname, // API provides fullname
              role: userRole === 'etudiant' ? 'tuteur' : 'etudiant', // Assuming contacts are the other role
              image: contact.image || null, // Use image if provided
              lastMessage: null, // This might need to be fetched or updated separately
              unreadCount: 0 // This might need to be fetched or updated separately
            }))
          }
        };
      } else {
        console.error('Failed to fetch contacts:', response.data.message || 'Unknown error');
        return {
          data: {
            success: false,
            contacts: [],
            message: response.data.message || 'Failed to fetch contacts'
          }
        };
      }
    } catch (error) {
      console.error('Error fetching contacts:', error.response ? error.response.data : error.message);
      return {
        data: {
          success: false,
          contacts: [],
          message: error.response ? error.response.data.message : 'Error fetching contacts'
        }
      };
    }
  },

  getChatHistory: async (currentUserId, currentUserRole, partnerId, partnerRole) => {
    try {
      let authToken;
      if (currentUserRole === 'tuteur') {
        const tuteurInfo = JSON.parse(localStorage.getItem('TuteurAccountInfo'));
        authToken = tuteurInfo ? tuteurInfo.token : null;
      } else {
        const studentInfo = JSON.parse(localStorage.getItem('StudentAccountInfo'));
        authToken = studentInfo ? studentInfo.token : null;
      }

      if (!authToken) {
        console.error('Authentication token not found.');
        return {
          data: {
            success: false,
            messages: [],
            message: 'Authentication token not found.'
          }
        };
      }

      const response = await axios.get(
        `http://127.0.0.1:8000/api/chat/${currentUserRole}/messages/${partnerId}`,
        {
          headers: {
            Authorization: `Bearer ${authToken}`
          }
        }
      );

      if (response.data.success) {
        return {
          data: {
            success: true,
            messages: response.data.data.map(msg => ({
              id: msg.id,
              // Ensure consistent naming if backend provides sender_id/receiver_id or tuteur_id/etudiant_id
              tuteur_id: msg.tuteur_id, 
              etudiant_id: msg.etudiant_id,
              message: msg.message,
              read_at: msg.read_at,
              created_at: msg.created_at,
              sender_type: msg.sender_type // This indicates who sent the message
            }))
          }
        };
      } else {
        return {
          data: {
            success: false,
            messages: [],
            message: response.data.message || 'Failed to fetch messages'
          }
        };
      }
    } catch (error) {
      console.error('Error fetching chat history:', error.response ? error.response.data : error.message);
      return {
        data: {
          success: false,
          messages: [],
          message: error.response ? error.response.data.message : 'Error fetching chat history'
        }
      };
    }
  },

  getUnreadCount: async (token, currentUserRole) => {
    if (!token) {
      console.error('Authentication token not found for getUnreadCount.');
      return { 
        data: { 
          success: false, 
          message: 'Authentication token not found.', 
          total_unread_count: 0, 
          unread_by_user: [] 
        } 
      };
    }

    const apiUrl = `http://127.0.0.1:8000/api/chat/unread-count/${currentUserRole}`;

    try {
      const response = await axios.get(apiUrl, 
        {
          headers: {
            Authorization: `Bearer ${token}`,
            // 'Content-Type': 'application/json'
          }
        }
      );

      if (response.data && response.data.success) {
        return { 
          data: { 
            success: true, 
            total_unread_count: response.data.total_unread_count || 0,
            unread_by_user: response.data.unread_by_user || []
          } 
        };
      } else {
        console.error('Failed to fetch unread count:', response.data.message || 'Unknown error');
        return { 
          data: { 
            success: false, 
            message: response.data.message || 'Failed to fetch unread count', 
            total_unread_count: 0,
            unread_by_user: []
          } 
        };
      }
    } catch (error) {
      console.error('Error fetching unread count:', error.response ? error.response.data : error.message);
      return { 
        data: { 
          success: false, 
          message: error.response ? error.response.data.message : 'Error fetching unread count', 
          total_unread_count: 0,
          unread_by_user: []
        } 
      };
    }
  },

  sendMessage: async (messageData) => {
    try {
      // Get the appropriate token based on user role
      let authToken;
      if (messageData.senderRole === 'tuteur') {
        const tuteurInfo = JSON.parse(localStorage.getItem('TuteurAccountInfo'));
        authToken = tuteurInfo ? tuteurInfo.token : null;
      } else {
        const studentInfo = JSON.parse(localStorage.getItem('StudentAccountInfo'));
        authToken = studentInfo ? studentInfo.token : null;
      }

      if (!authToken) {
        console.error('Authentication token not found.');
        return {
          data: {
            success: false,
            message: 'Authentication token not found.'
          }
        };
      }

      // Determine if we're sending a file or regular message
      const isFormData = messageData instanceof FormData;
      
      // Prepare the message data and endpoint based on sender role
      let apiEndpoint;
      let apiMessageData;
      let headers = {
        Authorization: `Bearer ${authToken}`
      };

      if (isFormData) {
        // For file uploads, use FormData and don't set Content-Type (browser will set it automatically)
        apiMessageData = messageData;
      } else {
        // For regular messages, use JSON
        headers['Content-Type'] = 'application/json';
        if (messageData.senderRole === 'tuteur') {
          apiEndpoint = 'http://127.0.0.1:8000/api/chat/tutor/send';
          apiMessageData = {
            etudiant_id: messageData.receiverId,
            message: messageData.content,
          };
        } else {
          apiEndpoint = 'http://127.0.0.1:8000/api/chat/student/send';
          apiMessageData = {
            tuteur_id: messageData.receiverId,
            message: messageData.content,
          };
        }
      }

      // Set the appropriate endpoint for file uploads
      if (isFormData) {
        apiEndpoint = messageData.senderRole === 'tuteur' 
          ? 'http://127.0.0.1:8000/api/chat/tutor/send-file'
          : 'http://127.0.0.1:8000/api/chat/student/send-file';
      }

      // Send the message to the server
      const response = await axios.post(
        apiEndpoint,
        apiMessageData,
        { headers }
      );

      if (response.data && response.data.success) {
        return {
          data: {
            success: true,
            message: 'Message sent successfully',
            messageData: {
              id: response.data.message.id,
              tuteur_id: response.data.message.tuteur_id,
              etudiant_id: response.data.message.etudiant_id,
              message: response.data.message.message,
              read_at: response.data.message.read_at,
              created_at: response.data.message.created_at,
              sender_type: response.data.message.sender_type
            }
          }
        };
      } else {
        console.error('Failed to send message:', response.data.message || 'Unknown error');
        return {
          data: {
            success: false,
            message: response.data.message || 'Failed to send message'
          }
        };
      }
    } catch (error) {
      console.error('Error sending message:', error.response ? error.response.data : error.message);
      return {
        data: {
          success: false,
          message: error.response ? error.response.data.message : 'Error sending message'
        }
      };
    }
  },

  initializePusher: (userId, userRole, onMessageReceived) => {
    const pusher = new Pusher('edc2943b2a2068f8b38c', { 
      cluster: 'eu' 
    });
    
    const channelName = `chat.${userId}_${userRole}`; 
    console.log(`Subscribing to Pusher channel: ${channelName}`);
    const channel = pusher.subscribe(channelName);
    
    channel.bind('new.message', (data) => { 
      if (data && data.message) {
        console.log('Received message via Pusher:', data.message);
        if (typeof onMessageReceived === 'function') {
          onMessageReceived(data); 
        }
      } else {
        console.warn('Received empty or malformed message data via Pusher:', data);
      }
    });

    channel.bind('pusher:subscription_succeeded', () => {
      console.log(`Successfully subscribed to ${channelName}`);
    });

    channel.bind('pusher:subscription_error', (status) => {
      console.error(`Failed to subscribe to ${channelName}, status: ${status}`);
    });
    
    return { pusher, channel };
  },

  /**
   * Mark a message as read
   * @param {string} token - Authentication token
   * @param {number} messageId - Single message ID to mark as read
   * @returns {Promise} - Promise resolving to the API response
   */
  markMessagesAsRead: async (token, messageId) => {
    try {
      if (!token) {
        console.error('Authentication token not found for markMessagesAsRead.');
        return { 
          data: { 
            success: false, 
            message: 'Authentication token not found.' 
          } 
        };
      }
      
      const response = await axios.post('http://127.0.0.1:8000/api/chat/mark-read',
        { message_id: messageId },
        {
          headers: {
            Authorization: `Bearer ${token}`,
          }
        }
      );

      if (response.data && response.data.success) {
        return { 
          data: { 
            success: true, 
            message: 'Messages marked as read successfully' 
          } 
        };
      } else {
        console.error('Failed to mark messages as read:', response.data.message || 'Unknown error');
        return { 
          data: { 
            success: false, 
            message: response.data.message || 'Failed to mark messages as read' 
          } 
        };
      }
    } catch (error) {
      console.error('Error marking messages as read:', error.response ? error.response.data : error.message);
      return { 
        data: { 
          success: false, 
          message: error.response ? error.response.data.message : 'Error marking messages as read' 
        } 
      };
    }
  }
};

export default chatApiService;
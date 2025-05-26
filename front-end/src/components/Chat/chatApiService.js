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
              tuteur_id: msg.tuteur_id, 
              etudiant_id: msg.etudiant_id,
              message: msg.message,
              file_path: msg.file_path,
              file_type: msg.file_type,
              read_at: msg.read_at,
              created_at: msg.created_at,
              sender_type: msg.sender_type 
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
      let senderRole;
      
      // Determine if we're sending a FormData or regular message
      const isFormData = messageData instanceof FormData;
      
      // Extract the sender role from the FormData or messageData
      if (isFormData) {
        // For FormData, we need to check if it contains etudiant_id or tuteur_id
        if (messageData.has('etudiant_id')) {
          senderRole = 'tuteur';
        } else {
          senderRole = 'etudiant';
        }
      } else {
        senderRole = messageData.senderRole;
      }
      
      // Get the appropriate token based on user role
      if (senderRole === 'tuteur') {
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

      // Prepare the headers
      let headers = {
        Authorization: `Bearer ${authToken}`
      };

      // Use the correct API endpoint based on sender role
      let apiEndpoint;
      if (senderRole === 'tuteur') {
        apiEndpoint = 'http://127.0.0.1:8000/api/chat/tutor/send';
      } else {
        apiEndpoint = 'http://127.0.0.1:8000/api/chat/student/send';
      }

      // For regular messages (not FormData), set Content-Type and prepare data
      let apiMessageData = messageData;
      if (!isFormData) {
        headers['Content-Type'] = 'application/json';
        if (senderRole === 'tuteur') {
          apiMessageData = {
            etudiant_id: messageData.receiverId,
            message: messageData.content,
          };
        } else {
          apiMessageData = {
            tuteur_id: messageData.receiverId,
            message: messageData.content,
          };
        }
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
            messageData: response.data.data || response.data.message
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
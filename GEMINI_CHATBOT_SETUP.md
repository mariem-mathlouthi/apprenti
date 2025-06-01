# Gemini AI Chatbot Integration Setup

This document provides instructions for setting up the Gemini AI chatbot integration in the Apprenti+ platform.

## 🚀 Features

- **Floating Virtual Assistant**: Beautiful floating chatbot button in the bottom-right corner
- **Amazing Animations**: Smooth transitions, pulse effects, and typing indicators
- **Gemini Flash 1.5**: Powered by Google's latest Gemini Flash 1.5 model
- **Context-Aware**: Understands the Apprenti+ platform and provides relevant help
- **Responsive Design**: Works perfectly on desktop and mobile devices
- **Real-time Chat**: Instant responses with typing indicators
- **Quick Suggestions**: Pre-defined helpful questions for users
- **Authentication Integration**: Only shows for logged-in users

## 📋 Prerequisites

- Laravel backend running
- Vue.js 3 frontend running
- Google Cloud account with Gemini API access

## 🔧 Setup Instructions

### 1. Get Gemini API Key

1. Go to [Google AI Studio](https://makersuite.google.com/app/apikey)
2. Sign in with your Google account
3. Create a new API key
4. Copy the API key for the next step

### 2. Backend Configuration

1. **Add API Key to Environment**:
   ```bash
   # In backend/.env file, replace the placeholder:
   GEMINI_API_KEY=your_actual_gemini_api_key_here
   ```

2. **Verify Routes**: The following routes have been added automatically:
   ```
   POST /api/gemini/chat
   GET /api/gemini/suggestions
   GET /api/gemini/health
   ```

3. **Test Backend**:
   ```bash
   cd backend
   php artisan serve
   ```

### 3. Frontend Configuration

The frontend components have been automatically integrated:

- ✅ `GeminiChatbot.vue` component created
- ✅ `geminiService.js` service created
- ✅ Component added to `App.vue`
- ✅ Animations and styles included

### 4. Testing the Integration

1. **Start the backend**:
   ```bash
   cd backend
   php artisan serve
   ```

2. **Start the frontend**:
   ```bash
   cd front-end
   npm run dev
   ```

3. **Test the chatbot**:
   - Log in to the platform
   - Look for the floating AI assistant button in the bottom-right corner
   - Click to open the chat window
   - Try asking questions like:
     - "How do I apply for an internship?"
     - "What courses are available?"
     - "How to contact my tutor?"

## 🎨 Customization

### Changing Colors
Edit the CSS variables in `GeminiChatbot.vue`:
```css
/* Change the gradient colors */
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
```

### Adding More Quick Suggestions
Modify the `quickSuggestions` array in the backend `GeminiController.php`:
```php
$suggestions = [
    "Your custom question here",
    "Another helpful question",
    // ... more suggestions
];
```

### Customizing AI Context
Edit the system context in `GeminiController.php`:
```php
$systemContext = "Your custom AI assistant context here...";
```

## 🔍 Troubleshooting

### Common Issues

1. **Chatbot not appearing**:
   - Ensure you're logged in
   - Check browser console for errors
   - Verify the component is imported in `App.vue`

2. **API errors**:
   - Verify Gemini API key is correct
   - Check Laravel logs: `tail -f storage/logs/laravel.log`
   - Ensure routes are properly registered

3. **CORS issues**:
   - Verify CSRF token exclusions are in place
   - Check API base URL in `geminiService.js`

4. **Styling issues**:
   - Ensure FontAwesome icons are loaded
   - Check for CSS conflicts
   - Verify responsive design on mobile

### Debug Mode

Enable debug mode by adding to your `.env`:
```
APP_DEBUG=true
LOG_LEVEL=debug
```

## 📱 Mobile Responsiveness

The chatbot is fully responsive and includes:
- Adaptive sizing for mobile screens
- Touch-friendly buttons
- Optimized animations for mobile performance
- Hidden tooltips on small screens

## 🔒 Security Features

- Authentication required to access chatbot
- Input validation and sanitization
- Rate limiting (can be added if needed)
- CSRF protection exclusions for API routes

## 🚀 Performance Optimizations

- Lazy loading of chat history
- Efficient message rendering
- Optimized animations
- Minimal bundle size impact

## 📊 Analytics (Optional)

To track chatbot usage, you can add analytics events:
```javascript
// In GeminiChatbot.vue methods
analytics.track('chatbot_opened');
analytics.track('message_sent', { message_length: message.length });
```

## 🔄 Updates and Maintenance

- Monitor Gemini API usage and costs
- Update API key if needed
- Review and update quick suggestions based on user feedback
- Monitor error logs for issues

## 📞 Support

If you encounter any issues:
1. Check the troubleshooting section above
2. Review Laravel and browser console logs
3. Verify all setup steps were completed
4. Test with a fresh browser session

---

**Enjoy your new AI-powered virtual assistant! 🤖✨**

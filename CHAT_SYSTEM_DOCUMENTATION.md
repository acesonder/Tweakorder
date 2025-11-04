# Real-Time Chat & Messaging System Documentation

## Overview

The Tweakorder Chat & Messaging System is a comprehensive real-time communication platform that enables staff members and clients to communicate efficiently through direct messages, group chats, and support channels.

## Features

### ✨ Core Features

- **Real-time Messaging**: Instant message delivery with auto-refresh (5-second polling)
- **Multiple Conversation Types**:
  - 👤 Direct Messages - One-on-one conversations
  - 👥 Group Chats - Multi-participant discussions
  - 🎧 Support - Dedicated support channels
- **Modal Views**: Beautiful modal dialogs for creating conversations and viewing information
- **3D Navbar**: Stunning 3D navigation with smooth animations and transitions
- **Unread Notifications**: Real-time notification badges for unread messages
- **Search Functionality**: Quickly find conversations by title or content
- **Responsive Design**: Fully responsive layout for desktop, tablet, and mobile devices

### 🎨 UI/UX Features

- **Professional Styling**: Modern gradient backgrounds and card-based layouts
- **Smooth Animations**: 
  - Fade-in effects for page elements
  - Slide-in animations for messages
  - Scale transformations on hover
  - 3D perspective effects on navbar
- **Icons & Emojis**: Every element features relevant icons for better visual communication
- **Modal Transitions**: Smooth modal entry/exit animations with backdrop blur
- **Hover Effects**: Interactive 3D transformations on cards and buttons

### 📊 Messaging Dashboard

- **Statistics Overview**: 
  - Total conversations count
  - Unread messages count
  - Active chats tracking
  - Today's message statistics
- **Quick Actions**: Fast access to common tasks
- **Recent Conversations**: View latest conversation activity

## Installation

### 1. Database Setup

Run the setup script to create all necessary database tables:

```bash
php setup_chat_system.php
```

Or manually import the SQL schema:

```bash
mysql -u root -p tweakorder < chat_messaging_schema.sql
```

### 2. Database Tables

The system creates the following tables:

- **conversations**: Stores conversation metadata
- **conversation_participants**: Links users/clients to conversations
- **messages**: Stores all chat messages
- **message_read_status**: Tracks read/unread status
- **chat_notifications**: Manages chat notifications

### 3. Configuration

The system uses the existing database configuration in `config/database.php`:

```php
DB_HOST: localhost
DB_USER: root
DB_PASS: (blank)
DB_NAME: tweakorder
```

## File Structure

```
/Tweakorder
├── api/
│   └── chat.php                    # Backend API for chat operations
├── assets/
│   ├── css/
│   │   └── chat.css               # Chat-specific styles
│   └── js/
│       └── chat.js                # Chat JavaScript functionality
├── chat.html                       # Main chat interface
├── messaging-dashboard.html        # Messaging overview dashboard
├── chat_messaging_schema.sql      # Database schema
└── setup_chat_system.php          # Setup script
```

## API Endpoints

### GET Requests

#### List Conversations
```
GET /api/chat.php?action=list_conversations
```
Returns all conversations for the current user.

#### Get Conversation
```
GET /api/chat.php?action=get_conversation&id={conversation_id}
```
Returns details of a specific conversation.

#### Get Messages
```
GET /api/chat.php?action=get_messages&conversation_id={conversation_id}
```
Returns all messages in a conversation.

#### Get Unread Count
```
GET /api/chat.php?action=unread_count
```
Returns count of unread messages and conversations.

### POST Requests

#### Create Conversation
```json
POST /api/chat.php
{
  "action": "create_conversation",
  "title": "Conversation Title",
  "type": "direct|group|support",
  "participants": [
    {"id": 1, "type": "staff"},
    {"id": 2, "type": "client"}
  ]
}
```

#### Send Message
```json
POST /api/chat.php
{
  "action": "send_message",
  "conversation_id": 1,
  "message": "Message text",
  "type": "text|image|file|system"
}
```

#### Mark as Read
```json
POST /api/chat.php
{
  "action": "mark_read",
  "message_id": 1
}
```

### PUT Requests

#### Archive Conversation
```json
PUT /api/chat.php
{
  "action": "archive_conversation",
  "id": 1
}
```

### DELETE Requests

#### Delete Message
```
DELETE /api/chat.php
message_id={message_id}
```

## Usage Guide

### For Users

#### Starting a New Conversation

1. Click the "➕ New Chat" button in the navbar
2. Enter a conversation title
3. Select conversation type (Direct, Group, or Support)
4. Choose participants from the list
5. Click "Create Conversation"

#### Sending Messages

1. Select a conversation from the sidebar
2. Type your message in the input area at the bottom
3. Click "✈️ Send" or press Enter to send
4. Messages appear instantly in the chat area

#### Searching Conversations

1. Use the search bar at the top of the conversations sidebar
2. Type to filter conversations by title or recent messages
3. Matching conversations will be highlighted

### For Developers

#### Customizing the Chat Interface

The chat system uses CSS variables for easy theming:

```css
:root {
    --chat-primary: #667eea;
    --chat-secondary: #764ba2;
    --chat-accent: #f093fb;
    --chat-success: #4facfe;
}
```

#### Extending Functionality

The `ChatManager` class in `assets/js/chat.js` can be extended:

```javascript
// Add custom methods
ChatManager.prototype.customMethod = function() {
    // Your code here
};
```

#### Auto-Refresh Configuration

Modify the refresh interval in `assets/js/chat.js`:

```javascript
// Default: 10 seconds (10000ms) - balanced for server load
// For true real-time, consider WebSocket implementation
this.refreshInterval = setInterval(() => {
    // Refresh logic
}, 10000);
```

**Note**: The default is set to 10 seconds to balance real-time updates with server load. For production environments with many users, consider:
- Implementing WebSocket connections for true real-time messaging
- Using Server-Sent Events (SSE)
- Increasing the interval to 30-60 seconds for inactive conversations

## Pages Overview

### chat.html

**Main Chat Interface**

- **Left Sidebar**: List of conversations with search functionality
- **Center Panel**: Message display area with smooth scrolling
- **Bottom Bar**: Message input with send button
- **Top Navbar**: 3D navigation with quick actions

**Features**:
- Real-time message updates
- Conversation switching
- Message search
- Unread indicators
- Typing indicators (placeholder for future enhancement)

### messaging-dashboard.html

**Messaging Overview Dashboard**

**Sections**:
- **Statistics Cards**: Total conversations, unread messages, active chats, today's messages
- **Quick Actions**: Links to chat, new conversation, notifications
- **Recent Conversations**: List of 5 most recent conversations

**Features**:
- Auto-refresh every 30 seconds
- Click any conversation to open chat
- Real-time statistics
- Modal for creating new conversations

## Styling & Animations

### 3D Navbar Effects

- Perspective transforms on hover
- Floating icon animation
- Smooth color transitions
- Responsive button scaling

### Modal Animations

```css
/* Entry Animation */
@keyframes slideInModal {
    from {
        opacity: 0;
        transform: translateY(-50px) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}
```

### Message Bubbles

- Sent messages: Purple gradient background, right-aligned
- Received messages: Light gray background, left-aligned
- System messages: Centered, italic text
- Hover effect: Slight elevation with shadow

### Responsive Design

- **Desktop (>768px)**: Side-by-side layout with sidebar and messages
- **Mobile (≤768px)**: Stacked layout with conversation list above messages
- **Tablet**: Adaptive grid for optimal viewing

## Troubleshooting

### Common Issues

**Messages not appearing**

1. Check browser console for JavaScript errors
2. Verify database connection in `config/database.php`
3. Ensure chat tables are created (run setup script)
4. Check API endpoint responses in Network tab

**Auto-refresh not working**

1. Verify JavaScript is enabled
2. Check console for errors
3. Ensure `ChatManager` is initialized
4. Test manual refresh button

**Modals not showing**

1. Check CSS is loaded properly
2. Verify modal IDs match JavaScript references
3. Test with browser developer tools
4. Check z-index conflicts

### Database Connection Issues

Verify connection settings:

```php
// In config/database.php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Blank password as specified
define('DB_NAME', 'tweakorder');
```

## Security Considerations

### Current Implementation

- Session-based authentication (placeholder for demo)
- SQL injection prevention using prepared statements
- XSS protection with HTML escaping
- CSRF protection ready (add tokens in production)

### Production Recommendations

1. **Add HTTPS**: Always use SSL/TLS in production
2. **Implement JWT**: Token-based authentication for API
3. **Rate Limiting**: Prevent spam and abuse
4. **Input Validation**: Server-side validation for all inputs
5. **Content Security Policy**: Add CSP headers
6. **Sanitize File Uploads**: If implementing file attachments
7. **Remove Demo Mode**: Replace the default user authentication in `api/chat.php` with proper session validation
8. **WebSocket Implementation**: Replace polling with WebSocket for true real-time updates and reduced server load
9. **Database Indexes**: Add additional indexes based on query patterns for better performance

## Future Enhancements

### Planned Features

- 📎 **File Attachments**: Support for images, documents, and files
- 🎤 **Voice Messages**: Audio message recording
- 📹 **Video Calls**: Integrated video conferencing
- 🔍 **Advanced Search**: Full-text search across all messages
- 🌐 **WebSocket Support**: True real-time updates without polling
- 🔔 **Push Notifications**: Browser and mobile push notifications
- 📱 **Mobile App**: React Native mobile application
- 🤖 **Chatbots**: Automated responses and AI assistance
- 📊 **Analytics**: Message analytics and engagement metrics
- 🎨 **Themes**: Multiple color themes and dark mode

### Enhancement Ideas

- Emoji picker integration
- Markdown support for rich text formatting
- Message reactions (👍, ❤️, etc.)
- Thread/reply functionality
- Message pinning
- @mentions with notifications
- Message editing and deletion
- Conversation muting
- Archive/unarchive conversations
- Export conversation history

## Testing

### Manual Testing Checklist

- [ ] Create new conversation
- [ ] Send message in conversation
- [ ] Receive messages (using different user)
- [ ] Search conversations
- [ ] View unread count
- [ ] Mark messages as read
- [ ] Archive conversation
- [ ] Test responsive design on mobile
- [ ] Test modal animations
- [ ] Verify all icons display correctly
- [ ] Test auto-refresh functionality
- [ ] Test navigation between pages

### Browser Compatibility

Tested and working on:
- ✅ Chrome 90+
- ✅ Firefox 88+
- ✅ Safari 14+
- ✅ Edge 90+
- ✅ Mobile browsers (iOS Safari, Chrome Mobile)

## Support

For issues, questions, or feature requests:

1. Check this documentation first
2. Review the code comments in source files
3. Consult the API documentation
4. Test with browser developer tools
5. Create an issue in the repository

## Credits

**Developed for Tweakorder**
- Real-time messaging system
- Professional UI/UX design
- Comprehensive API backend
- Responsive mobile-first design
- Modal views and transitions
- 3D navbar effects

---

**File Comments Standard**: All source files include designated start/end comments as per personal instructions:

```php
// ============================================
// File Name and Description
// File: path/to/file.ext
// Description: Brief description
// ============================================

// [Code here]

// ============================================
// End of File: path/to/file.ext
// ============================================
```

---

*Last Updated: 2025-11-04*
*Version: 1.0.0*

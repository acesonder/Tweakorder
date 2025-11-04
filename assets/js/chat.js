// ============================================
// Chat & Messaging System JavaScript
// File: assets/js/chat.js
// Description: Handles real-time chat functionality and UI interactions
// ============================================

class ChatManager {
    constructor() {
        this.currentConversationId = null;
        this.currentUser = null;
        this.conversations = [];
        this.messages = [];
        this.refreshInterval = null;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadConversations();
        this.startAutoRefresh();
        this.loadUnreadCount();
    }

    setupEventListeners() {
        // Send message
        const sendBtn = document.getElementById('sendMessageBtn');
        const messageInput = document.getElementById('messageInput');
        
        if (sendBtn) {
            sendBtn.addEventListener('click', () => this.sendMessage());
        }
        
        if (messageInput) {
            messageInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                }
            });
        }

        // New conversation button
        const newConversationBtn = document.getElementById('newConversationBtn');
        if (newConversationBtn) {
            newConversationBtn.addEventListener('click', () => this.openNewConversationModal());
        }

        // Search conversations
        const searchInput = document.getElementById('searchConversation');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => this.searchConversations(e.target.value));
        }

        // Refresh button
        const refreshBtn = document.getElementById('refreshBtn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => {
                this.loadConversations();
                if (this.currentConversationId) {
                    this.loadMessages(this.currentConversationId);
                }
            });
        }
    }

    async loadConversations() {
        try {
            const response = await fetch('/api/chat.php?action=list_conversations');
            const data = await response.json();
            
            if (data.success) {
                this.conversations = data.conversations;
                this.renderConversations();
            } else {
                this.showError('Failed to load conversations');
            }
        } catch (error) {
            console.error('Error loading conversations:', error);
            this.showError('Error loading conversations');
        }
    }

    renderConversations() {
        const container = document.getElementById('conversationsList');
        if (!container) return;

        if (this.conversations.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <div class="empty-state-icon">💬</div>
                    <h3>No conversations yet</h3>
                    <p>Start a new conversation to get started</p>
                </div>
            `;
            return;
        }

        container.innerHTML = this.conversations.map(conv => {
            const lastMessageTime = conv.last_message_time ? 
                this.formatTime(conv.last_message_time) : '';
            const isActive = conv.id === this.currentConversationId ? 'active' : '';
            
            return `
                <div class="conversation-item ${isActive}" onclick="chatManager.selectConversation(${conv.id})">
                    <div class="conversation-header">
                        <span class="conversation-title">
                            ${this.getConversationIcon(conv.type)} ${conv.title || 'Conversation'}
                        </span>
                        <span class="conversation-time">${lastMessageTime}</span>
                    </div>
                    <div class="conversation-preview">
                        ${conv.last_message || 'No messages yet'}
                    </div>
                </div>
            `;
        }).join('');
    }

    getConversationIcon(type) {
        const icons = {
            'direct': '👤',
            'group': '👥',
            'support': '🎧'
        };
        return icons[type] || '💬';
    }

    async selectConversation(conversationId) {
        this.currentConversationId = conversationId;
        this.renderConversations(); // Re-render to show active state
        await this.loadMessages(conversationId);
        
        // Update header
        const conversation = this.conversations.find(c => c.id === conversationId);
        if (conversation) {
            this.updateChatHeader(conversation);
        }
    }

    updateChatHeader(conversation) {
        const titleEl = document.getElementById('chatTitle');
        const statusEl = document.getElementById('chatStatus');
        
        if (titleEl) {
            titleEl.innerHTML = `${this.getConversationIcon(conversation.type)} ${conversation.title || 'Conversation'}`;
        }
        
        if (statusEl) {
            const participantCount = conversation.participants ? conversation.participants.length : 0;
            statusEl.textContent = `${participantCount} participant${participantCount !== 1 ? 's' : ''}`;
        }
    }

    async loadMessages(conversationId) {
        try {
            const response = await fetch(`/api/chat.php?action=get_messages&conversation_id=${conversationId}`);
            const data = await response.json();
            
            if (data.success) {
                this.messages = data.messages;
                this.renderMessages();
                this.scrollToBottom();
                this.markMessagesAsRead();
            } else {
                this.showError('Failed to load messages');
            }
        } catch (error) {
            console.error('Error loading messages:', error);
            this.showError('Error loading messages');
        }
    }

    renderMessages() {
        const container = document.getElementById('messagesContainer');
        if (!container) return;

        if (this.messages.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <div class="empty-state-icon">📭</div>
                    <h3>No messages yet</h3>
                    <p>Send a message to start the conversation</p>
                </div>
            `;
            return;
        }

        container.innerHTML = this.messages.map(msg => {
            if (msg.message_type === 'system') {
                return `
                    <div class="message-system">
                        ${msg.message_text}
                    </div>
                `;
            }

            const isSent = msg.sender_type === 'staff'; // Simplified - should check against current user
            const bubbleClass = isSent ? 'message-sent' : 'message-received';
            
            return `
                <div class="message-group">
                    <div class="message-bubble ${bubbleClass}">
                        ${!isSent ? `<div class="message-sender">${msg.sender_name}</div>` : ''}
                        <div class="message-text">${this.escapeHtml(msg.message_text)}</div>
                        <div class="message-time">${this.formatTime(msg.created_at)}</div>
                    </div>
                </div>
            `;
        }).join('');
    }

    async sendMessage() {
        const input = document.getElementById('messageInput');
        if (!input || !input.value.trim()) return;
        
        if (!this.currentConversationId) {
            this.showError('Please select a conversation first');
            return;
        }

        const message = input.value.trim();
        
        try {
            const response = await fetch('/api/chat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'send_message',
                    conversation_id: this.currentConversationId,
                    message: message,
                    type: 'text'
                })
            });

            const data = await response.json();
            
            if (data.success) {
                input.value = '';
                await this.loadMessages(this.currentConversationId);
                await this.loadConversations(); // Update conversation list
            } else {
                this.showError(data.message || 'Failed to send message');
            }
        } catch (error) {
            console.error('Error sending message:', error);
            this.showError('Error sending message');
        }
    }

    async markMessagesAsRead() {
        if (!this.currentConversationId || this.messages.length === 0) return;

        // Mark the last message as read
        const lastMessage = this.messages[this.messages.length - 1];
        
        try {
            await fetch('/api/chat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'mark_read',
                    message_id: lastMessage.id
                })
            });
            
            this.loadUnreadCount();
        } catch (error) {
            console.error('Error marking messages as read:', error);
        }
    }

    async loadUnreadCount() {
        try {
            const response = await fetch('/api/chat.php?action=unread_count');
            const data = await response.json();
            
            if (data.success) {
                this.updateUnreadBadge(data.data.unread_messages);
            }
        } catch (error) {
            console.error('Error loading unread count:', error);
        }
    }

    updateUnreadBadge(count) {
        const badges = document.querySelectorAll('.notification-badge');
        badges.forEach(badge => {
            if (count > 0) {
                badge.textContent = count;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        });
    }

    openNewConversationModal() {
        const modal = document.getElementById('newConversationModal');
        if (modal) {
            modal.classList.add('show');
            this.loadAvailableParticipants();
        }
    }

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('show');
        }
    }

    async loadAvailableParticipants() {
        // In a real implementation, this would fetch users and clients from the API
        const participantsList = document.getElementById('participantsList');
        if (!participantsList) return;

        // Demo data
        const participants = [
            { id: 1, name: 'Admin User', type: 'staff' },
            { id: 2, name: 'Manager User', type: 'staff' },
            { id: 3, name: 'Worker User', type: 'staff' }
        ];

        participantsList.innerHTML = participants.map(p => `
            <div class="participant-item" onclick="chatManager.toggleParticipant(${p.id}, '${p.type}', this)">
                <input type="checkbox" class="participant-checkbox" data-id="${p.id}" data-type="${p.type}">
                <span>${p.name} (${p.type})</span>
            </div>
        `).join('');
    }

    toggleParticipant(id, type, element) {
        element.classList.toggle('selected');
        const checkbox = element.querySelector('.participant-checkbox');
        checkbox.checked = !checkbox.checked;
    }

    async createConversation() {
        const titleInput = document.getElementById('conversationTitle');
        const typeSelect = document.getElementById('conversationType');
        
        if (!titleInput || !typeSelect) return;

        const title = titleInput.value.trim();
        const type = typeSelect.value;

        if (!title) {
            this.showError('Please enter a conversation title');
            return;
        }

        // Get selected participants
        const selectedCheckboxes = document.querySelectorAll('.participant-checkbox:checked');
        const participants = Array.from(selectedCheckboxes).map(cb => ({
            id: parseInt(cb.dataset.id),
            type: cb.dataset.type
        }));

        try {
            const response = await fetch('/api/chat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'create_conversation',
                    title: title,
                    type: type,
                    participants: participants
                })
            });

            const data = await response.json();
            
            if (data.success) {
                this.closeModal('newConversationModal');
                this.showSuccess('Conversation created successfully');
                await this.loadConversations();
                this.selectConversation(data.id);
            } else {
                this.showError(data.message || 'Failed to create conversation');
            }
        } catch (error) {
            console.error('Error creating conversation:', error);
            this.showError('Error creating conversation');
        }
    }

    searchConversations(query) {
        const items = document.querySelectorAll('.conversation-item');
        const searchTerm = query.toLowerCase();

        items.forEach(item => {
            const title = item.querySelector('.conversation-title').textContent.toLowerCase();
            const preview = item.querySelector('.conversation-preview').textContent.toLowerCase();
            
            if (title.includes(searchTerm) || preview.includes(searchTerm)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }

    scrollToBottom() {
        const container = document.getElementById('messagesContainer');
        if (container) {
            setTimeout(() => {
                container.scrollTop = container.scrollHeight;
            }, 100);
        }
    }

    startAutoRefresh() {
        // Refresh messages every 5 seconds if a conversation is selected
        this.refreshInterval = setInterval(() => {
            if (this.currentConversationId) {
                this.loadMessages(this.currentConversationId);
            }
            this.loadUnreadCount();
        }, 5000);
    }

    stopAutoRefresh() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
        }
    }

    formatTime(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diff = now - date;
        
        // Less than 1 minute
        if (diff < 60000) {
            return 'Just now';
        }
        
        // Less than 1 hour
        if (diff < 3600000) {
            const minutes = Math.floor(diff / 60000);
            return `${minutes}m ago`;
        }
        
        // Less than 24 hours
        if (diff < 86400000) {
            const hours = Math.floor(diff / 3600000);
            return `${hours}h ago`;
        }
        
        // Less than 7 days
        if (diff < 604800000) {
            const days = Math.floor(diff / 86400000);
            return `${days}d ago`;
        }
        
        // Format as date
        return date.toLocaleDateString();
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    showError(message) {
        this.showNotification(message, 'error');
    }

    showSuccess(message) {
        this.showNotification(message, 'success');
    }

    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            background: ${type === 'error' ? '#f5576c' : '#4facfe'};
            color: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            z-index: 9999;
            animation: slideInRight 0.3s ease-out;
        `;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.3s ease-out';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
}

// Initialize chat manager when DOM is loaded
let chatManager;
document.addEventListener('DOMContentLoaded', () => {
    chatManager = new ChatManager();
});

// Add CSS animations for notifications
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// ============================================
// End of File: assets/js/chat.js
// ============================================

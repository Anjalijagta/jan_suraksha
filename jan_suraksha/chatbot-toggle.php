<?php
// chatbot-toggle.php - COMPLETE WORKING FLOATING CHATBOT (NAVBAR FIXED)
include 'config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<!-- Floating Chatbot Toggle Button -->
<button class="chat-toggle-btn" id="chatToggleBtn" title="Chat with Assistant">
    <i class="bi bi-robot"></i>
</button>

<!-- Floating Chatbot Container -->
<div class="floating-chat-container" id="floatingChatContainer">
    <div class="chat-header">
        <i class="bi bi-robot"></i>
        <span>Jan Suraksha Assistant</span>
        <span class="badge bg-success ms-auto">Online</span>
        <button class="chat-close-btn" id="chatCloseBtn" title="Close">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    
    <div class="chat-messages" id="chatMessages">
        <div class="message bot">
            <div class="message-avatar">🤖</div>
            <div class="message-content">
                Hello! I'm the Jan Suraksha Assistant. How can I help you today?
                <ul class="mb-0 mt-2">
                    <li>• Filing a complaint</li>
                    <li>• Tracking case status</li>
                    <li>• Account registration</li>
                    <li>• Safety guidelines</li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="typing-indicator" id="typingIndicator">
        <div class="typing-avatar">🤖</div>
        <div class="typing-dots">
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
            <div class="typing-dot"></div>
        </div>
    </div>
    
    <div class="chat-input-container">
        <input type="text" class="chat-input" id="chatInput" placeholder="Type your message..." maxlength="500">
        <button class="send-btn" id="sendBtn">
            <i class="bi bi-send-fill"></i>
        </button>
    </div>
</div>

<style>
:root {
    --color-primary: #2563eb;
    --color-bg: #f8fafc;
}

body {
    background-color: var(--color-bg);
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    margin: 0;
    padding: 0;
}

/* === FIXED TOGGLE BUTTON - ABOVE NAVBAR === */
.chat-toggle-btn {
    position: fixed;
    bottom: 25px;      /* Reduced base spacing */
    right: 25px;       /* Reduced right spacing */
    width: 70px;       /* Bigger button */
    height: 70px;      /* Bigger button */
    border-radius: 50%;
    background: var(--color-primary);
    color: white;
    border: none;
    box-shadow: 0 12px 35px rgba(37, 99, 235, 0.6);
    cursor: pointer;
    z-index: 99999 !important; /* MAX z-index */
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    font-size: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.chat-toggle-btn:hover {
    transform: scale(1.08) rotate(360deg);
    box-shadow: 0 16px 45px rgba(37, 99, 235, 0.7);
}

/* === FIXED CHAT CONTAINER === */
.floating-chat-container {
    position: fixed;
    bottom: 110px;     /* Perfect navbar clearance */
    right: 25px;
    width: 380px;
    max-height: 500px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 25px 60px rgba(0,0,0,0.3);
    z-index: 100000 !important;
    opacity: 0;
    transform: translateY(30px) scale(0.9);
    transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.2);
    display: none;
}

.floating-chat-container.active {
    opacity: 1;
    transform: translateY(0) scale(1);
    display: block !important;
}

/* === HEADER === */
.chat-header {
    background: linear-gradient(135deg, var(--color-primary), #1d4ed8);
    color: white;
    padding: 18px 20px;
    font-weight: 600;
    font-size: 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    position: relative;
}

.chat-header .badge {
    font-size: 0.75em;
    padding: 4px 8px;
}

.chat-close-btn {
    margin-left: auto !important;
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    transition: all 0.3s ease;
}

.chat-close-btn:hover {
    background: rgba(255,255,255,0.35);
    transform: rotate(90deg) scale(1.1);
}

/* === MESSAGES === */
.chat-messages {
    height: 360px;
    overflow-y: auto;
    padding: 20px;
    background: #f8fafc;
    scroll-behavior: smooth;
}

.message {
    margin-bottom: 16px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from { opacity: 0; transform: translateX(20px); }
    to { opacity: 1; transform: translateX(0); }
}

.message.user { justify-content: flex-end; }
.message.bot { justify-content: flex-start; }

.message-content {
    max-width: 75%;
    padding: 12px 16px;
    border-radius: 18px;
    word-wrap: break-word;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    line-height: 1.5;
    font-size: 14.5px;
}

.message.user .message-content {
    background: var(--color-primary);
    color: white;
    border-bottom-right-radius: 6px;
}

.message.bot .message-content {
    background: white;
    color: #374151;
    border: 1px solid #e5e7eb;
    border-bottom-left-radius: 6px;
}

.message-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: bold;
    flex-shrink: 0;
}

.message.user .message-avatar {
    background: var(--color-primary);
    color: white;
}

.message.bot .message-avatar {
    background: #6b7280;
    color: white;
}

/* === TYPING INDICATOR === */
.typing-indicator {
    display: none;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    margin: 0 20px 16px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 18px;
    border-bottom-left-radius: 6px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.typing-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #6b7280;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
}

.typing-dots {
    display: flex;
    gap: 4px;
}

.typing-dot {
    width: 8px;
    height: 8px;
    background: #9ca3af;
    border-radius: 50%;
    animation: typing 1.4s infinite ease-in-out;
}

.typing-dot:nth-child(2) { animation-delay: 0.2s; }
.typing-dot:nth-child(3) { animation-delay: 0.4s; }

@keyframes typing {
    0%, 60%, 100% { transform: translateY(0); }
    30% { transform: translateY(-8px); }
}

/* === INPUT === */
.chat-input-container {
    padding: 18px 20px;
    background: white;
    border-top: 1px solid #e5e7eb;
    display: flex;
    gap: 12px;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
}

.chat-input {
    flex: 1;
    border: 2px solid #e5e7eb;
    border-radius: 25px;
    padding: 12px 20px;
    outline: none;
    font-size: 14px;
    transition: all 0.3s ease;
}

.chat-input:focus {
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.chat-input:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.send-btn {
    background: var(--color-primary);
    color: white;
    border: none;
    border-radius: 50%;
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.send-btn:hover:not(:disabled) {
    background: #1d4ed8;
    transform: scale(1.05);
}

.send-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* === RESPONSIVE === */
@media (max-width: 768px) {
    .chat-toggle-btn {
        bottom: 20px;
        right: 20px;
        width: 65px;
        height: 65px;
    }
    
    .floating-chat-container {
        bottom: 95px;
        width: 92vw;
        max-width: 400px;
        right: 4vw;
        left: 4vw;
        margin: 0 auto;
        max-height: 70vh;
    }
    
    .chat-messages {
        height: 300px !important;
    }
}
</style>

<script>
class JanSurakshaChatbot {
    constructor() {
        this.chatToggleBtn = document.getElementById('chatToggleBtn');
        this.chatContainer = document.getElementById('floatingChatContainer');
        this.chatCloseBtn = document.getElementById('chatCloseBtn');
        this.chatMessages = document.getElementById('chatMessages');
        this.chatInput = document.getElementById('chatInput');
        this.sendBtn = document.getElementById('sendBtn');
        this.typingIndicator = document.getElementById('typingIndicator');
        
        // Initialize container as hidden
        this.chatContainer.style.display = 'none';
        
        this.responses = {
            greeting: [
                "Hello! How can I assist you with Jan Suraksha today?",
                "Hi there! What can I help you with?",
                "Welcome! I'm here to help with any questions."
            ],
            complaint: [
                "To file a complaint, click 'File a Complaint' on our homepage. You'll need incident details and evidence.",
                "Filing takes ~5 minutes. Visit homepage → 'File a Complaint' → Fill form.",
                "Easy process! Homepage → 'File a Complaint' → Enter details."
            ],
            track: [
                "Track status using your Case ID. Homepage → 'Check Complaint Status'.",
                "Use Case ID on 'Check Complaint Status' page for real-time updates.",
                "24/7 tracking available with your Case ID."
            ],
            register: [
                "Register: Homepage → 'Login' → 'Register Now'. Need name, email, phone.",
                "Create account for easy filing/tracking. Homepage → Login → Register.",
                "Account makes complaint management easier!"
            ],
            safety: [
                "Safety guidelines in 'Public Awareness' section on homepage.",
                "Cyber safety & women's safety tips in Public Awareness section.",
                "Visit 'Public Awareness' for comprehensive safety guidelines."
            ],
            emergency: [
                "🚨 EMERGENCY: Call 100 (Police) immediately! This is for non-urgent queries only.",
                "For emergencies, contact local police/emergency services right away.",
                "Urgent? Call emergency services. This system is for non-emergency use."
            ],
            default: [
                "I'll help you find the right section or contact support if needed.",
                "Check our homepage sections or use 'Contact' page for specific help.",
                "Not sure? Try asking about complaints, tracking, or registration."
            ]
        };
        
        this.init();
    }
    
    init() {
        this.chatToggleBtn.addEventListener('click', () => this.toggleChat());
        this.chatCloseBtn.addEventListener('click', () => this.closeChat());
        this.sendBtn.addEventListener('click', () => this.sendMessage());
        this.chatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });
        
        this.chatInput.addEventListener('focus', () => {
            this.scrollToBottom();
        });
    }
    
    toggleChat() {
        this.chatContainer.style.display = 'block';
        setTimeout(() => this.chatContainer.classList.add('active'), 10);
        this.chatToggleBtn.style.opacity = '0';
        setTimeout(() => {
            this.chatToggleBtn.style.display = 'none';
        }, 300);
        setTimeout(() => this.chatInput.focus(), 400);
    }
    
    closeChat() {
        this.chatContainer.classList.remove('active');
        setTimeout(() => {
            this.chatContainer.style.display = 'none';
            this.chatToggleBtn.style.display = 'flex';
            this.chatToggleBtn.style.opacity = '1';
        }, 300);
    }
    
    sendMessage() {
        const message = this.chatInput.value.trim();
        if (!message) return;
        
        this.chatInput.disabled = true;
        this.sendBtn.disabled = true;
        
        this.addMessage(message, 'user');
        this.chatInput.value = '';
        
        this.showTyping();
        
        setTimeout(() => {
            this.hideTyping();
            const response = this.generateResponse(message);
            this.addMessage(response, 'bot');
            
            this.chatInput.disabled = false;
            this.sendBtn.disabled = false;
            this.chatInput.focus();
        }, 1200 + Math.random() * 800);
    }
    
    addMessage(content, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${sender}`;
        
        const avatar = document.createElement('div');
        avatar.className = 'message-avatar';
        avatar.textContent = sender === 'user' ? '👤' : '🤖';
        
        const messageContent = document.createElement('div');
        messageContent.className = 'message-content';
        messageContent.textContent = content;
        
        messageDiv.appendChild(avatar);
        messageDiv.appendChild(messageContent);
        this.chatMessages.appendChild(messageDiv);
        this.scrollToBottom();
    }
    
    generateResponse(message) {
        const lowerMessage = message.toLowerCase();
        
        if (lowerMessage.includes('hello') || lowerMessage.includes('hi') || lowerMessage.includes('hey')) {
            return this.getRandomResponse('greeting');
        } else if (lowerMessage.includes('complaint') || lowerMessage.includes('file') || lowerMessage.includes('report')) {
            return this.getRandomResponse('complaint');
        } else if (lowerMessage.includes('track') || lowerMessage.includes('status') || lowerMessage.includes('case')) {
            return this.getRandomResponse('track');
        } else if (lowerMessage.includes('register') || lowerMessage.includes('account') || lowerMessage.includes('sign') || lowerMessage.includes('login')) {
            return this.getRandomResponse('register');
        } else if (lowerMessage.includes('safety') || lowerMessage.includes('guide') || lowerMessage.includes('tip')) {
            return this.getRandomResponse('safety');
        } else if (lowerMessage.includes('emergency') || lowerMessage.includes('urgent')) {
            return this.getRandomResponse('emergency');
        } else {
            return this.getRandomResponse('default');
        }
    }
    
    getRandomResponse(category) {
        const responses = this.responses[category];
        return responses[Math.floor(Math.random() * responses.length)];
    }
    
    showTyping() {
        this.typingIndicator.style.display = 'flex';
        this.scrollToBottom();
    }
    
    hideTyping() {
        this.typingIndicator.style.display = 'none';
    }
    
    scrollToBottom() {
        this.chatMessages.scrollTop = this.chatMessages.scrollHeight;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new JanSurakshaChatbot();
});
</script>

</body>
</html>

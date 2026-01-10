<?php
// help.php

// Header already does config.php + session_start()
include 'header.php';
?>

<style>
/* For pages with custom backgrounds, override body background */
body {
    background-color: var(--color-bg) !important;
    background-image: var(--custom-bg, none) !important;
}

/* Update hardcoded colors to use CSS vars */
.text-primary { color: var(--color-primary) !important; }
.btn-primary { 
    background-color: var(--color-primary); 
    border-color: var(--color-primary); 
}
.btn-primary:hover {
    background-color: color-mix(in srgb, var(--color-primary) 90%, black);
    border-color: color-mix(in srgb, var(--color-primary) 80%, black);
}

/* Chatbot specific styles */
.chat-container {
    max-width: 800px;
    margin: 0 auto;
    height: 600px;
    border: 1px solid #dee2e6;
    border-radius: 10px;
    overflow: hidden;
    background: white;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.chat-header {
    background: var(--color-primary);
    color: white;
    padding: 15px 20px;
    font-weight: bold;
    display: flex;
    align-items: center;
    gap: 10px;
}

.chat-messages {
    height: 450px;
    overflow-y: auto;
    padding: 20px;
    background: #f8f9fa;
}

.message {
    margin-bottom: 15px;
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.message.user {
    justify-content: flex-end;
}

.message.bot {
    justify-content: flex-start;
}

.message-content {
    max-width: 70%;
    padding: 10px 15px;
    border-radius: 15px;
    word-wrap: break-word;
}

.message.user .message-content {
    background: var(--color-primary);
    color: white;
    border-bottom-right-radius: 5px;
}

.message.bot .message-content {
    background: white;
    color: #333;
    border: 1px solid #dee2e6;
    border-bottom-left-radius: 5px;
}

.message-avatar {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: bold;
}

.message.user .message-avatar {
    background: var(--color-primary);
    color: white;
}

.message.bot .message-avatar {
    background: #6c757d;
    color: white;
}

.chat-input-container {
    padding: 15px 20px;
    background: white;
    border-top: 1px solid #dee2e6;
    display: flex;
    gap: 10px;
}

.chat-input {
    flex: 1;
    border: 1px solid #dee2e6;
    border-radius: 20px;
    padding: 10px 15px;
    outline: none;
}

.chat-input:focus {
    border-color: var(--color-primary);
}

.send-btn {
    background: var(--color-primary);
    color: white;
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background-color 0.3s;
}

.send-btn:hover {
    background: color-mix(in srgb, var(--color-primary) 90%, black);
}

.typing-indicator {
    display: none;
    align-items: center;
    gap: 5px;
    padding: 10px 15px;
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 15px;
    border-bottom-left-radius: 5px;
    max-width: 70%;
}

.typing-dot {
    width: 8px;
    height: 8px;
    background: #6c757d;
    border-radius: 50%;
    animation: typing 1.4s infinite;
}

.typing-dot:nth-child(2) {
    animation-delay: 0.2s;
}

.typing-dot:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typing {
    0%, 60%, 100% {
        transform: translateY(0);
    }
    30% {
        transform: translateY(-10px);
    }
}

/* Responsive design */
@media (max-width: 768px) {
    .chat-container {
        height: 500px;
        margin: 0 15px;
    }
    
    .chat-messages {
        height: 350px;
    }
    
    .message-content {
        max-width: 85%;
    }
}
</style>

<header class="hero-section">
    <div class="container">
        <h1 class="display-4 fw-bold">Help & Support</h1>
        <p class="lead col-lg-8 mx-auto">
            Get instant assistance with our chatbot. We're here to help you with any questions about our services.
        </p>
    </div>
</header>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="chat-container">
                    <div class="chat-header">
                        <i class="bi bi-robot"></i>
                        <span>Jan Suraksha Assistant</span>
                        <span class="ms-auto badge bg-success">Online</span>
                    </div>
                    
                    <div class="chat-messages" id="chatMessages">
                        <div class="message bot">
                            <div class="message-avatar">🤖</div>
                            <div class="message-content">
                                Hello! I'm the Jan Suraksha Assistant. How can I help you today? You can ask me about:
                                <ul class="mb-0 mt-2">
                                    <li>Filing a complaint</li>
                                    <li>Tracking your case status</li>
                                    <li>Account registration</li>
                                    <li>Safety guidelines</li>
                                    <li>General information about our services</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="typing-indicator" id="typingIndicator">
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    </div>
                    
                    <div class="chat-input-container">
                        <input type="text" class="chat-input" id="chatInput" placeholder="Type your message here..." maxlength="500">
                        <button class="send-btn" id="sendBtn">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <p class="text-muted">
                        <i class="bi bi-info-circle"></i> 
                        This chatbot provides general information. For urgent matters, please contact your local police station.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Chatbot functionality
class Chatbot {
    constructor() {
        this.chatMessages = document.getElementById('chatMessages');
        this.chatInput = document.getElementById('chatInput');
        this.sendBtn = document.getElementById('sendBtn');
        this.typingIndicator = document.getElementById('typingIndicator');
        
        this.responses = {
            greeting: [
                "Hello! How can I assist you with Jan Suraksha today?",
                "Hi there! What can I help you with?",
                "Welcome! I'm here to help with any questions you have."
            ],
            complaint: [
                "To file a complaint, click on the 'File a Complaint' button on our homepage. You'll need to provide details about the incident, your contact information, and any supporting evidence.",
                "You can file a complaint by visiting our homepage and clicking 'File a Complaint'. The process takes about 5 minutes.",
                "Filing a complaint is easy! Go to our homepage, click 'File a Complaint', and fill out the form with all relevant details about the incident."
            ],
            track: [
                "To track your complaint status, go to our homepage and click 'Check Complaint Status'. You'll need your Case ID to get real-time updates.",
                "You can track your complaint using the unique Case ID provided when you filed your complaint. Visit our homepage and click 'Check Complaint Status'.",
                "Complaint tracking is available 24/7. Use your Case ID on the 'Check Complaint Status' page to see the latest updates."
            ],
            register: [
                "To register for an account, click 'Login' on our homepage and then 'Register Now'. You'll need to provide basic information like your name, email, and phone number.",
                "Registration is simple! Click 'Login' on the homepage, then 'Register Now'. Fill in your details to create an account for filing and tracking complaints.",
                "Create an account by clicking 'Login' → 'Register Now' on our homepage. Having an account makes it easier to file complaints and track their status."
            ],
            safety: [
                "For safety guidelines, check our 'Public Awareness' section on the homepage. We have tips for cyber safety, women's safety, and general crime prevention.",
                "Our homepage features a 'Public Awareness' section with comprehensive safety guidelines, including cyber crime prevention and women's safety tips.",
                "Safety is our priority! Visit the 'Public Awareness' section on our homepage for detailed guidelines on staying safe and preventing crimes."
            ],
            emergency: [
                "For emergency situations, please call your local emergency number (like 100 for police in India) immediately. This chatbot is for non-urgent inquiries only.",
                "If this is an emergency, please contact your local police station or emergency services right away. Don't use this platform for urgent matters.",
                "Emergency situations require immediate action. Please call your local emergency number or go to the nearest police station. This system is for non-urgent complaints."
            ],
            contact: [
                "You can reach us through the 'Contact' page on our website. We typically respond within 24-48 hours for non-urgent matters.",
                "For additional support, visit our 'Contact' page. We're available to help with any questions or concerns you may have.",
                "Check our 'Contact' page for ways to reach our support team. We're here to assist you!"
            ],
            default: [
                "It looks like I cannot solve your problem at the moment. For additional information, please visit the relevant section on our website or contact us directly via the contact page.",
                "It looks like I cannot solve your problem at the moment. For specific assistance with this matter, you might want to check our homepage or contact our support team."
            ]
        };
        
        this.init();
    }
    
    init() {
        this.sendBtn.addEventListener('click', () => this.sendMessage());
        this.chatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.sendMessage();
            }
        });
    }
    
    sendMessage() {
        const message = this.chatInput.value.trim();
        if (!message) return;
        
        // Add user message
        this.addMessage(message, 'user');
        this.chatInput.value = '';
        
        // Show typing indicator
        this.showTyping();
        
        // Simulate bot response delay
        setTimeout(() => {
            this.hideTyping();
            const response = this.generateResponse(message);
            this.addMessage(response, 'bot');
        }, 1000 + Math.random() * 1000);
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
        
        // Check for keywords and return appropriate response
        if (lowerMessage.includes('hello') || lowerMessage.includes('hi') || lowerMessage.includes('hey')) {
            return this.getRandomResponse('greeting');
        } else if (lowerMessage.includes('complaint') || lowerMessage.includes('file') || lowerMessage.includes('report')) {
            return this.getRandomResponse('complaint');
        } else if (lowerMessage.includes('track') || lowerMessage.includes('status') || lowerMessage.includes('case')) {
            return this.getRandomResponse('track');
        } else if (lowerMessage.includes('register') || lowerMessage.includes('account') || lowerMessage.includes('sign up') || lowerMessage.includes('login') || lowerMessage.includes('log')) {
            return this.getRandomResponse('register');
        } else if (lowerMessage.includes('safety') || lowerMessage.includes('guideline') || lowerMessage.includes('tips')) {
            return this.getRandomResponse('safety');
        } else if (lowerMessage.includes('emergency') || lowerMessage.includes('urgent') || lowerMessage.includes('help now')) {
            return this.getRandomResponse('emergency');
        } else if (lowerMessage.includes('contact') || lowerMessage.includes('reach') || lowerMessage.includes('support')) {
            return this.getRandomResponse('contact');
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

// Initialize chatbot when page loads
document.addEventListener('DOMContentLoaded', () => {
    new Chatbot();
});
</script>

<?php
// closes </main>, outputs footer + scripts
include 'footer.php';
?>

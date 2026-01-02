<!-- Chatbot Widget -->
<div id="chatbot-widget" class="fixed bottom-6 right-6 z-50 font-sans" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
    <!-- Chat Window -->
    <div id="chatbot-window" class="hidden mb-4 w-[360px] max-w-[calc(100vw-3rem)] bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden transform transition-all duration-300 scale-95 opacity-0">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-sm">Promo Alu Plus</h3>
                    <p class="text-xs text-blue-100">
                        <span class="inline-block w-2 h-2 bg-green-400 rounded-full mr-1 animate-pulse"></span>
                        {{ app()->getLocale() === 'ar' ? 'متصل الآن' : (app()->getLocale() === 'en' ? 'Online now' : 'En ligne') }}
                    </p>
                </div>
            </div>
            <button onclick="toggleChatbot()" class="p-1 hover:bg-white/20 rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Messages Container -->
        <div id="chatbot-messages" class="h-[350px] overflow-y-auto p-4 space-y-4 bg-gray-50">
            <!-- Messages will be inserted here -->
        </div>

        <!-- Quick Replies Container -->
        <div id="chatbot-quick-replies" class="px-4 py-2 bg-white border-t border-gray-100 flex flex-wrap gap-2 max-h-[120px] overflow-y-auto">
            <!-- Quick replies will be inserted here -->
        </div>

        <!-- Input Area -->
        <div class="p-4 bg-white border-t border-gray-200">
            <form id="chatbot-form" class="flex gap-2">
                <input 
                    type="text" 
                    id="chatbot-input" 
                    placeholder="{{ app()->getLocale() === 'ar' ? 'اكتب رسالتك...' : (app()->getLocale() === 'en' ? 'Type your message...' : 'Écrivez votre message...') }}"
                    class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none text-sm transition-all"
                    autocomplete="off"
                >
                <button 
                    type="submit" 
                    class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-colors flex items-center justify-center"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 {{ app()->getLocale() === 'ar' ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Toggle Button -->
    <button 
        id="chatbot-toggle" 
        onclick="toggleChatbot()" 
        class="w-14 h-14 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center group relative"
    >
        <!-- Chat Icon -->
        <svg id="chatbot-icon-chat" xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        <!-- Close Icon -->
        <svg id="chatbot-icon-close" xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 hidden transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
        <!-- Notification Badge -->
        <span id="chatbot-badge" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center font-bold animate-bounce">1</span>
    </button>
</div>

<style>
    #chatbot-widget {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }
    
    #chatbot-messages::-webkit-scrollbar {
        width: 6px;
    }
    
    #chatbot-messages::-webkit-scrollbar-track {
        background: transparent;
    }
    
    #chatbot-messages::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }
    
    #chatbot-messages::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    .chatbot-message {
        animation: messageSlide 0.3s ease-out;
    }
    
    @keyframes messageSlide {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .chatbot-typing {
        display: flex;
        gap: 4px;
        padding: 12px 16px;
    }
    
    .chatbot-typing span {
        width: 8px;
        height: 8px;
        background: #94a3b8;
        border-radius: 50%;
        animation: typing 1.4s infinite;
    }
    
    .chatbot-typing span:nth-child(2) {
        animation-delay: 0.2s;
    }
    
    .chatbot-typing span:nth-child(3) {
        animation-delay: 0.4s;
    }
    
    @keyframes typing {
        0%, 60%, 100% {
            transform: translateY(0);
        }
        30% {
            transform: translateY(-8px);
        }
    }
    
    .quick-reply-btn {
        transition: all 0.2s;
    }
    
    .quick-reply-btn:hover {
        transform: translateY(-1px);
    }
</style>

<script>
    const chatbotConfig = {
        welcomeUrl: '{{ route("chatbot.welcome") }}',
        messageUrl: '{{ route("chatbot.message") }}',
        csrfToken: '{{ csrf_token() }}',
        locale: '{{ app()->getLocale() }}',
        whatsappNumber: '{{ \App\Models\SiteSetting::get("contact_whatsapp", "+21612345678") }}'
    };
    
    let chatbotOpen = false;
    let chatbotInitialized = false;
    
    function toggleChatbot() {
        const window = document.getElementById('chatbot-window');
        const iconChat = document.getElementById('chatbot-icon-chat');
        const iconClose = document.getElementById('chatbot-icon-close');
        const badge = document.getElementById('chatbot-badge');
        
        chatbotOpen = !chatbotOpen;
        
        if (chatbotOpen) {
            window.classList.remove('hidden');
            setTimeout(() => {
                window.classList.remove('scale-95', 'opacity-0');
                window.classList.add('scale-100', 'opacity-100');
            }, 10);
            iconChat.classList.add('hidden');
            iconClose.classList.remove('hidden');
            badge.classList.add('hidden');
            
            if (!chatbotInitialized) {
                loadWelcomeMessage();
                chatbotInitialized = true;
            }
            
            scrollToBottom();
        } else {
            window.classList.add('scale-95', 'opacity-0');
            window.classList.remove('scale-100', 'opacity-100');
            setTimeout(() => {
                window.classList.add('hidden');
            }, 300);
            iconChat.classList.remove('hidden');
            iconClose.classList.add('hidden');
        }
    }
    
    async function loadWelcomeMessage() {
        showTypingIndicator();
        
        try {
            const response = await fetch(chatbotConfig.welcomeUrl);
            const data = await response.json();
            
            hideTypingIndicator();
            
            if (data.success) {
                addBotMessage(data.message);
                showQuickReplies(data.quick_replies || []);
            }
        } catch (error) {
            hideTypingIndicator();
            addBotMessage('Une erreur est survenue. Veuillez réessayer.');
        }
    }
    
    async function sendMessage(action = null, message = null) {
        if (message) {
            addUserMessage(message);
        }
        
        clearQuickReplies();
        showTypingIndicator();
        
        try {
            const response = await fetch(chatbotConfig.messageUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': chatbotConfig.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ action, message })
            });
            
            const data = await response.json();
            
            hideTypingIndicator();
            
            if (data.success) {
                addBotMessage(data.message);
                showQuickReplies(data.quick_replies || []);
                
                // Handle automatic actions
                if (data.action === 'whatsapp') {
                    setTimeout(() => {
                        openWhatsApp(data.action_value || chatbotConfig.whatsappNumber);
                    }, 1000);
                } else if (data.action === 'url' && data.action_value) {
                    setTimeout(() => {
                        window.location.href = data.action_value;
                    }, 1000);
                }
            }
        } catch (error) {
            hideTypingIndicator();
            addBotMessage('Une erreur est survenue. Veuillez réessayer.');
        }
    }
    
    function handleQuickReply(action) {
        if (action.startsWith('url:')) {
            window.location.href = action.replace('url:', '');
            return;
        }
        
        sendMessage(action, null);
    }
    
    function addBotMessage(text) {
        const container = document.getElementById('chatbot-messages');
        const formattedText = formatMessage(text);
        
        const messageHtml = `
            <div class="chatbot-message flex gap-3">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="bg-white rounded-2xl rounded-tl-sm px-4 py-3 shadow-sm border border-gray-100 max-w-[85%]">
                    <div class="text-sm text-gray-700 whitespace-pre-line">${formattedText}</div>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', messageHtml);
        scrollToBottom();
    }
    
    function addUserMessage(text) {
        const container = document.getElementById('chatbot-messages');
        
        const messageHtml = `
            <div class="chatbot-message flex gap-3 justify-end">
                <div class="bg-blue-600 text-white rounded-2xl rounded-tr-sm px-4 py-3 shadow-sm max-w-[85%]">
                    <div class="text-sm">${escapeHtml(text)}</div>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', messageHtml);
        scrollToBottom();
    }
    
    function showQuickReplies(replies) {
        const container = document.getElementById('chatbot-quick-replies');
        container.innerHTML = '';
        
        replies.forEach(reply => {
            const btn = document.createElement('button');
            btn.className = 'quick-reply-btn px-3 py-2 bg-white border border-gray-200 hover:border-blue-400 hover:bg-blue-50 rounded-xl text-sm text-gray-700 hover:text-blue-600 transition-all';
            btn.textContent = reply.text;
            btn.onclick = () => handleQuickReply(reply.action);
            container.appendChild(btn);
        });
    }
    
    function clearQuickReplies() {
        document.getElementById('chatbot-quick-replies').innerHTML = '';
    }
    
    function showTypingIndicator() {
        const container = document.getElementById('chatbot-messages');
        
        const typingHtml = `
            <div id="typing-indicator" class="chatbot-message flex gap-3">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="bg-white rounded-2xl rounded-tl-sm px-4 py-3 shadow-sm border border-gray-100">
                    <div class="chatbot-typing">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
            </div>
        `;
        
        container.insertAdjacentHTML('beforeend', typingHtml);
        scrollToBottom();
    }
    
    function hideTypingIndicator() {
        const indicator = document.getElementById('typing-indicator');
        if (indicator) {
            indicator.remove();
        }
    }
    
    function formatMessage(text) {
        // Convert **bold** to <strong>
        text = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        // Convert URLs to links
        text = text.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" class="text-blue-600 underline" target="_blank">$1</a>');
        return text;
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function scrollToBottom() {
        const container = document.getElementById('chatbot-messages');
        setTimeout(() => {
            container.scrollTop = container.scrollHeight;
        }, 100);
    }
    
    function openWhatsApp(number) {
        const cleanNumber = number.replace(/[^0-9+]/g, '');
        const message = encodeURIComponent('Bonjour, je souhaite obtenir un devis pour un projet.');
        window.open(`https://wa.me/${cleanNumber}?text=${message}`, '_blank');
    }
    
    // Form submission
    document.getElementById('chatbot-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const input = document.getElementById('chatbot-input');
        const message = input.value.trim();
        
        if (message) {
            sendMessage(null, message);
            input.value = '';
        }
    });
    
    // Auto-open after delay (optional)
    setTimeout(() => {
        const badge = document.getElementById('chatbot-badge');
        if (badge && !chatbotOpen) {
            badge.classList.remove('hidden');
        }
    }, 5000);
</script>

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function sendMessage() {
    const message = document.getElementById('user-input').value;
    // Empty message check
    if (message.trim() === '') {
        console.log('Empty message - not sending');
        return;  // Stop the function here
    }
    fetch('chatmsg.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'message=' + encodeURIComponent(message)
    })
    .then(response => {
        if (response.status === 401) {
            window.location.href = 'login.php?timeout=1';
            return;
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            document.getElementById('user-input').value = '';
            loadMessages(); // Refresh the chat
        }
    });
}

function loadMessages() {
    const currentUser = document.getElementById('username').value;
    fetch('chatmsg.php')
        .then(response => {
            if (response.status === 401) {
                window.location.href = 'login.php?timeout=1';
                return;
            }
            return response.json();
        })
        .then(messages => {
            const chatBox = document.getElementById('chat-box');
            chatBox.innerHTML = ''; // Clear existing messages
            
            messages.forEach(msg => {
                const messageDiv = document.createElement('div');
                messageDiv.className = 'message';
                
                // Check if this message is from current user
                if (msg.person === currentUser) {
                    messageDiv.classList.add('my-message');
                } else {
                    messageDiv.classList.add('other-message');
                }
                
                // Create message HTML
                messageDiv.innerHTML = `
                    <div class="message-sender">${msg.person}</div>
                    <div class="message-time">${new Date(msg.time * 1000).toLocaleTimeString()}</div>
                    <div class="message-text">${escapeHtml(msg.message)}</div>
                `;
                
                chatBox.appendChild(messageDiv);
            });
            
            // Scroll to bottom
            chatBox.scrollTop = chatBox.scrollHeight;
        });
}

// Start polling when page loads
document.addEventListener('DOMContentLoaded', function() {
    loadMessages();
    setInterval(loadMessages, 5000);
});
const messages = document.getElementById('messages');
const preview = document.getElementById('preview');
const form = document.getElementById('chat-form');

let openPickerMessageId = null; // Track which message has an open picker

// Scroll to bottom
function scrollToBottom() {
    messages.scrollTop = messages.scrollHeight;
}

// Check if user is near bottom
function isUserAtBottom() {
    return messages.scrollHeight - messages.scrollTop - messages.clientHeight < 50;
}

// Load messages
function loadMessages() {
    const shouldScroll = isUserAtBottom();

    fetch('../messenger/messages.php')
        .then(res => res.text())
        .then(html => {
            messages.innerHTML = html;

            if (shouldScroll) scrollToBottom();

            setupEmojiPickers();

            // Restore previously open picker
            if (openPickerMessageId) {
                const message = messages.querySelector(`.message[data-id="${openPickerMessageId}"]`);
                const btn = message?.querySelector('.open-picker');
                if (message && btn) {
                    createEmojiPicker(message, openPickerMessageId, btn);
                } else {
                    openPickerMessageId = null;
                }
            }
        });
}

// Setup emoji pickers
function setupEmojiPickers() {
    document.querySelectorAll('.open-picker').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const message = this.closest('.message');
            const messageId = message.getAttribute('data-id');

            // Toggle picker: remove if already exists
            const existing = message.querySelector('.emoji-picker');
            if (existing) {
                existing.remove();
                openPickerMessageId = null;
                return;
            }

            // Remove other pickers
            document.querySelectorAll('.emoji-picker').forEach(p => p.remove());

            // Save state of open picker
            openPickerMessageId = messageId;

            createEmojiPicker(message, messageId, this);
        });
    });
}

// Create emoji picker
function createEmojiPicker(message, messageId, btnElement) {
    const picker = document.createElement('div');
    picker.className = 'emoji-picker';
    picker.style.position = 'absolute';
    picker.style.background = '#fff';
    picker.style.border = '1px solid #ccc';
    picker.style.padding = '5px';
    picker.style.borderRadius = '6px';
    picker.style.zIndex = 1000;
    picker.style.bottom = '50px';
    picker.style.left = '0';

    const reactionEmojis = [
        '👍', '👎', '❤️', '💔', '😂', '🤣', '😅', '😊',
        '😍', '😘', '😎', '🤔', '😮', '😢', '😭', '😡', '🤯',
        '🙏', '👏', '🙌', '🤝', '💯', '🔥', '🎉', '✨',
        '😏', '😜', '🤪', '🥳', '😇', '🤗', '😴', '🤤',
        '😐', '😑', '😶', '🙄', '🤨', '😕'
    ];

    reactionEmojis.forEach(e => {
        const span = document.createElement('span');
        span.textContent = e;
        span.style.cursor = 'pointer';
        span.style.margin = '3px';
        span.style.fontSize = '20px';

        span.addEventListener('click', function () {
            // Send reaction via AJAX
            fetch('../messenger/react.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `message_id=${messageId}&reaction=${encodeURIComponent(e)}`
            }).then(() => {
                // Reload messages without scrolling
                loadMessages();
                // Close picker after selecting emoji
                picker.remove();
                openPickerMessageId = null;
            });
        });

        picker.appendChild(span);
    });

    btnElement.parentNode.appendChild(picker);
}

// Initial load
loadMessages();

// Refresh messages periodically
setInterval(loadMessages, 5000); // every 5 seconds


// Send message without reload
form.addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(form);

    fetch('../messenger/send.php', {
        method: 'POST',
        body: formData

    }).then(() => {
        form.reset();
        preview.style.display = 'none';
        loadMessages();// auto-scroll if user at bottom
        scrollToBottom();
    });
});


// Image preview
document.getElementById('chat_image').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function (ev) {
            preview.src = ev.target.result;
            preview.style.display = 'inline';
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
});

// Close emoji picker when clicking outside
document.addEventListener('click', function (e) {
    if (!e.target.closest('.emoji-picker') && !e.target.classList.contains('open-picker')) {
        document.querySelectorAll('.emoji-picker').forEach(p => p.remove());
        openPickerMessageId = null;
    }
});



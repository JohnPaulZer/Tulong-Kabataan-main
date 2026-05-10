import { sendChatbotMessage } from '../services/chatbot-api';

const WELCOME_MESSAGE =
    'Hi! I\u2019m the Tulong Kabataan assistant. I can help you understand how to use the platform, submit requests, check status, and navigate user-side features.';
const ERROR_MESSAGE = 'Sorry, I could not reach the assistant right now. Please try again later.';
const MAX_HISTORY = 8;

function createMessageElement(role, content, isLoading = false) {
    const item = document.createElement('div');
    item.className = `tk-chatbot-message tk-chatbot-message--${role}`;

    const bubble = document.createElement('div');
    bubble.className = 'tk-chatbot-bubble';
    bubble.textContent = content;

    if (isLoading) {
        bubble.classList.add('is-loading');
        bubble.setAttribute('aria-label', 'Assistant is typing');
    }

    item.appendChild(bubble);
    return item;
}

function initChatbot(root) {
    const endpoint = root.dataset.endpoint;
    const csrfToken = root.dataset.csrf;
    const panel = root.querySelector('[data-chatbot-panel]');
    const toggle = root.querySelector('[data-chatbot-toggle]');
    const close = root.querySelector('[data-chatbot-close]');
    const form = root.querySelector('[data-chatbot-form]');
    const input = root.querySelector('[data-chatbot-input]');
    const messagesEl = root.querySelector('[data-chatbot-messages]');
    const errorEl = root.querySelector('[data-chatbot-error]');
    const sendButton = root.querySelector('[data-chatbot-send]');

    if (!endpoint || !csrfToken || !panel || !toggle || !form || !input || !messagesEl || !sendButton) {
        return;
    }

    const history = [];
    let isOpen = false;
    let isSending = false;

    const scrollToLatest = () => {
        messagesEl.scrollTop = messagesEl.scrollHeight;
    };

    const setError = (message = '') => {
        errorEl.textContent = message;
        errorEl.hidden = message === '';
    };

    const addMessage = (role, content, save = true) => {
        messagesEl.appendChild(createMessageElement(role, content));

        if (save) {
            history.push({ role, content });
            while (history.length > MAX_HISTORY) {
                history.shift();
            }
        }

        scrollToLatest();
    };

    const setOpen = (nextOpen) => {
        isOpen = nextOpen;
        panel.hidden = !isOpen;
        root.classList.toggle('is-open', isOpen);
        toggle.setAttribute('aria-expanded', String(isOpen));
        toggle.setAttribute('aria-label', isOpen ? 'Close chatbot' : 'Open chatbot');

        if (isOpen) {
            window.setTimeout(() => input.focus(), 80);
            scrollToLatest();
        }
    };

    const setSending = (nextSending) => {
        isSending = nextSending;
        sendButton.disabled = isSending;
        input.disabled = isSending;
        root.classList.toggle('is-sending', isSending);
    };

    const resizeInput = () => {
        input.style.height = 'auto';
        input.style.height = `${Math.min(input.scrollHeight, 120)}px`;
    };

    messagesEl.appendChild(createMessageElement('assistant', WELCOME_MESSAGE));

    toggle.addEventListener('click', () => {
        setOpen(!isOpen);
    });

    close?.addEventListener('click', () => {
        setOpen(false);
    });

    input.addEventListener('input', resizeInput);

    input.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            form.requestSubmit();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && isOpen) {
            setOpen(false);
        }
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const message = input.value.trim();

        if (message === '' || isSending) {
            return;
        }

        const requestHistory = history.slice(-MAX_HISTORY);

        setError();
        addMessage('user', message);
        input.value = '';
        resizeInput();
        setSending(true);

        const loadingEl = createMessageElement('assistant', 'Thinking...', true);
        messagesEl.appendChild(loadingEl);
        scrollToLatest();

        try {
            const reply = await sendChatbotMessage({
                endpoint,
                csrfToken,
                message,
                history: requestHistory,
            });

            loadingEl.remove();
            addMessage('assistant', reply);
        } catch (error) {
            loadingEl.remove();
            setError(error?.message || ERROR_MESSAGE);
        } finally {
            setSending(false);
            input.focus();
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-tk-chatbot]').forEach(initChatbot);
});

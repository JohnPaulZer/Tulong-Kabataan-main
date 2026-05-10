<div class="tk-chatbot" data-tk-chatbot data-endpoint="{{ route('chatbot.message', [], false) }}" data-csrf="{{ csrf_token() }}">
    <section class="tk-chatbot-panel" data-chatbot-panel aria-labelledby="tkChatbotTitle" hidden>
        <header class="tk-chatbot-header">
            <div class="tk-chatbot-title">
                <span class="tk-chatbot-title-icon" aria-hidden="true">
                    <i class="ri-chat-smile-3-line"></i>
                </span>
                <div>
                    <h2 id="tkChatbotTitle">Tulong Kabataan Assistant</h2>
                    <p>Public user guide</p>
                </div>
            </div>
            <button type="button" class="tk-chatbot-icon-btn" data-chatbot-close aria-label="Close chatbot">
                <i class="ri-close-line" aria-hidden="true"></i>
            </button>
        </header>

        <div class="tk-chatbot-messages" data-chatbot-messages role="log" aria-live="polite" aria-relevant="additions"></div>

        <div class="tk-chatbot-error" data-chatbot-error role="alert" hidden></div>

        <form class="tk-chatbot-form" data-chatbot-form>
            <label class="tk-sr-only" for="tkChatbotInput">Ask about using Tulong Kabataan</label>
            <textarea id="tkChatbotInput" class="tk-chatbot-input" data-chatbot-input maxlength="600" rows="1"
                placeholder="Type your question..."></textarea>
            <button type="submit" class="tk-chatbot-send" data-chatbot-send aria-label="Send message">
                <i class="ri-send-plane-2-fill" aria-hidden="true"></i>
            </button>
        </form>
    </section>

    <button type="button" class="tk-chatbot-toggle" data-chatbot-toggle aria-label="Open chatbot"
        aria-expanded="false">
        <i class="ri-message-3-line" aria-hidden="true"></i>
    </button>
</div>

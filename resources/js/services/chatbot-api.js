export async function sendChatbotMessage({ endpoint, csrfToken, message, history }) {
    const response = await fetch(endpoint, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({
            message,
            history,
        }),
    });

    let payload = {};

    try {
        payload = await response.json();
    } catch {
        payload = {};
    }

    if (!response.ok) {
        throw new Error(payload.reply || 'The assistant is unavailable right now.');
    }

    return payload.reply || 'I do not have enough official information about that yet. Please check the latest Tulong Kabataan announcement or contact support.';
}

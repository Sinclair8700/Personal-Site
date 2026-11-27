// Solves the contact form's proof-of-work bot check (see app/Support/ContactChallenge.php):
// find the number n for which SHA-256(salt + n) equals the challenge, then enable Send.
const form = document.querySelector('[data-contact-challenge]');

if (form) {
    const field = (name) => form.querySelector(`input[name="${name}"]`);
    const status = form.querySelector('[data-challenge-status]');
    const submit = form.querySelector('[data-challenge-submit]');
    const numberField = field('challenge_number');
    const salt = field('challenge_salt').value;
    const max = Number(numberField.dataset.challengeMax);
    const target = Uint8Array.from(field('challenge').value.match(/../g), (byte) => parseInt(byte, 16));

    const setStatus = (text) => {
        if (status) status.textContent = text;
    };

    const matches = (buffer) => {
        const bytes = new Uint8Array(buffer);
        for (let i = 0; i < bytes.length; i++) {
            if (bytes[i] !== target[i]) return false;
        }
        return true;
    };

    const solve = async () => {
        const encoder = new TextEncoder();
        const batch = 1000;
        for (let start = 0; start <= max; start += batch) {
            const end = Math.min(start + batch, max + 1);
            const pending = [];
            for (let n = start; n < end; n++) {
                pending.push(crypto.subtle.digest('SHA-256', encoder.encode(salt + n)));
            }
            const index = (await Promise.all(pending)).findIndex(matches);
            if (index !== -1) return start + index;
        }
        return null;
    };

    // A page restored from the back/forward cache holds a challenge that may already be used up.
    window.addEventListener('pageshow', (event) => {
        if (event.persisted) window.location.reload();
    });

    if (!window.crypto?.subtle) {
        setStatus('The bot check needs a secure (https) connection.');
    } else {
        setStatus('Checking you\'re not a bot…');
        solve()
            .then((n) => {
                if (n === null) {
                    setStatus('The bot check failed. Please reload the page.');
                    return;
                }
                numberField.value = String(n);
                submit.disabled = false;
                setStatus('✓ Bot check passed');
            })
            .catch(() => setStatus('The bot check failed. Please reload the page.'));
    }
}

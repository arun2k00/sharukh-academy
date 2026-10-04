/**
 * A left-to-right character-scramble reveal — the classic "decoding" text
 * effect. Spaces and line breaks are left untouched so words and a
 * deliberate two-line layout never wobble mid-animation.
 */
const SCRAMBLE_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

function randomChar() {
    return SCRAMBLE_CHARS[Math.floor(Math.random() * SCRAMBLE_CHARS.length)];
}

export default function scrambleText(el, finalText, { duration = 800, frameRate = 40 } = {}) {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        el.textContent = finalText;
        return;
    }

    if (el._scrambleTimer) {
        clearInterval(el._scrambleTimer);
    }

    const characters = finalText.split('');
    const totalFrames = Math.round(duration / frameRate);

    // Non-whitespace characters lock in left to right, with a little jitter
    // so it reads as organic rather than a perfectly uniform metronome.
    const lockFrame = characters.map((char, index) => {
        if (char === '\n' || char === ' ') {
            return 0;
        }

        const progress = index / characters.length;

        return Math.round((progress * 0.7 + Math.random() * 0.3) * totalFrames);
    });

    let frame = 0;

    el._scrambleTimer = setInterval(() => {
        frame++;

        el.textContent = characters
            .map((char, index) => {
                if (char === '\n' || char === ' ') {
                    return char;
                }

                return frame >= lockFrame[index] ? char : randomChar();
            })
            .join('');

        if (frame >= totalFrames) {
            clearInterval(el._scrambleTimer);
            el._scrambleTimer = null;
            el.textContent = finalText;
        }
    }, frameRate);
}

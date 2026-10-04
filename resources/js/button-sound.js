/**
 * A soft, synthesized click tone for buttons — no audio file to ship, and it
 * respects prefers-reduced-motion as a proxy for "less incidental feedback",
 * since there is no dedicated reduced-sound media query.
 */
let audioContext = null;

function playClickTone() {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    try {
        audioContext ??= new (window.AudioContext || window.webkitAudioContext)();

        if (audioContext.state === 'suspended') {
            audioContext.resume();
        }

        const now = audioContext.currentTime;
        const oscillator = audioContext.createOscillator();
        const gain = audioContext.createGain();

        oscillator.type = 'sine';
        oscillator.frequency.setValueAtTime(880, now);
        oscillator.frequency.exponentialRampToValueAtTime(660, now + 0.05);

        gain.gain.setValueAtTime(0.05, now);
        gain.gain.exponentialRampToValueAtTime(0.0001, now + 0.06);

        oscillator.connect(gain).connect(audioContext.destination);
        oscillator.start(now);
        oscillator.stop(now + 0.07);
    } catch {
        // Sound is a non-essential enhancement — never let it break a click.
    }
}

export default function initButtonSound() {
    document.addEventListener('click', (event) => {
        if (event.target.closest('.js-btn-sound')) {
            playClickTone();
        }
    });
}

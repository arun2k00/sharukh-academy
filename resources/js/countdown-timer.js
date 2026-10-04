/**
 * A per-visitor countdown persisted in localStorage so it counts down
 * consistently for that browser instead of resetting on every page load —
 * the reload-and-it's-back-to-14:00:00 version of this is the one that
 * actually makes people distrust a site. Silently rolls over to a fresh
 * window once it hits zero rather than sitting stuck at 00:00:00 forever.
 */
export default function countdownTimer(key, hours) {
    return {
        remaining: 0,
        target: 0,
        dismissed: false,
        storageKey: `academy_countdown_${key}`,

        init() {
            const windowMs = hours * 60 * 60 * 1000;
            const stored = this.readStoredTarget();
            const now = Date.now();

            this.target = stored && stored > now ? stored : now + windowMs;
            this.writeStoredTarget(this.target);

            this.tick(windowMs);
            setInterval(() => this.tick(windowMs), 1000);
        },

        tick(windowMs) {
            this.remaining = Math.max(0, this.target - Date.now());

            if (this.remaining === 0) {
                this.target = Date.now() + windowMs;
                this.writeStoredTarget(this.target);
            }
        },

        readStoredTarget() {
            try {
                return parseInt(localStorage.getItem(this.storageKey), 10) || null;
            } catch {
                return null;
            }
        },

        writeStoredTarget(value) {
            try {
                localStorage.setItem(this.storageKey, String(value));
            } catch {
                // Private browsing or blocked storage — the timer still runs
                // for this page view, it just won't persist across reloads.
            }
        },

        get hh() {
            return String(Math.floor(this.remaining / 3600000)).padStart(2, '0');
        },

        get mm() {
            return String(Math.floor((this.remaining % 3600000) / 60000)).padStart(2, '0');
        },

        get ss() {
            return String(Math.floor((this.remaining % 60000) / 1000)).padStart(2, '0');
        },
    };
}

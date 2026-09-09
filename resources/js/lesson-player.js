/**
 * Tracks how much of a recorded lesson has actually been watched.
 *
 * The YouTube iframe player reports playback position; we send it to the server
 * at intervals so a student's progress survives a refresh, and so a lesson can
 * complete itself once they reach the end. The server clamps and only ever
 * raises the stored value, so nothing here is trusted as authoritative.
 */

const API_SRC = 'https://www.youtube.com/iframe_api';

let apiPromise = null;

function loadYouTubeApi() {
    if (apiPromise) {
        return apiPromise;
    }

    apiPromise = new Promise((resolve) => {
        if (window.YT && window.YT.Player) {
            resolve(window.YT);

            return;
        }

        const previousCallback = window.onYouTubeIframeAPIReady;

        window.onYouTubeIframeAPIReady = () => {
            if (typeof previousCallback === 'function') {
                previousCallback();
            }

            resolve(window.YT);
        };

        if (!document.querySelector(`script[src="${API_SRC}"]`)) {
            const script = document.createElement('script');
            script.src = API_SRC;
            script.async = true;
            document.head.appendChild(script);
        }
    });

    return apiPromise;
}

export default function lessonPlayer({ frameId, endpoint, startPercent = 0, alreadyComplete = false }) {
    return {
        percent: startPercent,
        complete: alreadyComplete,
        saving: false,
        lastReportedPercent: startPercent,
        player: null,
        ticker: null,

        init() {
            if (!frameId || !document.getElementById(frameId)) {
                return;
            }

            loadYouTubeApi().then((YT) => {
                this.player = new YT.Player(frameId, {
                    events: {
                        onStateChange: (event) => this.handleStateChange(event, YT),
                    },
                });
            });

            // Stop the timer when the student navigates away mid-lesson.
            this.$el.addEventListener('alpine:destroyed', () => this.stopTicking());
        },

        handleStateChange(event, YT) {
            if (event.data === YT.PlayerState.PLAYING) {
                this.startTicking();

                return;
            }

            this.stopTicking();

            // Pausing or finishing is a good moment to persist the position.
            this.report({ force: event.data === YT.PlayerState.ENDED });
        },

        startTicking() {
            this.stopTicking();
            this.ticker = setInterval(() => this.report(), 10000);
        },

        stopTicking() {
            if (this.ticker) {
                clearInterval(this.ticker);
                this.ticker = null;
            }
        },

        currentPosition() {
            if (!this.player || typeof this.player.getDuration !== 'function') {
                return null;
            }

            const duration = this.player.getDuration();
            const seconds = this.player.getCurrentTime();

            if (!duration || duration <= 0) {
                return null;
            }

            return {
                seconds: Math.floor(seconds),
                percent: Math.min(100, Math.floor((seconds / duration) * 100)),
            };
        },

        async report({ force = false } = {}) {
            const position = this.currentPosition();

            if (!position || this.saving) {
                return;
            }

            const ended = force || position.percent >= 99;

            // Avoid a request for every tick when the needle has barely moved.
            if (!ended && position.percent - this.lastReportedPercent < 5) {
                return;
            }

            this.saving = true;

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        seconds: position.seconds,
                        percent: ended ? 100 : position.percent,
                    }),
                });

                if (!response.ok) {
                    return;
                }

                const data = await response.json();

                this.percent = data.watched_percent;
                this.lastReportedPercent = data.watched_percent;

                if (data.completed && !this.complete) {
                    this.complete = true;
                }
            } catch {
                // A dropped progress ping is not worth interrupting the lesson;
                // the next tick or the manual button will record it instead.
            } finally {
                this.saving = false;
            }
        },
    };
}

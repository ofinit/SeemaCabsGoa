import Alpine from 'alpinejs';

/**
 * Formats a date for display as DD-MM-YYYY (the format required app-wide),
 * converting a backend/native ISO "Y-m-d" string. Already-formatted or
 * unrecognized strings pass through unchanged.
 */
window.formatDMY = (dateStr) => {
    const iso = /^(\d{4})-(\d{2})-(\d{2})/.exec(dateStr || '');
    return iso ? `${iso[3]}-${iso[2]}-${iso[1]}` : (dateStr || '');
};

/**
 * Formats a time for display as 12-hour with AM/PM (the format required
 * app-wide), converting a 24-hour "HH:MM" or "HH:MM:SS" string — the format
 * both native <input type="time"> values and the backend use.
 */
window.formatTime = (timeStr) => {
    const m = /^(\d{1,2}):(\d{2})/.exec(timeStr || '');
    if (!m) return timeStr || '';
    let h = parseInt(m[1], 10);
    const period = h >= 12 ? 'PM' : 'AM';
    h = h % 12 || 12;
    return `${h}:${m[2]} ${period}`;
};

/**
 * Fetch wrapper for the /app/actions/* JSON endpoints. Attaches the CSRF
 * token, unwraps the {status, data, message} envelope, and throws a plain
 * Error (with the backend's message) on status:false or a non-2xx response
 * so Alpine components can just `.catch(e => toast.show('Error', e.message))`.
 */
window.apiFetch = async function apiFetch(url, options = {}) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const isFormData = options.body instanceof FormData;

    const response = await fetch(url, {
        ...options,
        headers: {
            Accept: 'application/json',
            ...(isFormData ? {} : { 'Content-Type': 'application/json' }),
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            ...(options.headers || {}),
        },
        body: isFormData ? options.body : options.body ? JSON.stringify(options.body) : undefined,
    });

    let payload = null;
    try {
        payload = await response.json();
    } catch (e) {
        // no JSON body
    }

    if (!response.ok || (payload && payload.status === false)) {
        const message = payload?.message || 'Something went wrong. Please try again.';
        const error = new Error(message);
        error.payload = payload;
        throw error;
    }

    return payload;
};

// Shared Alpine state/helpers used across customer Blade views.
Alpine.data('bottomSheet', (initialOpen = false) => ({
    open: initialOpen,
    show() { this.open = true; document.body.style.overflow = 'hidden'; },
    hide() { this.open = false; document.body.style.overflow = ''; },
}));

Alpine.data('toast', () => ({
    visible: false,
    message: '',
    title: '',
    actionLabel: '',
    actionHref: '',
    timer: null,
    show(title, message, opts = {}) {
        this.title = title;
        this.message = message;
        this.actionLabel = opts.actionLabel || '';
        this.actionHref = opts.actionHref || '';
        this.visible = true;
        clearTimeout(this.timer);
        this.timer = setTimeout(() => (this.visible = false), opts.duration || 6000);
    },
}));

// Swipe-to-confirm slider (used on the active-trip "Swipe To Cancel Ride" control).
Alpine.data('swipeToConfirm', (onConfirm) => ({
    dragging: false,
    x: 0,
    maxX: 0,
    confirmed: false,
    init() {
        this.maxX = this.$refs.track.offsetWidth - this.$refs.handle.offsetWidth;
    },
    start() {
        if (this.confirmed) return;
        this.dragging = true;
    },
    move(e) {
        if (!this.dragging || this.confirmed) return;
        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const trackRect = this.$refs.track.getBoundingClientRect();
        this.x = Math.min(Math.max(0, clientX - trackRect.left - this.$refs.handle.offsetWidth / 2), this.maxX);
    },
    end() {
        this.dragging = false;
        if (this.x > this.maxX * 0.8) {
            this.x = this.maxX;
            this.confirmed = true;
            this.$dispatch('swipe-confirmed');
        } else {
            this.x = 0;
        }
    },
}));

// Add-to-Home-Screen support. `beforeinstallprompt` (Chrome/Android/desktop
// Chrome) can fire before any Alpine component has mounted, so it's captured
// at module scope here rather than inside the component itself, with a tiny
// pub/sub so components that initialize afterward still pick it up.
let deferredInstallPrompt = null;
const installPromptListeners = new Set();

window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredInstallPrompt = e;
    installPromptListeners.forEach((fn) => fn());
});

window.addEventListener('appinstalled', () => {
    deferredInstallPrompt = null;
    installPromptListeners.forEach((fn) => fn());
});

function isStandaloneDisplay() {
    return window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
}

function isIosDevice() {
    return /iPad|iPhone|iPod/.test(navigator.userAgent)
        || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);
}

/**
 * Shared by the dismissible Home-screen banner and the always-available
 * "Add to Home Screen" action on the Account page. Chrome/Android/desktop
 * get a real native install prompt; iOS Safari has no such API, so it gets
 * step-by-step Share-sheet instructions instead.
 */
Alpine.data('installPrompt', (opts = {}) => ({
    dismissible: opts.dismissible ?? false,
    dismissed: false,
    installed: false,
    isIos: false,
    showIosInstructions: false,
    canPromptNatively: false,
    get show() {
        return !this.installed && !this.dismissed && (this.canPromptNatively || this.isIos);
    },
    init() {
        this.installed = isStandaloneDisplay();
        this.isIos = isIosDevice();
        this.canPromptNatively = deferredInstallPrompt !== null;
        this.dismissed = this.dismissible && localStorage.getItem('pwaInstallDismissed') === '1';

        const sync = () => {
            this.canPromptNatively = deferredInstallPrompt !== null;
            this.installed = isStandaloneDisplay();
        };
        installPromptListeners.add(sync);
        this.$watch('installed', () => {}); // keep property reactive
        window.addEventListener('appinstalled', () => { this.installed = true; });
    },
    async install() {
        if (this.isIos) {
            this.showIosInstructions = true;
            return;
        }
        if (!deferredInstallPrompt) return;
        deferredInstallPrompt.prompt();
        await deferredInstallPrompt.userChoice;
        deferredInstallPrompt = null;
        this.canPromptNatively = false;
    },
    dismiss() {
        this.dismissed = true;
        localStorage.setItem('pwaInstallDismissed', '1');
    },
}));

window.Alpine = Alpine;
Alpine.start();

// Bridge for triggering the global toast from anywhere (plain onclick=""
// handlers, other Alpine components). Defined here in a real JS closure
// rather than inline in an `x-init=""` expression string — an x-init arrow
// function that closes over reactive scope and escapes onto `window` causes
// Alpine to eagerly evaluate it, flipping `visible` true before the user
// ever triggers a toast (reproduced and confirmed via isolated test page).
window.showToast = (title, message, opts) => {
    const el = document.querySelector('[data-toast-host]');
    if (el) Alpine.$data(el).show(title, message, opts);
};

/**
 * Google Identity Services wiring, shared by the Login and Signup pages'
 * "Google Login"/"Google Sign Up" buttons. Loads Google's SDK lazily (only
 * once, only when a client ID is actually configured). The ID token it
 * returns is verified server-side in Customer\AuthController (signature +
 * audience + issuer), never trusted as-is client-side.
 *
 * Deliberately skips One Tap (prompt()) — it silently does *nothing* (no
 * error, no UI) whenever the browser blocks third-party cookies, the user
 * previously dismissed it (Google's own cooldown), or — confirmed by direct
 * testing — the newer FedCM path fails ("Not signed in with the identity
 * provider" thrown at a level that never invokes the legacy notification
 * callback, so there's no reliable failure signal to fall back on). Instead
 * this goes straight to rendering Google's own real button in a bottom
 * sheet: a directly-clicked Google button always works via the standard,
 * stable OAuth popup, regardless of cookie policy or FedCM state.
 */
let googleSdkPromise = null;
function loadGoogleSdk() {
    if (!googleSdkPromise) {
        googleSdkPromise = new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = 'https://accounts.google.com/gsi/client';
            script.async = true;
            script.defer = true;
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }
    return googleSdkPromise;
}

function showGoogleButtonFallback() {
    let overlay = document.getElementById('google-signin-fallback');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'google-signin-fallback';
        overlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:60;display:flex;align-items:flex-end;justify-content:center;';
        overlay.innerHTML = `
            <div style="background:#fff;width:100%;max-width:480px;border-radius:28px 28px 0 0;padding:24px;text-align:center;">
                <p style="font-weight:600;margin:0 0 16px;color:#171717;">Continue with Google</p>
                <div id="google-signin-real-button" style="display:flex;justify-content:center;"></div>
                <button type="button" id="google-signin-fallback-close" style="margin-top:16px;color:#7D8A9C;font-size:14px;background:none;border:none;">Cancel</button>
            </div>`;
        document.body.appendChild(overlay);
        overlay.querySelector('#google-signin-fallback-close').addEventListener('click', () => overlay.remove());
        overlay.addEventListener('click', (e) => { if (e.target === overlay) overlay.remove(); });
    }
    overlay.style.display = 'flex';
    window.google.accounts.id.renderButton(
        document.getElementById('google-signin-real-button'),
        { type: 'standard', theme: 'outline', size: 'large', text: 'continue_with', shape: 'pill' }
    );
}

window.triggerGoogleSignIn = async function triggerGoogleSignIn(clientId) {
    if (!clientId) {
        showToast('Coming soon', "Google sign-in isn't set up yet — please use email & password.");
        return;
    }
    try {
        await loadGoogleSdk();
        window.google.accounts.id.initialize({
            client_id: clientId,
            callback: async (response) => {
                try {
                    const res = await apiFetch('/app/auth/google', { method: 'POST', body: { credential: response.credential } });
                    window.location.href = res.data.redirect;
                } catch (e) {
                    showToast('Google sign-in failed', e.message);
                }
            },
        });
        // Not using prompt()/One Tap here: Google is mid-migration to FedCM,
        // and in that failure mode (no Google account signed into the browser)
        // the browser throws "Not signed in with the identity provider" at a
        // level that never invokes the legacy notification callback — so
        // isNotDisplayed()/isSkippedMoment() checks never fire and there is
        // no reliable signal to fall back on (confirmed by direct testing).
        // Going straight to Google's own rendered button sidesteps this
        // entirely: it always works via the standard, stable OAuth popup.
        showGoogleButtonFallback();
    } catch (e) {
        showToast('Google sign-in unavailable', 'Could not load Google sign-in right now. Please try again or use email & password.');
    }
};

// Service worker registration is deliberately deferred to the PWA-polish
// phase: an earlier NetworkFirst runtime-caching rule caused fetch() calls
// to /app/actions/* to hang indefinitely once the SW took control (see
// .wolf/buglog.json). The manifest below still makes the app installable;
// offline caching will be re-enabled with a carefully scoped strategy later.

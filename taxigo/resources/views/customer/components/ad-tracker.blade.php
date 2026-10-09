{{--
    Viewable-impression tracker for ads. Any element with data-ad-id (and
    optional data-ad-screen) counts as one view per page load once at least
    50% of it has been visible for 1 second. Views are batched and sent to
    /app/actions/ad-impressions; no personal or location data is sent.
--}}
<script>
(function () {
    if (!('IntersectionObserver' in window)) return;
    var endpoint = @js(route('customer.actions.ad-impressions'));
    var csrf = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
    var seen = {}, queue = [], timers = new WeakMap();

    function flush() {
        if (!queue.length) return;
        var items = queue.splice(0, 20);
        try {
            fetch(endpoint, {
                method: 'POST', keepalive: true, credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ platform: 'pwa', items: items })
            }).catch(function () {});
        } catch (e) {}
        if (queue.length) flush();
    }

    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            var el = entry.target;
            var key = el.dataset.adId + ':' + (el.dataset.adScreen || 0);
            if (seen[key]) return;
            if (entry.isIntersecting && entry.intersectionRatio >= 0.5) {
                if (!timers.has(el)) {
                    timers.set(el, setTimeout(function () {
                        if (seen[key]) return;
                        seen[key] = true;
                        queue.push({ id: parseInt(el.dataset.adId, 10), screen: parseInt(el.dataset.adScreen || '0', 10) });
                    }, 1000));
                }
            } else if (timers.has(el)) {
                clearTimeout(timers.get(el));
                timers.delete(el);
            }
        });
    }, { threshold: [0, 0.5, 1] });

    function scan() {
        document.querySelectorAll('[data-ad-id]:not([data-ad-observed])').forEach(function (el) {
            if (!el.dataset.adId) return;
            el.setAttribute('data-ad-observed', '1');
            observer.observe(el);
        });
    }

    document.addEventListener('DOMContentLoaded', scan);
    new MutationObserver(scan).observe(document.documentElement, { childList: true, subtree: true, attributes: true, attributeFilter: ['data-ad-id'] });
    setInterval(flush, 5000);
    window.addEventListener('pagehide', flush);
    document.addEventListener('visibilitychange', function () { if (document.visibilityState === 'hidden') flush(); });
})();
</script>

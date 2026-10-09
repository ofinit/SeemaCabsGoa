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

    // "Sponsored ⓘ" opens a small sheet: why this is shown + report it.
    var reportUrl = @js(route('customer.actions.ad-report'));
    var reasons = @js(\App\Models\AdReport::REASONS);
    document.addEventListener('click', function (event) {
        var tag = event.target.closest && event.target.closest('.ad-report');
        if (!tag) return;
        event.preventDefault();
        event.stopPropagation();
        var holder = tag.closest('[data-ad-id]');
        var adId = holder && parseInt(holder.dataset.adId, 10);
        if (!adId) return;
        var sheet = document.createElement('div');
        sheet.style.cssText = 'position:fixed;inset:0;z-index:60;background:rgba(0,0,0,.5);display:flex;align-items:flex-end;justify-content:center';
        var box = document.createElement('div');
        box.style.cssText = 'background:#fff;width:100%;max-width:480px;border-radius:24px 24px 0 0;padding:20px;font-family:inherit';
        box.innerHTML = '<p style="font-weight:700;margin:0 0 4px">Sponsored</p><p style="font-size:13px;color:#6b7280;margin:0 0 12px">This ad was paid for by a local business and checked by our team. Is something wrong with it?</p>';
        Object.keys(reasons).forEach(function (key) {
            var b = document.createElement('button');
            b.type = 'button';
            b.textContent = reasons[key];
            b.style.cssText = 'display:block;width:100%;text-align:left;padding:12px 14px;margin:0 0 6px;border:1px solid rgba(0,0,0,.08);border-radius:14px;background:#fff;font-size:14px';
            b.addEventListener('click', function () {
                try {
                    fetch(reportUrl, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify({ id: adId, reason: key, platform: 'pwa' }) }).catch(function () {});
                } catch (e) {}
                box.innerHTML = '<p style="font-weight:700;margin:0 0 4px">Thanks</p><p style="font-size:13px;color:#6b7280;margin:0">Our team will review this ad.</p>';
                setTimeout(function () { sheet.remove(); }, 1500);
            });
            box.appendChild(b);
        });
        var close = document.createElement('button');
        close.type = 'button';
        close.textContent = 'Close';
        close.style.cssText = 'display:block;width:100%;padding:10px;border:0;background:none;color:#6b7280;font-size:14px';
        close.addEventListener('click', function () { sheet.remove(); });
        box.appendChild(close);
        sheet.appendChild(box);
        sheet.addEventListener('click', function (e) { if (e.target === sheet) sheet.remove(); });
        document.body.appendChild(sheet);
    }, true);

    document.addEventListener('DOMContentLoaded', scan);
    new MutationObserver(scan).observe(document.documentElement, { childList: true, subtree: true, attributes: true, attributeFilter: ['data-ad-id'] });
    setInterval(flush, 5000);
    window.addEventListener('pagehide', flush);
    document.addEventListener('visibilitychange', function () { if (document.visibilityState === 'hidden') flush(); });
})();
</script>

/*
 * Seema Cabs Goa — website ads (P9) for the static marketing pages.
 * Fills every <div data-seema-ad></div> with one live ad for this page's
 * group, counts a view when ≥ 50% of it is visible for 1 second, and lets
 * visitors report it. No cookies, no personal data.
 */
(function () {
    var slots = document.querySelectorAll('[data-seema-ad]');
    if (!slots.length || !window.fetch) return;

    var page = (location.pathname.split('/').pop() || 'index').replace(/\.html$/, '') || 'index';
    var reasons = [
        ['misleading', 'Misleading or a scam'],
        ['offensive', 'Offensive or inappropriate'],
        ['alcohol_gambling', 'Alcohol, betting or gambling'],
        ['broken', "Link doesn't work"],
        ['irrelevant', 'Not relevant'],
    ];

    function post(url, body) {
        try {
            return fetch(url, { method: 'POST', keepalive: true, headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify(body) });
        } catch (e) { return Promise.resolve(); }
    }

    function render(slot, ad) {
        slot.innerHTML = '';
        var box = document.createElement('div');
        box.style.cssText = 'position:relative;border-radius:12px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.08)';
        var link = document.createElement('a');
        link.href = ad.click_url || ad.banner_url || '#';
        link.target = '_blank';
        link.rel = 'noopener sponsored';
        var img = document.createElement('img');
        img.src = ad.banner_image;
        img.alt = 'Advertisement';
        img.loading = 'lazy';
        img.style.cssText = 'display:block;width:100%;height:auto';
        link.appendChild(img);
        var tag = document.createElement('span');
        tag.textContent = 'Sponsored';
        tag.style.cssText = 'position:absolute;top:6px;right:6px;background:rgba(0,0,0,.55);color:#fff;font:600 10px/1 Arial,sans-serif;text-transform:uppercase;letter-spacing:.5px;padding:4px 6px;border-radius:4px';
        box.appendChild(link);
        box.appendChild(tag);
        slot.appendChild(box);

        var report = document.createElement('div');
        report.style.cssText = 'text-align:right;font:12px Arial,sans-serif;margin-top:4px';
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = 'Report this ad';
        btn.style.cssText = 'background:none;border:0;color:#6b7280;text-decoration:underline;cursor:pointer;font:inherit;padding:0';
        report.appendChild(btn);
        slot.appendChild(report);
        btn.addEventListener('click', function () {
            report.innerHTML = '';
            reasons.forEach(function (r) {
                var b = document.createElement('button');
                b.type = 'button';
                b.textContent = r[1];
                b.style.cssText = 'margin:2px;padding:4px 8px;border:1px solid #ddd;border-radius:999px;background:#fff;cursor:pointer;font:12px Arial,sans-serif';
                b.addEventListener('click', function () {
                    post('/ads/report', { id: ad.id, reason: r[0], platform: 'website' });
                    report.textContent = 'Thanks — our team will review this ad.';
                });
                report.appendChild(b);
            });
        });

        if ('IntersectionObserver' in window) {
            var timer = null, counted = false;
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (e) {
                    if (counted) return;
                    if (e.isIntersecting && e.intersectionRatio >= 0.5) {
                        timer = timer || setTimeout(function () {
                            counted = true;
                            io.disconnect();
                            post('/ads/i', { platform: 'website', items: [{ id: ad.id, screen: ad.screen || 15 }] });
                        }, 1000);
                    } else if (timer) { clearTimeout(timer); timer = null; }
                });
            }, { threshold: [0, 0.5, 1] });
            io.observe(box);
        }
    }

    fetch('/ads/web?page=' + encodeURIComponent(page), { headers: { 'Accept': 'application/json' } })
        .then(function (r) { return r.ok ? r.json() : null; })
        .then(function (data) {
            if (!data || !data.ad) return;
            slots.forEach(function (slot) { render(slot, data.ad); });
        })
        .catch(function () {});
})();

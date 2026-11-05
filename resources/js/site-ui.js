const chrome = document.querySelector('[data-site-chrome]');
const menuButton = document.querySelector('[data-menu-toggle]');
const menu = document.querySelector('[data-collapsible-nav]');

if (chrome && menuButton && menu) {
    const setMenuOpen = (open) => {
        menu.classList.toggle('is-open', open);
        menuButton.setAttribute('aria-expanded', String(open));
        menuButton.querySelector('.sr-only').textContent = open ? 'Tutup navigasi' : 'Buka navigasi';
    };

    menuButton.addEventListener('click', () => {
        setMenuOpen(menuButton.getAttribute('aria-expanded') !== 'true');
    });

    menu.addEventListener('click', (event) => {
        if (event.target.closest('a')) setMenuOpen(false);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && menuButton.getAttribute('aria-expanded') === 'true') {
            setMenuOpen(false);
            menuButton.focus();
        }
    });

    let scrollFrame = 0;
    window.addEventListener('scroll', () => {
        if (scrollFrame) return;

        scrollFrame = window.requestAnimationFrame(() => {
            chrome.classList.toggle('is-scrolled', window.scrollY > 8);
            scrollFrame = 0;
        });
    }, { passive: true });
}

const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const revealTargets = document.querySelectorAll('[data-reveal], [data-count-up]');

if (reduceMotion || !('IntersectionObserver' in window)) {
    document.querySelectorAll('[data-reveal]').forEach((element) => element.classList.add('is-revealed'));
} else if (revealTargets.length > 0) {
    document.documentElement.classList.add('can-reveal');

    const numberFormat = new Intl.NumberFormat('id-ID');
    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;

            entry.target.classList.add('is-revealed');

            if (entry.target.hasAttribute('data-count-up') && entry.target.dataset.counted !== 'true') {
                entry.target.dataset.counted = 'true';
                const finalValue = Number(entry.target.dataset.countUp);
                const startTime = performance.now();
                const duration = 720;

                const updateCount = (now) => {
                    const progress = Math.min((now - startTime) / duration, 1);
                    const easedProgress = 1 - ((1 - progress) ** 3);
                    entry.target.textContent = numberFormat.format(Math.round(finalValue * easedProgress));

                    if (progress < 1) window.requestAnimationFrame(updateCount);
                };

                if (Number.isFinite(finalValue)) window.requestAnimationFrame(updateCount);
            }

            observer.unobserve(entry.target);
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -24px 0px' });

    revealTargets.forEach((element) => revealObserver.observe(element));
}

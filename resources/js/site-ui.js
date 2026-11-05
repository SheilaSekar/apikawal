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

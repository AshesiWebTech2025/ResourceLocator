// Mobile sidebar toggle (defensive checks)
document.addEventListener('DOMContentLoaded', function () {
    const mobileBtn = document.getElementById('mobile-menu-button');
    const sidebar = document.getElementById('sidebar');

    if (mobileBtn && sidebar) {
        mobileBtn.addEventListener('click', function () {
            // Toggle overlay if present
            const overlay = document.getElementById('sidebar-overlay');
            if (overlay) overlay.classList.toggle('hidden');

            sidebar.classList.toggle('-translate-x-full');
        });
    }

    // Hamburger / sidebar toggle (mobile) - centralized
    const btn = document.getElementById('hamburgerBtn');
    if (btn && sidebar) {
        const openSidebar = () => {
            sidebar.classList.remove('-translate-x-full');
            btn.classList.add('open');
            btn.setAttribute('aria-expanded','true');
        };
        const closeSidebar = () => {
            sidebar.classList.add('-translate-x-full');
            btn.classList.remove('open');
            btn.setAttribute('aria-expanded','false');
        };

        btn.addEventListener('click', () => {
            if (btn.classList.contains('open')) closeSidebar();
            else openSidebar();
        });

        // close when a sidebar link is clicked (mobile)
        document.querySelectorAll('#sidebar a').forEach(a => a.addEventListener('click', () => {
            if (window.innerWidth < 768) closeSidebar();
        }));

        // ensure correct state on resize/load
        const syncOnResize = () => {
            if (window.innerWidth >= 768) {
                sidebar.classList.remove('-translate-x-full');
                btn.classList.remove('open');
                btn.setAttribute('aria-expanded','false');
            } else {
                sidebar.classList.add('-translate-x-full');
            }
        };
        window.addEventListener('resize', syncOnResize);
        syncOnResize();
    }
});

// Basic jQuery animations and simple interactions (defensive checks)
// Only run if jQuery is available
if (typeof jQuery !== 'undefined') {
    $(function () {
        var $content = $('#content');
        var $list = $('#bookings-list');
        var $btn = $('#toggle-btn');

        // Fade the main content in (simple, non-complicated effect)
        if ($content.length) {
            $content.fadeIn(600);
        }

        // Toggle the bookings-list with a slide + subtle styling toggle
        if ($btn.length && $list.length) {
            $btn.on('click', function () {
                $list.slideToggle(300, function () {
                    $list.toggleClass('show', $list.is(':visible'));
                });
            });
        }

        // Small accessibility tweak: pressing Enter on the button triggers it
        if ($btn.length) {
            $btn.on('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    $(this).trigger('click');
                }
            });
        }
    });
}
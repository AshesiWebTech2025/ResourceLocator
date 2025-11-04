// JavaScript for Mobile Sidebar Toggle
document.getElementById('mobile-menu-button').addEventListener('click', function() {
    const sidebar = document.getElementById('sidebar');
    // Check for overlay and toggle it if it exists
    const overlay = document.getElementById('sidebar-overlay');
    if (overlay) {
        overlay.classList.toggle('hidden');
    }
    sidebar.classList.toggle('-translate-x-full');
});

// Hamburger / sidebar toggle (mobile) - centralized
(function(){
  const btn = document.getElementById('hamburgerBtn');
  const sidebar = document.getElementById('sidebar');
  if (!btn || !sidebar) return;

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
  document.addEventListener('DOMContentLoaded', syncOnResize);
})();
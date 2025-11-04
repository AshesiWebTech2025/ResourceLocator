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
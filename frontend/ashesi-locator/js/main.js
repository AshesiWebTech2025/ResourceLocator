// Mobile sidebar toggle (defensive checks)
document.addEventListener('DOMContentLoaded', function () {
    const mobileBtn = document.getElementById('mobile-menu-button');
    const sidebar = document.getElementById('sidebar');

    if (!mobileBtn || !sidebar) return;

    mobileBtn.addEventListener('click', function () {
        // Toggle overlay if present
        const overlay = document.getElementById('sidebar-overlay');
        if (overlay) overlay.classList.toggle('hidden');

        sidebar.classList.toggle('-translate-x-full');
    });
});

// Basic jQuery animations and simple interactions (defensive checks)
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
  $btn.on('keydown', function (e) {
    if (e.key === 'Enter' || e.key === ' ') {
      e.preventDefault();
      $(this).trigger('click');
    }
  });
});
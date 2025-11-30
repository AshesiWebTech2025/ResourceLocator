//kharis code
/*$(document).ready(function () {
            const $panel = $("#allocatorSection");

            // Open panel when clicking the map
            $("#ashesi-map").on("click", function (e) {
                e.stopPropagation(); // prevent document click from firing
                $panel.removeClass("hidden").addClass("open");
            });

            // Close button inside the panel
            $panel.on("click", ".close-btn", function (e) {
                e.preventDefault();
                e.stopPropagation();
                $panel.removeClass("open");
                setTimeout(() => $panel.addClass("hidden"), 300);
            });

            // Prevent clicks inside the panel from closing it
            $panel.on("click", function (e) {
                e.stopPropagation();
            });

            // Clicking outside closes the panel
            $(document).on("click", function (e) {
                if (!$(e.target).closest("#allocatorSection, #ashesi-map").length) {
                    if ($panel.hasClass("open")) {
                        $panel.removeClass("open");
                        setTimeout(() => $panel.addClass("hidden"), 300);
                    }
                }
            });

            // Handle form submission
            $("#allocatorForm").on("submit", function (e) {
                e.preventDefault();

                const name = $("#name").val().trim();
                const type = $("#type").val().trim();
                const capacity = $("#capacity").val().trim(); // optional
                const description = $("#description").val().trim();

                // Validate required fields
                if (!name || !type || !description) {
                    alert("Please fill in the Name, Type, and Description fields.");
                    return;
                }

                alert(
                    `Location saved (frontend only)`
                );

                // Reset and close form
                this.reset();
                $panel.removeClass("open");
                setTimeout(() => $panel.addClass("hidden"), 300);
            });
            //handle sidebar
             document.getElementById('mobile-menu-button').addEventListener('click', function () {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        });
        });



//end of kharis code*/


//start of inez code
// <!-- Inline JS: static data + modal logic (swap to dynamic later) -->
(function () {
    // Static hotspots array (replaceable by dynamic fetch)
    const hotspots = [
        { id: 1, name: 'Library - Main Hall', tag: 'library_main', coords: [0, 0] },
        { id: 2, name: 'Engineering Block - Room 204', tag: 'eng_204', coords: [0, 0] },
        { id: 3, name: 'Student Center - Seminar Room', tag: 'student_seminar', coords: [0, 0] },
        { id: 4, name: 'Science Lab - Hotspot A', tag: 'sci_lab_a', coords: [0, 0] }
    ];

    // Render list
    function renderList() {
        const container = document.getElementById('hotspot-list');
        container.innerHTML = '';
        hotspots.forEach(h => {
            const el = document.createElement('button');
            el.type = 'button';
            el.className = 'w-full text-left p-3 rounded-lg hover:bg-gray-50 border border-transparent hover:border-gray-100 flex items-center justify-between';
            el.innerHTML = `
			<div>
			  <div class="font-medium text-gray-800">${h.name}</div>
			  <div class="text-xs text-gray-500">${h.tag}</div>
			</div>
			<div class="text-xs text-ashesi-maroon">Edit</div>
		  `;
            el.addEventListener('click', () => openModal(h));
            container.appendChild(el);
        });
    }

    // Modal logic
    const modal = document.getElementById('sessionModal');
    const resourceNameEl = document.getElementById('resourceName');
    const slotList = document.getElementById('slotList');
    let currentResource = null;
    // store slots in-memory for demo (in real app use API)
    const resourceSlots = {
        1: [{ day: 'monday', start: '09:00', end: '11:00' }],
        2: [{ day: 'tuesday', start: '13:00', end: '15:00' }],
        3: [],
        4: [{ day: 'wednesday', start: '10:00', end: '12:00' }]
    };

    function openModal(resource) {
        currentResource = resource;
        document.getElementById('modalTitle').textContent = 'Edit Times — ' + resource.name;
        resourceNameEl.textContent = resource.name;
        renderSlots();
        $(modal).removeClass('hidden').hide().fadeIn(150);
    }

    function closeModal() {
        $(modal).fadeOut(150, function () { $(modal).addClass('hidden'); });
        currentResource = null;
    }

    function renderSlots() {
        slotList.innerHTML = '';
        const slots = resourceSlots[currentResource.id] || [];
        if (slots.length === 0) {
            slotList.innerHTML = '<li class="text-sm text-gray-500">No slots yet.</li>';
            return;
        }
        slots.forEach((s, idx) => {
            const li = document.createElement('li');
            li.className = 'flex items-center justify-between bg-gray-50 p-2 rounded';
            li.innerHTML = `
			<div class="text-sm">${capitalize(s.day)} — ${s.start} to ${s.end}</div>
			<div class="flex gap-2">
			  <button class="text-sm text-ashesi-maroon" data-idx="${idx}">Remove</button>
			</div>
		  `;
            li.querySelector('button').addEventListener('click', () => {
                resourceSlots[currentResource.id].splice(idx, 1);
                renderSlots();
            });
            slotList.appendChild(li);
        });
    }

    // Add slot button
    document.getElementById('addSlot').addEventListener('click', function () {
        if (!currentResource) return alert('Select a resource first');
        const day = document.getElementById('daySelect').value;
        const start = document.getElementById('startTime').value;
        const end = document.getElementById('endTime').value;
        if (!start || !end) { return alert('Please enter start and end times'); }
        if (!resourceSlots[currentResource.id]) resourceSlots[currentResource.id] = [];
        resourceSlots[currentResource.id].push({ day, start, end });
        renderSlots();
    });

    // Save & Cancel
    document.getElementById('saveTimes').addEventListener('click', function () {
        // Placeholder: in real app send resourceSlots[currentResource.id] to backend
        console.log('Saving slots for', currentResource.id, resourceSlots[currentResource.id]);
        closeModal();
    });
    document.getElementById('cancelTimes').addEventListener('click', closeModal);
    document.getElementById('closeModal').addEventListener('click', closeModal);

    function capitalize(s) { return s.charAt(0).toUpperCase() + s.slice(1); }

    // initial render
    renderList();

    // Expose for debugging
    window.__hotspots = hotspots;
    window.__resourceSlots = resourceSlots;

})();
//end of inez code

// ashesi-locator JavaScript (from ashesi-locator/js/main.js)
// Mobile sidebar toggle (defensive checks)
/*document.addEventListener('DOMContentLoaded', function () {

    const sidebar = document.getElementById('sidebar');
    const mobileBtn = document.getElementById('mobile-menu-button');   
    const hamburgerBtn = document.getElementById('hamburgerBtn');       
    const overlay = document.getElementById('sidebar-overlay');

    if (!sidebar) return;

    
    const openSidebar = () => {
        sidebar.classList.remove('-translate-x-full');
        document.body.classList.add('mobile-nav-open');
        if (hamburgerBtn) {
            hamburgerBtn.classList.add('open');
            hamburgerBtn.setAttribute('aria-expanded', 'true');
        }
        if (overlay) overlay.classList.remove('hidden');
    };

    const closeSidebar = () => {
        sidebar.classList.add('-translate-x-full');
        document.body.classList.remove('mobile-nav-open');
        if (hamburgerBtn) {
            hamburgerBtn.classList.remove('open');
            hamburgerBtn.setAttribute('aria-expanded', 'false');
        }
        if (overlay) overlay.classList.add('hidden');
    };

    const isMobile = () => window.innerWidth < 768;

    //button click handlers
    if (mobileBtn) {
        mobileBtn.addEventListener('click', () => {
            if (sidebar.classList.contains('-translate-x-full')) openSidebar();
            else closeSidebar();
        });
    }

    if (hamburgerBtn) {
        hamburgerBtn.addEventListener('click', () => {
            if (hamburgerBtn.classList.contains('open')) closeSidebar();
            else openSidebar();
        });
    }

    //click outside sidebar to close (mobile)
    document.addEventListener('click', (e) => {
        if (!isMobile()) return; // only mobile behavior

        const clickedInside = sidebar.contains(e.target);
        const clickedHamburger = hamburgerBtn && hamburgerBtn.contains(e.target);
        const clickedMobileBtn = mobileBtn && mobileBtn.contains(e.target);

        const sidebarOpen = hamburgerBtn
            ? hamburgerBtn.classList.contains('open')
            : !sidebar.classList.contains('-translate-x-full');

        if (sidebarOpen && !clickedInside && !clickedHamburger && !clickedMobileBtn) {
            closeSidebar();
        }
    });

    //resizing
    const syncOnResize = () => {
        if (!isMobile()) {
            // Desktop: sidebar always open
            sidebar.classList.remove('-translate-x-full');
            if (overlay) overlay.classList.add('hidden');
            if (hamburgerBtn) {
                hamburgerBtn.classList.remove('open');
                hamburgerBtn.setAttribute('aria-expanded', 'false');
            }
        } else {
            // Mobile: sidebar closed by default
            sidebar.classList.add('-translate-x-full');
        }
    };

    window.addEventListener('resize', syncOnResize);
    syncOnResize();


    //sidebar link highlighting
    try {
        const path = window.location.pathname || '';
        const file = path.substring(path.lastIndexOf('/') + 1).toLowerCase();
        const fileBase = file.replace(/\.[^/.]+$/, ''); // strip extension

        const links = document.querySelectorAll('#sidebar a');
        if (!links || links.length === 0) return;

        links.forEach(a => {
            // remove old active styling
            a.classList.remove('bg-white/20', 'bg-ashesi-maroon', 'text-white');
            a.removeAttribute('aria-current');

            const href = a.getAttribute('href') || '';
            const hrefFile = href.substring(href.lastIndexOf('/') + 1).toLowerCase();
            const hrefBase = hrefFile.replace(/\.[^/.]+$/, '');

            // match base name → smooth php/html navigation
            if (hrefBase && fileBase && hrefBase === fileBase) {
                a.classList.add('bg-white/20');
                a.setAttribute('aria-current', 'page');
            }

            // special root/home case
            if ((!file || fileBase === '') &&
                (hrefBase === 'home' || hrefBase === 'home.php' || hrefBase === 'home.html')) {
                a.classList.add('bg-white/20');
                a.setAttribute('aria-current', 'page');
            }
        });
    } catch (e) {
        console.warn("Active link highlighter failed:", e);
    }

});*/


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
// end of ashesi-locator JavaScript


//kharis code
$(document).ready(function () {
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

//end of kharis code


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
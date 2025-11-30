<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once('../backend/dbConnector.php'); 
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    $_SESSION['message'] = "Please log in to access this page.";
    $_SESSION['message_type'] = "error";
    header('Location: login_signup.php'); 
    exit();
}
$user_role = $_SESSION['role'] ?? 'Admin';
$user_first_name = $_SESSION["first_name"];
$user_last_name = $_SESSION["last_name"];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ashesi Campus Resource Locator</title>
    <!-- Load Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Configure Tailwind for custom colors (matching the maroon/red scheme) and font -->
    <script src="js/tailwindConfig.js">
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <!-- map box integration below-->
    <script src='https://api.mapbox.com/mapbox-gl-js/v3.4.0/mapbox-gl.js'></script>
    <link href='https://api.mapbox.com/mapbox-gl-js/v3.4.0/mapbox-gl.css' rel='stylesheet' />
</head>

<body class="bg-gray-50 font-sans antialiased flex h-screen overflow-hidden">

    <!-- 1. Sidebar Navigation (Left Column) -->
    <aside id="sidebar"
        class="fixed inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition duration-200 ease-in-out bg-ashesi-maroon text-white w-64 flex flex-col z-20 shadow-xl">

        <!-- Sidebar Header/Logo -->
        <div class="p-6 flex items-center h-16 border-b border-white/20">
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
            </svg>
            <span class="text-xl font-semibold">Ashesi Locator</span>
        </div>

        <!-- Primary Navigation Links -->
        <nav class="flex-grow p-4 space-y-2">
            <a href="resourceAllocator.php"
                class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                    </path>
                </svg>
                Resource Allocator
            </a>
            <a href="available_sessions.php"
                class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Available Sessions
            </a>
            
        </nav>

        <!-- Bottom Settings and Sign Out Links -->
        <div class="p-4 space-y-2 border-t border-white/20">
            <a href="#"
                class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z">
                    </path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Settings
            </a>
            <a href="login_signup.php"
                class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                    </path>
                </svg>
                Sign Out
            </a>
        </div>
    </aside>

    <!-- 2. Main Content Area (Right Column) -->
    <div class="flex-1 flex flex-col overflow-y-auto main-content">

        <!-- Main Content Header (Student Portal) -->
        <header class="bg-white shadow-sm h-16 flex justify-between items-center px-6 md:px-10 sticky top-0 z-10">
            <button id="hamburgerBtn" class="hamburger-btn md:hidden mr-4 p-2 focus:outline-none focus:ring-2 focus:ring-ashesi-maroon rounded" aria-label="Toggle menu" aria-expanded="false" type="button">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                </svg>
            </button>
            <h1 class="text-xl md:text-2xl font-semibold text-gray-800">Ashesi Campus Resource Locator</h1>
            <div
                class="flex items-center text-ashesi-maroon font-medium border border-ashesi-maroon rounded-full py-1 px-4 cursor-pointer hover:bg-ashesi-maroon hover:text-white transition duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span class="text-sm md:text-base hidden sm:inline">Admin Portal</span>
            </div>

            <!-- Mobile Menu Button (Hidden on Desktop) 
            <button id="mobile-menu-button" class="md:hidden p-2 text-ashesi-maroon">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                    </path>
                </svg>
            </button>-->
        </header>

        <!-- Body -->
        <main class="p-6 md:p-10 flex-1 relative overflow-hidden">
            <h1 class="text-2xl font-semibold text-gray-800 mb-4">Resource Allocator</h1>

            <div id="map-container" class="lg:col-span-3 min-h-[300px] lg:min-h-full">
                <div id="ashesi-map"
                    class="w-full h-[400px] bg-gray-200 flex items-center justify-center text-gray-500 cursor-pointer">
                    Click anyhwere to simulate mouse click
                </div>

            </div>


            <div id="allocatorSection"
                class="hidden fixed top-0 right-0 h-full w-80 bg-white shadow-xl border-l border-gray-200 p-6 z-30">
                <button type="button"
                    class="close-btn absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-2xl">&times;</button>

                <h2 class="text-xl font-semibold text-ashesi-maroon mb-4">Allocate a Resource</h2>


                <form method='POST' action='../backend/resourceAllocator.php' id="allocatorForm"
                    class="space-y-4 max-w-lg">
                    <div>
                        <label for="name" class="block text-gray-700 mb-1">Name:</label>
                        <input type="text" id="name" name="name" required placeholder="Enter resource name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-ashesi-maroon">
                    </div>

                    <div>
                        <label for="type" class="block text-gray-700 mb-1">Type:</label>
                        <select id="type" name="type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-ashesi-maroon bg-white">
                            <option value="" disabled selected>Select the type</option>
                        </select>
                    </div>

                    <div>
                        <label for="capacity" class="block text-gray-700 mb-1">Capacity:</label>
                        <input type="number" id="capacity" name="capacity" placeholder="Enter resource capacity"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-ashesi-maroon">
                    </div>

                    <div>
                        <label for="description" class="block text-gray-700 mb-1">Description:</label>
                        <input type="text" id="description" name="description" required
                            placeholder="Enter resource description"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-ashesi-maroon">
                    </div>

                    <div>
                        <label for="latitude" class="block text-gray-700 mb-1">Latitude:</label>
                        <input type="number" id="latitude" name="latitude" required step="0.00001" min="5.74" max="5.77"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-ashesi-maroon">
                    </div>

                    <div>
                        <label for="longitude" class="block text-gray-700 mb-1">Longitude:</label>
                        <input type="number" id="longitude" name="longitude" required step="0.00001" min="-0.23"
                            max="-0.20"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-ashesi-maroon">
                    </div>




                    <button type="submit" name="submitResource"
                        class="bg-ashesi-maroon text-white px-4 py-2 rounded-md hover:bg-red-800 transition">
                        Save
                    </button>

                    <br>
                    <br>
                    <a href="#" id='addTypeLink'
                        style='display:block; margin-top:5px; color:blue; text-decoration:underline;'>Add a new resource
                        type</a>
                </form>
            </div>

            <div id='typeModal'
                class='hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50' role="dialog"
                aria-modal="true" aria-labelledby="modal-title">
                <div
                    class="relative top-20 mx-auto p-6 border w-96 shadow-2xl rounded-lg bg-white transform transition-all duration-300 ease-out">

                    <div class="flex justify-between items-center border-b pb-3 mb-4">
                        <h3 id="modal-title" class="text-xl font-bold text-ashesi-maroon">Add New Resource Type</h3>
                        <button type="button"
                            class="close-modal text-gray-400 hover:text-gray-900 rounded-lg text-lg p-1.5 ml-auto inline-flex items-center">
                            &times; </button>
                    </div>

                    <form action="../backend/addType.php" method="POST" id="addTypeForm" class="space-y-4">
                        <div>
                            <label for="type_name" class="block text-sm font-medium text-gray-700">Type Name:</label>
                            <input type="text" id="type_name" required placeholder="e.g. Lecture Hall, Lab, Dorm"
                                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-ashesi-maroon">
                        </div>
                        <div class="flex justify-end pt-2">
                            <button type="submit"
                                class="bg-ashesi-maroon text-white px-4 py-2 rounded-md hover:bg-red-800 transition text-sm font-medium">
                                Save Type
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 p-6 md:px-10 mt-auto">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pb-6">

                    <!-- Col 1: Ashesi University -->
                    <div>
                        <h4 class="text-lg font-bold text-gray-800 mb-3">Ashesi University</h4>
                        <p class="text-gray-600">Campus Resource Locator</p>
                    </div>

                    <!-- Col 2: Quick Links -->
                    <div>
                        <h4 class="text-lg font-bold text-gray-800 mb-3">Quick Links</h4>
                        <ul class="space-y-1 text-gray-600">
                            <li><a href="#" class="hover:text-ashesi-maroon">Resources</a></li>
                            <li><a href="#" class="hover:text-ashesi-maroon">Campus Map</a></li>
                            <li><a href="#" class="hover:text-ashesi-maroon">My Bookings</a></li>
                        </ul>
                    </div>

                    <!-- Col 3: Contact -->
                    <div>
                        <h4 class="text-lg font-bold text-gray-800 mb-3">Contact</h4>
                        <ul class="space-y-1 text-gray-600">
                            <li>support@ashesi.edu.gh</li>
                            <li>+233 XX XXX XXXX</li>
                        </ul>
                    </div>
                </div>

                <div class="text-center pt-4 border-t border-gray-100">
                    <p class="text-sm text-gray-500">© 2025 Ashesi University. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
      const hamburgerBtn = document.getElementById('hamburgerBtn');
      const sidebar = document.getElementById('sidebar');

      if (!hamburgerBtn || !sidebar) return;

      // Function to check if we're on mobile
      const isMobile = () => window.innerWidth < 768;

      // Initialize sidebar state based on screen size
      const initializeSidebar = () => {
        if (isMobile()) {
          // Mobile: sidebar hidden by default
          sidebar.classList.add('-translate-x-full');
          hamburgerBtn.setAttribute('aria-expanded', 'false');
          document.body.classList.remove('mobile-nav-open');
        } else {
          // Desktop: sidebar always visible
          sidebar.classList.remove('-translate-x-full');
          hamburgerBtn.setAttribute('aria-expanded', 'true');
          document.body.classList.remove('mobile-nav-open');
        }
      };

      // Toggle sidebar (mobile only)
      const toggleSidebar = () => {
        if (!isMobile()) return; // Don't toggle on desktop

        const isExpanded = hamburgerBtn.getAttribute('aria-expanded') === 'true';
        
        if (isExpanded) {
          // Close sidebar
          sidebar.classList.add('-translate-x-full');
          hamburgerBtn.setAttribute('aria-expanded', 'false');
          document.body.classList.remove('mobile-nav-open');
        } else {
          // Open sidebar
          sidebar.classList.remove('-translate-x-full');
          hamburgerBtn.setAttribute('aria-expanded', 'true');
          document.body.classList.add('mobile-nav-open');
        }
      };

      // Close sidebar (mobile only)
      const closeSidebar = () => {
        if (!isMobile()) return;
        
        sidebar.classList.add('-translate-x-full');
        hamburgerBtn.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('mobile-nav-open');
      };

      // Hamburger button click
      hamburgerBtn.addEventListener('click', (e) => {
        e.stopPropagation(); // Prevent event from bubbling
        toggleSidebar();
      });

      // Close sidebar when clicking navigation links (mobile only)
      sidebar.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
          closeSidebar();
        });
      });

      // Close sidebar when clicking outside (mobile only)
      document.addEventListener('click', (e) => {
        if (!isMobile()) return;

        const isOpen = hamburgerBtn.getAttribute('aria-expanded') === 'true';
        if (!isOpen) return;

        const clickedInsideSidebar = sidebar.contains(e.target);
        const clickedHamburger = hamburgerBtn.contains(e.target);

        if (!clickedInsideSidebar && !clickedHamburger) {
          closeSidebar();
        }
      });

      // Handle window resize
      let resizeTimer;
      window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
          initializeSidebar();
        }, 250); // Debounce resize events
      });

      // Initialize on load
      initializeSidebar();
    });
  </script>

    <!--JQuery Script for Form Toggle-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/map.js"></script>
    <!-- load main js -->
    <script src="js/resourceAllocator.js"></script>
    <!--<script defer src="js/main.js"></script>-->
    
    <script>
        document.addEventListener('DOMContentLoaded', function( ){
            const dayOrder = {'monday':1,'tuesday':2,'wednesday':3,'thursday':4,'friday':5,'saturday':6,'sunday':7};
            let hotspots = <?php echo json_encode($initialHotspots); ?>;
            let currentResource = null;
            let resourceSlots = {};

            function showMessage(message, type = 'error') {…}

            function hideMessage() {…}

            function formatTime(hour, minute) {…}

            function sortSlots(slots) {…}

            function renderSlots() {…}

            function openModal(resource) {

            function closeModal() {…}

            function renderResourceList() {…}

            // new helpers to normalize time/day for server
            function toTitleCaseDay(dayRaw) {
                if (!dayRaw) return '';
                return dayRaw.trim().toLowerCase().replace(/^\w/, c => c.toUpperCase());
            }

            function normalizeTimeParts(hour, minute) {
                // ensure numeric and within ranges; returns HH:MM:SS
                let h = parseInt(hour, 10);
                let m = parseInt(minute, 10);
                if (isNaN(h) || isNaN(m)) return null;
                if (h < 0 || h > 23 || m < 0 || m > 59) return null;
                return ('0' + h).slice(-2) + ':' + ('0' + m).slice(-2) + ':00';
            }

            // addSlot should already push to resourceSlots; only update validation when saving
            document.getElementById('addSlot').addEventListener('click', function() {
                renderSlots();
            });

            // saveTimes now validates and normalizes data before sending to server
            document.getElementById('saveTimes').addEventListener('click', function() {
                if (!currentResource) {
                    showMessage('No resource selected', 'error');
                    return;
                }

                // collect slots from UI (assumes resourceSlots[currentResource.resource_id] exists)
                const slots = resourceSlots[currentResource.resource_id] || [];
                if (!slots.length) {
                    showMessage('No slots to save', 'error');
                    return;
                }

                // Validate and normalize
                const payloadSlots = [];
                const validDays = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
                for (let i = 0; i < slots.length; i++) {
                    const s = slots[i];

                    // Accept either start/end string, or parts as numbers
                    let start = null, end = null, day = null;
                    if (s.start && s.end) {
                        // attempt to normalize if format is HH:MM or HH:MM:SS
                        // normalize / pad to seconds
                        const partsStart = s.start.split(':');
                        const partsEnd = s.end.split(':');
                        if (partsStart.length >= 2 && partsEnd.length >= 2) {
                            let sh = parseInt(partsStart[0], 10), sm = parseInt(partsStart[1], 10);
                            let eh = parseInt(partsEnd[0], 10), em = parseInt(partsEnd[1], 10);
                            let startNorm = normalizeTimeParts(sh, sm);
                            let endNorm = normalizeTimeParts(eh, em);
                            if (!startNorm || !endNorm) {
                                showMessage('Invalid time provided for a slot', 'error');
                                return;
                            }
                            start = startNorm; end = endNorm;
                        } else {
                            showMessage('Invalid time format. Use HH:MM or HH:MM:SS', 'error');
                            return;
                        }
                    } else if ('start_hour' in s && 'start_minute' in s && 'end_hour' in s && 'end_minute' in s) {
                        start = normalizeTimeParts(s.start_hour, s.start_minute);
                        end = normalizeTimeParts(s.end_hour, s.end_minute);
                        if (!start || !end) { showMessage('Invalid hour/minute for a slot', 'error'); return; }
                    } else {
                        showMessage('Slot missing time fields', 'error');
                        return;
                    }

                    if (!s.day) { showMessage('Slot missing day', 'error'); return; }
                    day = toTitleCaseDay(s.day); // "Monday" etc.
                    if (!validDays.includes(day)) { showMessage('Invalid day: ' + s.day, 'error'); return; }

                    // ensure start < end
                    const startSecs = (parseInt(start.substring(0,2),10) * 3600) + (parseInt(start.substring(3,5),10) * 60);
                    const endSecs = (parseInt(end.substring(0,2),10) * 3600) + (parseInt(end.substring(3,5),10) * 60);
                    if (startSecs >= endSecs) { showMessage('Start time must be before end time', 'error'); return; }

                    payloadSlots.push({ day: day, start: start, end: end });
                }

                // Build payload
                const payload = {
                    resource_id: currentResource.resource_id,
                    slots: payloadSlots
                };

                // POST to updateSlots
                fetch('available_sessions.php?action=updateSlots', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                })
                .then(r => r.json())
                .then(resp => {
                    if (resp.success) {
                        showMessage('Availability saved', 'success');
                        // refresh UI
                        loadAvailabilityForResource(currentResource.resource_id);
                        closeModal();
                    } else {
                        showMessage(resp.message || 'Failed to save availability', 'error');
                    }
                })
                .catch(err => {
                    console.error(err);
                    showMessage('Failed to save availability: ' + err.message, 'error');
                });
            });
            });

            document.getElementById('closeModal').addEventListener('click', closeModal);
            document.getElementById('cancelTimes').addEventListener('click', closeModal);
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeModal();
            });

            renderResourceList();
        });
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', () => {
          const hamburgerBtn = document.getElementById('hamburgerBtn');
          const sidebar = document.getElementById('sidebar');

          if (hamburgerBtn && sidebar) {
              hamburgerBtn.addEventListener('click', () => {
                  const isExpanded = hamburgerBtn.getAttribute('aria-expanded') === 'true';
                  
                  // Toggle sidebar visibility class
                  sidebar.classList.toggle('-translate-x-full');
                  
                  // Toggle button state and body overflow for mobile
                  hamburgerBtn.setAttribute('aria-expanded', !isExpanded);

                  if (!isExpanded) {
                      // Lock body scrolling when sidebar is open
                      document.body.classList.add('mobile-nav-open');
                  } else {
                      // Re-enable body scrolling
                      document.body.classList.remove('mobile-nav-open');
                  }
              });
              sidebar.querySelectorAll('a').forEach(link => {
                  link.addEventListener('click', () => {
                      if (window.innerWidth < 768) {
                          sidebar.classList.add('-translate-x-full');
                          hamburgerBtn.setAttribute('aria-expanded', 'false');
                          document.body.classList.remove('mobile-nav-open');
                      }
                  });
              });
          }
      });
    </script>

</body>


</html>
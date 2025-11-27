<?php
    // Fetch available resources from database
    require_once '../backend/dbConnector.php';
    $db = connectDB();
    $resources = [];
    
    if ($db) {
        $stmt = $db->prepare("
            SELECT r.resource_id, r.name, r.capacity, r.description, rt.type_name
            FROM Resources r
            JOIN Resource_Types rt ON r.type_id = rt.type_id
            WHERE r.is_bookable = 1
            ORDER BY rt.type_name, r.name
        ");
        
        if ($stmt) {
            $results = $stmt->execute();
            if ($results) {
                while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
                    $resources[] = $row;
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Ashesi Campus Resource Locator</title>
    <link rel="stylesheet" href="./css/style.css">
    
    <!-- Mapbox GL JS -->
    <script src='https://api.mapbox.com/mapbox-gl-js/v3.4.0/mapbox-gl.js'></script>
    <link href='https://api.mapbox.com/mapbox-gl-js/v3.4.0/mapbox-gl.css' rel='stylesheet' />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'ashesi-maroon': '#800020',
                        'ashesi-light': '#fef2f2',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 font-sans antialiased flex h-screen overflow-hidden">

    <button id="hamburgerBtn" class="hamburger-btn" aria-label="Toggle menu" aria-expanded="false" type="button">
        <span></span><span></span><span></span>
    </button>

    <aside id="sidebar" class="fixed inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition duration-200 ease-in-out bg-ashesi-maroon text-white w-64 flex flex-col z-20 shadow-xl">
        <div class="p-6 flex items-center h-16 border-b border-white/20">
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
            <span class="text-xl font-semibold">Ashesi Locator</span>
        </div>
        <nav class="flex-grow p-4 space-y-2">
            <a href="home.php" id="nav-home" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">Home</a>
            <a href="resourceLocator.php" id="nav-map" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">Campus Map</a>
            <a href="bookings.php" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">My Bookings</a>
            <a href="about.php" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">About</a>
            <a href="software_architecture.php" id="nav-architecture" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">Architecture</a>
        </nav>
        <div class="p-4 space-y-2 border-t border-white/20">
            <a href="#" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">Settings</a>
            <a href="login_signup.php" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">Sign Out</a>
        </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-y-auto main-content">
        <header class="bg-white shadow-sm h-16 flex justify-between items-center px-6 md:px-10 sticky top-0 z-10">
            <h1 class="text-xl md:text-2xl font-semibold text-gray-800">Home</h1>
            <div class="flex items-center text-ashesi-maroon font-medium border border-ashesi-maroon rounded-full py-1 px-4 cursor-pointer hover:bg-ashesi-maroon hover:text-white transition duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                <span class="text-sm md:text-base">Student Portal</span>
            </div>
            <button id="mobile-menu-button" class="md:hidden p-2 text-ashesi-maroon">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
        </header>

        <main class="p-6 md:p-10 flex-1">
            <!-- Home View -->
            <section id="home-view">
                <section class="bg-white p-8 rounded-xl shadow-lg mb-10 border border-gray-100">
                    <h2 class="text-3xl font-bold text-gray-900 mb-3">Welcome to the Ashesi Resource Locator</h2>
                    <p class="text-lg text-gray-600 mb-6">Your one-stop portal to find and book campus resources, from study rooms to faculty office hours and support services.</p>
                    <button onclick="openBookingModal()" class="inline-block bg-ashesi-maroon text-white font-semibold py-3 px-6 rounded-lg shadow-lg hover:bg-ashesi-maroon/90 transition duration-200">Book a Resource</button>
                </section>

                <section>
                    <h3 class="text-2xl font-semibold text-gray-800 mb-6">What You Can Do Here</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:border-ashesi-maroon cursor-pointer">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4 bg-ashesi-light text-ashesi-maroon transition-all duration-300 group-hover:scale-110">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <h4 class="text-xl font-bold text-gray-800 mb-2 transition-colors duration-300 hover:text-ashesi-maroon">Find Resources</h4>
                            <p class="text-gray-600">Easily search for academic advisors, counselors, health services, and study spaces available on campus.</p>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:border-ashesi-maroon cursor-pointer">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4 bg-ashesi-light text-ashesi-maroon transition-all duration-300 group-hover:scale-110">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <h4 class="text-xl font-bold text-gray-800 mb-2 transition-colors duration-300 hover:text-ashesi-maroon">Book Appointments</h4>
                            <p class="text-gray-600">Check real-time availability and schedule meetings with faculty or support staff directly through the portal.</p>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 transition-all duration-300 hover:shadow-2xl hover:scale-105 hover:border-ashesi-maroon cursor-pointer">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4 bg-ashesi-light text-ashesi-maroon transition-all duration-300 group-hover:scale-110">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <h4 class="text-xl font-bold text-gray-800 mb-2 transition-colors duration-300 hover:text-ashesi-maroon">Navigate Campus</h4>
                            <p class="text-gray-600">Use the interactive campus map to find the exact location of any resource, office, or classroom.</p>
                        </div>
                    </div>
                </section>
            </section>

            <!-- Map View (Hidden by default) -->
            <section id="map-view" class="hidden flex-1 h-full flex flex-col">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Interactive Campus Map</h2>
                <div class="flex-1 grid grid-cols-1 lg:grid-cols-4 gap-4 md:gap-6 min-h-0">
                    <!-- Map container -->
                    <div id="map-container" class="lg:col-span-3 min-h-[500px] lg:min-h-full">
                        <div id="ashesi-map" class="w-full h-full"></div>
                    </div>
                    <!-- Resource search/list view -->
                    <div class="lg:col-span-1 bg-white p-4 rounded-xl shadow-lg border border-gray-100 flex flex-col overflow-hidden">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Find a Resource</h3>
                        <!-- Search Input -->
                        <div class="mb-4">
                            <input type="text" id="resource-search" placeholder="Search by name, type, or capacity..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-ashesi-maroon focus:border-ashesi-maroon transition duration-150">
                        </div>                        
                        <!-- Scrollable resource list -->
                        <div id="resource-list" class="flex-1 overflow-y-auto space-y-3 pr-2">
                            <?php if (!empty($resources)): ?>
                                <?php 
                                $currentCategory = '';
                                foreach ($resources as $resource): 
                                    // Show category header when it changes
                                    if ($currentCategory !== $resource['type_name']):
                                        if ($currentCategory !== '') echo '<div class="pt-2"></div>';
                                        $currentCategory = $resource['type_name'];
                                ?>
                                        <div class="text-xs font-bold text-ashesi-maroon uppercase tracking-wide pt-2 pb-1">
                                            <?php echo htmlspecialchars($resource['type_name']); ?>s
                                        </div>
                                <?php endif; ?>
                                
                                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-ashesi-light hover:border-ashesi-maroon transition duration-150 resource-item" 
                                     data-resource-id="<?php echo $resource['resource_id']; ?>"
                                     data-name="<?php echo htmlspecialchars($resource['name']); ?>"
                                     data-type="<?php echo htmlspecialchars($resource['type_name']); ?>"
                                     data-capacity="<?php echo $resource['capacity']; ?>">
                                    <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($resource['name']); ?></p>
                                    <p class="text-sm text-gray-500"><?php echo htmlspecialchars($resource['type_name']); ?> | Capacity: <?php echo $resource['capacity']; ?></p>
                                    <p class="text-xs text-gray-400 mt-1"><?php echo htmlspecialchars($resource['description']); ?></p>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-sm text-center text-gray-400 p-4">
                                    No resources available at the moment.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="bg-white border-t border-gray-200 p-6 md:px-10 mt-auto">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pb-6">
                    <div>
                        <h4 class="text-lg font-bold text-gray-800 mb-3">Ashesi University</h4>
                        <p class="text-gray-600">Campus Resource Locator</p>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-800 mb-3">Quick Links</h4>
                        <ul class="space-y-1 text-gray-600">
                            <li><a href="#" class="hover:text-ashesi-maroon">Resources</a></li>
                            <li><a href="#" class="hover:text-ashesi-maroon">Campus Map</a></li>
                            <li><a href="bookings.php" class="hover:text-ashesi-maroon">My Bookings</a></li>
                        </ul>
                    </div>
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

    <!-- Booking Modal -->
    <div id="bookingModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-200 p-6 flex justify-between items-center">
                <h3 class="text-2xl font-bold text-gray-900">Book a Resource</h3>
                <button onclick="closeBookingModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form action="../backend/create_booking.php" method="POST" class="p-6 space-y-4">
                <!-- Resource Selection Dropdown -->
                <div>
                    <label for="resource_id" class="block text-sm font-medium text-gray-700 mb-2">Select Resource</label>
                    <select name="resource_id" id="resource_id" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ashesi-maroon focus:border-ashesi-maroon transition">
                        <option value="">-- Choose a resource --</option>
                        <?php 
                            $currentType = '';
                            foreach ($resources as $resource): 
                                if ($currentType !== $resource['type_name']) {
                                    if ($currentType !== '') echo '</optgroup>';
                                    echo '<optgroup label="' . htmlspecialchars($resource['type_name']) . '">';
                                    $currentType = $resource['type_name'];
                                }
                        ?>
                            <option value="<?php echo $resource['resource_id']; ?>">
                                <?php echo htmlspecialchars($resource['name']); ?> (Capacity: <?php echo $resource['capacity']; ?>)
                            </option>
                        <?php 
                            endforeach; 
                            if ($currentType !== '') echo '</optgroup>';
                        ?>
                    </select>
                </div>

                <!-- Date Selection -->
                <div>
                    <label for="booking_date" class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                    <input type="date" name="booking_date" id="booking_date" required
                           min="<?php echo date('Y-m-d'); ?>"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ashesi-maroon focus:border-ashesi-maroon transition">
                </div>

                <!-- Time Selection -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                        <input type="time" name="start_time" id="start_time" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ashesi-maroon focus:border-ashesi-maroon transition">
                    </div>
                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                        <input type="time" name="end_time" id="end_time" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ashesi-maroon focus:border-ashesi-maroon transition">
                    </div>
                </div>

                <!-- Purpose -->
                <div>
                    <label for="purpose" class="block text-sm font-medium text-gray-700 mb-2">Purpose</label>
                    <textarea name="purpose" id="purpose" rows="3" required
                              placeholder="e.g., Group study session, Project meeting..."
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ashesi-maroon focus:border-ashesi-maroon transition"></textarea>
                </div>

                <!-- Submit Button -->
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeBookingModal()"
                            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-ashesi-maroon text-white rounded-lg hover:bg-ashesi-maroon/90 transition font-semibold">
                        Book Resource
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="./js/map.js"></script>
    <script src="./js/main.js"></script>
    <script>
        function openBookingModal() {
            document.getElementById('bookingModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeBookingModal() {
            document.getElementById('bookingModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        document.getElementById('bookingModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeBookingModal();
            }
        });
    </script>
    <script>
        // View switching for home.html
        document.addEventListener('DOMContentLoaded', function() {
            const navHome = document.getElementById('nav-home');
            const navMap = document.getElementById('nav-map');
            const homeView = document.getElementById('home-view');
            const mapView = document.getElementById('map-view');

            function showHomeView() {
                homeView.classList.remove('hidden');
                mapView.classList.add('hidden');
                navHome.classList.add('bg-white/20');
                navMap.classList.remove('bg-white/20');
            }

            function showMapView() {
                homeView.classList.add('hidden');
                mapView.classList.remove('hidden');
                navHome.classList.remove('bg-white/20');
                navMap.classList.add('bg-white/20');
                
                // Resize map if it exists
                if (window.mapInstance) {
                    setTimeout(() => {
                        window.mapInstance.resize();
                    }, 100);
                }
            }

            // Initialize - show home view by default
            showHomeView();

            if (navHome) {
                navHome.addEventListener('click', function(e) {
                    e.preventDefault();
                    showHomeView();
                });
            }

            if (navMap) {
                navMap.addEventListener('click', function(e) {
                    e.preventDefault();
                    showMapView();
                });
            }
            
            // Search functionality for resources
            const searchInput = document.getElementById('resource-search');
            const resourceItems = document.querySelectorAll('.resource-item');
            
            if (searchInput && resourceItems.length > 0) {
                searchInput.addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase().trim();
                    
                    resourceItems.forEach(item => {
                        const name = item.dataset.name.toLowerCase();
                        const type = item.dataset.type.toLowerCase();
                        const capacity = item.dataset.capacity;
                        
                        const matches = name.includes(searchTerm) || 
                                      type.includes(searchTerm) || 
                                      capacity.includes(searchTerm);
                        
                        if (matches || searchTerm === '') {
                            item.style.display = '';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                    
                    // Hide/show category headers based on visible items
                    const categoryHeaders = document.querySelectorAll('#resource-list .text-xs.font-bold');
                    categoryHeaders.forEach(header => {
                        let nextElement = header.nextElementSibling;
                        let hasVisibleItems = false;
                        
                        while (nextElement && !nextElement.classList.contains('font-bold')) {
                            if (nextElement.classList.contains('resource-item') && nextElement.style.display !== 'none') {
                                hasVisibleItems = true;
                                break;
                            }
                            nextElement = nextElement.nextElementSibling;
                        }
                        
                        header.style.display = hasVisibleItems ? '' : 'none';
                    });
                });
            }
        });
    </script>
</body>
</html>

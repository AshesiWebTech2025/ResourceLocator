<?php
session_start();
require_once('../backend/dbConnector.php'); 
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    //quick message to user to show not logged in
    $_SESSION['message'] = "Please log in to access this page.";
    $_SESSION['message_type'] = "error";
    //redirect to login page
    header('Location: login_signup.php'); 
    exit();
}
$user_role = $_SESSION['role'] ?? 'Student';
$user_first_name = $_SESSION["first_name"];
$user_last_name = $_SESSION["first_name"];
$header_text = htmlspecialchars($user_role) . " Portal";

//initial database connection
$db = connectDB();
$resources = [];

if ($db) {
    //usning predefined function in dbConnector.php
    $resources = getAllResources($db);
    $db->close();
}

/**
 * Generates the HTML card for a single resource.
 * This function is duplicated here (and in fetch_resources.php) for simplicity, 
 * but ideally would be in a common utility file.
 *
 * @param array $resource The resource data array.
 * @return string The HTML markup.
 */
function generateResourceCard(array $resource): string {
    $name = htmlspecialchars($resource['name'] ?? 'Unknown Resource');
    $type = htmlspecialchars($resource['type_name'] ?? 'General');
    $capacity = htmlspecialchars($resource['capacity'] ?? 'N/A');
    $resource_id = htmlspecialchars($resource['resource_id'] ?? '0');
    $latitude = htmlspecialchars($resource['latitude'] ?? '0.0');
    $longitude = htmlspecialchars($resource['longitude'] ?? '0.0');

    return "
        <div data-id=\"$resource_id\" 
             data-lat=\"$latitude\" 
             data-lon=\"$longitude\" 
             class=\"resource-card p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-ashesi-light transition duration-150\"
             onclick=\"zoomToResource(this)\">
            <p class=\"font-semibold text-gray-800\">$name</p>
            <p class=\"text-sm text-gray-500\">$type | Capacity: $capacity</p>
        </div>
    ";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ashesi Campus Resource Locator - <?php echo $user_role; ?></title>
    <!-- tailwind css -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- map box integration below-->
    <script src='https://api.mapbox.com/mapbox-gl-js/v3.4.0/mapbox-gl.js'></script>
    <link href='https://api.mapbox.com/mapbox-gl-js/v3.4.0/mapbox-gl.css' rel='stylesheet' />
    <!-- styles css -->
     <link rel="stylesheet" href="css/style.css">
    <!-- mapbox integration above -->
    <!-- customising tailwind for color scheme -->
    <script src="js/tailwindConfig.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">

    <!-- CSS/Tailwind Custom Colors for Ashesi -->
    <style>
        .bg-ashesi-maroon { background-color: #800000; }
        .text-ashesi-maroon { color: #800000; }
        .border-ashesi-maroon { border-color: #800000; }
        .focus\:ring-ashesi-maroon:focus { --tw-ring-color: #800000; }
        .focus\:border-ashesi-maroon:focus { border-color: #800000; }
        .hover\:bg-ashesi-light:hover { background-color: #F0F0F0; } /* Light gray hover */
    </style>
</head>
<body class="bg-gray-50 font-sans antialiased flex h-screen overflow-hidden">
    <!-- side nav -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition duration-200 ease-in-out bg-ashesi-maroon text-white w-64 flex flex-col z-20 shadow-xl">
        <!-- sidebar header -->
        <div class="p-6 flex items-center h-16 border-b border-white/20">
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
            <span class="text-xl font-semibold">Ashesi Locator</span>
        </div>
        <!-- navigation links -->
        <nav class="flex-grow p-4 space-y-2">
            <a href="home.php" id="nav-map" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Home
            </a>            
            <a href="resourceLocator.php" id="nav-map" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Campus Map
            </a>
            <a href="bookings.php" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                My Bookings
            </a>
            <a href="about.php" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                About
            </a>
            <a href="software_architecture.php" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Architecture
            </a>
            <a href="pageflow.php" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Page Flow
            </a>
        </nav>
        <!-- bottom. settings and signout links -->
        <div class="p-4 space-y-2 border-t border-white/20">
            <a href="#" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Settings
            </a>
            <a href="login_signup.php" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Sign Out
            </a>
        </div>
    </aside>
    <!-- main content area -->
    <div class="flex-1 flex flex-col overflow-y-auto main-content">
        <!-- main content header -->
        <header class="bg-white shadow-sm h-16 flex justify-between items-center px-6 md:px-10 sticky top-0 z-10">
            <h1 class="text-xl md:text-2xl font-semibold text-gray-800">Ashesi Campus Resource Locator</h1>
            <span class="text-sm md:text-base"> Welcome <?php echo $user_first_name ?></span>
            <div class="flex items-center text-ashesi-maroon font-medium border border-ashesi-maroon rounded-full py-1 px-4 cursor-pointer hover:bg-ashesi-maroon hover:text-white transition duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                <span class="text-sm md:text-base"><?php echo $header_text; ?></span>
            </div>
            <!-- mobile menu button (Hidden on Desktop) -->
            <button id="mobile-menu-button" class="md:hidden p-2 text-ashesi-maroon">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
        </header>
        <!-- main content view container -->
        <main class="p-4 md:p-8 flex-1 h-full flex flex-col">
            <!-- the user is first greeted with the campus map view -->
            <section id="map-view" class="flex-1 h-full flex flex-col">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Interactive Campus Map</h2>
                <div class="flex-1 grid grid-cols-1 lg:grid-cols-4 gap-4 md:gap-6 min-h-0">
                    <!-- map container which takes 3/4th of the screen size -->
                    <div id="map-container" class="lg:col-span-3 min-h-[300px] lg:min-h-full">
                        <div id="ashesi-map" class="w-full h-full"></div>
                    </div>
                    <!-- resource search/list view -->
                    <div class="lg:col-span-1 bg-white p-4 rounded-xl shadow-lg border border-gray-100 flex flex-col overflow-hidden">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4 border-b pb-2">Find a Resource</h3>
                        <!-- search Input -->
                        <div class="mb-4">
                            <input type="text" id="resource-search-input" placeholder="Search by name, type, or capacity..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-ashesi-maroon focus:border-ashesi-maroon transition duration-150">
                        </div>                        
                        <!-- scrollable resource list (Dynamic Content Here) -->
                        <div id="resource-list-container" class="flex-1 overflow-y-auto space-y-3 pr-2">
                            <?php if (count($resources) > 0): ?>
                                <?php foreach ($resources as $resource): ?>
                                    <?php echo generateResourceCard($resource); ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-sm text-center text-gray-500 p-4 border border-dashed rounded-lg">
                                    No resources found in the database.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>            
            <!-- my bookings view (it is originally hidden) -->
            <section id="bookings-view" class="hidden flex-1 h-full flex flex-col">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">My Scheduled Bookings</h2>
                
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 flex-1 overflow-y-auto">
                    <!-- booking card placehodlers -->
                    <div class="space-y-4">
                        <!-- confirmed booking placeholder -->
                        <div class="bg-blue-50 border-l-4 border-ashesi-maroon p-4 rounded-lg shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center">
                            <div class="mb-2 sm:mb-0">
                                <p class="font-bold text-lg text-gray-800">KRB 101 - Classroom</p>
                                <p class="text-sm text-gray-600">Friday, Oct 25, 2025 | 10:00 AM - 12:00 PM</p>
                                <p class="text-xs text-ashesi-maroon font-medium mt-1">Status: Confirmed</p>
                            </div>
                            <button class="text-sm text-red-600 hover:text-red-800 font-medium py-1 px-3 rounded-lg border border-red-300 hover:bg-red-50 transition">Cancel Booking</button>
                        </div>
                        <!-- pending booking example -->
                        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg shadow-sm flex flex-col sm:flex-row justify-between items-start sm:items-center">
                            <div class="mb-2 sm:mb-0">
                                <p class="font-bold text-lg text-gray-800">Conference Hall A - Event</p>
                                <p class="text-sm text-gray-600">Saturday, Nov 2, 2025 | 4:00 PM - 8:00 PM</p>
                            </div>
                            <button class="text-sm text-red-600 hover:text-red-800 font-medium py-1 px-3 rounded-lg border border-red-300 hover:bg-red-50 transition">Cancel Booking</button>
                        </div>
                        
                        <!-- empty state placeholder -->
                        <div class="flex justify-center items-center h-48 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                            <p class="text-gray-500 italic">No further upcoming bookings found.</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- footer -->
        <footer class="bg-white border-t border-gray-200 p-6 md:px-10 mt-auto">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pb-6">
                    
                    <!-- ashesi university name -->
                    <div>
                        <h4 class="text-lg font-bold text-gray-800 mb-3">Ashesi University</h4>
                        <p class="text-gray-600">Campus Resource Locator</p>
                    </div>

                    <!-- quick links -->
                    <div>
                        <h4 class="text-lg font-bold text-gray-800 mb-3">Quick Links</h4>
                        <ul class="space-y-1 text-gray-600">
                            <li><a href="#" class="hover:text-ashesi-maroon">Resources</a></li>
                            <li><a href="#" class="hover:text-ashesi-maroon">Campus Map</a></li>
                            <li><a href="bookings.html" class="hover:text-ashesi-maroon">My Bookings</a></li>
                        </ul>
                    </div>

                    <!-- contact -->
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
    
    <!-- JavaScript for Search and Map Interaction -->
    <script>
        //placeholder for map initialization
        function zoomToResource(element) {
            const lat = parseFloat(element.getAttribute('data-lat'));
            const lon = parseFloat(element.getAttribute('data-lon'));
            const name = element.querySelector('p:first-child').textContent;
            
            console.log(`Zooming map to: ${name} (${lat}, ${lon})`);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('resource-search-input');
            const listContainer = document.getElementById('resource-list-container');
            let debounceTimeout;

            //function to fetch and update the resource list based on search term
            const updateResourceList = (searchTerm) => {
                //show loading indicator
                listContainer.innerHTML = `<div class="text-center p-4 text-gray-400">
                                            <svg class="animate-spin h-5 w-5 mr-3 text-ashesi-maroon inline-block" viewBox="0 0 24 24"></svg>
                                            Searching...
                                        </div>`;

                //this path below points to the separate file handling the AJAX request
                fetch('../backend/fetch_resources.php?search=' + encodeURIComponent(searchTerm))
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.text();
                    })
                    .then(html => {
                        listContainer.innerHTML = html;
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        listContainer.innerHTML = `<div class="text-center p-4 text-red-500">Error loading resources. Check console for details.</div>`;
                    });
            };

            //debounce the search input to limit server calls while typing
            searchInput.addEventListener('keyup', (e) => {
                clearTimeout(debounceTimeout);
                const searchTerm = e.target.value.trim();
                
                debounceTimeout = setTimeout(() => {
                    updateResourceList(searchTerm);
                }, 300); //wait 300ms after user stops typing
            });

            //view switching logic
            const mapView = document.getElementById('map-view');
            const bookingsView = document.getElementById('bookings-view');
            const navMap = document.getElementById('nav-map');
            const navBookings = document.getElementById('nav-bookings');

            function switchView(viewId) {
                mapView.classList.add('hidden');
                bookingsView.classList.add('hidden');

                navMap.classList.remove('bg-white/20');
                navBookings.classList.remove('bg-white/20');
                
                if (viewId === 'map-view') {
                    mapView.classList.remove('hidden');
                    navMap.classList.add('bg-white/20');
                } else if (viewId === 'bookings-view') {
                    bookingsView.classList.remove('hidden');
                    navBookings.classList.add('bg-white/20');
                }
            }

            navMap.addEventListener('click', (e) => {
                e.preventDefault();
                switchView('map-view');
            });

            navBookings.addEventListener('click', (e) => {
                e.preventDefault();
                switchView('bookings-view');
            });
            //mobile menu toggle
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const sidebar = document.getElementById('sidebar');

            mobileMenuButton.addEventListener('click', () => {
                sidebar.classList.toggle('-translate-x-full');
            });
        });
    </script>
    <script src="js/map.js"></script>
</body>
</html>
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
            <a href="home.php" id="nav-home" class="flex items-center p-3 rounded-lg bg-white/20 transition duration-150 ease-in-out font-medium">Home</a>
            <a href="#" id="nav-map" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">Campus Map</a>
            <a href="bookings.php" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">My Bookings</a>
            <a href="about.php" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">About</a>
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
                    <a href="bookings.php" class="inline-block bg-ashesi-maroon text-white font-semibold py-3 px-6 rounded-lg shadow-lg hover:bg-ashesi-maroon/90 transition duration-200">Book a Resource</a>
                </section>

                <section>
                    <h3 class="text-2xl font-semibold text-gray-800 mb-6">What You Can Do Here</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4 bg-ashesi-light text-ashesi-maroon">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <h4 class="text-xl font-bold text-gray-800 mb-2">Find Resources</h4>
                            <p class="text-gray-600">Easily search for academic advisors, counselors, health services, and study spaces available on campus.</p>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4 bg-ashesi-light text-ashesi-maroon">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <h4 class="text-xl font-bold text-gray-800 mb-2">Book Appointments</h4>
                            <p class="text-gray-600">Check real-time availability and schedule meetings with faculty or support staff directly through the portal.</p>
                        </div>
                        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center mb-4 bg-ashesi-light text-ashesi-maroon">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <h4 class="text-xl font-bold text-gray-800 mb-2">Navigate Campus</h4>
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
                            <input type="text" placeholder="Search by name, type, or capacity..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-ashesi-maroon focus:border-ashesi-maroon transition duration-150">
                        </div>                        
                        <!-- Scrollable resource list -->
                        <div class="flex-1 overflow-y-auto space-y-3 pr-2">
                            <!-- Dummy resources -->
                            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-ashesi-light transition duration-150">
                                <p class="font-semibold text-gray-800">KRB 101</p>
                                <p class="text-sm text-gray-500">Classroom | Capacity: 40</p>
                            </div>                            
                            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-ashesi-light transition duration-150">
                                <p class="font-semibold text-gray-800">Conference Hall A</p>
                                <p class="text-sm text-gray-500">Conference Hall | Capacity: 200</p>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-ashesi-light transition duration-150">
                                <p class="font-semibold text-gray-800">Engineering Seminar Room</p>
                                <p class="text-sm text-gray-500">Seminar Room | Capacity: 15</p>
                            </div>
                            <div class="text-sm text-center text-gray-400 p-2 mt-4">
                                (More resources will load here)
                            </div>
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

    <script src="./js/map.js"></script>
    <script src="./js/main.js"></script>
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
        });
    </script>
</body>
</html>

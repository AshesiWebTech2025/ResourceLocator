<?php
session_start();
// Require login to access this admin/documentation page
if (!isset($_SESSION['user_id'])) {
    header('Location: login_signup.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Architecture - Ashesi Campus Resource Locator</title>
    <link rel="stylesheet" href="./css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
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

<body class="bg-gray-50 font-sans antialiased min-h-screen">
    <div class="flex min-h-screen">

    <aside id="sidebar" class="fixed inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition duration-200 ease-in-out bg-ashesi-maroon text-white w-64 flex flex-col z-20 shadow-xl">
        <div class="p-6 flex items-center h-16 border-b border-white/20">
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
            </svg>
            <span class="text-xl font-semibold">Ashesi Locator</span>
        </div>
        <nav class="flex-grow p-4 space-y-2">
            <a href="home.php" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">Home</a>
            <a href="resourceLocator.php" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">Campus Map</a>
            <a href="bookings.php" id="nav-bookings" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">My Bookings</a>
            <a href="about.php" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">About</a>
            <a href="software_architecture.php" class="flex items-center p-3 rounded-lg bg-white/20 transition duration-150 ease-in-out font-medium">Architecture</a>
            <a href="pageflow.php" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">Page Flow</a>
        </nav>
        <div class="p-4 space-y-2 border-t border-white/20">
            <a href="#" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">Settings</a>
            <a href="login_signup.php" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">Sign Out</a>
        </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-y-auto main-content">
        <header class="bg-white shadow-sm h-16 flex items-center px-6 md:px-10 sticky top-0 z-10">
            <button id="hamburgerBtn" class="hamburger-btn md:hidden mr-4 p-2 focus:outline-none focus:ring-2 focus:ring-ashesi-maroon rounded" aria-label="Toggle menu" aria-expanded="false" type="button">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
            </button>
            <h1 class="text-xl md:text-2xl font-semibold text-gray-800 mr-auto">Architecture</h1>
            <div class="flex items-center text-ashesi-maroon font-medium border border-ashesi-maroon rounded-full py-1 px-4 cursor-pointer hover:bg-ashesi-maroon hover:text-white transition duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm md:text-base hidden sm:inline">Documentation</span>
            </div>
        </header>

        <main class="p-6 md:p-10 flex-1">
            <!-- Header Section -->
            <section class="bg-gradient-to-r from-ashesi-maroon to-red-900 text-white p-8 rounded-xl shadow-lg mb-8">
                <h2 class="text-3xl md:text-4xl font-bold mb-2">System Architecture</h2>
                <p class="text-lg opacity-90">Technical Overview of the Ashesi Campus Resource Locator</p>
            </section>

            <!-- Overview -->
            <section class="bg-white p-8 rounded-xl shadow-lg mb-8 border border-gray-100">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Overview
                </h3>
                <p class="text-gray-700 leading-relaxed">
                    The Ashesi Campus Resource Locator (ACRL) is built on a <strong>three-tier web application architecture</strong> that separates concerns between presentation, application logic, and data management. Our architecture prioritizes <strong>modularity, scalability, and maintainability</strong> while delivering an intuitive user experience for campus resource discovery and booking.
                </p>
            </section>

            <!-- Architectural Pattern -->
            <section class="bg-white p-8 rounded-xl shadow-lg mb-8 border border-gray-100">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    Architectural Pattern
                </h3>
                <p class="text-gray-700 mb-4">We employ a <strong>traditional three-tier MVC (Model-View-Controller) architecture</strong> that cleanly separates:</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 p-5 rounded-lg border-l-4 border-blue-500">
                        <h4 class="font-bold text-blue-800 mb-2">Presentation Layer (View)</h4>
                        <p class="text-gray-700 text-sm">User-facing HTML/CSS/JavaScript interfaces</p>
                    </div>
                    <div class="bg-green-50 p-5 rounded-lg border-l-4 border-green-500">
                        <h4 class="font-bold text-green-800 mb-2">Application Layer (Controller)</h4>
                        <p class="text-gray-700 text-sm">PHP server-side logic handling routing, business rules, and request processing</p>
                    </div>
                    <div class="bg-purple-50 p-5 rounded-lg border-l-4 border-purple-500">
                        <h4 class="font-bold text-purple-800 mb-2">Data Layer (Model)</h4>
                        <p class="text-gray-700 text-sm">MySQL database managing persistent storage</p>
                    </div>
                </div>
                <p class="text-gray-700 mt-4 text-sm italic">This separation ensures that each module has a specific responsibility, making development, testing, and scaling more manageable while enhancing team collaboration and promoting code reusability.</p>
            </section>

            <!-- Major Pages -->
            <section class="bg-white p-8 rounded-xl shadow-lg mb-8 border border-gray-100">
                <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Major Pages
                </h3>

                <!-- Implemented Pages -->
                <div class="mb-6">
                    <h4 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <span class="bg-green-100 text-green-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">Implemented</span>
                        Pages
                    </h4>
                    <div class="space-y-4">
                        <!-- Home Page -->
                        <div class="border-l-4 border-ashesi-maroon pl-6 py-3 bg-gray-50 rounded-r-lg">
                            <h5 class="font-bold text-lg text-gray-800 mb-1">1. Home Page (<code class="text-sm bg-gray-200 px-2 py-1 rounded">home.php</code>)</h5>
                            <p class="text-gray-700 mb-2"><strong>Purpose:</strong> Landing page introducing the ACRL system</p>
                            <p class="text-gray-700 mb-2"><strong>Features:</strong> Hero section with welcome message, feature cards highlighting key functionality, about section, and responsive navigation</p>
                            <p class="text-gray-600 text-sm"><strong>Supporting Functions:</strong> None (static content page)</p>
                        </div>

                        <!-- Resource Locator -->
                        <div class="border-l-4 border-ashesi-maroon pl-6 py-3 bg-gray-50 rounded-r-lg">
                            <h5 class="font-bold text-lg text-gray-800 mb-1">2. Resource Locator Page (<code class="text-sm bg-gray-200 px-2 py-1 rounded">resourceLocator.php</code>)</h5>
                            <p class="text-gray-700 mb-2"><strong>Purpose:</strong> Primary interface for students, faculty, and visitors to discover and search campus resources</p>
                            <p class="text-gray-700 mb-2"><strong>Features:</strong></p>
                            <ul class="list-disc list-inside text-gray-700 ml-4 space-y-1">
                                <li>Interactive Mapbox-powered campus map with clickable hotspot markers</li>
                                <li>Real-time search and filtering by resource type, capacity, and keywords</li>
                                <li>Dynamic display of resource details</li>
                                <li>Resource booking interface</li>
                            </ul>
                            <p class="text-gray-600 text-sm mt-2"><strong>Supporting PHP Functions:</strong> <code class="bg-gray-200 px-1 py-0.5 rounded text-xs">fetch_resources.php</code>, <code class="bg-gray-200 px-1 py-0.5 rounded text-xs">getTypes.php</code></p>
                            <p class="text-gray-600 text-sm"><strong>Supporting Tables:</strong> resources, availability, bookings</p>
                        </div>

                        <!-- Resource Allocator -->
                        <div class="border-l-4 border-ashesi-maroon pl-6 py-3 bg-gray-50 rounded-r-lg">
                            <h5 class="font-bold text-lg text-gray-800 mb-1">3. Resource Allocator Page (<code class="text-sm bg-gray-200 px-2 py-1 rounded">resourceAllocator.php</code>)</h5>
                            <p class="text-gray-700 mb-2"><strong>Purpose:</strong> Admin interface for adding new campus resources</p>
                            <p class="text-gray-700 mb-2"><strong>Features:</strong> Interactive map for coordinate selection, slide-in form for resource details, real-time coordinate capture</p>
                            <p class="text-gray-600 text-sm"><strong>Supporting PHP Functions:</strong> <code class="bg-gray-200 px-1 py-0.5 rounded text-xs">addType.php</code></p>
                            <p class="text-gray-600 text-sm"><strong>Supporting Tables:</strong> resources</p>
                        </div>

                        <!-- Available Sessions -->
                        <div class="border-l-4 border-ashesi-maroon pl-6 py-3 bg-gray-50 rounded-r-lg">
                            <h5 class="font-bold text-lg text-gray-800 mb-1">4. Available Sessions Page (<code class="text-sm bg-gray-200 px-2 py-1 rounded">available_sessions.php</code>)</h5>
                            <p class="text-gray-700 mb-2"><strong>Purpose:</strong> Admin interface for managing time slot availability for each resource</p>
                            <p class="text-gray-700 mb-2"><strong>Features:</strong> List view of resources, modal interface for time slot management, dynamic JavaScript-generated resource cards</p>
                            <p class="text-gray-600 text-sm"><strong>Supporting PHP Functions:</strong> <code class="bg-gray-200 px-1 py-0.5 rounded text-xs">available_sessions.php</code>, <code class="bg-gray-200 px-1 py-0.5 rounded text-xs">fetch_resources.php</code></p>
                            <p class="text-gray-600 text-sm"><strong>Supporting Tables:</strong> availability, resources</p>
                        </div>

                        <!-- Bookings -->
                        <div class="border-l-4 border-ashesi-maroon pl-6 py-3 bg-gray-50 rounded-r-lg">
                            <h5 class="font-bold text-lg text-gray-800 mb-1">5. Bookings Page (<code class="text-sm bg-gray-200 px-2 py-1 rounded">bookings.php</code>)</h5>
                            <p class="text-gray-700 mb-2"><strong>Purpose:</strong> User interface for viewing and managing personal bookings</p>
                            <p class="text-gray-700 mb-2"><strong>Features:</strong> List of current and upcoming reservations, booking summary, cancellation functionality</p>
                            <p class="text-gray-600 text-sm"><strong>Supporting PHP Functions:</strong> <code class="bg-gray-200 px-1 py-0.5 rounded text-xs">fetch_bookings.php</code>, <code class="bg-gray-200 px-1 py-0.5 rounded text-xs">add_booking.php</code></p>
                            <p class="text-gray-600 text-sm"><strong>Supporting Tables:</strong> bookings, resources, users</p>
                        </div>

                        <!-- Authentication -->
                        <div class="border-l-4 border-ashesi-maroon pl-6 py-3 bg-gray-50 rounded-r-lg">
                            <h5 class="font-bold text-lg text-gray-800 mb-1">6. Authentication Pages (<code class="text-sm bg-gray-200 px-2 py-1 rounded">login_signup.php</code>)</h5>
                            <p class="text-gray-700 mb-2"><strong>Purpose:</strong> User authentication and account creation</p>
                            <p class="text-gray-700 mb-2"><strong>Features:</strong> Login form, signup form with validation, session management</p>
                            <p class="text-gray-600 text-sm"><strong>Supporting PHP Functions:</strong> <code class="bg-gray-200 px-1 py-0.5 rounded text-xs">loginSignupPreprocessor.php</code></p>
                            <p class="text-gray-600 text-sm"><strong>Supporting Tables:</strong> users</p>
                        </div>
                    </div>
                </div>

                <!-- Planned Pages -->
                <div class="mt-8">
                    <h4 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">Planned</span>
                        Pages
                    </h4>
                    <div class="space-y-4">
                        <div class="border-l-4 border-gray-400 pl-6 py-3 bg-gray-50 rounded-r-lg">
                            <h5 class="font-bold text-lg text-gray-700 mb-1">7. Analytics Dashboard</h5>
                            <p class="text-gray-600 mb-2"><strong>Purpose:</strong> Admin insights into resource utilization and booking patterns</p>
                            <p class="text-gray-600"><strong>Features:</strong> Usage statistics, popular resources, peak booking times, occupancy rates</p>
                        </div>
                        <div class="border-l-4 border-gray-400 pl-6 py-3 bg-gray-50 rounded-r-lg">
                            <h5 class="font-bold text-lg text-gray-700 mb-1">8. Settings Page</h5>
                            <p class="text-gray-600 mb-2"><strong>Purpose:</strong> User and admin account management</p>
                            <p class="text-gray-600"><strong>Features:</strong> Profile editing, notification preferences, password changes</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Technology Stack -->
            <section class="bg-white p-8 rounded-xl shadow-lg mb-8 border border-gray-100">
                <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                    </svg>
                    Technology Stack
                </h3>

                <!-- Frontend -->
                <div class="mb-8">
                    <h4 class="text-xl font-bold text-blue-800 mb-4">Frontend Architecture</h4>

                    <div class="bg-blue-50 p-6 rounded-lg border border-blue-200 mb-4">
                        <h5 class="font-bold text-blue-900 mb-3">Core Technologies</h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
                            <div>
                                <p class="font-semibold text-gray-800 mb-1">• HTML5</p>
                                <p class="text-sm text-gray-600 ml-4">Semantic markup for page structure and accessibility</p>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 mb-1">• CSS3 & TailwindCSS</p>
                                <p class="text-sm text-gray-600 ml-4">Utility-first framework for rapid, consistent styling with custom configurations</p>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 mb-1">• JavaScript (ES6+)</p>
                                <p class="text-sm text-gray-600 ml-4">DOM manipulation, event handling, and API communication</p>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 mb-1">• jQuery 3.x</p>
                                <p class="text-sm text-gray-600 ml-4">AJAX requests, DOM traversal, and animation effects</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-indigo-50 p-6 rounded-lg border border-indigo-200 mb-4">
                        <h5 class="font-bold text-indigo-900 mb-3">Third-Party Libraries</h5>
                        <div class="text-gray-700">
                            <p class="font-semibold text-gray-800 mb-1">• Mapbox GL JS (v2.x)</p>
                            <ul class="text-sm text-gray-600 ml-4 list-disc list-inside space-y-1">
                                <li>Core interactive mapping service providing campus visualization</li>
                                <li>Custom marker styling for resource hotspots</li>
                                <li>Real-time coordinate capture for admin resource placement</li>
                            </ul>
                        </div>
                    </div>

                    <div class="bg-cyan-50 p-6 rounded-lg border border-cyan-200">
                        <h5 class="font-bold text-cyan-900 mb-2">Frontend Pattern</h5>
                        <p class="text-gray-700 text-sm">We implement a <strong>Multi-Page Application (MPA)</strong> approach enhanced with AJAX-powered dynamic content updates, progressive enhancement, and smooth CSS/JavaScript transitions.</p>
                    </div>
                </div>

                <!-- Backend -->
                <div class="mb-8">
                    <h4 class="text-xl font-bold text-green-800 mb-4">Backend Architecture</h4>

                    <div class="bg-green-50 p-6 rounded-lg border border-green-200 mb-4">
                        <h5 class="font-bold text-green-900 mb-3">Server-Side Technology</h5>
                        <div class="text-gray-700">
                            <p class="font-semibold text-gray-800 mb-2">• PHP 8.x</p>
                            <ul class="text-sm text-gray-600 ml-4 list-disc list-inside space-y-1">
                                <li>Primary server-side scripting language for routing, business logic, and database operations</li>
                                <li>Session management for authentication and authorization</li>
                                <li>RESTful-style endpoints for AJAX communication</li>
                            </ul>
                            <p class="text-xs text-gray-500 italic mt-2 ml-4">Note: Maintained PHP instead of Node.js to meet course requirements and leverage XAMPP environment</p>
                        </div>
                    </div>

                    <div class="bg-emerald-50 p-6 rounded-lg border border-emerald-200">
                        <h5 class="font-bold text-emerald-900 mb-3">Backend Design Principles</h5>
                        <ul class="text-gray-700 space-y-2">
                            <li class="flex items-start">
                                <span class="text-emerald-600 mr-2">▪</span>
                                <span><strong>Separation of Concerns:</strong> Distinct PHP files for different operations</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-emerald-600 mr-2">▪</span>
                                <span><strong>Modular Functions:</strong> Reusable database connection handling via <code class="bg-gray-200 px-1 py-0.5 rounded text-xs">dbConnector.php</code></span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-emerald-600 mr-2">▪</span>
                                <span><strong>Stateless Requests:</strong> Each request contains all information required for processing (except authentication sessions)</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- PHP Functions Table -->
                <div class="mb-8">
                    <h5 class="text-lg font-bold text-gray-800 mb-3">Key PHP Functions</h5>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b">Function/File</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b">Purpose</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-b">HTTP Method</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 border-b"><code class="bg-gray-200 px-2 py-1 rounded text-xs">dbConnector.php</code></td>
                                    <td class="px-4 py-3 border-b text-gray-700">Establishes PDO connection to MySQL with error handling</td>
                                    <td class="px-4 py-3 border-b"><span class="bg-gray-200 px-2 py-1 rounded text-xs">N/A</span></td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 border-b"><code class="bg-gray-200 px-2 py-1 rounded text-xs">fetch_resources.php</code></td>
                                    <td class="px-4 py-3 border-b text-gray-700">Retrieves all resources with coordinates for map rendering</td>
                                    <td class="px-4 py-3 border-b"><span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-semibold">GET</span></td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 border-b"><code class="bg-gray-200 px-2 py-1 rounded text-xs">fetch_bookings.php</code></td>
                                    <td class="px-4 py-3 border-b text-gray-700">Returns user-specific booking records</td>
                                    <td class="px-4 py-3 border-b"><span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-semibold">GET</span></td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 border-b"><code class="bg-gray-200 px-2 py-1 rounded text-xs">add_booking.php</code></td>
                                    <td class="px-4 py-3 border-b text-gray-700">Creates new booking entry after validation</td>
                                    <td class="px-4 py-3 border-b"><span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-semibold">POST</span></td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 border-b"><code class="bg-gray-200 px-2 py-1 rounded text-xs">addType.php</code></td>
                                    <td class="px-4 py-3 border-b text-gray-700">Inserts new resource into database from admin form</td>
                                    <td class="px-4 py-3 border-b"><span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-semibold">POST</span></td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 border-b"><code class="bg-gray-200 px-2 py-1 rounded text-xs">getTypes.php</code></td>
                                    <td class="px-4 py-3 border-b text-gray-700">Returns distinct resource types for filter dropdowns</td>
                                    <td class="px-4 py-3 border-b"><span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-semibold">GET</span></td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 border-b"><code class="bg-gray-200 px-2 py-1 rounded text-xs">available_sessions.php</code></td>
                                    <td class="px-4 py-3 border-b text-gray-700">CRUD operations for resource time slot management</td>
                                    <td class="px-4 py-3 border-b"><span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-semibold">GET/POST/PUT/DELETE</span></td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 border-b"><code class="bg-gray-200 px-2 py-1 rounded text-xs">loginSignupPreprocessor.php</code></td>
                                    <td class="px-4 py-3 border-b text-gray-700">User authentication, registration, and session initialization</td>
                                    <td class="px-4 py-3 border-b"><span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-semibold">POST</span></td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 border-b"><code class="bg-gray-200 px-2 py-1 rounded text-xs">dbTest.php</code></td>
                                    <td class="px-4 py-3 border-b text-gray-700">Database connectivity testing utility</td>
                                    <td class="px-4 py-3 border-b"><span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-semibold">GET</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <!-- Database Architecture -->
            <section class="bg-white p-8 rounded-xl shadow-lg mb-8 border border-gray-100">
                <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"></path>
                    </svg>
                    Database Architecture
                </h3>

                <p class="text-gray-700 mb-4">Database Management System: <strong>MySQL 8.x</strong> (managed via phpMyAdmin on XAMPP). Schema design follows normalization and indexing best practices for efficient queries.</p>

                <h4 class="text-xl font-bold text-gray-800 mb-3">Core Tables</h4>

                <div class="space-y-4">
                    <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-ashesi-maroon">
                        <h5 class="font-bold">1. resources</h5>
                        <p class="text-sm text-gray-700 mt-2"><strong>Purpose:</strong> Stores campus resource information and geographic data.</p>
                        <ul class="text-sm text-gray-700 ml-4 list-disc list-inside mt-2">
                            <li><code>resource_id</code> INT PRIMARY KEY AUTO_INCREMENT</li>
                            <li><code>name</code> VARCHAR(255) NOT NULL</li>
                            <li><code>type</code> VARCHAR(100) NOT NULL</li>
                            <li><code>capacity</code> INT NULL</li>
                            <li><code>description</code> TEXT NULL</li>
                            <li><code>latitude</code> DECIMAL(10,8) NOT NULL</li>
                            <li><code>longitude</code> DECIMAL(11,8) NOT NULL</li>
                            <li><code>created_at</code> TIMESTAMP DEFAULT CURRENT_TIMESTAMP</li>
                        </ul>
                        <p class="text-xs text-gray-600 mt-2">Relationships: referenced by <code>availability</code> and <code>bookings</code>.</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-ashesi-maroon">
                        <h5 class="font-bold">2. availability</h5>
                        <p class="text-sm text-gray-700 mt-2"><strong>Purpose:</strong> Defines when resources are available for booking.</p>
                        <ul class="text-sm text-gray-700 ml-4 list-disc list-inside mt-2">
                            <li><code>available_id</code> INT PRIMARY KEY AUTO_INCREMENT</li>
                            <li><code>resource_id</code> INT (FOREIGN KEY → resources.resource_id)</li>
                            <li><code>day_of_week</code> VARCHAR(10) NOT NULL</li>
                            <li><code>start_time</code> TIME NOT NULL</li>
                            <li><code>end_time</code> TIME NOT NULL</li>
                            <li><code>is_available</code> BOOLEAN DEFAULT TRUE</li>
                        </ul>
                        <p class="text-xs text-gray-600 mt-2">Relationships: many-to-one with <code>resources</code>.</p>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-ashesi-maroon">
                        <h5 class="font-bold">3. bookings</h5>
                        <p class="text-sm text-gray-700 mt-2"><strong>Purpose:</strong> Tracks user reservations.</p>
                        <ul class="text-sm text-gray-700 ml-4 list-disc list-inside mt-2">
                            <li><code>booking_id</code> INT PRIMARY KEY AUTO_INCREMENT</li>
                            <li><code>user_id</code> INT (FOREIGN KEY → users.user_id)</li>
                            <li><code>resource_id</code> INT (FOREIGN KEY → resources.resource_id)</li>
                            <li><code>booking_date</code> DATE NOT NULL</li>
                            <li><code>start_time</code> TIME NOT NULL</li>
                            <li><code>end_time</code> TIME NOT NULL</li>
                            <li><code>status</code> ENUM('pending','confirmed','cancelled') DEFAULT 'confirmed'</li>
                            <li><code>created_at</code> TIMESTAMP DEFAULT CURRENT_TIMESTAMP</li>
                        </ul>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-ashesi-maroon">
                        <h5 class="font-bold">4. users</h5>
                        <p class="text-sm text-gray-700 mt-2"><strong>Purpose:</strong> Stores user account information and credentials.</p>
                        <ul class="text-sm text-gray-700 ml-4 list-disc list-inside mt-2">
                            <li><code>user_id</code> INT PRIMARY KEY AUTO_INCREMENT</li>
                            <li><code>email</code> VARCHAR(255) UNIQUE NOT NULL</li>
                            <li><code>password_hash</code> VARCHAR(255) NOT NULL</li>
                            <li><code>name</code> VARCHAR(255) NOT NULL</li>
                            <li><code>role</code> ENUM('user','admin') DEFAULT 'user'</li>
                            <li><code>created_at</code> TIMESTAMP DEFAULT CURRENT_TIMESTAMP</li>
                        </ul>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-gray-400">
                        <h5 class="font-bold">Planned Tables</h5>
                        <p class="text-sm text-gray-700 mt-2"><strong>notifications:</strong> booking confirmations and reminders (notification_id, user_id, booking_id, message, is_read, created_at)</p>
                        <p class="text-sm text-gray-700 mt-2"><strong>audit_log:</strong> admin action tracking (log_id, user_id, action, table_affected, timestamp)</p>
                    </div>
                </div>
            </section>
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 border-b"><code class="bg-gray-200 px-2 py-1 rounded text-xs">addType.php</code></td>
                <td class="px-4 py-3 border-b text-ash-accent">Inserts new resource into database from admin form</td>
                <td class="px-4 py-3 border-b"><span class="bg-ashesi-maroon text-white px-2 py-1 rounded text-xs font-semibold">POST</span></td>
            </tr>
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 border-b"><code class="bg-gray-200 px-2 py-1 rounded text-xs">getTypes.php</code></td>
                <td class="px-4 py-3 border-b text-ash-accent">Returns distinct resource types for filter dropdowns</td>
                <td class="px-4 py-3 border-b"><span class="bg-ashesi-maroon text-white px-2 py-1 rounded text-xs font-semibold">GET</span></td>
            </tr>
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 border-b"><code class="bg-gray-200 px-2 py-1 rounded text-xs">available_sessions.php</code></td>
                <td class="px-4 py-3 border-b text-ash-accent">CRUD operations for resource time slot management</td>
                <td class="px-4 py-3 border-b"><span class="bg-ashesi-maroon text-white px-2 py-1 rounded text-xs font-semibold">GET/POST/PUT/DELETE</span></td>
            </tr>
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 border-b"><code class="bg-gray-200 px-2 py-1 rounded text-xs">loginSignupPreprocessor.php</code></td>
                <td class="px-4 py-3 border-b text-ash-accent">User authentication, registration, and session initialization</td>
                <td class="px-4 py-3 border-b"><span class="bg-ashesi-maroon text-white px-2 py-1 rounded text-xs font-semibold">POST</span></td>
            </tr>
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 border-b"><code class="bg-gray-200 px-2 py-1 rounded text-xs">dbTest.php</code></td>
                <td class="px-4 py-3 border-b text-ash-accent">Database connectivity testing utility</td>
                <td class="px-4 py-3 border-b"><span class="bg-ashesi-maroon text-white px-2 py-1 rounded text-xs font-semibold">GET</span></td>
            </tr>
            </tbody>
            </table>
    </div>
    </div>
    </section>

    <!-- db architecture - ERD Image -->
    <section class="bg-white p-8 rounded-xl shadow-lg mb-8 border border-gray-100">
        <h3 class="text-2xl font-bold text-ash-accent mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"></path>
            </svg>
            Database Architecture
        </h3>

        <p class="text-ash-accent mb-4">Below is the Entity Relationship Diagram for our current schema.</p>
        <div class="bg-ash-gray p-4 rounded">
            <img src="images/ERD-Diagram.PNG" alt="ERD Diagram" class="w-full h-auto rounded shadow-sm" />
        </div>
    </section>

    <!-- system integration and data flow -->
    <section class="bg-white p-8 rounded-xl shadow-lg mb-8 border border-gray-100">
        <h3 class="text-2xl font-bold text-gray-800 mb-4">System Integration & Data Flow</h3>

        <h4 class="font-bold text-gray-800 mt-4">User Booking Flow</h4>
        <ol class="list-decimal list-inside text-gray-700 ml-4 mt-2 space-y-2">
            <li>User applies filters on Resource Locator page → JavaScript builds query parameters.</li>
            <li><code>fetch_resources.php</code> receives GET request → executes SQL query with WHERE clauses.</li>
            <li>MySQL returns filtered resource records → JavaScript updates map markers and resource list.</li>
            <li>User clicks resource → modal displays availability from the <code>availability</code> table.</li>
            <li>User selects time slot → AJAX POST to <code>add_booking.php</code> → inserts record into <code>bookings</code> table.</li>
            <li>Success response → UI updates with booking confirmation.</li>
        </ol>

        <h4 class="font-bold text-gray-800 mt-6">Admin Resource Creation Flow</h4>
        <ol class="list-decimal list-inside text-gray-700 ml-4 mt-2 space-y-2">
            <li>Admin clicks map coordinate → Mapbox captures latitude/longitude.</li>
            <li>jQuery triggers slide-in form with coordinates pre-filled.</li>
            <li>Admin enters details → JavaScript validates → AJAX POST to <code>addType.php</code>.</li>
            <li>PHP sanitizes input → inserts into <code>resources</code> table with geographic data.</li>
            <li>Success/failure returned → UI provides feedback and refreshes map.</li>
        </ol>
    </section>

    <!-- architecture best practices -->
    <section class="bg-white p-8 rounded-xl shadow-lg mb-8 border border-gray-100">
        <h3 class="text-2xl font-bold text-gray-800 mb-4">Architecture Best Practices Implemented</h3>

        <ul class="list-disc list-inside text-gray-700 ml-4 space-y-2">
            <li><strong>Modular Design:</strong> Self-contained components with specific responsibilities for easier development, testing, and scaling.</li>
            <li><strong>Clean Code Structure:</strong> Organized file hierarchy separating backend PHP scripts, frontend assets, and HTML pages.</li>
            <li><strong>Separation of Concerns:</strong> Clear boundaries between presentation, behavior, and data management.</li>
            <li><strong>Progressive Enhancement:</strong> Core functionality works without JavaScript, enhanced with interactive features when available.</li>
            <li><strong>Responsive Design:</strong> Mobile-first approach using Tailwind CSS breakpoints.</li>
        </ul>

        <h4 class="text-lg font-bold text-gray-800 mt-6">Security Considerations</h4>
        <ul class="list-disc list-inside text-gray-700 ml-4 mt-2">
            <li>Password hashing for user credentials.</li>
            <li>PHP session management for authentication.</li>
            <li>Prepared statements (PDO) preventing SQL injection.</li>
            <li>Input validation on both client and server sides.</li>
        </ul>

        <h4 class="text-lg font-bold text-gray-800 mt-6">Scalability Planning</h4>
        <p class="text-gray-700">Database designed with proper indexing and normalization to support growth and efficient queries.</p>
    </section>

    <!-- architecture diagram (ASCII), will be updated soon -->
    <section class="bg-white p-8 rounded-xl shadow-lg mb-8 border border-gray-100">
        <h3 class="text-2xl font-bold text-gray-800 mb-4">Architecture Diagram</h3>
        <pre class="bg-gray-100 p-4 rounded text-sm overflow-auto">┌─────────────────────────────────────────────────────────────┐
│                      CLIENT LAYER                            │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │   Web Browser │  │  Mapbox GL   │  │   jQuery     │      │
│  │  (HTML/CSS)   │  │     JS       │  │              │      │
│  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘      │
│         │                  │                  │               │
│         └──────────────────┴──────────────────┘              │
│                            │                                  │
│                     HTTP Requests/AJAX                        │
│                            │                                  │
└────────────────────────────┼─────────────────────────────────┘
                             │
┌────────────────────────────┼─────────────────────────────────┐
│                      SERVER LAYER (PHP)                       │
│  ┌───────────────────────────────────────────────────┐       │
│  │  Apache Web Server (XAMPP)                        │       │
│  │  ┌─────────────────┐     ┌──────────────────┐    │       │
│  │  │ Routing Layer   │     │  Session Manager │    │       │
│  │  │  (.php pages)   │     │  (Authentication)│    │       │
│  │  └────────┬────────┘     └────────┬─────────┘    │       │
│  │           │                       │               │       │
│  │  ┌────────▼───────────────────────▼─────────┐    │       │
│  │  │     Business Logic Layer                 │    │       │
│  │  │  ┌──────────────────────────────────┐    │    │       │
│  │  │  │ fetch_resources.php              │    │    │       │
│  │  │  │ add_booking.php                  │    │    │       │
│  │  │  │ addType.php                      │    │    │       │
│  │  │  │ available_sessions.php           │    │    │       │
│  │  │  │ loginSignupPreprocessor.php      │    │    │       │
│  │  │  └───────────┬──────────────────────┘    │    │       │
│  │  └──────────────┼───────────────────────────┘    │       │
│  │                 │                                 │       │
│  │  ┌──────────────▼─────────────────────────┐      │       │
│  │  │  Database Connector (dbConnector.php)  │      │       │
│  │  └──────────────┬─────────────────────────┘      │       │
│  └─────────────────┼────────────────────────────────┘       │
│                    │ PDO Connection                          │
└────────────────────┼─────────────────────────────────────────┘
                     │
┌────────────────────┼─────────────────────────────────────────┐
│                DATABASE LAYER (MySQL)                         │
│  ┌────────────────────────────────────────────────┐          │
│  │            MySQL Database (phpMyAdmin)         │          │
│  │  ┌──────────┐  ┌──────────┐  ┌──────────┐     │          │
│  │  │ resources│  │   users  │  │ bookings │     │          │
│  │  └──────────┘  └──────────┘  └──────────┘     │          │
│  │  ┌──────────────┐                              │          │
│  │  │ availability │                              │          │
│  │  └──────────────┘                              │          │
│  └────────────────────────────────────────────────┘          │
└───────────────────────────────────────────────────────────────┘</pre>
    </section>

    <!-- hosting and development environment section -->
    <section class="bg-white p-8 rounded-xl shadow-lg mb-8 border border-gray-100">
        <h3 class="text-2xl font-bold text-gray-800 mb-4">Hosting & Development Environment</h3>
        <p class="text-gray-700">Current: <strong>XAMPP</strong> (Apache 2.4.x, MySQL 8.x, PHP 8.x) for local development. Planned Production: evaluation ongoing for cloud hosting deployment.</p>
    </section>

    <!-- possible future enhancements -->
    <section class="bg-white p-8 rounded-xl shadow-lg mb-8 border border-gray-100">
        <h3 class="text-2xl font-bold text-gray-800 mb-4">Future Architectural Enhancements (v.2.0)</h3>
        <ul class="list-disc list-inside text-gray-700 ml-4 space-y-2">
            <li>API Development: RESTful API layer for potential mobile app integration.</li>
            <li>Caching Layer: Redis for frequently accessed resource data.</li>
            <li>Real-time Updates: WebSocket integration for live availability updates.</li>
            <li>Analytics Integration: Custom analytics system for usage tracking.</li>
        </ul>
    </section>

    <!-- references -->
    <section class="bg-white p-8 rounded-xl shadow-lg mb-8 border border-gray-100">
        <h3 class="text-2xl font-bold text-gray-800 mb-4">References</h3>
        <ul class="list-disc list-inside text-gray-700 ml-4 space-y-2">
            <li>Cloudflare. (n.d.). What is web application architecture? <a class="text-ashesi-maroon underline" href="https://www.cloudflare.com/learning/web-application-security/what-is-web-application-architecture/" target="_blank" rel="noopener">https://www.cloudflare.com/learning/web-application-security/what-is-web-application-architecture/</a></li>
            <li>GeeksforGeeks. (2024). Web application architecture. <a class="text-ashesi-maroon underline" href="https://www.geeksforgeeks.org/web-application-architecture/" target="_blank" rel="noopener">https://www.geeksforgeeks.org/web-application-architecture/</a></li>
        </ul>
    </section>

    </main>
    </div>
    </div>

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
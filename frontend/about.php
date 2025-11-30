<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Page</title>
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

<body class="bg-gray-50 font-sans antialiased flex h-screen overflow-hidden">

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
            <a href="resourceLocator.php" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">Campus Map</a>
            <a href="bookings.php" id="nav-bookings" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">My Bookings</a>
            <a href="about.php" class="flex items-center p-3 rounded-lg bg-white/20 transition duration-150 ease-in-out font-medium">About</a>
            <a href="software_architecture.php" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">Architecture</a>
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
            <h1 class="text-xl md:text-2xl font-semibold text-gray-800 mr-auto">About</h1>
            <div class="flex items-center text-ashesi-maroon font-medium border border-ashesi-maroon rounded-full py-1 px-4 cursor-pointer hover:bg-ashesi-maroon hover:text-white transition duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm md:text-base">Hi Kwadwo :)</span>
            </div>
            <button id="hamburgerBtn" class="hamburger-btn md:hidden mr-4 p-2 focus:outline-none focus:ring-2 focus:ring-ashesi-maroon rounded" aria-label="Toggle menu" aria-expanded="false" type="button">
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                </svg>
            </button>
        </header>

        <main class="p-6 md:p-10 flex-1">

            <!-- Header Section -->
            <section class="bg-gradient-to-r from-ashesi-maroon to-red-900 text-white p-8 rounded-xl shadow-lg mb-8">
                <h2 class="text-3xl md:text-4xl font-bold mb-2">What we've achieved so far....</h2>
                <p class="text-lg opacity-90"> Ashesi Campus Resource Locator</p>
            </section>

            <!-- Group Members -->
            <section class="bg-white p-8 rounded-xl shadow-lg mb-8 border border-gray-100">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Group Members
                </h3>
                <ul class="space-y-2 text-gray-700">
                    <li class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <span class="w-2 h-2 bg-ashesi-maroon rounded-full mr-3"></span>
                        George Malimba Billa-Yandanbon
                    </li>
                    <li class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <span class="w-2 h-2 bg-ashesi-maroon rounded-full mr-3"></span>
                        Eyram-Makafui Awoye
                    </li>
                    <li class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <span class="w-2 h-2 bg-ashesi-maroon rounded-full mr-3"></span>
                        Eyra Inez Agbenu
                    </li>
                    <li class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <span class="w-2 h-2 bg-ashesi-maroon rounded-full mr-3"></span>
                        Kharis Ewurabena Dadzie
                    </li>
                </ul>
            </section>

            <!-- Description of Functionality -->
            <section class="bg-white p-8 rounded-xl shadow-lg mb-8 border border-gray-100">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Description of Functionality
                </h3>
                <div class="space-y-4 text-gray-700">
                    <div class="border-l-4 border-ashesi-maroon pl-4">
                        <h4 class="font-semibold text-lg text-gray-800 mb-1">Map Integration</h4>
                        <p>Mapbox renders the campus and displays interactive hotspot markers stored in MySQL.</p>
                    </div>
                    <div class="border-l-4 border-ashesi-maroon pl-4">
                        <h4 class="font-semibold text-lg text-gray-800 mb-1">Resource Creation (Admin)</h4>
                        <p>Clicking a map coordinate triggers a slide-in form where the admin inputs resource type and capacity. PHP stores this in the database.</p>
                    </div>
                    <div class="border-l-4 border-ashesi-maroon pl-4">
                        <h4 class="font-semibold text-lg text-gray-800 mb-1">Availability Editing (Admin)</h4>
                        <p>Each resource has a modal where admins assign time slots using CRUD operations.</p>
                    </div>
                    <div class="border-l-4 border-ashesi-maroon pl-4">
                        <h4 class="font-semibold text-lg text-gray-800 mb-1">Search Functionality (User)</h4>
                        <p>User inputs (type, capacity, keyword) filter the database and dynamically update the UI.</p>
                    </div>
                    <div class="border-l-4 border-ashesi-maroon pl-4">
                        <h4 class="font-semibold text-lg text-gray-800 mb-1">Booking System</h4>
                        <p>Users select a time slot, submit a booking request, and PHP stores it in the database.</p>
                    </div>
                    <div class="border-l-4 border-ashesi-maroon pl-4">
                        <h4 class="font-semibold text-lg text-gray-800 mb-1">Authentication</h4>
                        <p>Users must log in to access booking features.</p>
                    </div>
                </div>
            </section>

            <!-- User Guide -->
            <section class="bg-white p-8 rounded-xl shadow-lg mb-8 border border-gray-100">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    User Guide
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-ashesi-light p-6 rounded-lg">
                        <h4 class="font-bold text-lg text-ashesi-maroon mb-3">For Regular Users</h4>
                        <ol class="space-y-2 text-gray-700 list-decimal list-inside">
                            <li>Login/Signup</li>
                            <li>Navigate to Resource Locator page</li>
                            <li>Create Booking</li>
                            <li>Manage Bookings</li>
                        </ol>
                    </div>
                    <div class="bg-ashesi-light p-6 rounded-lg">
                        <h4 class="font-bold text-lg text-ashesi-maroon mb-3">For Admin Users</h4>
                        <ol class="space-y-2 text-gray-700 list-decimal list-inside">
                            <li>Login/Signup</li>
                            <li>Navigate to Resource Allocator page</li>
                            <li>Create/Manage Resources</li>
                            <li>Manage Resource Availability</li>
                        </ol>
                    </div>
                </div>
            </section>

            <!-- Architecture -->
            <section class="bg-white p-8 rounded-xl shadow-lg mb-8 border border-gray-100">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    Architecture
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <h4 class="font-bold text-blue-800 mb-2">Frontend</h4>
                            <ul class="space-y-1 text-gray-700">
                                <li>• HTML</li>
                                <li>• CSS & TailwindCSS</li>
                                <li>• JavaScript + jQuery</li>
                                <li>• Mapbox (interactive map service)</li>
                            </ul>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <h4 class="font-bold text-green-800 mb-2">Backend</h4>
                            <ul class="space-y-1 text-gray-700">
                                <li>• PHP (server-side logic and routing)</li>
                                <li>• Node.js (may be utilized)</li>
                            </ul>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                            <h4 class="font-bold text-purple-800 mb-2">Database</h4>
                            <p class="text-gray-700 mb-2">SQLite (may transition to MySQL via phpMyAdmin)</p>
                            <p class="text-sm text-gray-600 font-semibold mb-1">Stores:</p>
                            <ul class="space-y-1 text-gray-600 text-sm">
                                <li>• Resource details</li>
                                <li>• Location coordinates</li>
                                <li>• Capacity information</li>
                                <li>• Resource availability</li>
                                <li>• User accounts</li>
                                <li>• Bookings</li>
                            </ul>
                        </div>
                        <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                            <h4 class="font-bold text-orange-800 mb-2">Hosting</h4>
                            <p class="text-gray-700">Currently running on local XAMPP environment during development</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Testing Strategy -->
            <section class="bg-white p-8 rounded-xl shadow-lg mb-8 border border-gray-100">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    Testing Strategy
                </h3>
                <p class="text-gray-700 mb-4">We plan to conduct end-to-end functional testing, including:</p>
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-6 rounded-lg border border-gray-200">
                    <h4 class="font-bold text-lg text-gray-800 mb-3">User Testing</h4>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start">
                            <span class="text-ashesi-maroon mr-2">•</span>
                            <span>Share the app link with students, faculty, and staff (end users)</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-ashesi-maroon mr-2">•</span>
                            <span>Let them use the map, search, and booking features (testing functionality)</span>
                        </li>
                        <li class="flex items-start">
                            <span class="text-ashesi-maroon mr-2">•</span>
                            <span>Collect feedback via satisfaction surveys</span>
                        </li>
                    </ul>
                </div>
            </section>

            <!-- Retrospection -->
            <section class="bg-white p-8 rounded-xl shadow-lg mb-8 border border-gray-100">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    Retrospection
                </h3>
                <h4 class="font-bold text-lg text-gray-800 mb-3">What We Learned:</h4>
                <div class="space-y-3">
                    <div class="flex items-start p-4 bg-gray-50 rounded-lg">
                        <span class="text-2xl mr-3">✓</span>
                        <p class="text-gray-700">Importance of modularized code to avoid merge conflicts and improve collaboration</p>
                    </div>
                    <div class="flex items-start p-4 bg-gray-50 rounded-lg">
                        <span class="text-2xl mr-3">✓</span>
                        <p class="text-gray-700">The need for clear team communication so everyone understands the features and what they are to do</p>
                    </div>
                    <div class="flex items-start p-4 bg-gray-50 rounded-lg">
                        <span class="text-2xl mr-3">✓</span>
                        <p class="text-gray-700">The value of maintaining a clean folder structure, especially when handling frontend + backend + map scripts</p>
                    </div>
                    <div class="flex items-start p-4 bg-gray-50 rounded-lg">
                        <span class="text-2xl mr-3">✓</span>
                        <p class="text-gray-700">How to integrate Mapbox and combine it with PHP–SQLite workflows</p>
                    </div>
                    <div class="flex items-start p-4 bg-gray-50 rounded-lg">
                        <span class="text-2xl mr-3">✓</span>
                        <p class="text-gray-700">The importance of planning admin workflows early</p>
                    </div>
                </div>
            </section>

            <!-- Team Contribution Summary -->
            <section class="bg-white p-8 rounded-xl shadow-lg mb-8 border border-gray-100">
                <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Team Contribution Summary
                </h3>
                <div class="space-y-6">
                    <div class="border-l-4 border-blue-500 pl-6 py-3">
                        <h4 class="font-bold text-lg text-blue-800 mb-2">George Malimba Billa-Yandanbon</h4>
                        <h5 class="text-gray-700">Sprint 1</h5>
                        <p class="text-gray-700">Implemented the resource locator page which will be viewed by visitors, students and faculty. Ensured the inclusion of an adequate search functionality that helps filter locations by room type and capacity. Implemented the base logic for the map rendering using Mapbox and its CDN. Ensured that all files presented by fellow group members were adequately structured to follow the entire project structure. Compiled and aggregated all code bases including but not limited to style sheets, and JavaScript. Started minimal work on authentication using PHP for login and signup functionality.</p>
                        <br>
                        <h5 class="text-gray-700">Sprint 2</h5>
                        <p class='text-gray-700'>Created the SQLite database and wrote the dbConnector.php to connect the project to the database. Also created the users table. Worked on the login-sign up backend for user authentication</p>
                        <br>
                        <h5 class="text-gray-700">Sprint 3</h5>
                        <p class='text-gray-700'>Worked on routing of users.</p>
                    </div>
                    <div class="border-l-4 border-green-500 pl-6 py-3">
                        <h4 class="font-bold text-lg text-green-800 mb-2">Eyram-Makafui Awoye</h4>
                        <h5 class="text-gray-700">Sprint 1</h5>
                        <p class="text-gray-700">Implemented the UI of Ashesi Campus Resource Locator's front end, creating responsive 'Home' and 'Bookings' pages through the use of HTML and Tailwind CSS. Created a consistent interface and navigation system, including a collapsible sidebar for mobile, a fixed sidebar on desktop, and a header on top that displays page titles and action buttons on each page. Implemented the basic pages' elements, including 'Home'—featuring a hero section for greeting, feature cards, and 'about' information on a dedicated 'Footer' section, and 'Booking'—featuring 'Booking' list section, 'empty' section, and summary section on its 'Sidebar.' Additionally, configured and customized Tailwind CSS settings for branding colors and font, offering a fully 'modern,' 'Accessible,' and 'Interactive' interface that supports 'hover' effects and 'Smooth' page transition and navigation, along with added 'light' functionality for 'JAVASCRIPT.'</p>
                        <br>
                        <h5 class="text-gray-700">Sprint 2</h5>
                        <p class="text-gray-700">For this part of the project, I focused on integrating the database with the site's main pages. I started by refining dbConnector.php, making sure the table creation matched my ERD and setting up the database structure properly for the rest of the application. After that, I updated bookings.php so it connects to the database and retrieves bookings for a sample user. This allowed me to test the booking functionality even without full user authentication in place. I then worked on home.php, setting up the page to load and display a list of bookable resources directly from the database. This confirmed that the database connection works correctly and that the site can now serve dynamic content instead of hard-coded information. Finally, I checked about.php, which is mainly a static page. Since it doesn’t need any database interaction, I left it unchanged.</p>
                        <br>
                        <h5 class="text-gray-700">Sprint 3</h5>
                        <p class='text-gray-700'>Fixed booking page errors</p>


                    </div>
                    <div class="border-l-4 border-purple-500 pl-6 py-3">
                        <h4 class="font-bold text-lg text-purple-800 mb-2">Eyra Inez Agbenu</h4>
                        <p class="text-gray-700">Developed the Admin "Available Sessions" page, which serves as the main interface for managing resource availability across multiple hotspot locations on campus. Designed the full-page layout using HTML and Tailwind CSS including a responsive sidebar with navigation for Resource Allocator, Available Sessions, Analytics, Settings, and Sign Out tabs/pages. Created a dynamic JavaScript-generated hotspot list that displays each resource's name and tag, lets the admin click a hotspot to open the editing modal, and is fully generated from a JavaScript array, making it easy to later connect to backend data. Built a center-aligned interactive modal where administrators can add, remove, and edit time slots for each resource. Implemented all the front-end logic for state management, slot rendering, and modal transitions, and set up the Mapbox container for integration with the team's map script. Overall, provided a functional and responsive interface for managing session availability within the admin dashboard.</p>
                        <br>
                        <h5 class="text-gray-700">Sprint 2</h5>
                        <p class='text-gray-700'>Created an additional entity table, resource_availability, to help determining the status of a resource and included its attributed in the dbConnector.php file (which houses our database details). That is, to tell which ones are occupied or not and at what times. I then drew our Entity-Relationship Diagram (ERD) which you can access in the root folder of our repository.</p>
                        <br>
                        <h5 class="text-gray-700">Sprint 3</h5>
                        <p class='text-gray-700'>Worked on software architecture page. Fixed errors with Available Sessions Page</p>

                    </div>

                    <div class="border-l-4 border-orange-500 pl-6 py-3">
                        <h4 class="font-bold text-lg text-orange-800 mb-2">Kharis Ewurabena Dadzie</h4>
                        <h5 class="text-gray-700">Sprint 1</h5>
                        <p class="text-gray-700">Implemented the resource allocator page which is viewed by the admin. This page is where the admin adds a new resource available on campus to be able to viewed by students, visitors and others. Making use of Map.js, created a map of the school where the admin clicks on a particular location in school where the new resource can be found. Using jQuery, developed a form that slides in from the right where the admin then enters the resource's name, the capacity (if applicable) and a brief description of the resource. When the admin clicks 'Save', the data will be entered into the database and then, users like students will be able to locate this new resource on the Ashesi map and view details about it.</p>
                        <br>
                        <h5 class="text-gray-700">Sprint 2</h5>
                        <p class='text-gray-700'>For this sprint, I was responsible for creating the resources and resource_types tables that the resourceAllocator.html would be sending data to. I moved all Javascript code relating to the actions on that html page into resourceAllocator.js. This handles receving input from the map to populate the newly added longitude and latitude fields in the resource allocator form. I created a link to a modal for adding new resource types as well. I created resourceAllocator.php to process input from the form and insert it into the database. I also added addType.php and getType.php for inserting new types into the database and retrieving current types listed in the database to dynamically update the dropdown type menu respectively.</p>
                        <br>
                        <h5 class="text-gray-700">Sprint 3</h5>
                        <p class='text-gray-700'>Worked on pageflow page. Worked on sidebars</p>
                    </div>
                </div>
            </section>

            <!-- GitHub Link -->
            <section class="bg-gradient-to-r from-gray-800 to-gray-900 text-white p-8 rounded-xl shadow-lg">
                <h3 class="text-2xl font-bold mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z" />
                    </svg>
                    Link to GitHub Repository
                </h3>
                <a href="https://github.com/AshesiWebTech2025/ResourceLocator.git" target="_blank" rel="noopener noreferrer" class="inline-flex items-center bg-white text-gray-900 font-semibold py-3 px-6 rounded-lg hover:bg-gray-100 transition duration-200">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z" />
                    </svg>
                    View on GitHub
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                    </svg>
                </a>
            </section>
        </main>

        <footer class="bg-white border-t border-gray-200 p-6 md:px-10 mt-auto">
            <div class="text-center">
                <p class="text-sm text-gray-500">© 2025 Ashesi University - Team 3. All rights reserved.</p>
            </div>
        </footer>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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


</body>

</html>
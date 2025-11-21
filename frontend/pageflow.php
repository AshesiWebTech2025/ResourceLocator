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
    <title>Page Flow - Ashesi Campus Resource Locator</title>
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
                <a href="bookings.php" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">Campus Map</a>
                <a href="bookings.php" id="nav-bookings" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">My Bookings</a>
                <a href="about.php" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">About</a>
                <a href="software_architecture.php" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">Architecture</a>
                <a href="pageflow.php" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">Page Flow</a>

            </nav>
            <div class="p-4 space-y-2 border-t border-white/20">
                <a href="#" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">Settings</a>
                <a href="login_signup.php" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">Sign Out</a>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-y-auto main-content">
            <header class="bg-white shadow-sm h-16 flex justify-between items-center px-6 md:px-10 sticky top-0 z-10">
                <h1 class="text-xl md:text-2xl font-semibold text-gray-800">Page Flow</h1>
                
            </header>

            <main class="p-6 md:p-10 flex-1">
                <!-- Header Section -->
                <section class="bg-gradient-to-r from-ashesi-maroon to-red-900 text-white p-8 rounded-xl shadow-lg mb-8">
                    <h2 class="text-3xl md:text-4xl font-bold mb-2">Page Flow</h2>
                    <p class="text-lg opacity-90">Page Flow of the Ashesi Campus Resource Locator</p>
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
                        This is the page flow of the Ashesi Campus Resource Locator (ACRL), detailing how a user, depending on their role, is able to navigate through the web application with ease. Please note that this <strong>does not include</strong> pages detailing the architecture. Rather, it shpws the pages that are provide the functionality of the web application.
                    </p>
                </section>

                <section class="bg-white p-8 rounded-xl shadow-lg mb-8 border border-gray-100">
                    <h3 class="text-2xl font-bold text-ash-accent mb-6 flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"></path>
                        </svg>
                        Page Flow Diagram
                    </h3>

                    <div class="bg-ash-gray p-4 rounded">
                        <img src="images/pageflow.jpg" alt="Page Flow Diagram" class="w-full h-auto rounded shadow-sm" />
                    </div>
                </section>

                <section class="bg-white p-8 rounded-xl shadow-lg mb-8 border border-gray-100">
                <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Flow
                </h3>

                <div class="mb-6">
                    <div class="space-y-4">
                        <div class="border-l-4 border-ashesi-maroon pl-6 py-3 bg-gray-50 rounded-r-lg">
                            <p class="text-gray-700 mb-2">This is the entry point to the application, where the Login form is displayed first. Users who are already currently registered can simply enter their credentials and gain access to the application.</p>
                            <p class="text-gray-700 mb-2">Users who have not been registered can click the link at the bottom of the form. This sends them to the Sign Up form, where the necessary details are entered and then submitted. Upon a successful submission, the user is redirected back to the Login page, where they can now enter their credentials and gain access to the application.</p>
                        </div>

                        <div class="border-l-4 border-ashesi-maroon pl-6 py-3 bg-gray-50 rounded-r-lg">
                            <p class="text-gray-700 mb-2">Now, user identification and authentication is done, and based on the role of the user, different pages are shown. During registeration, once you select 'Student', 'Viewer','Faculty', or 'Staff', you are directed to the landing page of the application (<code class="text-sm bg-gray-200 px-2 py-1 rounded">login_signup.php</code>). This page (and all pages) includes a sidebar that allows you to access other pages.</p>
                            <p class="text-gray-700 mb-2">The pages available to these users include</p>
                            <ul>
                                <li>Home</li>
                                <li>Campus Map</li>
                                <li>My Bookings</li>
                                <li>About</li>
                            </ul>
                            <p class="text-gray-700 mb-2">On the other hand, if you are identfied as an 'Admin', upon authentication, you are sent to the Resource Allocator Page.</p>
                            <p class="text-gray-700 mb-2">The pages available to these users include</p>
                            <ul>
                                <li>Resource Allocator</li>
                                <li>Available Sessions</li>
                                <li>About</li>
                            </ul>
                        </div>
                        <div class="border-l-4 border-ashesi-maroon pl-6 py-3 bg-gray-50 rounded-r-lg">
                            <p class="text-gray-700 mb-2">From here on, users can utilise the sidebar to navigate to any page of their choosing.</p>
                        </div>
                    </div>
                </div>        
            </main>
        </div>
    </div>
</body>  
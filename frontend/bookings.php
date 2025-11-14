<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Bookings — Ashesi Locator</title>

  <!-- Project stylesheet -->
  <link rel="stylesheet" href="./css/style.css">

  <!-- Mapbox GL JS -->
  <script src='https://api.mapbox.com/mapbox-gl-js/v3.4.0/mapbox-gl.js'></script>
  <link href='https://api.mapbox.com/mapbox-gl-js/v3.4.0/mapbox-gl.css' rel='stylesheet' />

  <!-- Tailwind (match index.html setup) -->
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

  <!-- jQuery (used for animations/effects, kept from original) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- App script (defer to let DOM load) -->
  <script src="./js/main.js" defer></script>
</head>
<body class="bg-gray-50 font-sans antialiased flex h-screen overflow-hidden">

  <?php
    // 1. Include the database connector
    require_once '../backend/dbConnector.php';

    // 2. Establish a connection
    $db = connectDB(); // Assumes connectDB() is the function in dbConnector.php

    // 3. Fetch bookings for a specific user (using a hardcoded user_id=1 for now)
    // This query joins Bookings with Resources to get the resource name.
    $userId = 1; // In a real app, this would come from a login session
    $stmt = $db->prepare("
        SELECT 
            b.start_time, 
            b.end_time, 
            b.status, 
            r.name AS resource_name
        FROM Bookings b
        JOIN Resources r ON b.resource_id = r.resource_id
        WHERE b.user_id = :user_id
        ORDER BY b.start_time DESC
    ");
    $stmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
    $results = $stmt->execute();
  ?>

  <button id="hamburgerBtn" class="hamburger-btn" aria-label="Toggle menu" aria-expanded="false" type="button">
    <span class="hamburger-icon"></span>
  </button>

  <aside id="sidebar" class="fixed inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition duration-200 ease-in-out bg-ashesi-maroon text-white w-64 flex flex-col z-20 shadow-xl">
      <div class="p-6 flex items-center h-16 border-b border-white/20">
          <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
          <span class="text-xl font-semibold">Ashesi Locator</span>
      </div>
      <nav class="flex-grow p-4 space-y-2">
          <a href="home.php" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">Home</a>
          <a href="#" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">Campus Map</a>
          <a href="bookings.php" class="flex items-center p-3 rounded-lg bg-white/20 transition duration-150 ease-in-out font-medium">My Bookings</a>
      </nav>
      <div class="p-4 space-y-2 border-t border-white/20">
          <a href="#" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">Settings</a>
          <a href="#" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">Sign Out</a>
      </div>
  </aside>

  <div class="flex-1 flex flex-col overflow-y-auto main-content">
    <header class="bg-white shadow-sm h-16 flex justify-between items-center px-6 md:px-10 sticky top-0 z-10">
      <h1 class="text-xl md:text-2xl font-semibold text-gray-800">My Bookings</h1>
      <div class="flex items-center gap-4">
        <button id="toggle-btn" class="bg-ashesi-maroon text-white font-semibold py-2 px-4 rounded-lg hover:bg-ashesi-maroon/90 transition">Toggle bookings</button>
      </div>
    </header>

    <main class="p-6 md:p-10 flex-1">
      <!-- Bookings View -->
      <section id="bookings-view">
        <section class="bg-white p-8 rounded-xl shadow-lg mb-6 border border-gray-100">
          <h2 class="text-2xl font-bold text-gray-900 mb-2">Upcoming & Past Bookings</h2>
          <p class="text-gray-600">Manage your resource reservations. Click a booking to view details or cancel.</p>
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Bookings list column -->
          <div class="col-span-2">
            <div id="bookings-list" class="space-y-4" aria-live="polite">
            
            <?php
              // 4. Loop through the results and display them
              $hasBookings = false;
              while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
                $hasBookings = true;
                // Format dates and times for display
                $startTime = new DateTime($row['start_time']);
                $endTime = new DateTime($row['end_time']);
            ?>
            <!-- Dynamic booking card -->
            <div class="booking-card bg-white p-6 rounded-xl shadow border border-gray-100 flex items-start justify-between">
              <div>
                <h3 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($row['resource_name']); ?></h3>
                <p class="text-sm text-gray-600 mt-1">Date: <span class="font-medium text-gray-700"><?php echo $startTime->format('M d, Y'); ?></span> • Time: <span class="font-medium text-gray-700"><?php echo $startTime->format('H:i') . ' - ' . $endTime->format('H:i'); ?></span></p>
                <p class="text-sm text-gray-500 mt-2">Status: <span class="inline-block bg-ashesi-light text-ashesi-maroon px-2 py-1 rounded-full text-xs font-medium"><?php echo htmlspecialchars($row['status']); ?></span></p>
              </div>
              <div class="flex flex-col items-end gap-2">
                <button class="text-ashesi-maroon border border-ashesi-maroon rounded-full px-3 py-1 text-sm hover:bg-ashesi-maroon hover:text-white transition">View</button>
                <button class="text-red-600 border border-red-200 rounded-full px-3 py-1 text-sm hover:bg-red-50 transition">Cancel</button>
              </div>
            </div>
            <?php } // End of while loop ?>

            <!-- Empty state (shown if no bookings were found) -->
            <div id="empty-state" class="<?php if ($hasBookings) echo 'hidden'; ?> bg-white p-8 rounded-xl shadow border border-gray-100 text-center">
              <svg class="mx-auto w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 6h18M5 18h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
              <h4 class="text-lg font-semibold text-gray-800 mb-2">No bookings yet</h4>
              <p class="text-gray-600 mb-4">You don't have any bookings. Use the "Book a Resource" link on the Home page to create one.</p>
              <a href="home.php" class="inline-block bg-ashesi-maroon text-white font-semibold py-2 px-4 rounded-lg hover:bg-ashesi-maroon/90 transition">Go to Home</a>
            </div>
          </div>
        </div>

        <!-- Sidebar summary column -->
        <aside class="bg-white p-6 rounded-xl shadow border border-gray-100 h-fit">
            <h4 class="text-lg font-semibold text-gray-800 mb-3">Booking Summary</h4>
            <ul class="text-sm text-gray-600 space-y-3">
              <li>Total bookings: <span class="font-medium text-gray-800">1</span></li>
              <li>Upcoming: <span class="font-medium text-gray-800">1</span></li>
              <li>Past: <span class="font-medium text-gray-800">0</span></li>
            </ul>
            <div class="mt-6">
              <a href="home.php" class="inline-block bg-ashesi-maroon text-white font-semibold py-2 px-4 rounded-lg hover:bg-ashesi-maroon/90 transition">Book a Resource</a>
            </div>
        </aside>
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
      <div class="max-w-7xl mx-auto text-center">
        <p class="text-sm text-gray-500">© 2025 Ashesi University. All rights reserved.</p>
      </div>
    </footer>
  </div>

  <script src="./js/map.js"></script>
  <script src="./js/main.js"></script>
  <script>
    // View switching for bookings.html
    document.addEventListener('DOMContentLoaded', function() {
      const navMap = document.getElementById('nav-map');
      const navBookings = document.getElementById('nav-bookings');
      const bookingsView = document.getElementById('bookings-view');
      const mapView = document.getElementById('map-view');

      function showBookingsView() {
        bookingsView.classList.remove('hidden');
        mapView.classList.add('hidden');
        navBookings.classList.add('bg-white/20');
        navMap.classList.remove('bg-white/20');
      }

      function showMapView() {
        bookingsView.classList.add('hidden');
        mapView.classList.remove('hidden');
        navBookings.classList.remove('bg-white/20');
        navMap.classList.add('bg-white/20');
        
        // Resize map if it exists
        if (window.mapInstance) {
          setTimeout(() => {
            window.mapInstance.resize();
          }, 100);
        }
      }

      if (navBookings) {
        navBookings.addEventListener('click', function(e) {
          e.preventDefault();
          showBookingsView();
        });
      }

      if (navMap) {
        navMap.addEventListener('click', function(e) {
          e.preventDefault();
          showMapView();
        });
      }

      // Initialize the correct view on page load
      showBookingsView();
    });
  </script>
</body>
</html>

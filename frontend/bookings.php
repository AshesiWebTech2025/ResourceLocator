<?php
    // --- START OF PHP ERROR DEBUGGING ---
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    // --- END OF PHP ERROR DEBUGGING ---

    // 1. Include the database connector
    require_once '../backend/dbConnector.php';

    // 2. Establish a connection
    $db = connectDB();
    $bookings = [];
    $query_error = false;

    if ($db) {
        $userId = 1; // Hardcoded user_id=1 for now (in production, this would come from session)
        
        // Join Bookings with Resources to get the resource name
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
        
        if ($stmt) {
            $stmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
            $results = $stmt->execute();

            if ($results) {
                while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
                    $bookings[] = $row;
                }
            } else {
                $query_error = "Database query failed: " . $db->lastErrorMsg();
            }
        } else {
            $query_error = "Failed to prepare the database statement.";
        }
    } else {
        $query_error = "Database connection failed.";
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Bookings — Ashesi Locator</title>

  <!-- Project stylesheet -->
  <link rel="stylesheet" href="./css/style.css">

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

  <!-- jQuery (for animations/effects, kept from original) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- App script (defer to let DOM load) -->
  <script src="./js/main.js" defer></script>
</head>
<body class="bg-gray-50 font-sans antialiased flex h-screen overflow-hidden">

  <!-- Hamburger toggle (mobile) -->
  <button id="hamburgerBtn" class="hamburger-btn" aria-label="Toggle menu" aria-expanded="false" type="button">
    <span></span><span></span><span></span>
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
          <a href="about.php" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">About</a>
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
        <a href="home.php" class="bg-green-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-green-700 transition duration-150">Book New Resource</a>
        <button id="toggle-map-btn" class="bg-ashesi-maroon text-white font-semibold py-2 px-4 rounded-lg hover:bg-ashesi-maroon/90 transition duration-150">Toggle Map View</button>
      </div>
    </header>

    <main class="p-6 md:p-10 flex-1">
      <section class="bg-white p-8 rounded-xl shadow-lg mb-6 border border-gray-100">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Upcoming & Past Bookings</h2>
        <p class="text-gray-600">Manage your resource reservations. Click a booking to view details or cancel.</p>
      </section>

      <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Bookings list column -->
        <div class="col-span-2">
          <div id="bookings-list" class="space-y-4" aria-live="polite">
            
            <?php if ($query_error): ?>
                <!-- Display error message if the query failed -->
                <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-lg">
                    <strong>Error:</strong> Failed to load bookings. Please try again.
                </div>

            <?php elseif (empty($bookings)): ?>
                <!-- Empty state (shown if no bookings were found) -->
                <div id="empty-state" class="bg-white p-8 rounded-xl shadow border border-gray-100 text-center">
                  <svg class="mx-auto w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 6h18M5 18h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                  <h4 class="text-lg font-semibold text-gray-800 mb-2">No bookings yet</h4>
                  <p class="text-gray-600 mb-4">You don't have any bookings. Use the "Book a Resource" link on the Home page to create one.</p>
                  <a href="home.php" class="inline-block bg-ashesi-maroon text-white font-semibold py-2 px-4 rounded-lg hover:bg-ashesi-maroon/90 transition">Go to Home</a>
                </div>

            <?php else: ?>
                <!-- Loop through and display each booking -->
                <?php foreach ($bookings as $booking): ?>
                    <?php
                        $startTime = new DateTime($booking['start_time']);
                        $endTime = new DateTime($booking['end_time']);
                    ?>
                    <div class="booking-card bg-white p-6 rounded-xl shadow border border-gray-100 flex items-start justify-between">
                      <div>
                        <h3 class="text-lg font-semibold text-gray-800"><?php echo htmlspecialchars($booking['resource_name']); ?></h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Date: <span class="font-medium text-gray-700"><?php echo $startTime->format('M d, Y'); ?></span> 
                            • Time: <span class="font-medium text-gray-700"><?php echo $startTime->format('H:i'); ?> - <?php echo $endTime->format('H:i'); ?></span>
                        </p>
                        <p class="text-sm text-gray-500 mt-2">
                            Status: 
                            <span class="inline-block <?php 
                              switch ($booking['status']) {
                                case 'Confirmed': echo 'bg-green-100 text-green-800'; break;
                                case 'Cancelled': echo 'bg-red-100 text-red-800'; break;
                                case 'Completed': echo 'bg-gray-100 text-gray-800'; break;
                                default: echo 'bg-blue-100 text-blue-800'; break;
                              }
                            ?> px-2 py-1 rounded-full text-xs font-medium"><?php echo htmlspecialchars($booking['status']); ?></span>
                        </p>
                      </div>
                      <div class="flex flex-col items-end gap-2">
                        <button class="text-ashesi-maroon border border-ashesi-maroon rounded-full px-3 py-1 text-sm hover:bg-ashesi-maroon hover:text-white transition">View</button>
                        <button class="text-red-600 border border-red-200 rounded-full px-3 py-1 text-sm hover:bg-red-50 transition">Cancel</button>
                      </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

          </div>
        </div>

        <!-- Sidebar summary column -->
        <aside class="bg-white p-6 rounded-xl shadow border border-gray-100 h-fit">
          <h4 class="text-lg font-semibold text-gray-800 mb-3">Booking Summary</h4>
          <ul class="text-sm text-gray-600 space-y-3">
            <li>Total bookings: <span class="font-medium text-gray-800"><?php echo count($bookings); ?></span></li>
            <li>Upcoming: <span class="font-medium text-gray-800">
                <?php 
                    $now = new DateTime();
                    $upcoming = array_filter($bookings, function($b) use ($now) {
                        $start = new DateTime($b['start_time']);
                        return $start > $now && $b['status'] === 'Confirmed';
                    });
                    echo count($upcoming);
                ?>
            </span></li>
            <li>Past: <span class="font-medium text-gray-800">
                <?php 
                    $past = array_filter($bookings, function($b) use ($now) {
                        $end = new DateTime($b['end_time']);
                        return $end < $now || $b['status'] === 'Completed';
                    });
                    echo count($past);
                ?>
            </span></li>
          </ul>
          <div class="mt-6">
            <a href="home.php" class="block text-center bg-ashesi-maroon text-white font-semibold py-2 px-4 rounded-lg hover:bg-ashesi-maroon/90 transition">Book a Resource</a>
          </div>
        </aside>
      </section>
    </main>

    <footer class="bg-white border-t border-gray-200 p-6 md:px-10 mt-auto">
      <div class="max-w-7xl mx-auto text-center">
        <p class="text-sm text-gray-500">© 2025 Ashesi University. All rights reserved.</p>
      </div>
    </footer>
  </div>

  <script src="./js/main.js"></script>
</body>
</html>
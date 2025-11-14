<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Bookings — Ashesi Locator</title>

  <!-- Project stylesheet -->
  <link rel="stylesheet" href="css/style.css">

  <!-- Tailwind (match index.html setup) -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
// ... existing code ...
      }
    }
  </script>

  <!-- jQuery (used for animations/effects, kept from original) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- App script (defer to let DOM load) -->
  <script src="js/main.js" defer></script>
</head>
<body class="bg-gray-50 font-sans antialiased flex h-screen overflow-hidden">

  <?php
    // 1. Include the database connector
    require_once '../backend/dbConnector.php';

    // 2. Establish a connection
    $db = connectDB();

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
    <span></span><span></span><span></span>
  </button>

  <aside id="sidebar" class="fixed inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition duration-200 ease-in-out bg-ashesi-maroon text-white w-64 flex flex-col z-20 shadow-xl">
// ... existing code ...
      <nav class="flex-grow p-4 space-y-2">
          <a href="home.php" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">Home</a>
          <a href="#" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">Campus Map</a>
          <a href="bookings.php" class="flex items-center p-3 rounded-lg bg-white/20 transition duration-150 ease-in-out font-medium">My Bookings</a>
      </nav>
// ... existing code ...
    <main class="p-6 md:p-10 flex-1">
      <section class="bg-white p-8 rounded-xl shadow-lg mb-6 border border-gray-100">
// ... existing code ...
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
                <p class="text-sm text-gray-500 mt-2">Status: <span class="inline-block <?php 
                  switch ($row['status']) {
                    case 'Confirmed': echo 'bg-green-100 text-green-800'; break;
                    case 'Cancelled': echo 'bg-red-100 text-red-800'; break;
                    default: echo 'bg-gray-100 text-gray-800'; break;
                  }
                ?> px-2 py-1 rounded-full text-xs font-medium"><?php echo htmlspecialchars($row['status']); ?></span></p>
              </div>
              <div class="flex flex-col items-end gap-2">
                <button class="text-ashesi-maroon border border-ashesi-maroon rounded-full px-3 py-1 text-sm hover:bg-ashesi-maroon hover:text-white transition">View</button>
                <button class="text-red-600 border border-red-200 rounded-full px-3 py-1 text-sm hover:bg-red-50 transition">Cancel</button>
              </div>
            </div>
            <?php } // End of while loop ?>

            <!-- Empty state (shown if no bookings were found) -->
            <div id="empty-state" class="<?php if ($hasBookings) echo 'hidden'; ?> bg-white p-8 rounded-xl shadow border border-gray-100 text-center">
// ... existing code ...
              <p class="text-gray-600 mb-4">You don't have any bookings. Use the "Book a Resource" link on the Home page to create one.</p>
              <a href="home.php" class="inline-block bg-ashesi-maroon text-white font-semibold py-2 px-4 rounded-lg hover:bg-ashesi-maroon/90 transition">Go to Home</a>
            </div>
          </div>
        </div>

        <!-- Sidebar summary column -->
        <aside class="bg-white p-6 rounded-xl shadow border border-gray-100 h-fit">
// ... existing code ...
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
    </main>

    <footer class="bg-white border-t border-gray-200 p-6 md:px-10 mt-auto">
// ... existing code ...
      <div class="max-w-7xl mx-auto text-center">
        <p class="text-sm text-gray-500">© 2025 Ashesi University. All rights reserved.</p>
      </div>
    </footer>
  </div>

  <script src="js/main.js"></script>
</body>
</html>
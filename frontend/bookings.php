<?php
//setup handling
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once('../backend/dbConnector.php'); 
//authentication check
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    $_SESSION['message'] = "Please log in to access this page.";
    $_SESSION['message_type'] = "error";
    header('Location: login_signup.php'); 
    exit();
}
//user info
$user_role = $_SESSION['role'] ?? 'Student';
$user_first_name = $_SESSION["first_name"];
$user_last_name = $_SESSION["last_name"];
$userId = $_SESSION["user_id"];
$header_text = htmlspecialchars($user_role) . " Portal";
//database connection and data fetch
$db = connectDB();
$resources = [];
$bookings = [];
$query_error = false;

if ($db) { 
    //get all resources for the bookin modal
    $resources = getAllResources($db); 
    //get all bookings for the user using getAllBookings
    $bookings = getAllBookings($db, $userId);
    //check if db call was unsuccessful
    if ($bookings === false) {
        $query_error = true;
        $bookings = [];
    }
    $db->close();
} else {
    //if data basec onnection failed
    $query_error = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Bookings — Ashesi Locator</title>
  <!-- Project stylesheet -->
  <link rel="stylesheet" href="css/style.css">
  <!-- Tailwind -->
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
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>\
  <!-- App script -->
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
          <a href="resourceLocator.php" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">Campus Map</a>
          <a href="bookings.php" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">My Bookings</a>
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
      <h1 class="text-xl md:text-2xl font-semibold text-gray-800">My Bookings</h1>
      <div class="flex items-center gap-4">
        <button onclick="openBookingModal()" class="bg-green-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-green-700 transition duration-150">Book New Resource</button>
        <button onclick="window.lco" id="toggle-map-btn" class="bg-ashesi-maroon text-white font-semibold py-2 px-4 rounded-lg hover:bg-ashesi-maroon/90 transition duration-150" ><a href="resourceLocator.php">Toggle Map View</a></button>
      </div>
    </header>

    <main class="p-6 md:p-10 flex-1">
      <?php if (isset($_SESSION['booking_success'])): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-lg mb-6">
          <strong>Success!</strong> <?php echo htmlspecialchars($_SESSION['booking_success']); unset($_SESSION['booking_success']); ?>
        </div>
      <?php endif; ?>
      
      <?php if (isset($_SESSION['booking_error'])): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-lg mb-6">
          <strong>Error!</strong> <?php echo htmlspecialchars($_SESSION['booking_error']); unset($_SESSION['booking_error']); ?>
        </div>
      <?php endif; ?>
      
      <section class="bg-white p-8 rounded-xl shadow-lg mb-6 border border-gray-100">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Upcoming & Past Bookings</h2>
        <p class="text-gray-600">Manage your resource reservations. Click a booking to view details or cancel.</p>
      </section>

      <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Bookings list column -->
        <div class="col-span-2">
          <div id="bookings-list" class="space-y-4" aria-live="polite">
            
            <?php if ($query_error): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-lg">
                    <strong>Error:</strong> Failed to load bookings. Please try again.
                </div>

            <?php elseif (empty($bookings)): ?>
                <!-- Empty state -->
                <div id="empty-state" class="bg-white p-8 rounded-xl shadow border border-gray-100 text-center">
                  <svg class="mx-auto w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 6h18M5 18h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                  <h4 class="text-lg font-semibold text-gray-800 mb-2">No bookings yet</h4>
                  <p class="text-gray-600 mb-4">You don't have any bookings. Use the "Book a Resource" link on the Home page to create one.</p>
                  <a href="home.php" class="inline-block bg-ashesi-maroon text-white font-semibold py-2 px-4 rounded-lg hover:bg-ashesi-maroon/90 transition">Go to Home</a>
                </div>

            <?php else: ?>
                <?php foreach ($bookings as $booking): ?>
                    <?php
                        $startTime = new DateTime($booking['start_time']);
                        $endTime = new DateTime($booking['end_time']);
                        $now = new DateTime();
                        $isPast = $endTime < $now;
                        $isCancellable = $booking['status'] === 'Confirmed' && $startTime > $now;
                        
                        //preparebookingdataasJSONforJavaScript
                        $bookingJson = json_encode([
                            'booking_id' => $booking['booking_id'] ?? 0,
                            'resource_name' => $booking['resource_name'],
                            'start_time' => $booking['start_time'],
                            'end_time' => $booking['end_time'],
                            'status' => $booking['status'],
                            'purpose' => $booking['purpose'] ?? 'N/A'
                        ]);
                    ?>
                    <div class="booking-card bg-white p-6 rounded-xl shadow border border-gray-100 flex items-start justify-between" data-booking-id="<?php echo $booking['booking_id'] ?? 0; ?>">
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
                        <button onclick='viewBookingDetails(<?php echo $bookingJson; ?>)' class="text-ashesi-maroon border border-ashesi-maroon rounded-full px-3 py-1 text-sm hover:bg-ashesi-maroon hover:text-white transition">View</button>
                        <?php if ($isCancellable): ?>
                            <button onclick="confirmCancelBooking(<?php echo $booking['booking_id'] ?? 0; ?>)" class="text-red-600 border border-red-200 rounded-full px-3 py-1 text-sm hover:bg-red-50 transition">Cancel</button>
                        <?php else: ?>
                            <button disabled class="text-gray-400 border border-gray-200 rounded-full px-3 py-1 text-sm cursor-not-allowed">
                                <?php echo ($booking['status'] === 'Cancelled') ? 'Cancelled' : 'Past'; ?>
                            </button>
                        <?php endif; ?>
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
                                //call upcomoning bookings
                    $upcoming = array_filter($bookings, function($b) use ($now) {
                        $start = new DateTime($b['start_time']);
                        return $start > $now && $b['status'] === 'Confirmed';
                    });
                    echo count($upcoming);
                ?>
            </span></li>
            <li>Past: <span class="font-medium text-gray-800">
                <?php 
                    //calculate the past bookings count in php
                    $past = array_filter($bookings, function($b) use ($now) {
                        $end = new DateTime($b['end_time']);
                        return $end < $now || $b['status'] === 'Completed' || $b['status'] === 'Cancelled';
                    });
                    echo count($past);
                ?>
            </span></li>
          </ul>
          <div class="mt-6">
            <button onclick="openBookingModal()" class="w-full text-center bg-ashesi-maroon text-white font-semibold py-2 px-4 rounded-lg hover:bg-ashesi-maroon/90 transition">Book a Resource</button>
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

  <!-- View Booking Details Modal -->
  <div id="viewBookingModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
          <div class="bg-ashesi-maroon text-white p-6 rounded-t-xl">
              <div class="flex justify-between items-center">
                  <h3 class="text-2xl font-bold">Booking Details</h3>
                  <button onclick="closeViewModal()" class="text-white hover:text-gray-200 transition">
                      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                      </svg>
                  </button>
              </div>
          </div>
          
          <div class="p-6 space-y-4">
              <div class="border-b border-gray-200 pb-3">
                  <p class="text-sm text-gray-500">Resource</p>
                  <p id="view-resource-name" class="text-lg font-semibold text-gray-900">-</p>
              </div>
              
              <div class="grid grid-cols-2 gap-4">
                  <div>
                      <p class="text-sm text-gray-500">Date</p>
                      <p id="view-date" class="font-medium text-gray-900">-</p>
                  </div>
                  <div>
                      <p class="text-sm text-gray-500">Status</p>
                      <p id="view-status" class="font-medium">-</p>
                  </div>
              </div>
              
              <div class="grid grid-cols-2 gap-4">
                  <div>
                      <p class="text-sm text-gray-500">Start Time</p>
                      <p id="view-start-time" class="font-medium text-gray-900">-</p>
                  </div>
                  <div>
                      <p class="text-sm text-gray-500">End Time</p>
                      <p id="view-end-time" class="font-medium text-gray-900">-</p>
                  </div>
              </div>
              
              <div class="border-t border-gray-200 pt-3">
                  <p class="text-sm text-gray-500 mb-1">Purpose</p>
                  <p id="view-purpose" class="text-gray-900">-</p>
              </div>
              
              <div class="flex gap-3 pt-4">
                  <button onclick="closeViewModal()"
                          class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">
                      Close
                  </button>
              </div>
          </div>
      </div>
  </div>

  <!-- Cancel Booking Confirmation Modal -->
  <div id="cancelBookingModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
          <div class="p-6">
              <div class="flex items-center gap-4 mb-4">
                  <div class="bg-red-100 rounded-full p-3">
                      <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                      </svg>
                  </div>
                  <div>
                      <h3 class="text-xl font-bold text-gray-900">Cancel Booking?</h3>
                      <p class="text-sm text-gray-600">This action cannot be undone.</p>
                  </div>
              </div>
              
              <p class="text-gray-700 mb-6">Are you sure you want to cancel this booking? You will lose your reservation.</p>
              
              <form action="../backend/cancel_booking.php" method="POST" class="flex gap-3">
                  <input type="hidden" name="booking_id" id="cancel-booking-id" value="">
                  <button type="button" onclick="closeCancelModal()"
                          class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                      Keep Booking
                  </button>
                  <button type="submit"
                          class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold">
                      Yes, Cancel
                  </button>
              </form>
          </div>
      </div>
  </div>

  <script>
      function openBookingModal() {
          document.getElementById('bookingModal').classList.remove('hidden');
          document.body.style.overflow = 'hidden';
      }
      function closeBookingModal() {
          document.getElementById('bookingModal').classList.add('hidden');
          document.body.style.overflow = 'auto';
      }
      function viewBookingDetails(booking) {
          const modal = document.getElementById('viewBookingModal');
          // Parse dates
          const startDate = new Date(booking.start_time);
          const endDate = new Date(booking.end_time);
          // Format dates and times
          const dateStr = startDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
          const startTimeStr = startDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: false });
          const endTimeStr = endDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: false });
          // Status badge styling
          let statusClass = 'bg-blue-100 text-blue-800';
          if (booking.status === 'Confirmed') statusClass = 'bg-green-100 text-green-800';
          if (booking.status === 'Cancelled') statusClass = 'bg-red-100 text-red-800';
          if (booking.status === 'Completed') statusClass = 'bg-gray-100 text-gray-800';
          // Populate modal
          document.getElementById('view-resource-name').textContent = booking.resource_name;
          document.getElementById('view-date').textContent = dateStr;
          document.getElementById('view-start-time').textContent = startTimeStr;
          document.getElementById('view-end-time').textContent = endTimeStr;
          document.getElementById('view-purpose').textContent = booking.purpose;
          const statusEl = document.getElementById('view-status');
          statusEl.textContent = booking.status;
          statusEl.className = `inline-block px-2 py-1 rounded-full text-xs font-medium ${statusClass}`;
          modal.classList.remove('hidden');
          document.body.style.overflow = 'hidden';
      }
      function closeViewModal() {
          document.getElementById('viewBookingModal').classList.add('hidden');
          document.body.style.overflow = 'auto';
      }

      function confirmCancelBooking(bookingId) {
          document.getElementById('cancel-booking-id').value = bookingId;
          document.getElementById('cancelBookingModal').classList.remove('hidden');
          document.body.style.overflow = 'hidden';
      }

      function closeCancelModal() {
          document.getElementById('cancelBookingModal').classList.add('hidden');
          document.body.style.overflow = 'auto';
      }
      // Close modals when clicking outside
      document.getElementById('bookingModal')?.addEventListener('click', function(e) {
          if (e.target === this) closeBookingModal();
      });
      document.getElementById('viewBookingModal')?.addEventListener('click', function(e) {
          if (e.target === this) closeViewModal();
      });

      document.getElementById('cancelBookingModal')?.addEventListener('click', function(e) {
          if (e.target === this) closeCancelModal();
      });
  </script>

  <script src="./js/main.js"></script>
</body>
</html>
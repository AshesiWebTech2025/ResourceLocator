<?php
session_start();

// Check if this is an API request
$isApiRequest = isset($_GET['action']);

// For API requests, suppress error output to ensure clean JSON
if ($isApiRequest) {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
    ob_start(); // Start output buffering to catch any stray output
} else {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

require_once('../backend/dbConnector.php'); 

if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    if ($isApiRequest) {
        // For API requests, return JSON instead of redirecting
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Session expired. Please log in again.']);
        exit;
    }
    $_SESSION['message'] = "please log in to access this page.";
    $_SESSION['message_type'] = "error";
    header('Location: login_signup.php'); 
    exit();
}

$db = connectDB();
$db_error = false;
$initialHotspots = [];

if (!$db) {
    $db_error = true;
    if ($isApiRequest) {
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'database connection failed.']);
        exit;
    }
}

if (!$db_error && $isApiRequest) {
    ob_end_clean(); // Clear any buffered output
    header('Content-Type: application/json');
    $action = $_GET['action'];
    $response = ['success' => false, 'message' => 'invalid action'];

    switch ($action) {
        case 'getResources':
            $resources = getBookableResources($db);
            $response = ['success' => true, 'data' => $resources];
            break;

        case 'getAvailabilitySlots':
            $resourceId = isset($_GET['resource_id']) ? intval($_GET['resource_id']) : 0;
            if ($resourceId > 0) {
                $slots = getResourceAvailability($db, $resourceId);
                $formattedSlots = [];
                foreach ($slots as $slot) {
                    // parse the time strings back to numbers for frontend
                    $startParts = explode(':', $slot['start_time']);
                    $endParts = explode(':', $slot['end_time']);
                    
                    // Return both formats for compatibility
                    $formattedSlots[] = [
                        'day' => strtolower($slot['day_of_week']),  // lowercase to match select values
                        'day_of_week' => strtolower($slot['day_of_week']),
                        'start_hour' => intval($startParts[0]),
                        'start_minute' => intval($startParts[1]),
                        'end_hour' => intval($endParts[0]),
                        'end_minute' => intval($endParts[1]),
                        'start_time' => $slot['start_time'],
                        'end_time' => $slot['end_time']
                    ];
                }
                $response = ['success' => true, 'data' => $formattedSlots];
            } else {
                $response['message'] = 'invalid resource id';
            }
            break;

        case 'updateSlots':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $rawData = file_get_contents('php://input');
                
                // Log the raw input for debugging
                error_log('updateSlots raw input: ' . $rawData);
                
                $data = json_decode($rawData, true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $response['message'] = 'invalid json data: ' . json_last_error_msg() . ' Raw: ' . substr($rawData, 0, 200);
                    error_log('JSON decode error: ' . json_last_error_msg());
                    break;
                }
                
                error_log('updateSlots decoded data: ' . print_r($data, true));
                
                $resourceId = isset($data['resource_id']) ? intval($data['resource_id']) : 0;
                $slots = isset($data['slots']) ? $data['slots'] : null;
                
                if ($resourceId > 0 && is_array($slots)) {
                    // convert number inputs to time strings
                    $dbSlots = [];
                    foreach ($slots as $slot) {
                        $day = null;
                        $startTime = null;
                        $endTime = null;
                        
                        // Get day - handle both 'day' and 'day_of_week' keys
                        if (isset($slot['day'])) {
                            $day = $slot['day'];
                        } elseif (isset($slot['day_of_week'])) {
                            $day = $slot['day_of_week'];
                        }
                        
                        // Handle start_hour/start_minute format
                        if (isset($slot['start_hour']) && isset($slot['start_minute'])) {
                            $startTime = sprintf('%02d:%02d:00', intval($slot['start_hour']), intval($slot['start_minute']));
                        }
                        // Handle start_time string format (HH:MM or HH:MM:SS)
                        elseif (isset($slot['start_time'])) {
                            $parts = explode(':', $slot['start_time']);
                            if (count($parts) >= 2) {
                                $startTime = sprintf('%02d:%02d:00', intval($parts[0]), intval($parts[1]));
                            }
                        }
                        // Handle 'start' string format
                        elseif (isset($slot['start'])) {
                            $parts = explode(':', $slot['start']);
                            if (count($parts) >= 2) {
                                $startTime = sprintf('%02d:%02d:00', intval($parts[0]), intval($parts[1]));
                            }
                        }
                        
                        // Handle end_hour/end_minute format
                        if (isset($slot['end_hour']) && isset($slot['end_minute'])) {
                            $endTime = sprintf('%02d:%02d:00', intval($slot['end_hour']), intval($slot['end_minute']));
                        }
                        // Handle end_time string format (HH:MM or HH:MM:SS)
                        elseif (isset($slot['end_time'])) {
                            $parts = explode(':', $slot['end_time']);
                            if (count($parts) >= 2) {
                                $endTime = sprintf('%02d:%02d:00', intval($parts[0]), intval($parts[1]));
                            }
                        }
                        // Handle 'end' string format
                        elseif (isset($slot['end'])) {
                            $parts = explode(':', $slot['end']);
                            if (count($parts) >= 2) {
                                $endTime = sprintf('%02d:%02d:00', intval($parts[0]), intval($parts[1]));
                            }
                        }
                        
                        // Only add if we have all required fields
                        if ($day && $startTime && $endTime) {
                            $dbSlots[] = [
                                'day' => $day,
                                'start' => $startTime,
                                'end' => $endTime
                            ];
                        }
                    }
                    
                    if (setResourceAvailability($db, $resourceId, $dbSlots)) {
                        $response = ['success' => true, 'message' => 'availability updated successfully'];
                    } else {
                        $response['message'] = 'database save failed';
                    }
                } else {
                    $response['message'] = 'invalid data';
                }
            }
            break;
    }

    if ($db) $db->close();
    echo json_encode($response);
    exit;
}

if (!$db_error && $db) {
    $initialHotspots = getBookableResources($db);
    $db->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Available Sessions — Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src='https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.js'></script>
    <link href='https://api.mapbox.com/mapbox-gl-js/v2.14.1/mapbox-gl.css' rel='stylesheet' />
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
</head>

<body class="bg-gray-50 font-sans antialiased flex h-screen overflow-hidden">

    <aside id="sidebar" class="fixed inset-y-0 left-0 transform -translate-x-full md:relative md:translate-x-0 transition duration-200 ease-in-out bg-ashesi-maroon text-white w-64 flex flex-col z-20 shadow-xl">
        <div class="p-6 flex items-center h-16 border-b border-white/20">
            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.608 3.35 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="text-xl font-semibold">Admin</span>
        </div>

        <nav class="flex-grow p-4 space-y-2">
            <a href="resourceAllocator.php" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">Resource Allocator</a>
            <a href="available_sessions.php" class="flex items-center p-3 rounded-lg bg-white/20 transition duration-150 ease-in-out font-medium">Available Sessions</a>
            <a href="home.php" class="flex items-center p-3 rounded-lg hover:bg-white/10 transition duration-150 ease-in-out font-medium">Home</a>
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
            <h1 class="text-xl md:text-2xl font-semibold text-gray-800 mr-auto">Available Sessions</h1>
            <div class="flex items-center text-ashesi-maroon font-medium border border-ashesi-maroon rounded-full py-1 px-4">Admin Mode</div>
        </header>

        <main class="p-6 md:p-10 flex-1">
            <?php if ($db_error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Database Error!</strong>
                    <span class="block sm:inline">could not connect to the database to load resources.</span>
                </div>
            <?php endif; ?>

            <section class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <aside class="lg:col-span-1 bg-white rounded-xl shadow border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold mb-4">Bookable Resources</h2>
                    <div id="hotspot-list" class="space-y-3" aria-live="polite"></div>
                    <div class="mt-6 text-sm text-gray-500">click a resource to edit its weekly availability slots.</div>
                </aside>

                <div id="map-container" class="lg:col-span-3 h-[calc(100vh-250px)]">
                    <div id="ashesi-map" class="w-full h-full rounded-lg"></div>
                </div>
            </section>
        </main>

        <footer class="bg-white border-t border-gray-200 p-6 md:px-10 mt-auto">
            <div class="text-center">
                <p class="text-sm text-gray-500">© 2025 ashesi university. all rights reserved.</p>
            </div>
        </footer>
    </div>

    <div id="sessionModal" class="fixed inset-0 hidden flex items-center justify-center z-50">
        <div class="absolute inset-0 bg-black/50 modal-backdrop"></div>
        <div class="relative bg-white rounded-xl shadow-lg w-11/12 max-w-2xl p-6 z-60">
            <div class="flex items-start justify-between">
                <h3 id="modalTitle" class="text-xl font-semibold">Edit Times</h3>
                <button id="closeModal" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <p id="modalSub" class="text-sm text-gray-600 mt-2">edit availability times for the selected hotspot/resource below.</p>

            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Resource</label>
                    <div id="resourceName" class="mt-1 text-gray-800 font-medium">(resource)</div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Time Slots</label>
                    <ul id="slotList" class="space-y-2 max-h-48 overflow-y-auto pr-2 border rounded-lg p-3 bg-gray-50"></ul>

                    <div class="mt-3 space-y-3 p-3 border rounded-lg bg-gray-50">
                        <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-end">
                            <select id="daySelect" class="border rounded-md px-2 py-1 w-full sm:w-auto">
                                <option value="monday">Monday</option>
                                <option value="tuesday">Tuesday</option>
                                <option value="wednesday">Wednesday</option>
                                <option value="thursday">Thursday</option>
                                <option value="friday">Friday</option>
                                <option value="saturday">Saturday</option>
                                <option value="sunday">Sunday</option>
                            </select>
                            
                            <div class="flex items-center gap-2">
                                <label class="text-sm text-gray-600">Start:</label>
                                <select id="startHour" class="border rounded-md px-2 py-1 w-16">
                                    <?php for($i=0; $i<=23; $i++): ?>
                                        <option value="<?php echo $i; ?>"><?php echo sprintf('%02d', $i); ?></option>
                                    <?php endfor; ?>
                                </select>
                                <span>:</span>
                                <select id="startMinute" class="border rounded-md px-2 py-1 w-16">
                                    <option value="0">00</option>
                                    <option value="15">15</option>
                                    <option value="30">30</option>
                                    <option value="45">45</option>
                                </select>
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <label class="text-sm text-gray-600">End:</label>
                                <select id="endHour" class="border rounded-md px-2 py-1 w-16">
                                    <?php for($i=0; $i<=23; $i++): ?>
                                        <option value="<?php echo $i; ?>"><?php echo sprintf('%02d', $i); ?></option>
                                    <?php endfor; ?>
                                </select>
                                <span>:</span>
                                <select id="endMinute" class="border rounded-md px-2 py-1 w-16">
                                    <option value="0">00</option>
                                    <option value="15">15</option>
                                    <option value="30">30</option>
                                    <option value="45">45</option>
                                </select>
                            </div>
                            
                            <button id="addSlot" type="button" class="bg-ashesi-maroon text-white px-3 py-2 rounded-md hover:bg-ashesi-maroon/90 transition">Add Slot</button>
                        </div>
                    </div>
                </div>
                
                <div id="saveMessage" class="hidden p-3 text-sm rounded-lg" role="alert"></div>

                <div class="flex justify-end gap-3">
                    <button id="saveTimes" type="button" class="bg-ashesi-maroon text-white px-4 py-2 rounded-lg font-semibold hover:bg-ashesi-maroon/90 transition">Save</button>
                    <button id="cancelTimes" type="button" class="border rounded-lg px-4 py-2 hover:bg-gray-50 transition">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dayOrder = {'monday':1,'tuesday':2,'wednesday':3,'thursday':4,'friday':5,'saturday':6,'sunday':7};
            let hotspots = <?php echo json_encode($initialHotspots); ?>;
            let currentResource = null;
            let resourceSlots = {};

            function showMessage(message, type = 'error') {
                const el = document.getElementById('saveMessage');
                el.textContent = message;
                el.className = 'p-3 text-sm rounded-lg ' + (type === 'error' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800');
                el.classList.remove('hidden');
                if (type === 'success') setTimeout(() => el.classList.add('hidden'), 3000);
            }

            function hideMessage() {
                document.getElementById('saveMessage').classList.add('hidden');
            }

            function formatTime(hour, minute) {
                return `${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}`;
            }

            function sortSlots(slots) {
                return slots.sort((a, b) => {
                    // Handle both 'day' and 'day_of_week' keys
                    const dayA = (a.day || a.day_of_week || '').toLowerCase();
                    const dayB = (b.day || b.day_of_week || '').toLowerCase();
                    if (dayOrder[dayA] !== dayOrder[dayB]) return (dayOrder[dayA] || 99) - (dayOrder[dayB] || 99);
                    if (a.start_hour !== b.start_hour) return a.start_hour - b.start_hour;
                    return a.start_minute - b.start_minute;
                });
            }

            function renderSlots() {
                const slotList = document.getElementById('slotList');
                const slots = currentResource && resourceSlots[currentResource.resource_id] ? resourceSlots[currentResource.resource_id] : [];
                const sortedSlots = sortSlots([...slots]);
                
                slotList.innerHTML = '';
                
                if (sortedSlots.length === 0) {
                    slotList.innerHTML = '<li class="text-sm text-gray-500 text-center py-2">no availability slots defined</li>';
                    return;
                }
                
                sortedSlots.forEach((slot, index) => {
                    const li = document.createElement('li');
                    li.className = 'flex items-center justify-between bg-white p-3 rounded border';
                    // Handle both 'day' and 'day_of_week' keys
                    const dayName = slot.day || slot.day_of_week || 'Unknown';
                    li.innerHTML = `
                        <div class="text-sm">
                            <span class="font-medium capitalize">${dayName}</span> — ${formatTime(slot.start_hour, slot.start_minute)} to ${formatTime(slot.end_hour, slot.end_minute)}
                        </div>
                        <button type="button" class="text-red-600 hover:text-red-800 text-sm font-medium" data-index="${index}">
                            Remove
                        </button>
                    `;
                    li.querySelector('button').addEventListener('click', function() {
                        const idx = parseInt(this.getAttribute('data-index'));
                        resourceSlots[currentResource.resource_id].splice(idx, 1);
                        renderSlots();
                    });
                    slotList.appendChild(li);
                });
            }

            function openModal(resource) {
                currentResource = resource;
                hideMessage();
                document.getElementById('modalTitle').textContent = 'Edit Times — ' + resource.name;
                document.getElementById('resourceName').textContent = resource.name + ' (' + (resource.type_name || 'N/A') + ')';
                
                document.getElementById('slotList').innerHTML = '<li class="text-sm text-gray-500 text-center py-2">loading availability...</li>';
                
                fetch(`available_sessions.php?action=getAvailabilitySlots&resource_id=${resource.resource_id}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Network error');
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            resourceSlots[resource.resource_id] = data.data || [];
                        } else {
                            resourceSlots[resource.resource_id] = [];
                        }
                        renderSlots();
                    })
                    .catch(error => {
                        console.error('Error loading slots:', error);
                        resourceSlots[resource.resource_id] = [];
                        renderSlots();
                        showMessage('failed to load existing slots', 'error');
                    })
                    .finally(() => {
                        $('#sessionModal').removeClass('hidden').hide().fadeIn(150);
                    });
            }

            function closeModal() {
                $('#sessionModal').fadeOut(150, function() {
                    $(this).addClass('hidden');
                    currentResource = null;
                });
            }

            function renderResourceList() {
                const list = document.getElementById('hotspot-list');
                list.innerHTML = '';
                
                if (hotspots.length === 0) {
                    list.innerHTML = '<div class="text-gray-500 text-center py-4">no bookable resources found</div>';
                    return;
                }
                
                hotspots.forEach(resource => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'w-full text-left p-4 rounded-lg hover:bg-gray-100 border border-gray-200 focus:ring-2 focus:ring-ashesi-maroon focus:outline-none transition-all duration-200';
                    button.innerHTML = `
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="font-semibold text-gray-900">${resource.name}</div>
                                <div class="text-xs text-gray-600 mt-1">${resource.type_name || 'No type'}</div>
                            </div>
                            <div class="text-ashesi-maroon font-medium text-sm bg-ashesi-maroon/10 px-2 py-1 rounded">Edit</div>
                        </div>
                    `;
                    button.addEventListener('click', () => openModal(resource));
                    list.appendChild(button);
                });
            }

            document.getElementById('addSlot').addEventListener('click', function() {
                hideMessage();
                if (!currentResource) {
                    showMessage('please select a resource first', 'error');
                    return;
                }
                
                const day = document.getElementById('daySelect').value;
                const startHour = parseInt(document.getElementById('startHour').value);
                const startMinute = parseInt(document.getElementById('startMinute').value);
                const endHour = parseInt(document.getElementById('endHour').value);
                const endMinute = parseInt(document.getElementById('endMinute').value);
                
                // validate times
                if (startHour > endHour || (startHour === endHour && startMinute >= endMinute)) {
                    showMessage('end time must be after start time', 'error');
                    return;
                }
                
                if (!resourceSlots[currentResource.resource_id]) {
                    resourceSlots[currentResource.resource_id] = [];
                }
                
                resourceSlots[currentResource.resource_id].push({
                    day: day,
                    start_hour: startHour,
                    start_minute: startMinute,
                    end_hour: endHour,
                    end_minute: endMinute
                });
                
                renderSlots();
            });

            document.getElementById('saveTimes').addEventListener('click', function() {
                if (!currentResource) {
                    showMessage('no resource selected', 'error');
                    return;
                }
                
                const button = this;
                const originalText = button.textContent;
                button.textContent = 'Saving...';
                button.disabled = true;
                hideMessage();
                
                // Get slots and prepare data
                const slots = resourceSlots[currentResource.resource_id] || [];
                console.log('Slots to save:', slots);
                
                // Convert slots to ensure consistent format
                const normalizedSlots = slots.map(function(slot) {
                    return {
                        day: slot.day || slot.day_of_week || 'monday',
                        start_hour: parseInt(slot.start_hour, 10) || 0,
                        start_minute: parseInt(slot.start_minute, 10) || 0,
                        end_hour: parseInt(slot.end_hour, 10) || 0,
                        end_minute: parseInt(slot.end_minute, 10) || 0
                    };
                });
                
                const payload = {
                    resource_id: currentResource.resource_id,
                    slots: normalizedSlots
                };
                
                console.log('Sending payload:', JSON.stringify(payload));
                
                // Use XMLHttpRequest for maximum compatibility
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'available_sessions.php?action=updateSlots', true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        console.log('XHR status:', xhr.status);
                        console.log('XHR response:', xhr.responseText);
                        
                        button.textContent = originalText;
                        button.disabled = false;
                        
                        if (xhr.status === 200) {
                            try {
                                const data = JSON.parse(xhr.responseText);
                                if (data.success) {
                                    showMessage('availability saved successfully!', 'success');
                                    setTimeout(closeModal, 1000);
                                } else {
                                    showMessage('Save failed: ' + (data.message || 'Unknown error'), 'error');
                                }
                            } catch (e) {
                                console.error('Parse error:', e);
                                showMessage('Invalid server response: ' + xhr.responseText.substring(0, 100), 'error');
                            }
                        } else {
                            showMessage('Server error: ' + xhr.status, 'error');
                        }
                    }
                };
                
                xhr.onerror = function() {
                    console.error('XHR error');
                    button.textContent = originalText;
                    button.disabled = false;
                    showMessage('Network error occurred', 'error');
                };
                
                try {
                    xhr.send(JSON.stringify(payload));
                } catch (e) {
                    console.error('Send error:', e);
                    button.textContent = originalText;
                    button.disabled = false;
                    showMessage('Could not send request: ' + e.message, 'error');
                }
            });

            document.getElementById('closeModal').addEventListener('click', closeModal);
            document.getElementById('cancelTimes').addEventListener('click', closeModal);
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeModal();
            });

            renderResourceList();
        });
    </script>

    <script>
        // Hamburger menu functionality
        document.addEventListener('DOMContentLoaded', () => {
            const hamburgerBtn = document.getElementById('hamburgerBtn');
            const sidebar = document.getElementById('sidebar');

            if (hamburgerBtn && sidebar) {
                hamburgerBtn.addEventListener('click', () => {
                    const isExpanded = hamburgerBtn.getAttribute('aria-expanded') === 'true';
                    sidebar.classList.toggle('-translate-x-full');
                    hamburgerBtn.setAttribute('aria-expanded', !isExpanded);

                    if (!isExpanded) {
                        document.body.classList.add('mobile-nav-open');
                    } else {
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

    <script src="js/map.js"></script>
</body>
</html>
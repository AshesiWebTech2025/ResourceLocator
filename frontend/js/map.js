// --- Configuration Constants ---
// Mapbox Public Access Token
const MAPBOX_ACCESS_TOKEN = 'pk.eyJ1IjoibWFsaW1iYSIsImEiOiJjbTgyeWYwMTEwaWFmMmtxdml6endnZjFmIn0.TF-YvTG_Xa-Nx301EfmZTw'; 

// Coordinates for Ashesi University (Long, Lat): -0.21972, 5.75972
const ASHESI_CENTER = [-0.21972, 5.75972]; 
const INITIAL_ZOOM = 17;

// Global Mapbox instance holder (used for resizing)
let mapInstance = null;

/**
 * Global function to switch between main views in the Canvas.
 * It handles showing the correct content and updating the sidebar active link.
 * @param {string} viewId - The ID of the view container to show ('map-view' or 'bookings-view').
 */
function showView(viewId) {
    const mapView = document.getElementById('map-view');
    const bookingsView = document.getElementById('bookings-view');
    const navMap = document.getElementById('nav-map');
    const navBookings = document.getElementById('nav-bookings');

    // 1. Hide all views
    if (mapView) mapView.classList.add('hidden');
    if (bookingsView) bookingsView.classList.add('hidden');
    
    // 2. Show the requested view
    const targetView = document.getElementById(viewId);
    if (targetView) {
        targetView.classList.remove('hidden');
        
        // 3. Special case: If map view is shown, ensure Mapbox recalculates its size
        if (viewId === 'map-view' && mapInstance) {
            // A small delay is necessary to ensure the CSS reflow completes before Mapbox attempts to resize.
            setTimeout(() => {
                 mapInstance.resize();
            }, 50); 
        }
    }
    
    // 4. Update active link styling
    if (navMap) navMap.classList.remove('bg-white/20');
    if (navBookings) navBookings.classList.remove('bg-white/20');

    if (viewId === 'map-view' && navMap) {
        navMap.classList.add('bg-white/20');
    } else if (viewId === 'bookings-view' && navBookings) {
        navBookings.classList.add('bg-white/20');
    }
}


/**
 * Initializes the Mapbox map and stores the instance globally.
 */
function initializeMap() {
    // We check for the global Mapbox object which should be loaded via CDN script in index.html
    if (typeof mapboxgl === 'undefined' || typeof mapboxgl.Map === 'undefined') {
        console.error("Mapbox GL JS library not loaded. Cannot initialize map.");
        return;
    }
    
    mapboxgl.accessToken = MAPBOX_ACCESS_TOKEN; 

    const map = new mapboxgl.Map({
        container: 'ashesi-map', // HTML element ID for the map
        style: 'mapbox://styles/mapbox/satellite-streets-v12',
        center: ASHESI_CENTER,
        zoom: INITIAL_ZOOM,
        pitch: 45,
        bearing: 0,
        maxBounds: [
            [-0.23, 5.74], 
            [-0.20, 5.77]
        ]
    });

    // Store the map instance globally for the resize function in showView
    mapInstance = map; 

    // Add navigation control (zoom and compass)
    map.addControl(new mapboxgl.NavigationControl(), 'top-right');
    
    // Add a marker exactly at the center of the campus (Main Administration area)
    new mapboxgl.Marker({ color: '#800020' })
        .setLngLat(ASHESI_CENTER)
        .setPopup(new mapboxgl.Popup().setHTML("<h4>Ashesi University</h4><p>1 University Avenue, Berekuso</p>"))
        .addTo(map);

    // Handle map resize on window resize to ensure responsiveness
    window.addEventListener('resize', () => {
        map.resize();
    });
    
    // On load, ensure the map flies to the center with a nice transition
    map.on('load', () => {
        map.flyTo({
            center: ASHESI_CENTER,
            zoom: INITIAL_ZOOM,
            speed: 1.5,
            curve: 1,
            easing(t) { return t; }
        });
    });
}

/**
 * Sets up the event listener for the mobile sidebar toggle button.
 */
function setupSidebarToggle() {
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    if (mobileMenuButton) {
        mobileMenuButton.addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        });
    }
}

/**
 * Sets up listeners for sidebar navigation links to handle view switching.
 */
function setupNavigation() {
    // Use the new IDs for robust targeting
    const mapLink = document.getElementById('nav-map');
    const bookingsLink = document.getElementById('nav-bookings');

    if (mapLink) {
        mapLink.addEventListener('click', function(e) {
            e.preventDefault(); 
            showView('map-view');
        });
    }

    if (bookingsLink) {
        bookingsLink.addEventListener('click', function(e) {
            e.preventDefault();
            showView('bookings-view');
        });
    }
}

// --- Direct Execution on Script Load ---
// We wait for DOMContentLoaded to ensure all elements referenced by ID exist before calling setup functions.
document.addEventListener('DOMContentLoaded', () => {
    initializeMap(); 
    setupSidebarToggle();
    setupNavigation();
    // Ensure we start on the map view and style is correct
    showView('map-view');
});

//mapbox public access token
const MAPBOX_ACCESS_TOKEN = 'pk.eyJ1IjoibWFsaW1iYSIsImEiOiJjbTgyeWYwMTEwaWFmMmtxdml6endnZjFmIn0.TF-YvTG_Xa-Nx301EfmZTw';

//coordinates for Ashesi University (Long, Lat): -0.21972, 5.75972
const ASHESI_CENTER = [-0.21972, 5.75972];
const INITIAL_ZOOM = 17;

//global Mapbox instance holder (used for resizing) - exposed globally
window.mapInstance = null;

function showView(viewId) {
    const mapView = document.getElementById('map-view');
    const bookingsView = document.getElementById('bookings-view');
    const navMap = document.getElementById('nav-map');
    const navBookings = document.getElementById('nav-bookings');

    //hide all views
    if (mapView) mapView.classList.add('hidden');
    if (bookingsView) bookingsView.classList.add('hidden');

    //show requested view
    const targetView = document.getElementById(viewId);
    if (targetView) {
        targetView.classList.remove('hidden');

        //special case: If map view is shown, ensure Mapbox recalculates its size
        if (viewId === 'map-view' && mapInstance) {
            // A small delay is necessary to ensure the CSS reflow completes before Mapbox attempts to resize.
            setTimeout(() => {
                mapInstance.resize();
            }, 50);
        }
    }

    //update active link styling
    if (navMap) navMap.classList.remove('bg-white/20');
    if (navBookings) navBookings.classList.remove('bg-white/20');

    if (viewId === 'map-view' && navMap) {
        navMap.classList.add('bg-white/20');
    } else if (viewId === 'bookings-view' && navBookings) {
        navBookings.classList.add('bg-white/20');
    }
}


/**initializes the Mapbox map and stores the instance globally.*/
function initializeMap() {
    //we check for the global Mapbox object which should be loaded via CDN script in the base html file
    if (typeof mapboxgl === 'undefined' || typeof mapboxgl.Map === 'undefined') {
        console.error("Mapbox GL JS library not loaded. Cannot initialize map.");
        return;
    }

    mapboxgl.accessToken = MAPBOX_ACCESS_TOKEN;

    const map = new mapboxgl.Map({
        container: 'ashesi-map', //HTML element ID for the map
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
    //store the map instance globally for the resize function in showView
    mapInstance = map;
    window.mapInstance = map; // Also expose it globally
    //add navigation control (zoom and compass)
    map.addControl(new mapboxgl.NavigationControl(), 'top-right');
    //add a marker exactly at the center of the campus (Main Administration area)
    new mapboxgl.Marker({ color: '#800020' })
        .setLngLat(ASHESI_CENTER)
        .setPopup(new mapboxgl.Popup().setHTML("<h4>Ashesi University</h4><p>1 University Avenue, Berekuso</p>"))
        .addTo(map);
    //handle map resize on window resize to ensure responsiveness
    window.addEventListener('resize', () => {
        map.resize();
    });

    //on load, ensure the map flies to the center with a nice transition
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

/*sets up the event listener for the mobile sidebar toggle button.*/
function setupSidebarToggle() {
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    if (mobileMenuButton) {
        mobileMenuButton.addEventListener('click', function () {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        });
    }
}

/*sets up listeners for sidebar navigation links to handle view switching.*/
function setupNavigation() {
    const mapLink = document.getElementById('nav-map');
    const bookingsLink = document.getElementById('nav-bookings');
    const bookingsView = document.getElementById('bookings-view');

    if (mapLink) {
        mapLink.addEventListener('click', function (e) {
            e.preventDefault();
            showView('map-view');
        });
    }

    // Only prevent default on bookings link if bookings-view exists on this page
    if (bookingsLink && bookingsView) {
        bookingsLink.addEventListener('click', function (e) {
            e.preventDefault();
            showView('bookings-view');
        });
    }
}


//wait for DOMContentLoaded to ensure all elements referenced by ID exist before calling setup functions.
document.addEventListener('DOMContentLoaded', () => {
    // Check if this page uses the standard resourceLocator view structure
    const mapView = document.getElementById('map-view');
    const bookingsView = document.getElementById('bookings-view');
    const homeView = document.getElementById('home-view');
    
    // Only run map.js initialization if we have the resourceLocator structure
    // (map-view and/or bookings-view, but NOT home-view)
    const isResourceLocatorPage = (mapView || bookingsView) && !homeView;
    
    if (isResourceLocatorPage) {
        setupSidebarToggle();
        setupNavigation();
    } else {
        // Still setup sidebar toggle for mobile on all pages
        setupSidebarToggle();
    }
    
    // Always initialize the map if the container exists
    const mapContainer = document.getElementById('ashesi-map');
    if (mapContainer) {
        initializeMap();
    }
});

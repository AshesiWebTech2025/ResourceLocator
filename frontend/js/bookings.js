

const BOOKING_API = '../backend/add_booking.php';
const FETCH_API = '../backend/fetch_bookings.php';

// Helper to format date and time strings for display
function formatDateTime(dateTimeString) {
    const date = new Date(dateTimeString);
    const options = {
        month: 'short', day: 'numeric', year: 'numeric',
        hour: '2-digit', minute: '2-digit', hour12: false
    };
    return date.toLocaleString('en-US', options);
}

// Function to render a single booking card
function renderBookingCard(booking) {
    const startTimeStr = formatDateTime(booking.start_time);
    const endTimeStr = formatDateTime(booking.end_time);

    // Determine card styling based on status
    const status = booking.status || 'Confirmed';
    let statusClass = 'bg-ashesi-light text-ashesi-maroon';
    if (status === 'Cancelled') {
        statusClass = 'bg-red-100 text-red-600';
    } else if (new Date(booking.end_time) < new Date()) {
        statusClass = 'bg-gray-100 text-gray-500';
    }

    // Determine if it's past or upcoming
    const isPast = new Date(booking.end_time) < new Date();
    const actionButton = isPast 
        ? `<button disabled class="text-gray-400 border border-gray-200 rounded-full px-3 py-1 text-sm cursor-not-allowed">Ended</button>`
        : `<button data-id="${booking.id}" class="cancel-btn text-red-600 border border-red-200 rounded-full px-3 py-1 text-sm hover:bg-red-50 transition">Cancel</button>`;


    return `
        <div class="booking-card bg-white p-6 rounded-xl shadow border border-gray-100 flex items-start justify-between data-id="${booking.id}">
            <div>
                <h3 class="text-lg font-semibold text-gray-800">${booking.resource_name}</h3>
                <p class="text-sm text-gray-600 mt-1">
                    From: <span class="font-medium text-gray-700">${startTimeStr}</span> 
                    To: <span class="font-medium text-gray-700">${endTimeStr}</span>
                </p>
                <p class="text-sm text-gray-500 mt-2">
                    Status: <span class="inline-block ${statusClass} px-2 py-1 rounded-full text-xs font-medium">${status}</span>
                </p>
            </div>
            <div class="flex flex-col items-end gap-2">
                ${actionButton}
            </div>
        </div>
    `;
}

// Function to fetch and render all bookings
async function fetchAndRenderBookings() {
    const listContainer = document.getElementById('bookings-list');
    const emptyState = document.getElementById('empty-state');
    const totalCountSpan = document.getElementById('total-bookings-count');
    const upcomingCountSpan = document.getElementById('upcoming-bookings-count');
    const pastCountSpan = document.getElementById('past-bookings-count');
    
    // Clear previous content and show a loader
    listContainer.innerHTML = '<div class="text-center p-8 text-gray-500">Loading your bookings...</div>';
    emptyState.classList.add('hidden');

    try {
        // REQUESTING DATA: Rely solely on the PHP session for user authentication/ID.
        const response = await fetch(FETCH_API); 
        const data = await response.json();

        if (data.success && data.bookings.length > 0) {
            listContainer.innerHTML = data.bookings.map(renderBookingCard).join('');
            emptyState.classList.add('hidden');
            
            // Calculate summaries
            const now = new Date();
            const upcomingCount = data.bookings.filter(b => new Date(b.start_time) > now).length;
            const pastCount = data.bookings.length - upcomingCount;

            totalCountSpan.textContent = data.bookings.length;
            upcomingCountSpan.textContent = upcomingCount;
            pastCountSpan.textContent = pastCount;

        } else {
            listContainer.innerHTML = '';
            emptyState.classList.remove('hidden');
            totalCountSpan.textContent = 0;
            upcomingCountSpan.textContent = 0;
            pastCountSpan.textContent = 0;
            
            if (!data.success && data.message.includes('authenticated')) {
                 listContainer.innerHTML = '<div class="text-center p-8 text-red-500">Please sign in to view your bookings.</div>';
            }
        }

    } catch (error) {
        console.error('Error fetching bookings:', error);
        listContainer.innerHTML = '<div class="text-center p-8 text-red-500">Failed to load bookings. Please try again.</div>';
    }
}

// Function to handle booking submission
async function submitNewBooking(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    // SUBMITTING DATA: Removed client-side user_id append. PHP session will be used.

    const submitBtn = document.getElementById('book-submit-btn');
    const messageBox = document.getElementById('booking-message');

    submitBtn.textContent = 'Booking...';
    submitBtn.disabled = true;
    messageBox.textContent = '';
    messageBox.className = 'text-center text-sm mt-3';

    try {
        const response = await fetch(BOOKING_API, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();

        if (data.success) {
            messageBox.textContent = data.message;
            messageBox.classList.add('text-green-600');
            form.reset();
            // Refresh the bookings list on success
            fetchAndRenderBookings(); 
            // Close modal after a delay
            setTimeout(() => {
                document.getElementById('booking-modal').classList.add('hidden');
            }, 1500);

        } else {
            messageBox.textContent = data.message;
            messageBox.classList.add('text-red-600');
        }

    } catch (error) {
        console.error('Submission error:', error);
        messageBox.textContent = 'An unexpected error occurred during submission.';
        messageBox.classList.add('text-red-600');
    } finally {
        submitBtn.textContent = 'Confirm Booking';
        submitBtn.disabled = false;
    }
}


// Event listeners setup
document.addEventListener('DOMContentLoaded', function() {
    
    // --- Booking Modal Setup ---
    const bookForm = document.getElementById('new-booking-form');
    if (bookForm) {
        bookForm.addEventListener('submit', submitNewBooking);
    }
    
    const openModalBtn = document.getElementById('open-booking-modal');
    const closeModalBtn = document.getElementById('close-booking-modal');
    const modal = document.getElementById('booking-modal');

    if (openModalBtn) {
        openModalBtn.addEventListener('click', () => {
            modal.classList.remove('hidden');
        });
    }

    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', () => {
            modal.classList.add('hidden');
            document.getElementById('new-booking-form').reset();
            document.getElementById('booking-message').textContent = '';
        });
    }
    
    // Fetch bookings on page load
    fetchAndRenderBookings();
});
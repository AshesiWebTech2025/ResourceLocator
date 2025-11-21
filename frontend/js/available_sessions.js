/**
 * available_sessions.js
 * this connects frontend to backend: ../backend/available_sessions.php
 */

let currentResourceId = null;
let currentSlots = [];

document.addEventListener('DOMContentLoaded', () => {
    bindUi();
    loadResources();
});

function bindUi() {
    document.getElementById('refreshResources')?.addEventListener('click', loadResources);
    document.getElementById('closeModal')?.addEventListener('click', closeModal);
    document.getElementById('cancelTimes')?.addEventListener('click', closeModal);
    document.getElementById('addSlot')?.addEventListener('click', addSlot);
    document.getElementById('saveTimes')?.addEventListener('click', saveAllSlots);
    document.getElementById('closeViewModal')?.addEventListener('click', closeViewModal);
    document.getElementById('closeViewModalFooter')?.addEventListener('click', closeViewModal);

    // close edit modal on backdrop click
    document.getElementById('sessionModal')?.addEventListener('click', function (e) {
        if (e.target.id === 'sessionModal') closeModal();
    });
}

// --- Resources / list ---
async function loadResources() {
    try {
        // compute robust backend path (works when page is served from /acrl/frontend/...)
        const backendBase = getBackendBase();
        // BACKEND: GET <backendBase>?action=getResources
        const res = await fetch(`${backendBase}?action=getResources`);
        const json = await res.json();
        if (json.success && Array.isArray(json.data)) {
            displayResourceList(json.data);
        } else {
            displayResourceList([]);
            console.error('Failed to load resources', json.message);
        }
    } catch (err) {
        console.error('Error loading resources', err);
        displayResourceList([]);
    }
}

function displayResourceList(resources) {
    const hotspotList = document.getElementById('hotspot-list');
    hotspotList.innerHTML = '';
    if (!resources || resources.length === 0) {
        hotspotList.innerHTML = '<p class="text-gray-500">No resources found</p>';
        return;
    }

    resources.forEach(resource => {
        const div = document.createElement('div');
        div.className = 'p-3 border border-transparent rounded-lg hover:bg-gray-50 flex items-center justify-between';

        div.innerHTML = `
            <div>
                <div class="font-medium text-gray-800">${escapeHtml(resource.name)}</div>
                <div class="text-xs text-gray-500">${escapeHtml(resource.type_name || '')}</div>
            </div>
        `;

        const actions = document.createElement('div');
        actions.className = 'flex gap-2 items-center';

        const viewBtn = document.createElement('button');
        viewBtn.className = 'text-xs text-ashesi-maroon px-2 py-1 rounded hover:bg-white/10';
        viewBtn.textContent = 'View Slots';
        viewBtn.addEventListener('click', (e) => { e.stopPropagation(); openViewModal(resource); });

        const editBtn = document.createElement('button');
        editBtn.className = 'text-xs text-ashesi-maroon px-2 py-1 rounded hover:bg-white/10';
        editBtn.textContent = 'Edit';
        editBtn.addEventListener('click', (e) => { e.stopPropagation(); openResourceModal(resource); });

        actions.appendChild(viewBtn);
        actions.appendChild(editBtn);
        div.appendChild(actions);

        div.addEventListener('click', () => openResourceModal(resource));
        hotspotList.appendChild(div);
    });
}

// --- View-only modal ---
async function openViewModal(resource) {
    const viewTitle = document.getElementById('viewModalTitle');
    const viewSlotList = document.getElementById('viewSlotList');
    viewTitle.textContent = `Available Slots — ${resource.name}`;
    viewSlotList.innerHTML = '<div class="text-sm text-gray-500">Loading...</div>';

    try {
        // BACKEND: GET ../backend/available_sessions.php?action=getAvailabilitySlots&resource_id=ID
        // BACKEND: GET <backendBase>?action=getAvailabilitySlots&resource_id=ID
        const backendBase = getBackendBase();
        const res = await fetch(`${backendBase}?action=getAvailabilitySlots&resource_id=${resource.resource_id ?? resource.id}`);
        const json = await res.json();
        if (json.success && Array.isArray(json.data) && json.data.length) {
            viewSlotList.innerHTML = '';
            json.data.forEach(s => {
                const el = document.createElement('div');
                el.className = 'p-2 bg-gray-50 rounded mb-2';
                el.textContent = `${capitalize(s.day_of_week)} — ${s.start_time} to ${s.end_time}`;
                viewSlotList.appendChild(el);
            });
        } else {
            viewSlotList.innerHTML = '<div class="text-sm text-gray-500">No slots available.</div>';
        }
    } catch (err) {
        console.error('Failed to load slots', err);
        viewSlotList.innerHTML = '<div class="text-sm text-red-500">Network error</div>';
    } finally {
        $('#viewSlotsModal').removeClass('hidden').hide().fadeIn(150);
    }
}

function closeViewModal() { $('#viewSlotsModal').fadeOut(150, function () { $(this).addClass('hidden'); }); }

// --- Edit modal (open, load slots, add/remove/save) ---
async function openResourceModal(resource) {
    currentResourceId = resource.resource_id ?? resource.id;
    document.getElementById('modalTitle').textContent = `Edit Times — ${resource.name}`;
    document.getElementById('resourceName').textContent = resource.name;
    await loadResourceSlots(currentResourceId);
    $('#sessionModal').removeClass('hidden').hide().fadeIn(150);
}

async function loadResourceSlots(resourceId) {
    try {
        // BACKEND: GET ../backend/available_sessions.php?action=getAvailabilitySlots&resource_id=ID
        const backendBase = getBackendBase();
        // BACKEND: GET <backendBase>?action=getAvailabilitySlots&resource_id=ID
        const res = await fetch(`${backendBase}?action=getAvailabilitySlots&resource_id=${resourceId}`);
        const json = await res.json();
        if (json.success && Array.isArray(json.data)) {
            currentSlots = json.data.map(s => ({ day_of_week: s.day_of_week, start_time: s.start_time, end_time: s.end_time }));
        } else {
            currentSlots = [];
        }
    } catch (err) {
        console.error('Failed to load slots', err);
        currentSlots = [];
    }
    displaySlots(currentSlots);
}

function displaySlots(slots) {
    const slotList = document.getElementById('slotList');
    slotList.innerHTML = '';
    if (!slots || slots.length === 0) {
        slotList.innerHTML = '<li class="text-sm text-gray-500">No slots yet.</li>';
        return;
    }
    slots.forEach((slot, idx) => {
        const li = document.createElement('li');
        li.className = 'flex items-center justify-between bg-gray-50 p-2 rounded';
        li.innerHTML = `<div class="text-sm">${capitalize(slot.day_of_week || slot.day)} — ${slot.start_time || slot.start} to ${slot.end_time || slot.end}</div><div class="flex gap-2"><button class="text-sm text-ashesi-maroon remove-slot-btn" data-idx="${idx}">Remove</button></div>`;
        li.querySelector('.remove-slot-btn').addEventListener('click', () => { removeSlot(idx); });
        slotList.appendChild(li);
    });
}

function addSlot() {
    if (!currentResourceId) return alert('Select a resource first');
    const day = document.getElementById('daySelect').value;
    const start = document.getElementById('startTime').value;
    const end = document.getElementById('endTime').value;
    if (!start || !end) return alert('Please enter start and end times');
    if (start >= end) return alert('End time must be after start time');

    // simple overlap check
    const overlap = currentSlots.some(s => s.day_of_week === day && ((start >= s.start_time && start < s.end_time) || (end > s.start_time && end <= s.end_time) || (start <= s.start_time && end >= s.end_time)));
    if (overlap) return alert('Time slot overlaps with an existing slot');

    currentSlots.push({ day_of_week: day, start_time: start, end_time: end });
    // sort
    const order = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    currentSlots.sort((a, b) => order.indexOf(a.day_of_week) - order.indexOf(b.day_of_week) || a.start_time.localeCompare(b.start_time));
    displaySlots(currentSlots);
}

function removeSlot(index) {
    currentSlots.splice(index, 1);
    displaySlots(currentSlots);
}

async function saveAllSlots() {
    if (!currentResourceId) return alert('No resource selected');
    try {
        // BACKEND: POST ../backend/available_sessions.php?action=updateSlots with resource_id & slots
        const backendBase = getBackendBase();
        const body = new URLSearchParams({ resource_id: currentResourceId, slots: JSON.stringify(currentSlots) });
        const res = await fetch(`${backendBase}?action=updateSlots`, { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body });
        const json = await res.json();
        if (json.success) {
            alert('Time slots saved successfully!');
            closeModal();
            loadResources();
        } else {
            alert('Failed to save slots: ' + (json.message || 'Unknown'));
        }
    } catch (err) {
        console.error('Error saving slots', err);
        alert('Error saving time slots');
    }
}

function closeModal() { $('#sessionModal').fadeOut(150, function () { $(this).addClass('hidden'); }); currentResourceId = null; currentSlots = []; }

function capitalize(s) { if (!s) return ''; return s.charAt(0).toUpperCase() + s.slice(1); }

function escapeHtml(str) { if (!str) return ''; return String(str).replace(/[&<>"'`]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;', '`': '&#96;' }[c])); }

// expose removeSlot for legacy onclick hooks (if any)
window.removeSlot = removeSlot;

// Helper: compute backend base path from current location
function getBackendBase() {
    try {
        const path = window.location.pathname; // e.g. /acrl/frontend/available_sessions.html
        const marker = '/frontend/';
        const idx = path.indexOf(marker);
        if (idx !== -1) {
            const base = path.substring(0, idx); // '/acrl'
            return `${window.location.origin}${base}/backend/available_sessions.php`;
        }
    } catch (e) {
        // fallthrough
    }
    // fallback to relative path
    return '../backend/available_sessions.php';
}
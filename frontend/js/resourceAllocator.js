$(document).ready(function () {
    // --- Selectors ---
    
    // Main Panel Selectors
    const $panel = $("#allocatorSection");
    const $map = $("#ashesi-map");
    const $allocatorForm = $("#allocatorForm");
    const $typeSelect = $("#type");

    // Modal Selectors (Ensure $modal, $addTypeLink, $addTypeForm are accessible globally in this function)
    const $modal = $("#typeModal");
    const $addTypeLink = $("#addTypeLink");
    const $addTypeForm = $("#addTypeForm");
    
    // --- Helper Functions ---

    // Function to open the main allocation panel
    function openPanel() {
        $panel.removeClass("hidden").addClass("open");
    }

    // Function to close the main allocation panel
    function closePanel() {
        if ($panel.hasClass("open")) {
            $panel.removeClass("open");
            setTimeout(() => $panel.addClass("hidden"), 300); 
        }
    }
    
    // Function to open the resource type modal
    function openModal() {
        $modal.removeClass('hidden'); 
    }

    // Function to close the resource type modal
    function closeModal() {
        $modal.addClass('hidden'); 
        $addTypeForm[0].reset(); 
    }

    

    // A. Open panel when clicking the map
    $map.on("click", function (e) {
        e.stopPropagation(); 
        openPanel();
    });

    // B. Close button inside the panel
    $panel.on("click", ".close-btn", function (e) {
        e.preventDefault();
        e.stopPropagation();
        closePanel();
    });

    // C. Prevent clicks inside the panel from closing it (Good for nested forms)
    $panel.on("click", function (e) {
        e.stopPropagation();
    });

    // D. Handle form submission (Resource allocation)
    $allocatorForm.on("submit", function (e) {
        e.preventDefault();

        const name = $("#name").val().trim();
        const type = $typeSelect.val();
        const capacity = $("#capacity").val().trim(); 
        const description = $("#description").val().trim();

        if (!name || !type || !description) {
            alert("Please fill in the Name, Type, and Description fields.");
            return;
        }

        alert(`Location saved (frontend only): ${name} (${type})`);

        this.reset();
        closePanel();
    });

    
    // A. Open modal when 'Add a new resource type' link is clicked
    $addTypeLink.on('click', function (e) {
        e.preventDefault();
        openModal();
    });

    // B. Close modal via the 'x' button (Using delegated event for robustness)
    $modal.on('click', '.close-modal', function(e) {
        e.preventDefault(); 
        closeModal();
    });

    // C. Close modal when clicking on the dark backdrop
    $modal.on('click', function(event) {
        if ($(event.target).is($modal)) {
            closeModal();
        }
    });

    // D. Handle 'Add New Resource Type' form submission (AJAX)
    $addTypeForm.on('submit', function (e) {
        e.preventDefault();

        const typeName = $("#type_name").val().trim();
        if (!typeName) {
            alert("Please enter a Type Name.");
            return;
        }

        // Placeholder for AJAX submission to addType.php
        $.ajax({
            url: 'backend/addType.php', 
            method: 'POST',
            data: { type_name: typeName },
            success: function(response) {
                // Dynamic update of the main form's select element
                const newOption = `<option value="${typeName}" selected>${typeName}</option>`;
                $typeSelect.append(newOption);
                
                alert(`Resource Type Added: ${typeName}`); 
                closeModal();
            },
            error: function(xhr, status, error) {
                alert("An error occurred while adding the resource type.");
                console.error("AJAX Error:", status, error);
            }
        });
    });

    
    
    // A. Clicking outside closes the panel (Global Handler)
    $(document).on("click", function (e) {
        if ($(e.target).closest("#typeModal").length) {
            // Click occurred inside the modal or on the modal's children, so do nothing.
            return;
        }
        // If click is outside the panel, map, OR modal, close the panel.
        if (!$(e.target).closest("#allocatorSection, #ashesi-map").length) {
            closePanel();
        }
    });

    // B. Handle sidebar (Moved from the old code)
    document.getElementById('mobile-menu-button').addEventListener('click', function () {
        const sidebar = document.getElementById('sidebar');
        // const overlay = document.getElementById('sidebar-overlay'); // removed overlay reference for simplicity
        sidebar.classList.toggle('-translate-x-full');
        // overlay.classList.toggle('hidden');
    });
});
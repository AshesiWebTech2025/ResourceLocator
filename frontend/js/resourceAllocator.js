$(document).ready(function () {
    const $panel = $("#allocatorSection");
    const $map = $("#ashesi-map");
    const $allocatorForm = $("#allocatorForm");
    const $typeSelect = $("#type");

    const $modal = $("#typeModal");
    const $addTypeLink = $("#addTypeLink");
    const $addTypeForm = $("#addTypeForm");
    

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

    

    // Open panel when clicking the map
    $map.on("click", function (e) {
        e.stopPropagation(); 
        openPanel();
    });

    //Close button inside the panel
    $panel.on("click", ".close-btn", function (e) {
        e.preventDefault();
        e.stopPropagation();
        closePanel();
    });

    //Prevent clicks inside the panel from closing it (Good for nested forms)
    $panel.on("click", function (e) {
        e.stopPropagation();
    });


    // Open modal when 'Add a new resource type' link is clicked
    $addTypeLink.on('click', function (e) {
        e.preventDefault();
        openModal();
    });

    //Close modal via the 'x' button (Using delegated event for robustness)
    $modal.on('click', '.close-modal', function(e) {
        e.preventDefault(); 
        closeModal();
    });

    //Close modal when clicking on the dark backdrop
    $modal.on('click', function(event) {
        if ($(event.target).is($modal)) {
            closeModal();
        }
    });

    // Handle 'Add New Resource Type' form submission (AJAX)
    $addTypeForm.on('submit', function (e) {
        e.preventDefault();

        const typeName = $("#type_name").val().trim();
        if (!typeName) {
            alert("Please enter a Type Name.");
            return;
        }

        $.ajax({
            url: '../backend/addType.php', 
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

    
    
    //Clicking outside closes the panel (Global Handler)
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

    //Handle sidebar
    document.getElementById('mobile-menu-button').addEventListener('click', function () {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('-translate-x-full');
    });
});

$(document).ready(function() {
    const $form = $("#allocatorForm");
    const $typeSelect = $("#type");

    // Load resource types from the backend
    function loadResourceTypes() {
        $.get("../backend/getTypes.php", function(data) {
            $typeSelect.empty();
            $typeSelect.append('<option value="" disabled selected>Select the type</option>');
            data.forEach(type => {
                $typeSelect.append(`<option value="${type.type_name}">${type.type_name}</option>`);
            });
        }, "json");
    }

    loadResourceTypes();

    // Handle map clicks for coordinates
    if (window.mapInstance) {
        mapInstance.on("click", function (e) {
        let lng = e.lngLat.lng;
        let lat = e.lngLat.lat;

        // Round to 5 decimals
        const nearestLat = Number(lat.toFixed(5));
        const nearestLng = Number(lng.toFixed(5));

        // Remove previous marker
        if (window.resourceMarker) window.resourceMarker.remove();

        window.resourceMarker = new mapboxgl.Marker({ color: "#FF0000" })
            .setLngLat([nearestLng, nearestLat])
            .addTo(mapInstance);

        // Fill the form dynamically with clicked coordinates
        $("#latitude").val(nearestLat);
        $("#longitude").val(nearestLng);
    });

    }

    // Submit form via AJAX
    $form.on("submit", function(e) {
        e.preventDefault();
        const name = $("#name").val().trim();
        const type = $typeSelect.val();
        const capacity = $("#capacity").val();
        const description = $("#description").val().trim();
        const latitude = $("#latitude").val();
        const longitude = $("#longitude").val();

        if (!name || !type || !description || !latitude || !longitude) {
            alert("Please fill all fields and click on the map to select a location.");
            return;
        }

        $.post("../backend/resourceAllocator.php", {
            name, type, capacity, description, latitude, longitude
        }, function(response) {
            alert(response);
            $form[0].reset();
            if (window.resourceMarker) window.resourceMarker.remove();
        }).fail(function(xhr) {
            alert("Error: " + xhr.responseText);
        });
    });
});
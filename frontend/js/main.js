//kharis code
$(document).ready(function () {
            const $panel = $("#allocatorSection");

            // Open panel when clicking the map
            $("#ashesi-map").on("click", function (e) {
                e.stopPropagation(); // prevent document click from firing
                $panel.removeClass("hidden").addClass("open");
            });

            // Close button inside the panel
            $panel.on("click", ".close-btn", function (e) {
                e.preventDefault();
                e.stopPropagation();
                $panel.removeClass("open");
                setTimeout(() => $panel.addClass("hidden"), 300);
            });

            // Prevent clicks inside the panel from closing it
            $panel.on("click", function (e) {
                e.stopPropagation();
            });

            // Clicking outside closes the panel
            $(document).on("click", function (e) {
                if (!$(e.target).closest("#allocatorSection, #ashesi-map").length) {
                    if ($panel.hasClass("open")) {
                        $panel.removeClass("open");
                        setTimeout(() => $panel.addClass("hidden"), 300);
                    }
                }
            });

            // Handle form submission
            $("#allocatorForm").on("submit", function (e) {
                e.preventDefault();

                const name = $("#name").val().trim();
                const type = $("#type").val().trim();
                const capacity = $("#capacity").val().trim(); // optional
                const description = $("#description").val().trim();

                // Validate required fields
                if (!name || !type || !description) {
                    alert("Please fill in the Name, Type, and Description fields.");
                    return;
                }

                alert(
                    `Location saved (frontend only)`
                );

                // Reset and close form
                this.reset();
                $panel.removeClass("open");
                setTimeout(() => $panel.addClass("hidden"), 300);
            });
            //handle sidebar
             document.getElementById('mobile-menu-button').addEventListener('click', function () {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        });
        });


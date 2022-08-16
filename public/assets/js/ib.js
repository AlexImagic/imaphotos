(function() {
    'use strict';
    $(document).ready(function() {
        // Copy image URL
        $("#copy").on("click", function() {
            var copyText = document.getElementById("sharelink");
            copyText.select();
            copyText.setSelectionRange(0, 99999);
            document.execCommand("copy");
        });
        // Show share buttons on desktop
        $("#share").on("click", function() { $(".share-buttons").toggle(); });
        // Show share buttons on mobile
        $("#share-mobile").on("click", function() { $(".share-buttons").toggle(); });

    });
})(jQuery);
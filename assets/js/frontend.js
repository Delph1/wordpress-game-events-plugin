/**
 * Frontend accordion functionality for game events
 */

jQuery(document).ready(function ($) {
    // Accordion toggle functionality
    $(document).on("click", ".hge-events-accordion-header", function (e) {
        e.preventDefault();

        const $header = $(this);
        const $content = $header.next(".hge-events-accordion-content");
        const isActive = $header.hasClass("active");

        if (isActive) {
            // Close accordion
            $header.removeClass("active");
            $content.removeClass("active");
        } else {
            // Open accordion
            $header.addClass("active");
            $content.addClass("active");
        }
    });
});

$(document).ready(function() {
    // Get current page URL
    var currentPageUrl = window.location.pathname.split('/').pop();
    if (currentPageUrl === "" || currentPageUrl === "index.php") { // Handle index page
        $('#main-menu a[href$="index.php"]').addClass('active-menu');
    } else {
        // Find the menu link that matches the current page and add 'active-menu' class
        $('#main-menu a.menu-link').each(function() {
            var linkHref = $(this).attr('href').split('/').pop();
            if (linkHref === currentPageUrl) {
                $(this).addClass('active-menu');
            }
        });
    }
});
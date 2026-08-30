document.addEventListener("DOMContentLoaded", function () {
    const links = document.querySelectorAll(".sidebar a");
    links.forEach(function (link) {
        link.addEventListener("click", function () {
            // Navigation is handled by normal links.
        });
    });
});
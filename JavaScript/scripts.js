$(document).ready(function () {
    console.log("jQuery ready");

    // Optional: auto-hide alerts after 5s
    setTimeout(function () {
        $(".alert").fadeOut();
    }, 5000);
});
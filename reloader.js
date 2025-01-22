setInterval(function (e) {
    document.location.reload()
}, 600000);

window.addEventListener("load", function (e) {
    var scrollpos = sessionStorage.getItem('scrollpos');
    if (scrollpos) {
        window.scrollTo(0, scrollpos);
        sessionStorage.removeItem('scrollpos');
    }
});

window.addEventListener("beforeunload", function (e) {
    sessionStorage.setItem('scrollpos', window.scrollY);
});
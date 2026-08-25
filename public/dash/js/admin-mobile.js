(function () {
    function isMobile() {
        return window.innerWidth <= 991;
    }

    function isNavOpen() {
        return document.documentElement.classList.contains("nav_open");
    }

    function syncBodyLock() {
        if (!document.body) {
            return;
        }
        if (!isMobile() || !isNavOpen()) {
            document.body.style.overflow = "";
            return;
        }
        document.body.style.overflow = "hidden";
    }

    function closeNav() {
        document.documentElement.classList.remove("topbar_open");
        if (isNavOpen()) {
            var toggle = document.querySelector(".sidenav-toggler");
            if (toggle) {
                toggle.click();
            } else {
                document.documentElement.classList.remove("nav_open");
            }
        }
        syncBodyLock();
    }

    function neutralizeMobileScrollTraps() {
        if (!isMobile()) {
            return;
        }

        document.querySelectorAll(".card.full-height, .card-stats").forEach(function (el) {
            el.style.height = "auto";
            el.style.maxHeight = "none";
            el.style.overflow = "visible";
        });

        document.querySelectorAll(".row-card-no-pd .scroll-element, .card-stats .scroll-element, .page-inner .card .scroll-element").forEach(function (el) {
            el.style.display = "none";
        });

        document.querySelectorAll(".row-card-no-pd .scroll-content, .card-stats .scroll-content, .page-inner .card .scroll-content").forEach(function (el) {
            el.style.height = "auto";
            el.style.maxHeight = "none";
            el.style.overflow = "visible";
        });
    }

    function bindNav() {
        var dim = document.getElementById("admin-nav-overlay");
        if (dim && !dim.getAttribute("data-admin-mobile-bound")) {
            dim.setAttribute("data-admin-mobile-bound", "1");
            dim.addEventListener("click", function () {
                closeNav();
            });
        }

        document.querySelectorAll(".sidebar .nav-primary a[href]").forEach(function (link) {
            if (link.getAttribute("data-admin-mobile-bound")) {
                return;
            }
            link.setAttribute("data-admin-mobile-bound", "1");
            link.addEventListener("click", function () {
                if (!isMobile() || link.getAttribute("data-toggle") === "collapse") {
                    return;
                }
                closeNav();
            });
        });

        document.querySelectorAll(".sidenav-toggler").forEach(function (btn) {
            if (btn.getAttribute("data-admin-mobile-bound")) {
                return;
            }
            btn.setAttribute("data-admin-mobile-bound", "1");
            btn.addEventListener("click", function () {
                setTimeout(syncBodyLock, 0);
            });
        });
    }

    function init() {
        bindNav();
        neutralizeMobileScrollTraps();
        syncBodyLock();
    }

    document.addEventListener("DOMContentLoaded", function () {
        init();

        window.addEventListener("resize", function () {
            if (!isMobile() && isNavOpen()) {
                closeNav();
            }
            neutralizeMobileScrollTraps();
            syncBodyLock();
        });
    });

    document.addEventListener("livewire:load", init);
    document.addEventListener("livewire:update", neutralizeMobileScrollTraps);
})();

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

    document.addEventListener("DOMContentLoaded", function () {
        var dim = document.getElementById("admin-nav-overlay");
        if (dim) {
            dim.addEventListener("click", function () {
                closeNav();
            });
        }

        document.querySelectorAll(".sidebar .nav-primary a[href]").forEach(function (link) {
            link.addEventListener("click", function () {
                if (!isMobile() || link.getAttribute("data-toggle") === "collapse") {
                    return;
                }
                closeNav();
            });
        });

        document.querySelectorAll(".sidenav-toggler").forEach(function (btn) {
            btn.addEventListener("click", function () {
                setTimeout(syncBodyLock, 0);
            });
        });

        window.addEventListener("resize", function () {
            if (!isMobile() && isNavOpen()) {
                closeNav();
            }
            syncBodyLock();
        });
    });
})();

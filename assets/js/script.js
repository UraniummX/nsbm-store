function simulatePurchase(productName, price) {
    alert("Order Confirmation\n-------------------\nProduct: " + productName + "\nTotal: LKR " + price + "\n\nThank you for supporting NSBM student businesses!");
}

document.addEventListener("DOMContentLoaded", function() {
    const overlay        = document.getElementById("drawer-overlay");
    const settingsDrawer = document.getElementById("settings-drawer");
    const settingsToggle = document.getElementById("settings-toggle");
    const settingsClose  = document.getElementById("settings-close");

    const sidebarLeft  = document.querySelector(".sidebar-left");
    const sidebarRight = document.querySelector(".sidebar-right");

    const filterToggle  = document.getElementById("mobile-filter-toggle");
    const sellersToggle = document.getElementById("mobile-sellers-toggle");
    const filterClose   = document.getElementById("sidebar-left-close");
    const sellersClose  = document.getElementById("sidebar-right-close");

    function closeAllDrawers() {
        if (settingsDrawer) settingsDrawer.classList.remove("active");
        if (sidebarLeft)    sidebarLeft.classList.remove("active");
        if (sidebarRight)   sidebarRight.classList.remove("active");
        if (overlay)        overlay.classList.remove("active");
        document.body.style.overflow = "";
    }

    function openDrawer(drawer) {
        closeAllDrawers();
        drawer.classList.add("active");
        if (overlay) overlay.classList.add("active");
        document.body.style.overflow = "hidden";
    }

    if (settingsToggle && settingsDrawer) settingsToggle.addEventListener("click", e => { e.preventDefault(); openDrawer(settingsDrawer); });
    if (settingsClose)                    settingsClose.addEventListener("click", closeAllDrawers);
    if (filterToggle && sidebarLeft)      filterToggle.addEventListener("click", () => openDrawer(sidebarLeft));
    if (filterClose)                      filterClose.addEventListener("click", closeAllDrawers);
    if (sellersToggle && sidebarRight)    sellersToggle.addEventListener("click", () => openDrawer(sidebarRight));
    if (sellersClose)                     sellersClose.addEventListener("click", closeAllDrawers);
    if (overlay)                          overlay.addEventListener("click", closeAllDrawers);

    const themeToggleBtn = document.getElementById("theme-toggle-btn");
    const themeIcon      = document.getElementById("theme-icon");
    const lightBtn       = document.getElementById("theme-light-btn");
    const darkBtn        = document.getElementById("theme-dark-btn");

    function applyTheme(theme) {
        document.documentElement.setAttribute("data-theme", theme);
        localStorage.setItem("theme", theme);

        if (themeIcon) {
            themeIcon.className = theme === "dark" ? "fa-regular fa-sun" : "fa-regular fa-moon";
        }

        if (theme === "dark") {
            if (darkBtn)  darkBtn.classList.add("active");
            if (lightBtn) lightBtn.classList.remove("active");
        } else {
            if (lightBtn) lightBtn.classList.add("active");
            if (darkBtn)  darkBtn.classList.remove("active");
        }
    }

    applyTheme(localStorage.getItem("theme") || "light");

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener("click", function() {
            const current = document.documentElement.getAttribute("data-theme") || "light";
            applyTheme(current === "dark" ? "light" : "dark");
        });
    }

    if (lightBtn) lightBtn.addEventListener("click", () => applyTheme("light"));
    if (darkBtn)  darkBtn.addEventListener("click",  () => applyTheme("dark"));
});

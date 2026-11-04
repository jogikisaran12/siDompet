/**
 * Dompetku - Mode Gelap / Terang
 *
 * Script ini dimuat di dalam <head> (blocking, tanpa defer) sehingga tema
 * yang tersimpan sudah diterapkan SEBELUM browser menggambar halaman.
 * Hasilnya: tidak ada efek berkedip (flash) saat halaman pertama dimuat.
 *
 * Tema default : light
 * Penyimpanan   : localStorage dengan key "dompetku-theme"
 */
(function () {
    "use strict";

    var STORAGE_KEY = "dompetku-theme";
    var listeners = [];

    function readStoredTheme() {
        try {
            return localStorage.getItem(STORAGE_KEY);
        } catch (e) {
            return null;
        }
    }

    function storeTheme(theme) {
        try {
            localStorage.setItem(STORAGE_KEY, theme);
        } catch (e) {
            // localStorage tidak tersedia (mis. mode privat) - tema tetap jalan di sesi ini
        }
    }

    function getTheme() {
        return document.documentElement.classList.contains("dark") ? "dark" : "light";
    }

    function applyTheme(theme) {
        var isDark = theme === "dark";

        document.documentElement.classList.toggle("dark", isDark);
        document.documentElement.setAttribute("data-theme", isDark ? "dark" : "light");

        var meta = document.querySelector('meta[name="theme-color"]');
        if (meta) {
            meta.setAttribute("content", isDark ? "#0F172A" : "#10B981");
        }

        for (var i = 0; i < listeners.length; i++) {
            try {
                listeners[i](isDark ? "dark" : "light");
            } catch (e) {
                // abaikan error listener agar tema tetap terpasang
            }
        }
    }

    // Terapkan tema tersimpan lebih dulu (anti berkedip).
    applyTheme(readStoredTheme() === "dark" ? "dark" : "light");

    function syncButtonLabel(button) {
        var isDark = getTheme() === "dark";
        button.setAttribute("aria-label", isDark ? "Aktifkan mode terang" : "Aktifkan mode gelap");
        button.setAttribute("title", isDark ? "Aktifkan Mode Terang" : "Aktifkan Mode Gelap");
    }

    function bindToggles() {
        var toggles = document.querySelectorAll("#themeToggle, [data-theme-toggle]");

        Array.prototype.forEach.call(toggles, function (button) {
            if (button.getAttribute("data-theme-bound") === "1") {
                return;
            }
            button.setAttribute("data-theme-bound", "1");
            syncButtonLabel(button);

            button.addEventListener("click", function () {
                var next = getTheme() === "dark" ? "light" : "dark";
                applyTheme(next);
                storeTheme(next);
                syncButtonLabel(button);
            });
        });
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", bindToggles);
    } else {
        bindToggles();
    }

    // API publik untuk view (mis. grafik dashboard yang perlu menyesuaikan warna)
    window.DompetkuTheme = {
        get: getTheme,
        set: function (theme) {
            var next = theme === "dark" ? "dark" : "light";
            applyTheme(next);
            storeTheme(next);
        },
        toggle: function () {
            var next = getTheme() === "dark" ? "light" : "dark";
            applyTheme(next);
            storeTheme(next);
        },
        onChange: function (callback) {
            if (typeof callback === "function") {
                listeners.push(callback);
            }
        }
    };
})();

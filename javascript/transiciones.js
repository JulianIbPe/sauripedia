// ============================================================
// transiciones.js
// Transición entre los apartados del nav: el contenido se desliza
//   - hacia la izquierda si vas a un apartado que está MÁS A LA DERECHA
//     en el nav (la página nueva entra desde la derecha),
//   - hacia la derecha si vas a uno que está MÁS A LA IZQUIERDA.
// Se carga en el <head> para que la animación de entrada empiece
// antes de que se pinte la página (sin parpadeos).
// ============================================================
(function () {
    "use strict";

    var CLAVE = "sauripedia_direccion_nav";
    var DURACION_SALIDA = 260; // ms (igual que en navegacion.css)
    var raiz = document.documentElement;
    var sinMovimiento = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    var CLASES = ["nav_anim", "sale_izq", "sale_der", "entra_izq", "entra_der"];

    function limpiar() {
        CLASES.forEach(function (c) { raiz.classList.remove(c); });
    }

    // --- Entrada: ¿venimos de un clic en el nav? ---
    var direccion = null;
    try {
        direccion = sessionStorage.getItem(CLAVE);
        sessionStorage.removeItem(CLAVE);
    } catch (e) { /* sin sessionStorage: simplemente no hay animación */ }

    if (direccion && !sinMovimiento) {
        raiz.classList.add("nav_anim", "entra_" + direccion);
        setTimeout(limpiar, 500);
    }

    // Al volver con "atrás" el navegador puede restaurar la página a mitad de salida
    window.addEventListener("pageshow", function (e) { if (e.persisted) limpiar(); });

    // --- Salida: al hacer clic en un enlace del nav ---
    document.addEventListener("DOMContentLoaded", function () {
        var enlaces = Array.from(document.querySelectorAll(".paginas ul a[href]"));
        if (!enlaces.length) return;

        var actual = enlaces.findIndex(function (a) {
            return a.classList.contains("opcion_seleccionada") || a.getAttribute("aria-current") === "page";
        });

        enlaces.forEach(function (enlace, destino) {
            enlace.addEventListener("click", function (e) {
                // Respeta clic con Ctrl/Cmd/Shift, botón central, nueva pestaña, etc.
                if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
                if (enlace.target && enlace.target !== "_self") return;
                if (sinMovimiento || destino === actual) return;

                e.preventDefault();
                // Destino más a la derecha => el contenido sale hacia la izquierda
                var haciaDerecha = actual === -1 || destino > actual;
                try { sessionStorage.setItem(CLAVE, haciaDerecha ? "der" : "izq"); } catch (err) {}

                raiz.classList.add("nav_anim", haciaDerecha ? "sale_izq" : "sale_der");
                setTimeout(function () { window.location.href = enlace.href; }, DURACION_SALIDA);
            });
        });
    });
})();

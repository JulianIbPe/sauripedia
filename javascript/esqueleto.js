// ============================================================
// esqueleto.js
// Mientras una imagen se descarga, le pone la clase "cargando"
// (skeleton con brillo, definido en base.css) y se la saca al terminar.
// Las imágenes que ya llegaron (por ejemplo, de la caché) no la reciben.
// ============================================================
(function () {
    "use strict";

    function listo(evento) {
        if (evento.target.tagName === "IMG") {
            evento.target.classList.remove("cargando");
        }
    }

    // load y error no "burbujean": se escuchan en fase de captura para cubrir todas las imágenes
    document.addEventListener("load", listo, true);
    document.addEventListener("error", listo, true);

    document.querySelectorAll("img").forEach(function (img) {
        if (!img.complete && img.getAttribute("src")) {
            img.classList.add("cargando");
        }
    });
})();

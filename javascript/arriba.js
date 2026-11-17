// ============================================================
// arriba.js
// Muestra el botón "volver arriba" al bajar y evita que el índice
// alfabético fijo (página Dinosaurios) se monte sobre el footer.
// ============================================================
(function () {
    "use strict";

    var boton = document.getElementById("boton_arriba");
    var indice = document.getElementById("lista_alfabetica");
    var pie = document.querySelector("footer");

    function actualizar() {
        if (boton) {
            boton.hidden = window.scrollY < 600;
        }
        if (indice && pie) {
            indice.style.transform = "";
            if (getComputedStyle(indice).position !== "fixed") {
                return; // en celular es sticky y no hace falta
            }
            var solape = indice.getBoundingClientRect().bottom + 20 - pie.getBoundingClientRect().top;
            if (solape > 0) {
                indice.style.transform = "translateY(calc(-50% - " + solape + "px))";
            }
        }
    }

    if (boton) {
        boton.addEventListener("click", function () {
            window.scrollTo({ top: 0, behavior: "smooth" });
        });
    }
    window.addEventListener("scroll", actualizar, { passive: true });
    window.addEventListener("resize", actualizar);
    actualizar();
})();

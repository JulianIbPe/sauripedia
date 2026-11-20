// ============================================================
// mapas.js
// Mapas de los continentes en "Mundo prehistórico".
// Siempre hay uno visible (el primero lo muestra PHP) y cada
// INTERVALO_MS pasa solo al período siguiente. Los botones eligen
// un período a mano y reinician la cuenta. Con el mouse encima
// de los mapas, la rotación se pausa para poder mirarlos tranquilo.
// aria-pressed indica a los lectores de pantalla qué botón está activo.
// ============================================================
(function () {
    "use strict";

    var INTERVALO_MS = 8000;
    var botones = Array.from(document.querySelectorAll(".boton_mapa"));
    var zona = document.querySelector(".fondo_mapas");
    if (!botones.length || !zona) {
        return;
    }

    var actual = Math.max(0, botones.findIndex(function (b) { return b.getAttribute("aria-pressed") === "true"; }));
    var temporizador = null;

    function mostrar(indice) {
        actual = indice;
        botones.forEach(function (boton, i) {
            boton.setAttribute("aria-pressed", String(i === indice));
            // al mostrarse, el CSS reproduce la animación de aparición
            document.getElementById("mapa_" + boton.dataset.periodo).hidden = i !== indice;
        });
    }

    function arrancar() {
        clearInterval(temporizador);
        temporizador = setInterval(function () {
            mostrar((actual + 1) % botones.length);
        }, INTERVALO_MS);
    }

    botones.forEach(function (boton, i) {
        boton.addEventListener("click", function () {
            mostrar(i);
            arrancar();
        });
    });

    zona.addEventListener("mouseenter", function () { clearInterval(temporizador); });
    zona.addEventListener("mouseleave", arrancar);

    arrancar();
})();

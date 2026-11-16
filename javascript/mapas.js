// ============================================================
// mapas.js
// Botones de período en "Mundo prehistórico": muestran el mapa elegido.
// Volver a tocar el botón activo oculta el mapa.
// aria-pressed indica a los lectores de pantalla qué botón está activo.
// ============================================================
(function () {
    "use strict";

    var botones = document.querySelectorAll(".boton_mapa");
    var mapas = document.querySelectorAll(".mapa");
    var ayuda = document.querySelector(".mapa_ayuda");

    botones.forEach(function (boton) {
        boton.addEventListener("click", function () {
            var yaEstabaActivo = boton.getAttribute("aria-pressed") === "true";
            var mapa = document.getElementById("mapa_" + boton.dataset.periodo);

            // Primero se apaga todo
            botones.forEach(function (b) { b.setAttribute("aria-pressed", "false"); });
            mapas.forEach(function (m) { m.hidden = true; });

            // Luego se enciende el elegido (si no estaba activo)
            if (!yaEstabaActivo && mapa) {
                boton.setAttribute("aria-pressed", "true");
                mapa.hidden = false; // al mostrarse, el CSS reproduce la animación de aparición
                mapa.scrollIntoView({ behavior: "smooth", block: "center" });
            }
            // El texto "Elige un período" solo se ve cuando no hay ningún mapa abierto
            if (ayuda) {
                ayuda.hidden = !yaEstabaActivo;
            }
        });
    });
})();

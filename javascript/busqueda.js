// ============================================================
// busqueda.js
// Filtra en vivo la lista de dinosaurios mientras se escribe.
// Ignora mayúsculas y tildes ("iguanodon" encuentra "Iguanodón")
// y oculta las letras que se quedan sin resultados.
// ============================================================
(function () {
    "use strict";

    var buscador = document.getElementById("busqueda");
    var sinResultados = document.getElementById("sin_resultados");
    var grupos = document.querySelectorAll(".grupo_letra");
    if (!buscador) {
        return;
    }

    // Pasa a minúsculas y quita tildes: "Pteranodón" -> "pteranodon"
    function normalizar(texto) {
        return texto.normalize("NFD").replace(/[̀-ͯ]/g, "").toLowerCase().trim();
    }

    // Se calcula una sola vez el texto de cada tarjeta (no en cada tecla)
    var tarjetas = Array.from(document.querySelectorAll(".lista_dinosaurios .tarjeta")).map(function (tarjeta) {
        return { elemento: tarjeta, texto: normalizar(tarjeta.textContent) };
    });

    // El formulario no se envía: la búsqueda es solo en la página
    buscador.form.addEventListener("submit", function (evento) {
        evento.preventDefault();
    });

    buscador.addEventListener("input", function () {
        var busqueda = normalizar(buscador.value);
        var visibles = 0;

        tarjetas.forEach(function (t) {
            var coincide = busqueda === "" || t.texto.includes(busqueda);
            var estabaOculta = t.elemento.hidden;
            t.elemento.hidden = !coincide;

            // Animación suave para las tarjetas que vuelven a aparecer
            if (coincide && estabaOculta) {
                t.elemento.classList.remove("reaparece");
                void t.elemento.offsetWidth; // fuerza reinicio de la animación
                t.elemento.classList.add("reaparece");
            }
            if (coincide) {
                visibles++;
            }
        });

        // Oculta el título de una letra si no le quedan tarjetas visibles
        grupos.forEach(function (grupo) {
            grupo.hidden = !grupo.querySelector(".tarjeta:not([hidden])");
        });

        sinResultados.hidden = visibles > 0;
    });
})();

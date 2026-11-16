// ============================================================
// usuario.js
// Abre y cierra el menú del círculo de perfil (cuando hay sesión iniciada).
//
// Antes este archivo tenía 300 líneas: calculaba rutas según la carpeta,
// preguntaba al servidor si había sesión y dibujaba el perfil.
// Ahora PHP ya dibuja la barra correcta (con o sin sesión) y los botones
// son enlaces <a>, así que solo queda el comportamiento del menú.
// ============================================================
(function () {
    "use strict";

    var avatar = document.querySelector(".perfil_avatar");
    var menu = document.getElementById("perfil_menu");
    if (!avatar || !menu) {
        return; // sin sesión no hay menú
    }

    function abrirMenu(abrir) {
        menu.hidden = !abrir;
        avatar.setAttribute("aria-expanded", String(abrir));
    }

    avatar.addEventListener("click", function () {
        abrirMenu(menu.hidden);
    });

    // Se cierra al hacer clic fuera del perfil...
    document.addEventListener("click", function (evento) {
        if (!menu.hidden && !avatar.parentElement.contains(evento.target)) {
            abrirMenu(false);
        }
    });

    // ...o al pulsar Escape (devolviendo el foco al círculo)
    document.addEventListener("keydown", function (evento) {
        if (evento.key === "Escape" && !menu.hidden) {
            abrirMenu(false);
            avatar.focus();
        }
    });
})();

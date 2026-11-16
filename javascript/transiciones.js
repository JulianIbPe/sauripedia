// ============================================================
// transiciones.js
// Decide hacia dónde se anima el cambio de página. Los movimientos
// están en css/transiciones.css; acá solo se elige cuál usar.
//
// Cada página indica su lugar en el sitio en la etiqueta <html>
// (lo escribe includes/cabecera.php):
//   data-orden = posición de su sección en el menú (0 = Inicio, 1 = Dinosaurios...)
//   data-nivel = 0 si es la página de la sección, 1 si es una subpágina (ficha, partida)
//
// Al irse de una página se guarda su posición; la página nueva la compara
// con la suya y elige el tipo: adelante, atras, entrar, salir o fundido.
// Funciona también con los botones Atrás/Adelante del navegador.
//
// Se carga en el <head> sin defer para preparar la animación antes de pintar.
// ============================================================
(function () {
    "use strict";

    var CLAVE = "sauripedia_pagina_anterior";
    var VIGENCIA_MS = 5000; // si pasó más tiempo, no es una navegación "directa"
    var raiz = document.documentElement;

    var actual = {
        orden: Number(raiz.dataset.orden) || 0,
        nivel: Number(raiz.dataset.nivel) || 0
    };

    // ¿El navegador soporta View Transitions entre páginas? (evento pagereveal)
    var soportaVistas = "onpagereveal" in window;

    // ---------- 1) Elegir el tipo de transición comparando con la página anterior ----------
    function tipoDeTransicion(anterior) {
        if (!anterior) {
            return null; // primera visita, enlace externo o pestaña nueva: sin animación
        }
        if (anterior.forzado) {
            return anterior.forzado; // ej. "Siguiente ficha" -> adelante
        }
        if (anterior.orden !== actual.orden) {
            return actual.orden > anterior.orden ? "adelante" : "atras";
        }
        if (anterior.nivel !== actual.nivel) {
            return actual.nivel > anterior.nivel ? "entrar" : "salir";
        }
        return "fundido";
    }

    function leerAnterior() {
        try {
            var guardado = JSON.parse(sessionStorage.getItem(CLAVE));
            sessionStorage.removeItem(CLAVE);
            if (guardado && Date.now() - guardado.momento < VIGENCIA_MS) {
                return guardado;
            }
        } catch (e) { /* sin sessionStorage (modo privado estricto): sin animación */ }
        return null;
    }

    // ---------- 2) Aplicar la animación de entrada ----------
    if (soportaVistas) {
        // El navegador ya está animando: en "pagereveal" le decimos qué tipo usar.
        // Se lee recién ahí porque la página vieja guarda su posición en "pageswap",
        // que el navegador garantiza que ocurre antes.
        window.addEventListener("pagereveal", function (evento) {
            var tipo = tipoDeTransicion(leerAnterior());
            if (!evento.viewTransition) {
                return;
            }
            if (!tipo) {
                evento.viewTransition.skipTransition(); // sin origen conocido: aparece sin animar
            } else if (evento.viewTransition.types) {
                evento.viewTransition.types.add(tipo);
            }
        }, { once: true });
    } else {
        // Respaldo: clase en <html> que anima solo la entrada del <main>
        var tipo = tipoDeTransicion(leerAnterior());
        if (tipo) {
            raiz.classList.add("entrada-" + tipo);
            setTimeout(function () { raiz.classList.remove("entrada-" + tipo); }, 900);
        }
    }

    // ---------- 3) Al irse de esta página, guardar su posición ----------
    var forzado = null;

    // Los enlaces rel="next" / rel="prev" (fichas siguiente/anterior) tienen dirección propia
    document.addEventListener("click", function (evento) {
        var enlace = evento.target.closest && evento.target.closest("a[rel~='next'], a[rel~='prev']");
        if (enlace) {
            forzado = enlace.relList.contains("next") ? "adelante" : "atras";
        }
    });

    function guardarPosicion() {
        try {
            sessionStorage.setItem(CLAVE, JSON.stringify({
                orden: actual.orden,
                nivel: actual.nivel,
                forzado: forzado,
                momento: Date.now()
            }));
        } catch (e) { /* sin sessionStorage: la próxima página entra sin animación */ }
    }

    // pageswap: justo antes de cambiar de página (navegadores con View Transitions).
    // pagehide: al salir, en todos los navegadores (clic, Atrás, Adelante, otra URL).
    window.addEventListener("pageswap", guardarPosicion);
    window.addEventListener("pagehide", function () {
        guardarPosicion();
        forzado = null;
    });
})();

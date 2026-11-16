// ============================================================
// comentarios.js
// Muestra los comentarios guardados y envía los nuevos al servidor.
// Todo el texto de los usuarios se inserta con textContent (nunca
// innerHTML), así un comentario con código HTML/JS se ve como texto
// y no se ejecuta (evita ataques XSS).
//
// Si el visitante no inició sesión, PHP no dibuja el formulario:
// este script solo carga la lista.
// ============================================================
(function () {
    "use strict";

    var MAX = 500;
    var raiz = document.documentElement.dataset.raiz || "";
    var API = raiz + "php/comentarios_be.php";

    var lista = document.getElementById("lista_comentarios");
    var formulario = document.getElementById("form_comentario"); // null sin sesión
    if (!lista) {
        return;
    }

    function formatearFecha(valor) {
        // La base guarda en UTC: se agrega "Z" para que el navegador la pase a la hora local
        var fecha = new Date(String(valor).replace(" ", "T") + "Z");
        return isNaN(fecha) ? "" : fecha.toLocaleString("es-AR", { dateStyle: "medium", timeStyle: "short" });
    }

    function crearComentario(c, esNuevo) {
        var item = document.createElement("article");
        item.className = "panel comentario_item" + (esNuevo ? " nuevo" : "");

        var autor = document.createElement("strong");
        autor.textContent = c.usuario;

        var fecha = document.createElement("time");
        fecha.dateTime = String(c.fecha).replace(" ", "T") + "Z";
        fecha.textContent = " · " + formatearFecha(c.fecha);

        var texto = document.createElement("p");
        texto.textContent = c.texto;

        item.append(autor, fecha, texto);
        return item;
    }

    function mensajeEnLista(texto) {
        var p = document.createElement("p");
        p.className = "panel comentario_item";
        p.textContent = texto;
        lista.replaceChildren(p);
    }

    // animarPrimero: true justo después de publicar, para animar el comentario nuevo
    async function cargarComentarios(animarPrimero) {
        try {
            var respuesta = await fetch(API, { cache: "no-store", credentials: "same-origin" });
            var datos = await respuesta.json();
            if (!respuesta.ok || !datos.ok) {
                throw new Error(datos.error || "HTTP " + respuesta.status);
            }
            if (datos.comentarios.length === 0) {
                mensajeEnLista("Todavía no hay comentarios. ¡Sé el primero!");
                return;
            }
            lista.replaceChildren.apply(lista, datos.comentarios.map(function (c, i) {
                return crearComentario(c, animarPrimero && i === 0);
            }));
        } catch (error) {
            console.error("No se pudieron cargar los comentarios:", error);
            mensajeEnLista("No se pudieron cargar los comentarios.");
        }
    }

    // ---------- Formulario (solo con sesión iniciada) ----------
    if (formulario) {
        var caja = document.getElementById("comentario");
        var boton = formulario.querySelector("button[type=submit]");
        var aviso = document.getElementById("aviso_comentario");
        var contador = document.getElementById("contador_comentario");
        var token = formulario.dataset.csrf;

        function mostrarAviso(texto, esError) {
            aviso.textContent = texto;
            aviso.className = "aviso " + (esError ? "aviso_error" : "aviso_ok");
        }

        caja.addEventListener("input", function () {
            contador.textContent = caja.value.length + " / " + MAX;
        });

        formulario.addEventListener("submit", async function (evento) {
            evento.preventDefault();
            var texto = caja.value.trim();
            if (!texto) {
                mostrarAviso("Escribe un comentario.", true);
                caja.focus();
                return;
            }

            boton.disabled = true;
            try {
                var respuesta = await fetch(API, {
                    method: "POST",
                    credentials: "same-origin",
                    headers: { "Content-Type": "application/json", "X-CSRF-Token": token },
                    body: JSON.stringify({ comentario: texto })
                });
                var datos = await respuesta.json();
                if (respuesta.ok && datos.ok) {
                    caja.value = "";
                    contador.textContent = "0 / " + MAX;
                    mostrarAviso("¡Comentario publicado!", false);
                    cargarComentarios(true);
                } else {
                    mostrarAviso(datos.error || "No se pudo publicar el comentario.", true);
                }
            } catch (error) {
                mostrarAviso("No se pudo conectar con el servidor.", true);
            }
            boton.disabled = false;
        });
    }

    cargarComentarios(false);
})();

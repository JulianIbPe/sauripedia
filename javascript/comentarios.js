// ============================================================
// comentarios.js
// Muestra los comentarios guardados y envía los nuevos al servidor.
// Todo el texto de los usuarios se inserta con textContent (nunca
// innerHTML), así un comentario con código HTML/JS se ve como texto
// y no se ejecuta (evita ataques XSS).
// ============================================================
(function () {
    const formulario = document.getElementById("form_comentario");
    const lista = document.getElementById("lista_comentarios");
    const aviso = document.getElementById("aviso_comentario");
    const contador = document.getElementById("contador_comentario");
    if (!formulario || !lista) { return; }

    const caja = document.getElementById("comentario");
    const boton = formulario.querySelector("button[type=submit]");
    const MAX = 500;

    function mostrarAviso(texto, esError) {
        aviso.textContent = texto;
        aviso.className = esError ? "aviso_error" : "aviso_ok";
    }

    function formatearFecha(valor) {
        const f = new Date(String(valor).replace(" ", "T") + "Z");
        return isNaN(f) ? "" : f.toLocaleString("es-AR", { dateStyle: "medium", timeStyle: "short" });
    }

    async function cargarComentarios() {
        try {
            const r = await fetch("../php/comentarios_be.php", { cache: "no-store", credentials: "same-origin" });
            const datos = await r.json();
            lista.replaceChildren();
            if (!datos.comentarios || datos.comentarios.length === 0) {
                const vacio = document.createElement("p");
                vacio.textContent = "Todavía no hay comentarios. ¡Sé el primero!";
                lista.appendChild(vacio);
                return;
            }
            datos.comentarios.forEach(function (c) {
                const item = document.createElement("article");
                item.className = "comentario_item";
                const autor = document.createElement("strong");
                autor.textContent = c.usuario;
                const fecha = document.createElement("time");
                fecha.textContent = " · " + formatearFecha(c.fecha);
                const texto = document.createElement("p");
                texto.textContent = c.texto;
                item.append(autor, fecha, texto);
                lista.appendChild(item);
            });
        } catch (e) {
            lista.textContent = "No se pudieron cargar los comentarios.";
        }
    }

    // Habilita o deshabilita el formulario según haya sesión iniciada
    async function revisarSesion() {
        let logueado = false;
        try {
            const r = await fetch("../php/sesion_be.php", { cache: "no-store", credentials: "same-origin" });
            const d = await r.json();
            logueado = !!(d && d.logueado);
        } catch (e) { /* sin servidor: se queda como sin sesión */ }
        caja.disabled = !logueado;
        boton.disabled = !logueado;
        if (!logueado) {
            mostrarAviso("Para comentar, inicia sesión o regístrate.", true);
        }
    }

    caja.addEventListener("input", function () {
        contador.textContent = caja.value.length + " / " + MAX;
    });

    formulario.addEventListener("submit", async function (evento) {
        evento.preventDefault();
        const texto = caja.value.trim();
        if (!texto) { mostrarAviso("Escribe un comentario.", true); return; }
        boton.disabled = true;
        try {
            const t = await fetch("../php/token_csrf.php", { cache: "no-store", credentials: "same-origin" });
            const { token } = await t.json();
            const r = await fetch("../php/comentarios_be.php", {
                method: "POST",
                credentials: "same-origin",
                headers: { "Content-Type": "application/json", "X-CSRF-Token": token },
                body: JSON.stringify({ comentario: texto })
            });
            const d = await r.json();
            if (r.ok && d.ok) {
                caja.value = "";
                contador.textContent = "0 / " + MAX;
                mostrarAviso("¡Comentario publicado!", false);
                cargarComentarios();
            } else {
                mostrarAviso(d.error || "No se pudo publicar el comentario.", true);
            }
        } catch (e) {
            mostrarAviso("No se pudo conectar con el servidor.", true);
        }
        boton.disabled = false;
    });

    cargarComentarios();
    revisarSesion();
})();

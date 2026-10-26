// ============================================================
// usuario.js
// Hace funcionar los botones "Iniciar sesión" y "Registrarse"
// (los que tienen el atributo data-accion en el HTML).
//
// Este archivo se usa en TODAS las páginas del sitio. Como cada
// página está en una carpeta distinta, la ruta hacia login.html
// y singin.html cambia según dónde esté el usuario.
// ============================================================

// Paso 1: las rutas de destino según la ubicación del usuario.
// Cada grupo tiene las rutas hacia login.html y singin.html.
const RUTAS_USUARIO = {
    // Estando en index.html (raíz del sitio)
    index: {
        login: "pages/login.html",
        singin: "pages/singin.html"
    },
    // Estando en una página dentro de pages/ (por ejemplo pages/mundo.html)
    pages: {
        login: "../pages/login.html",
        singin: "../pages/singin.html"
    },
    // Estando en login.html o singin.html
    loginSingin: {
        login: "../pages/login.html",
        singin: "../pages/singin.html"
    }
};

// Paso 2: averigua en qué parte del sitio está el usuario y
// devuelve el grupo de rutas que le corresponde.
// (pathname es la parte de la URL después del dominio,
//  por ejemplo "/sauripedia/pages/mundo.html")
function obtenerRutas(pathname) {
    // Nombre del archivo actual: lo que hay después de la última "/"
    const archivo = pathname.split("/").pop();

    // Posición de la carpeta "pages" dentro de la URL (-1 si no está)
    const posicionPages = pathname.lastIndexOf("/pages/");

    // Condición 1: estamos en login.html o singin.html.
    // Se comprueba primero porque esas dos páginas también están dentro de pages/.
    if (archivo === "login.html" || archivo === "singin.html") {
        return RUTAS_USUARIO.loginSingin;
    }

    // Condición 2: estamos en cualquier página dentro de pages/
    // (incluidas sus subcarpetas: dinosaurios/, quizzes/, etc.).
    if (posicionPages !== -1) {
        // Lo que queda después de "/pages/", por ejemplo "dinosaurios/alosaurio.html"
        const dentroDePages = pathname.slice(posicionPages + "/pages/".length);

        // Cada "/" en ese resto es una subcarpeta (0 en pages/mundo.html, 1 en pages/dinosaurios/x.html)
        const subcarpetas = dentroDePages.split("/").length - 1;

        // Cada subcarpeta obliga a subir un nivel más con "../"
        const subir = "../".repeat(subcarpetas);

        return {
            login: subir + RUTAS_USUARIO.pages.login,
            singin: subir + RUTAS_USUARIO.pages.singin
        };
    }

    // Condición 3: estamos en index.html (o en la raíz del sitio, que muestra el index)
    if (archivo === "index.html" || archivo === "") {
        return RUTAS_USUARIO.index;
    }

    // Ubicación desconocida
    return null;
}

// Paso 3: redirige a la página asociada a una acción ("login" o "singin").
function irA(accion) {
    const rutas = obtenerRutas(window.location.pathname);
    if (!rutas) {
        console.error("Ubicación de página desconocida:", window.location.pathname);
        return;
    }

    const ruta = rutas[accion];
    if (!ruta) {
        console.error("Acción de usuario desconocida:", accion);
        return;
    }
    window.location.href = ruta;
}

// Paso 4: conecta cada botón con data-accion a su redirección.
function iniciarBotonesUsuario() {
    document.querySelectorAll("[data-accion]").forEach(function (boton) {
        boton.addEventListener("click", function () {
            irA(boton.dataset.accion);
        });
    });
}

document.addEventListener("DOMContentLoaded", iniciarBotonesUsuario);
// ============================================================
// PERFIL DE USUARIO
// Cuando hay una sesión iniciada: oculta "Iniciar sesión" y
// "Registrarse" y muestra un círculo con la inicial del usuario
// (estilo Google) arriba a la derecha de la barra principal.
// Al hacer clic en el círculo aparece "Cerrar sesión"; al
// pulsarlo, los dos botones vuelven a aparecer.
//
// La barra principal se busca por la clase .paginas, NO por
// el elemento nav, porque hay otros nav con otros usos.
// ============================================================

// Paso 5: averigua la raíz del sitio a partir de la ruta de este mismo script
// (usuario.js siempre está en <raíz>/javascript/), así las rutas a php/ y css/
// funcionan desde cualquier página sin importar en qué carpeta esté.
// Se guarda ahora porque document.currentScript solo existe mientras se ejecuta el script.
const URL_SCRIPT_USUARIO = (function () {
    if (document.currentScript && document.currentScript.src) {
        return document.currentScript.src;
    }
    const script = document.querySelector('script[src$="usuario.js"]');
    return script ? script.src : window.location.href;
})();
const RAIZ_SITIO = new URL("../", URL_SCRIPT_USUARIO);

function urlSitio(rutaRelativa) {
    return new URL(rutaRelativa, RAIZ_SITIO).href;
}

// Paso 6: colores del círculo. Cada nombre siempre cae en el mismo color.
const COLORES_PERFIL = [
    "#1a73e8", "#c5221f", "#188038", "#b06000",
    "#8430ce", "#c2185b", "#007b83", "#5f6368"
];

function colorDePerfil(nombre) {
    let suma = 0;
    for (const letra of nombre) {
        suma = (suma * 31 + letra.codePointAt(0)) >>> 0;
    }
    return COLORES_PERFIL[suma % COLORES_PERFIL.length];
}

// Primera letra del nombre en mayúscula (Array.from respeta emojis y letras compuestas)
function inicialDe(nombre) {
    const letra = Array.from(nombre.trim())[0];
    return letra ? letra.toLocaleUpperCase("es") : "?";
}

// Paso 7: los <li> que contienen los botones "Iniciar sesión" y "Registrarse"
// (son los que tienen data-accion, igual que en iniciarBotonesUsuario).
function itemsBotonesSesion() {
    const botones = document.querySelectorAll(".paginas [data-accion]");
    return Array.from(botones).map(function (boton) {
        return boton.closest("li") || boton;
    });
}

function ocultarBotonesSesion() {
    itemsBotonesSesion().forEach(function (item) { item.style.display = "none"; });
}

function mostrarBotonesSesion() {
    itemsBotonesSesion().forEach(function (item) { item.style.display = ""; });
}

// Paso 8: crea el círculo con su menú desplegable.
function crearPerfil(nombre, alCerrarSesion) {
    const contenedor = document.createElement("div");
    contenedor.className = "perfil_usuario";

    const avatar = document.createElement("button");
    avatar.type = "button";
    avatar.className = "perfil_avatar";
    avatar.textContent = inicialDe(nombre);
    avatar.style.backgroundColor = colorDePerfil(nombre);
    avatar.setAttribute("aria-label", "Menú de usuario: " + nombre);
    avatar.setAttribute("aria-haspopup", "true");
    avatar.setAttribute("aria-expanded", "false");
    avatar.setAttribute("aria-controls", "perfil_menu");

    const menu = document.createElement("div");
    menu.className = "perfil_menu";
    menu.id = "perfil_menu";
    menu.hidden = true;

    const etiqueta = document.createElement("p");
    etiqueta.className = "perfil_nombre";
    etiqueta.textContent = nombre;

    const botonCerrar = document.createElement("button");
    botonCerrar.type = "button";
    botonCerrar.className = "boton_sesion perfil_cerrar";
    botonCerrar.textContent = "Cerrar sesión";

    menu.append(etiqueta, botonCerrar);
    contenedor.append(avatar, menu);

    function abrirMenu(abrir) {
        menu.hidden = !abrir;
        avatar.setAttribute("aria-expanded", String(abrir));
    }

    avatar.addEventListener("click", function () {
        abrirMenu(menu.hidden);
    });

    botonCerrar.addEventListener("click", function () {
        botonCerrar.disabled = true;
        alCerrarSesion();
    });

    // Se cierra al hacer clic fuera del perfil...
    document.addEventListener("click", function (evento) {
        if (!menu.hidden && !contenedor.contains(evento.target)) {
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

    return contenedor;
}

// Paso 9: cierra la sesión en el servidor y restaura los botones sin recargar la página.
async function cerrarSesion(barra, perfil) {
    try {
        const respuesta = await fetch(urlSitio("php/cerrar_sesion.php"), {
            method: "POST",
            cache: "no-store",
            credentials: "same-origin"
        });
        const datos = await respuesta.json();
        if (!datos.ok) {
            throw new Error("El servidor no confirmó el cierre de sesión");
        }
    } catch (error) {
        // Si algo falla, se usa el cierre de sesión clásico (GET + redirección al inicio)
        console.error("No se pudo cerrar sesión con fetch:", error);
        window.location.href = urlSitio("php/cerrar_sesion.php");
        return;
    }

    perfil.remove();
    barra.classList.remove("con_sesion");
    mostrarBotonesSesion();
}

// Paso 10: muestra el perfil en lugar de los botones.
function mostrarPerfil(barra, nombre) {
    // El CSS del perfil solo se descarga cuando hace falta
    if (!document.querySelector('link[data-perfil-css]')) {
        const enlace = document.createElement("link");
        enlace.rel = "stylesheet";
        enlace.href = urlSitio("css/perfil.css");
        enlace.setAttribute("data-perfil-css", "");
        document.head.appendChild(enlace);
    }

    const perfil = crearPerfil(nombre, function () {
        cerrarSesion(barra, perfil);
    });

    barra.classList.add("con_sesion");
    ocultarBotonesSesion();
    barra.appendChild(perfil);
}

// Paso 11: pregunta al servidor si hay sesión iniciada y actualiza la barra.
async function iniciarPerfilUsuario() {
    const barra = document.querySelector(".paginas");
    if (!barra) {
        return;
    }

    // Mientras se consulta al servidor, los botones se vuelven invisibles
    // (sin quitarles el sitio) para que no "parpadeen" si hay sesión.
    const items = itemsBotonesSesion();
    items.forEach(function (item) { item.style.visibility = "hidden"; });

    let nombre = null;
    try {
        const respuesta = await fetch(urlSitio("php/sesion_be.php"), {
            cache: "no-store",
            credentials: "same-origin"
        });
        if (respuesta.ok) {
            const datos = await respuesta.json();
            if (datos && datos.logueado && datos.usuario) {
                nombre = String(datos.usuario);
            }
        }
    } catch (error) {
        // Sin servidor PHP (o respuesta que no es JSON): se queda como "sin sesión"
        console.error("No se pudo comprobar la sesión:", error);
    }

    items.forEach(function (item) { item.style.visibility = ""; });

    if (nombre) {
        mostrarPerfil(barra, nombre);
    }
}

document.addEventListener("DOMContentLoaded", iniciarPerfilUsuario);

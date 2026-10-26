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

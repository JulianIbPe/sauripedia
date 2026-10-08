// Rutas de las páginas de usuario (relativas a index.html).
// Si cambias la ubicación de los archivos, solo hay que editar este objeto.
const RUTAS_USUARIO = {
    login: "/pages/login.html",
    singin: "/pages/singin.html"
};

// Redirige a la página asociada a una acción ("login" o "singin").
function irA(accion) {
    const ruta = RUTAS_USUARIO[accion];
    if (!ruta) {
        console.error("Acción de usuario desconocida:", accion);
        return;
    }
    window.location.href = ruta;
}

// Conecta cada botón con data-accion a su redirección.
function iniciarBotonesUsuario() {
    document.querySelectorAll("[data-accion]").forEach(function (boton) {
        boton.addEventListener("click", function () {
            irA(boton.dataset.accion);
        });
    });
}

document.addEventListener("DOMContentLoaded", iniciarBotonesUsuario);

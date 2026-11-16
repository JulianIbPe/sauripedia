document.addEventListener("click", function (e) {
    var tarjeta = e.target.closest(".tarjeta_dinosaurio");
    if (tarjeta) {
        window.location.href = tarjeta.dataset.href;
    }
});

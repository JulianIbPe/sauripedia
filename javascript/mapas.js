document.addEventListener('DOMContentLoaded', () => 
{
    const botones = document.querySelectorAll('nav button[data-periodo]');
    const mapas = document.querySelectorAll('.mapas');

    botones.forEach(boton => 
    {
        boton.addEventListener('click', () => 
        {
            const periodo = boton.dataset.periodo;
            const mapaElegido = document.getElementById(periodo);
            const yaEstabaActivo = mapaElegido.classList.contains('mapa_activo');

            // Oculta todos los mapas primero
            mapas.forEach(mapa => mapa.classList.remove('mapa_activo'));

            // Si el mapa clickeado no estaba activo, lo muestra y hace scroll hasta él
            if (!yaEstabaActivo) {
                mapaElegido.classList.add('mapa_activo');
                mapaElegido.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    });
});
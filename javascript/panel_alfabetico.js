document.addEventListener('DOMContentLoaded', () => 
{
    const botones = document.querySelectorAll('nav button[data-letra]');
    const titulos = document.querySelectorAll('.separacion_alfabetica');

    botones.forEach(boton => 
    {
        boton.addEventListener('click', () => 
        {
            const letra = boton.dataset.letra;

            // Busca el título (h2) cuyo texto coincide con la letra del botón
            const letraElegida = Array.from(titulos).find(titulo => titulo.textContent.trim().toUpperCase() === letra.toUpperCase());

            if (letraElegida) 
            {
                letraElegida.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
});
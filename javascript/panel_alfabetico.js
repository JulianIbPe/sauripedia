document.addEventListener('DOMContentLoaded', () => 
{
    const botones = document.querySelectorAll('nav button[data-letra]');
    const titulos = document.querySelectorAll('.separacion_alfabetica');

    botones.forEach(boton => 
    {
        boton.addEventListener('click', () => 
        {
            const letra = boton.dataset.letra;

            // Busca el h3 cuyo texto coincide con la letra del botón
            const letraElegida = Array.from(titulos).find(h3 => h3.textContent.trim().toUpperCase() === letra.toUpperCase());

            if (letraElegida) 
            {
                letraElegida.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
});
document.addEventListener('DOMContentLoaded', () => {
    const buscar = document.getElementById("busqueda");
    const grupos = document.querySelectorAll(".lista_dinosaurios");

    //Previene el envio del formulario
    const formulario = document.querySelector("form");
    formulario.addEventListener("submit", function(event) 
    {
        event.preventDefault();
    });

    buscar.addEventListener("input", function()
    {
        //guarda la busqueda e ignora mayusculas, minuscular y espacios en blanco
        let texto = buscar.value.toLowerCase().trim();

        //Empieza la busqueda usando como referencias las letras y despues busca por dinosaurio
        grupos.forEach(function(grupo)
        {
            let letra = grupo.previousElementSibling;
            let dinosaurios = grupo.querySelectorAll(".apartados");
            let hayVisibles = false;

            dinosaurios.forEach(function(dinosaurio)
            {
                //Busca los dinosaurios sin importar mayuscular y minuscular
                let saurus = dinosaurio.textContent.toLowerCase();

                //Muestra todo en caso de que no haya ninguna busqueda
                if(texto === "" || saurus.includes(texto))
                {
                    dinosaurio.style.display = "list-item";
                    hayVisibles = true;
                }
                else
                {
                    dinosaurio.style.display = "none";
                }
            })

            letra.style.display = hayVisibles ? "block" : "none";
        })
    })
});
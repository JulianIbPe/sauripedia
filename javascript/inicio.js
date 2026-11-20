// Portada: línea de tiempo animada.
(() => {
    const datos = JSON.parse(document.getElementById('lt_datos')?.textContent || '[]');
    if (!datos.length) return;

    const MAX = 252, MIN = 66;
    const pos = (ma) => (MAX - ma) / (MAX - MIN) * 100; // millones de años -> % del eje

    // --- Línea de tiempo: uno por uno, en orden cronológico ---
    const tarjeta = document.getElementById('lt_dino');
    const rango = document.getElementById('lt_rango');
    const img = tarjeta.querySelector('img');
    let i = 0;

    function mostrar() {
        const d = datos[i];
        tarjeta.classList.add('lt_oculto');
        setTimeout(() => {
            const centro = (pos(d.desde) + pos(d.hasta)) / 2;
            // Que la tarjeta no se salga de los bordes de la pista
            const margen = 90 / tarjeta.parentElement.offsetWidth * 100;
            tarjeta.style.left = Math.min(100 - margen, Math.max(margen, centro)) + '%';
            img.src = d.imagen;
            img.alt = d.nombre;
            tarjeta.href = d.ficha;
            tarjeta.querySelector('.lt_nombre').textContent = d.nombre;
            tarjeta.querySelector('.lt_fechas').textContent = `Hace ${d.desde}–${d.hasta} millones de años · ${d.periodo}`;
            rango.style.left = pos(d.desde) + '%';
            rango.style.width = (pos(d.hasta) - pos(d.desde)) + '%';
            tarjeta.hidden = rango.hidden = false;
            tarjeta.classList.remove('lt_oculto');
            i = (i + 1) % datos.length;
        }, 500);
    }
    mostrar();
    setInterval(mostrar, 3500);
})();

// Comparador de tamaño: el dinosaurio elegido al lado de una persona de 1,75 m, a la misma escala.
(() => {
    const select = document.getElementById('comp_select');
    if (!select) return;

    const PERSONA = 1.75; // metros
    const escena = document.getElementById('comp_escena');
    const persona = escena.querySelector('.comp_persona');
    const enlace = document.getElementById('comp_dino');
    const img = enlace.querySelector('img');
    const texto = document.getElementById('comp_datos');

    function dibujar() {
        const o = select.selectedOptions[0].dataset;
        const largo = Number(o.largo);
        const alto = largo / Number(o.aspecto); // alto de la imagen en metros, si su ancho = longitud
        // Escala (px por metro) para que entren los dos: a lo alto y a lo ancho (la persona ocupa ~0,5 m + espacio)
        const escala = Math.min(
            escena.clientHeight * 0.95 / Math.max(PERSONA, alto),
            escena.clientWidth * 0.9 / (largo + 1)
        );
        persona.style.height = PERSONA * escala + 'px';
        img.style.width = largo * escala + 'px';
        img.src = o.imagen;
        img.alt = select.selectedOptions[0].text;
        enlace.href = o.ficha;

        const veces = largo / PERSONA;
        texto.textContent = `Longitud: ${o.longitud}` + (o.altura ? ` · Altura: ${o.altura}` : '') + ' · ' +
            (veces >= 1.5 ? `tan largo como ${Math.round(veces)} personas en fila`
                          : veces >= 0.75 ? 'más o menos del largo de una persona'
                          : 'más chico que una persona');
    }

    select.addEventListener('change', dibujar);
    addEventListener('resize', dibujar);
    dibujar();
})();

// Mapa de hallazgos: un punto por lugar donde se encontró cada especie (usa Leaflet, cargado en index.php).
(() => {
    const contenedor = document.getElementById('mh_mapa');
    if (!contenedor || !window.L) return;

    const puntos = JSON.parse(document.getElementById('mh_datos').textContent);
    const css = getComputedStyle(document.documentElement);
    const colores = {
        carnivoro: css.getPropertyValue('--carnivoro').trim(),
        herbivoro: css.getPropertyValue('--herbivoro').trim(),
        omnivoro: css.getPropertyValue('--omnivoro').trim(),
    };

    const mapa = L.map(contenedor, { worldCopyJump: true, scrollWheelZoom: false }).setView([20, 10], 2);
    // Argenmap (IGN Argentina): mapa oscuro con los nombres en español, incluidas las Islas Malvinas
    L.tileLayer('https://wms.ign.gob.ar/geoserver/gwc/service/tms/1.0.0/argenmap_oscuro@EPSG%3A3857@png/{z}/{x}/{y}.png', {
        tms: true,
        maxZoom: 8,
        minZoom: 2,
        attribution: '<a href="https://www.ign.gob.ar/AreaServicios/Argenmap/Introduccion">Instituto Geográfico Nacional</a> + <a href="https://www.osm.org/copyright">OpenStreetMap</a>',
    }).addTo(mapa);

    // Varias especies en el mismo lugar: se separan un poco en círculo para que no se tapen
    const repetidos = {};
    puntos.forEach((p) => {
        const clave = p.lat.toFixed(1) + ',' + p.lng.toFixed(1);
        const n = repetidos[clave] = (repetidos[clave] || 0) + 1;
        const giro = n * 2.4, radio = n > 1 ? 0.35 * Math.sqrt(n) : 0;

        // El popup se arma con nodos (textContent), no con HTML pegado
        const popup = document.createElement('a');
        popup.className = 'mh_popup';
        popup.href = p.ficha;
        const img = document.createElement('img');
        img.src = p.imagen;
        img.alt = '';
        const nombre = document.createElement('strong');
        nombre.textContent = p.nombre;
        const lugar = document.createElement('span');
        lugar.textContent = p.lugar;
        popup.append(img, nombre, lugar);

        L.circleMarker([p.lat + radio * Math.sin(giro), p.lng + radio * Math.cos(giro)], {
            radius: 7,
            color: '#050a05',
            weight: 1,
            fillColor: colores[p.dieta] || colores.herbivoro,
            fillOpacity: 0.9,
        }).bindPopup(popup).bindTooltip(p.nombre).addTo(mapa);
    });
})();

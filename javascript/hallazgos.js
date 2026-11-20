// Mapa de hallazgos: un punto por lugar donde se encontró cada especie (usa Leaflet, cargado en pages/mundo.php).
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

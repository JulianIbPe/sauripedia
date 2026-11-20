<?php
// Página de inicio: presentación del sitio y accesos a cada sección.
require_once __DIR__ . '/includes/config.php';

$pagina = [
    'titulo'      => 'Sauripedia | La enciclopedia de dinosaurios en español',
    'descripcion' => 'Sauripedia es una enciclopedia de dinosaurios en español: especies, hábitats, dietas, mundo prehistórico y un quiz interactivo para poner a prueba lo que sabes.',
    'ruta'        => '',
    'seccion'     => 'inicio',
    'css'         => ['inicio.css'],
    'scripts'     => ['inicio.js'],
    'json_ld'     => [
        '@context'    => 'https://schema.org',
        '@type'       => 'WebSite',
        'name'        => SITIO_NOMBRE,
        'url'         => SITIO_URL,
        'inLanguage'  => 'es',
        'description' => 'Enciclopedia de dinosaurios en español con información didáctica e interactiva.',
    ],
];

// Tarjetas de la portada: texto propio de cada sección (la ruta sale de SECCIONES)
$apartados = [
    'dinosaurios' => 'Descubre el fascinante mundo de los dinosaurios y sus diferentes especies. Aprende sobre sus hábitats, dietas y comportamientos.',
    'mundo'       => 'Explora cómo era la Tierra en la era de los dinosaurios y recorre los distintos períodos geológicos.',
    'quiz'        => '¿Cuánto sabes sobre los dinosaurios? Pon a prueba tus conocimientos con preguntas de tres niveles de dificultad.',
    'comentarios' => 'Deja un comentario sobre el sitio o sus contenidos y ayúdanos a mejorar el proyecto.',
];

// Rango aproximado (millones de años) de cada subperíodo: el JSON solo trae el subperíodo
$rangos = [
    'Triásico tardío'    => [237, 201],
    'Jurásico inferior'  => [201, 174],
    'Jurásico medio'     => [174, 163],
    'Jurásico superior'  => [163, 145],
    'Cretácico inferior' => [145, 100],
    'Cretácico superior' => [100, 66],
];

// Datos para la línea de tiempo (los usa javascript/inicio.js)
$linea = [];
foreach (dinosaurios() as $dino) {
    $periodo = $dino['datos']['periodo'] ?? '';
    if (!isset($rangos[$periodo])) {
        continue; // fuera de las tres eras (ej. Dimetrodon, del Pérmico)
    }
    $linea[] = [
        'nombre'  => $dino['nombre'],
        'imagen'  => url('imagenes/' . $dino['miniatura']['archivo']),
        'ficha'   => url(ruta_dinosaurio($dino['id'])),
        'periodo' => $periodo,
        'desde'   => $rangos[$periodo][0],
        'hasta'   => $rangos[$periodo][1],
    ];
}
// En orden de aparición: primero los más antiguos
usort($linea, fn($a, $b) => [$b['desde'], $a['nombre']] <=> [$a['desde'], $b['nombre']]);

require __DIR__ . '/includes/cabecera.php';
?>
            <section class="panel bienvenida">
                <h1>La Sauripedia: enciclopedia de dinosaurios</h1>
                <p>La Sauripedia reúne información sobre <?= count(dinosaurios()) ?> especies de dinosaurios y reptiles prehistóricos, organizada de manera didáctica e interactiva, con el fin de expandir el conocimiento sobre estos fascinantes animales.</p>
            </section>

            <section class="contenedor_apartados" aria-label="Secciones del sitio">
<?php foreach ($apartados as $clave => $texto): ?>
                <article class="panel apartado">
                    <h2><a href="<?= url(SECCIONES[$clave]['ruta']) ?>"><?= e(SECCIONES[$clave]['titulo']) ?></a></h2>
                    <p><?= e($texto) ?></p>
                </article>
<?php endforeach; ?>
            </section>

            <section class="linea_tiempo" aria-labelledby="titulo_linea">
                <h2 id="titulo_linea">Línea de tiempo</h2>
                <div class="lt_pista">
                    <a class="lt_dino" id="lt_dino" href="#" hidden aria-live="polite">
                        <img src="" alt="" width="160" height="110">
                        <span class="lt_nombre"></span>
                        <span class="lt_fechas"></span>
                    </a>
                </div>
                <div class="lt_eje">
                    <span class="lt_rango" id="lt_rango" hidden></span>
                    <div class="lt_era" style="flex: 51"><strong>Triásico</strong><span>252 Ma</span></div>
                    <div class="lt_era" style="flex: 56"><strong>Jurásico</strong><span>201 Ma</span></div>
                    <div class="lt_era" style="flex: 79"><strong>Cretácico</strong><span>145 Ma</span><span class="lt_fin">66 Ma</span></div>
                </div>
                <script type="application/json" id="lt_datos"><?= json_encode($linea, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?></script>
            </section>

            <section class="comparador" aria-labelledby="titulo_comparador">
                <h2 id="titulo_comparador">Comparador de tamaño</h2>
                <p class="comp_elegir">
                    <label for="comp_select">Elige un dinosaurio:</label>
                    <select id="comp_select">
<?php foreach (dinosaurios() as $dino):
    // "3,4 m" -> 3.4 ; "11 m (envergadura)" -> 11
    $largo = (float) str_replace(',', '.', $dino['datos']['longitud'] ?? '');
    if ($largo <= 0) continue;
?>
                        <option value="<?= e($dino['id']) ?>"<?= $dino['id'] === 'tyrannosaurus' ? ' selected' : '' ?>
                                data-imagen="<?= e(url('imagenes/' . $dino['miniatura']['archivo'])) ?>"
                                data-ficha="<?= e(url(ruta_dinosaurio($dino['id']))) ?>"
                                data-largo="<?= $largo ?>"
                                data-aspecto="<?= round($dino['miniatura']['ancho'] / $dino['miniatura']['alto'], 3) ?>"
                                data-longitud="<?= e($dino['datos']['longitud']) ?>"
                                data-altura="<?= e($dino['datos']['altura'] ?? '') ?>"><?= e($dino['nombre_lista']) ?></option>
<?php endforeach; ?>
                    </select>
                </p>
                <div class="comp_escena" id="comp_escena">
                    <svg class="comp_persona" viewBox="0 0 50 175" role="img" aria-label="Persona de 1,75 m">
                        <circle cx="25" cy="12" r="11"/>
                        <path d="M12 28h26a6 6 0 0 1 6 6v50a4 4 0 0 1-8 0V42h-2v128a5 5 0 0 1-10 0V100h-2v70a5 5 0 0 1-10 0V42h-2v42a4 4 0 0 1-8 0V34a6 6 0 0 1 6-6z"/>
                    </svg>
                    <a class="comp_dino" id="comp_dino" href="#"><img src="" alt=""></a>
                </div>
                <p class="comp_datos" id="comp_datos" aria-live="polite"></p>
            </section>

<?php
// Puntos del mapa: un punto por cada lugar donde se halló cada especie
$puntos = [];
foreach (dinosaurios() as $dino) {
    foreach ($dino['hallazgos'] ?? [] as $h) {
        $puntos[] = [
            'nombre' => $dino['nombre'],
            'dieta'  => $dino['dieta'],
            'imagen' => url('imagenes/' . $dino['miniatura']['archivo']),
            'ficha'  => url(ruta_dinosaurio($dino['id'])),
            'lugar'  => $h['lugar'],
            'lat'    => $h['lat'],
            'lng'    => $h['lng'],
        ];
    }
}
?>
            <section class="mapa_hallazgos" aria-labelledby="titulo_hallazgos">
                <h2 id="titulo_hallazgos">Mapa de hallazgos</h2>
                <p class="mh_ayuda">Dónde se encontraron los fósiles de cada especie (ubicación aproximada). Toca un punto para ver el dinosaurio.</p>
                <ul class="mh_leyenda">
<?php foreach (DIETAS as $clave => $nombre): ?>
                    <li><span class="mh_color dieta_<?= $clave ?>"></span><?= e($nombre) ?></li>
<?php endforeach; ?>
                </ul>
                <div id="mh_mapa" class="mh_mapa" role="region" aria-label="Mapa del mundo con los hallazgos de fósiles"></div>
                <script type="application/json" id="mh_datos"><?= json_encode($puntos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?></script>
                <!-- Leaflet: librería del mapa (versión fija + integridad comprobada) -->
                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin="" defer></script>
            </section>
<?php require __DIR__ . '/includes/pie.php'; ?>

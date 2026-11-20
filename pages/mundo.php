<?php
// Mundo prehistórico: texto introductorio y mapas de los continentes por período.
require_once __DIR__ . '/../includes/config.php';

// Un mapa por período; los botones y las imágenes se generan de esta lista
$periodos = [
    'triasico'  => ['titulo' => 'Triásico',  'imagen' => 'mapa-triasico.webp',  'ancho' => 1774, 'alto' => 887],
    'jurasico'  => ['titulo' => 'Jurásico',  'imagen' => 'mapa-jurasico.webp',  'ancho' => 1536, 'alto' => 1024],
    'cretacico' => ['titulo' => 'Cretácico', 'imagen' => 'mapa-cretacico.webp', 'ancho' => 1774, 'alto' => 887],
];

$pagina = [
    'titulo'      => 'Mundo prehistórico: mapas del Triásico, Jurásico y Cretácico | Sauripedia',
    'descripcion' => 'Cómo era la Tierra en la era de los dinosaurios: clima, continentes y mapas de los períodos Triásico, Jurásico y Cretácico.',
    'ruta'        => 'pages/mundo.php',
    'seccion'     => 'mundo',
    'css'         => ['mundo.css'],
    'scripts'     => ['mapas.js', 'hallazgos.js'],
    'imagen'      => 'imagenes/mapa-cretacico.webp',
    'imagen_alt'  => 'Mapa de los continentes en el Cretácico',
    'json_ld'     => json_ld_pagina('Mundo prehistórico', 'pages/mundo.php', [
        ['Inicio', ''], ['Mundo prehistórico', 'pages/mundo.php'],
    ]),
];

require __DIR__ . '/../includes/cabecera.php';
?>
            <h1 class="titulo_pagina">Mundo prehistórico</h1>

            <section class="panel introduccion_mundo">
                <h2>¿Cómo era el mundo en la era de los dinosaurios?</h2>
                <p>Durante la era de los dinosaurios, el mundo era muy diferente al que conocemos hoy en día. Los continentes estaban en diferentes posiciones y el clima era más cálido y húmedo. Los océanos eran más extensos y había grandes áreas de tierra emergida que ahora están sumergidas bajo el agua.</p>
                <p>Los dinosaurios vivieron en diferentes períodos de tiempo, desde el Triásico hasta el Cretácico, y durante este tiempo la vida en la Tierra evolucionó de manera significativa. Los dinosaurios eran los animales dominantes, y coexistían con otros reptiles, aves primitivas y mamíferos pequeños.</p>
                <p>Aunque se extinguieron hace millones de años, su legado sigue vivo a través de los fósiles y la investigación científica.</p>
            </section>

            <section class="seccion_mapas" aria-labelledby="titulo_mapas">
                <h2 class="titulo_mapas" id="titulo_mapas">Mapas de los continentes durante la era de los dinosaurios</h2>

                <div class="botones_mapa" role="group" aria-label="Elegir período geológico">
<?php $primero = array_key_first($periodos); // este mapa se ve al entrar; después mapas.js los va rotando ?>
<?php foreach ($periodos as $clave => $periodo): ?>
                    <button class="boton boton_mapa" type="button" data-periodo="<?= $clave ?>" aria-pressed="<?= $clave === $primero ? 'true' : 'false' ?>" aria-controls="mapa_<?= $clave ?>">Período <?= e($periodo['titulo']) ?></button>
<?php endforeach; ?>
                </div>

                <div class="fondo_mapas">
<?php foreach ($periodos as $clave => $periodo): ?>
                    <img class="mapa" id="mapa_<?= $clave ?>" src="<?= url('imagenes/' . $periodo['imagen']) ?>"
                         alt="Mapa de los continentes durante el período <?= e($periodo['titulo']) ?>"
                         width="<?= $periodo['ancho'] ?>" height="<?= $periodo['alto'] ?>" loading="lazy" decoding="async"<?= $clave === $primero ? '' : ' hidden' ?>>
<?php endforeach; ?>
                </div>
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
                <!-- El skeleton queda detrás del mapa: se ve hasta que cargan las imágenes del mapa -->
                <div class="mh_marco esqueleto"><div id="mh_mapa" class="mh_mapa" role="region" aria-label="Mapa del mundo con los hallazgos de fósiles"></div></div>
                <script type="application/json" id="mh_datos"><?= json_encode($puntos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?></script>
                <!-- Leaflet: librería del mapa (versión fija + integridad comprobada) -->
                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin="" defer></script>
            </section>
<?php require __DIR__ . '/../includes/pie.php'; ?>

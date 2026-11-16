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
    'scripts'     => ['mapas.js'],
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
<?php foreach ($periodos as $clave => $periodo): ?>
                    <button class="boton boton_mapa" type="button" data-periodo="<?= $clave ?>" aria-pressed="false" aria-controls="mapa_<?= $clave ?>">Período <?= e($periodo['titulo']) ?></button>
<?php endforeach; ?>
                </div>

                <div class="fondo_mapas">
<?php foreach ($periodos as $clave => $periodo): ?>
                    <img class="mapa" id="mapa_<?= $clave ?>" src="<?= url('imagenes/' . $periodo['imagen']) ?>"
                         alt="Mapa de los continentes durante el período <?= e($periodo['titulo']) ?>"
                         width="<?= $periodo['ancho'] ?>" height="<?= $periodo['alto'] ?>" loading="lazy" decoding="async" hidden>
<?php endforeach; ?>
                    <p class="mapa_ayuda">Elige un período para ver su mapa.</p>
                </div>
            </section>
<?php require __DIR__ . '/../includes/pie.php'; ?>

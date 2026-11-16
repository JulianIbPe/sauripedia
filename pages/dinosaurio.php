<?php
// Ficha de un dinosaurio: UNA plantilla para las 109 especies.
// Uso: pages/dinosaurio.php?id=carnotauro
// Antes había 109 archivos .html casi iguales; corregir un detalle de diseño
// obligaba a editarlos todos. Ahora los datos están en datos/dinosaurios.json
// y el HTML en este único archivo (separación de datos y presentación).
require_once __DIR__ . '/../includes/config.php';

$id   = (string) ($_GET['id'] ?? '');
$dino = dinosaurio($id);

// ---------- Ficha inexistente: respuesta 404 real (los buscadores no la indexan) ----------
if ($dino === null) {
    http_response_code(404);
    $pagina = [
        'titulo'      => 'Dinosaurio no encontrado | Sauripedia',
        'descripcion' => 'La ficha que buscas no existe en Sauripedia.',
        'ruta'        => 'pages/dinosaurios.php',
        'seccion'     => 'dinosaurios',
        'nivel'       => 1,
        'css'         => ['dinosaurios.css'],
        'indexar'     => false,
    ];
    require __DIR__ . '/../includes/cabecera.php';
    ?>
            <section class="panel aviso_pagina">
                <h1>Este dinosaurio no está en la Sauripedia</h1>
                <p>Puede que el enlace esté mal escrito o que la ficha haya cambiado de lugar.</p>
                <p><a class="boton" href="<?= url('pages/dinosaurios.php') ?>">Ver la lista de dinosaurios</a></p>
            </section>
    <?php
    require __DIR__ . '/../includes/pie.php';
    exit();
}

// ---------- Ficha encontrada ----------
$ruta = ruta_dinosaurio($dino['id']);
$titulo = $dino['nombre'] . ': características, tamaño y datos | Sauripedia';
$imagen = 'imagenes/' . $dino['imagen']['archivo'];

// Etiquetas de cada dato (la clave coincide con "datos" del JSON)
$etiquetas = [
    'altura'      => 'Altura',
    'longitud'    => 'Longitud',
    'peso'        => 'Peso',
    'velocidad'   => 'Velocidad',
    'periodo'     => 'Período',
    'especimenes' => 'Especímenes',
];

// Ficha anterior y siguiente en orden alfabético (para navegar sin volver a la lista)
$todas = dinosaurios();
$posicion = array_search($dino['id'], array_column($todas, 'id'), true);
$anterior = $todas[$posicion - 1] ?? null;
$siguiente = $todas[$posicion + 1] ?? null;

$pagina = [
    'titulo'      => $titulo,
    'descripcion' => $dino['meta_descripcion'],
    'ruta'        => $ruta,
    'seccion'     => 'dinosaurios',
    'nivel'       => 1,
    'css'         => ['dinosaurios.css'],
    'fondo'       => 'fondo_' . $dino['dieta'],
    'og_tipo'     => 'article',
    'imagen'      => $imagen,
    'imagen_alt'  => 'Ilustración de ' . $dino['nombre'],
    'json_ld'     => json_ld_pagina($titulo, $ruta, [
        ['Inicio', ''], ['Dinosaurios', 'pages/dinosaurios.php'], [$dino['nombre'], $ruta],
    ], [
        'description' => $dino['meta_descripcion'],
        'primaryImageOfPage' => [
            '@type' => 'ImageObject', 'url' => url_absoluta($imagen),
            'width' => $dino['imagen']['ancho'], 'height' => $dino['imagen']['alto'],
        ],
    ]),
];

require __DIR__ . '/../includes/cabecera.php';
?>
            <nav class="migas" aria-label="Ruta de navegación">
                <ol>
                    <li><a href="<?= url('index.php') ?>">Inicio</a></li>
                    <li><a href="<?= url('pages/dinosaurios.php') ?>">Dinosaurios</a></li>
                    <li aria-current="page"><span class="nombre_dino"><?= e($dino['nombre']) ?></span></li>
                </ol>
            </nav>

            <article class="ficha dieta_<?= e($dino['dieta']) ?>">
                <div class="ficha_textos">
                    <header class="panel ficha_bloque">
                        <h1><?= e($dino['nombre']) ?></h1>
                        <p class="ficha_significado">«<?= e($dino['significado']) ?>»</p>
                        <p class="ficha_clasificacion"><?= e($dino['clasificacion']) ?></p>
                        <p class="dieta_etiqueta"><?= e(DIETAS[$dino['dieta']]) ?></p>
                    </header>

                    <section class="panel ficha_bloque" aria-label="Descripción">
                        <p><?= e($dino['ubicacion']) ?></p>
                        <p><?= e($dino['descripcion']) ?></p>
                    </section>

                    <!-- Pares dato/valor: <dl> es la etiqueta semántica para esto -->
                    <section class="panel ficha_bloque" aria-label="Datos">
                        <dl class="ficha_datos">
<?php foreach ($etiquetas as $clave => $etiqueta): ?>
<?php if (!empty($dino['datos'][$clave])): ?>
                            <div>
                                <dt><?= $etiqueta ?></dt>
                                <dd><?= e($dino['datos'][$clave]) ?></dd>
                            </div>
<?php endif; ?>
<?php endforeach; ?>
                        </dl>
                    </section>
                </div>

                <figure class="panel ficha_imagen">
                    <img src="<?= url($imagen) ?>" alt="Ilustración de <?= e($dino['nombre']) ?>"
                         width="<?= (int) $dino['imagen']['ancho'] ?>" height="<?= (int) $dino['imagen']['alto'] ?>" decoding="async" fetchpriority="high">
                </figure>
            </article>

            <nav class="ficha_navegacion" aria-label="Otras fichas">
<?php if ($anterior): ?>
                <a class="boton boton_secundario" href="<?= url(ruta_dinosaurio($anterior['id'])) ?>" rel="prev">← <span class="nombre_dino"><?= e($anterior['nombre_lista']) ?></span></a>
<?php endif; ?>
                <a class="boton" href="<?= url('pages/dinosaurios.php') ?>#letra-<?= e(mb_strtoupper(mb_substr($dino['nombre_lista'], 0, 1))) ?>">Volver a la lista</a>
<?php if ($siguiente): ?>
                <a class="boton boton_secundario" href="<?= url(ruta_dinosaurio($siguiente['id'])) ?>" rel="next"><span class="nombre_dino"><?= e($siguiente['nombre_lista']) ?></span> →</a>
<?php endif; ?>
            </nav>
<?php require __DIR__ . '/../includes/pie.php'; ?>

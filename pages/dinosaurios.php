<?php
// Lista de dinosaurios agrupada por letra, con buscador e índice alfabético.
// Se genera desde datos/dinosaurios.json: agregar una especie al JSON la suma acá sola.
require_once __DIR__ . '/../includes/config.php';

$grupos = dinosaurios_por_letra();

$pagina = [
    'titulo'      => 'Lista de dinosaurios de la A a la Z | Sauripedia',
    'descripcion' => 'Listado alfabético de ' . count(dinosaurios()) . ' dinosaurios y reptiles prehistóricos con su ficha: tamaño, peso, dieta, período y ubicación.',
    'ruta'        => 'pages/dinosaurios.php',
    'seccion'     => 'dinosaurios',
    'css'         => ['dinosaurios.css'],
    'scripts'     => ['busqueda.js'],
    'json_ld'     => json_ld_pagina('Lista de dinosaurios', 'pages/dinosaurios.php', [
        ['Inicio', ''], ['Dinosaurios', 'pages/dinosaurios.php'],
    ]),
];

require __DIR__ . '/../includes/cabecera.php';
?>
            <h1 class="titulo_pagina">Lista de dinosaurios</h1>

            <!-- Búsqueda en vivo: el filtrado lo hace busqueda.js, no se envía a ningún servidor -->
            <form class="buscador" role="search">
                <label for="busqueda" class="solo_lectores">Buscar dinosaurio</label>
                <input class="busqueda_saurus" type="search" id="busqueda" placeholder="Busca un dinosaurio..." autocomplete="off">
            </form>
            <p id="sin_resultados" class="panel aviso_busqueda" role="status" aria-live="polite" hidden>No se encontraron dinosaurios con ese nombre.</p>

            <!-- Índice alfabético: enlaces a anclas (#letra-A); funcionan sin JavaScript -->
            <nav id="lista_alfabetica" aria-label="Índice alfabético">
                <ul>
<?php foreach (array_keys($grupos) as $letra): ?>
                    <li><a class="boton_abc" href="#letra-<?= e($letra) ?>"><?= e($letra) ?></a></li>
<?php endforeach; ?>
                </ul>
            </nav>

<?php foreach ($grupos as $letra => $lista): ?>
            <section class="grupo_letra" id="letra-<?= e($letra) ?>" aria-labelledby="titulo-<?= e($letra) ?>">
                <h2 class="separacion_alfabetica" id="titulo-<?= e($letra) ?>"><?= e($letra) ?></h2>
                <ol class="lista_dinosaurios">
<?php foreach ($lista as $dino): ?>
                    <li class="tarjeta">
                        <!-- Enlace real: se puede abrir en otra pestaña y los buscadores lo siguen -->
                        <a class="tarjeta_dinosaurio dieta_<?= e($dino['dieta']) ?>" href="<?= url(ruta_dinosaurio($dino['id'])) ?>">
                            <img src="<?= url('imagenes/' . $dino['miniatura']['archivo']) ?>" alt=""
                                 width="<?= (int) $dino['miniatura']['ancho'] ?>" height="<?= (int) $dino['miniatura']['alto'] ?>" loading="lazy" decoding="async">
                            <span class="texto_lista"><?= e($dino['nombre_lista']) ?></span>
                            <span class="dieta_etiqueta"><?= e(DIETAS[$dino['dieta']]) ?></span>
                        </a>
                    </li>
<?php endforeach; ?>
                </ol>
            </section>
<?php endforeach; ?>
<?php require __DIR__ . '/../includes/pie.php'; ?>

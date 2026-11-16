<?php
// Página de inicio: presentación del sitio y accesos a cada sección.
require_once __DIR__ . '/includes/config.php';

$pagina = [
    'titulo'      => 'Sauripedia | La enciclopedia de dinosaurios en español',
    'descripcion' => 'Sauripedia es una enciclopedia de dinosaurios en español: especies, hábitats, dietas, mundo prehistórico y un quiz interactivo para poner a prueba lo que sabes.',
    'ruta'        => '',
    'seccion'     => 'inicio',
    'css'         => ['inicio.css'],
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
<?php require __DIR__ . '/includes/pie.php'; ?>

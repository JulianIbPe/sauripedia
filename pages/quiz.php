<?php
// Quiz: presentación y elección de nivel.
require_once __DIR__ . '/../includes/config.php';

$pagina = [
    'titulo'      => 'Quiz de dinosaurios: pon a prueba lo que sabes | Sauripedia',
    'descripcion' => 'Quiz de dinosaurios en tres niveles (fácil, medio y difícil) con preguntas de verdadero o falso, opción múltiple y elegir la opción correcta.',
    'ruta'        => 'pages/quiz.php',
    'seccion'     => 'quiz',
    'css'         => ['quiz.css'],
    'json_ld'     => json_ld_pagina('Quiz de dinosaurios', 'pages/quiz.php', [
        ['Inicio', ''], ['Quiz', 'pages/quiz.php'],
    ]),
];

require __DIR__ . '/../includes/cabecera.php';
?>
            <h1 class="titulo_pagina">Quiz de dinosaurios</h1>

            <section class="panel introduccion_quiz">
                <h2>¿Quieres poner a prueba tus conocimientos sobre dinosaurios?</h2>
                <p>El quiz tiene tres niveles de dificultad: fácil, medio y difícil. Además de verdadero o falso, incluye preguntas de opción múltiple y de elegir la opción correcta. ¡Una excelente manera de aprender y divertirse al mismo tiempo!</p>
            </section>

            <ul class="niveles_quiz">
<?php foreach (NIVELES_QUIZ as $nivel => $datos_nivel): ?>
                <li>
                    <a class="boton_nivel nivel_<?= $nivel ?>" href="<?= url(ruta_quiz($nivel)) ?>">
                        <span class="boton_nivel_titulo"><?= e($datos_nivel['titulo']) ?></span>
                        <span class="boton_nivel_texto"><?= e(ucfirst($datos_nivel['descripcion'])) ?></span>
                    </a>
                </li>
<?php endforeach; ?>
            </ul>
<?php require __DIR__ . '/../includes/pie.php'; ?>

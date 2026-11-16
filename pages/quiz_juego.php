<?php
// Partida del quiz. Una sola página para los tres niveles: pages/quiz_juego.php?nivel=facil
// (antes eran quiz_facil.html, quiz_medio.html y quiz_dificil.html, idénticos salvo el nivel).
// Las preguntas están en datos/preguntas.json y la lógica en javascript/quiz.js.
require_once __DIR__ . '/../includes/config.php';

$nivel = (string) ($_GET['nivel'] ?? '');

// Nivel inexistente: se vuelve a la elección de nivel
if (!isset(NIVELES_QUIZ[$nivel])) {
    redirigir('pages/quiz.php');
}

$datos_nivel = NIVELES_QUIZ[$nivel];
$ruta = ruta_quiz($nivel);
$titulo = 'Quiz de dinosaurios nivel ' . mb_strtolower($datos_nivel['titulo']) . ' | Sauripedia';

$pagina = [
    'titulo'      => $titulo,
    'descripcion' => 'Responde el quiz de dinosaurios de nivel ' . mb_strtolower($datos_nivel['titulo']) . ', con ' . $datos_nivel['descripcion'] . '. Verdadero o falso y opción múltiple, con puntaje al final.',
    'ruta'        => $ruta,
    'seccion'     => 'quiz',
    'nivel'       => 1,
    'css'         => ['quiz.css'],
    'scripts'     => ['quiz.js'],
    'json_ld'     => json_ld_pagina($titulo, $ruta, [
        ['Inicio', ''], ['Quiz', 'pages/quiz.php'], ['Nivel ' . mb_strtolower($datos_nivel['titulo']), $ruta],
    ]),
];

require __DIR__ . '/../includes/cabecera.php';
?>
            <section id="quiz" data-nivel="<?= e($nivel) ?>">
                <h1 class="titulo_pagina">Quiz de dinosaurios: nivel <?= e(mb_strtolower($datos_nivel['titulo'])) ?></h1>

                <!-- Pantalla de preguntas (la llena quiz.js) -->
                <div id="quiz-juego" class="panel" aria-live="polite">
                    <p class="quiz-cargando">Cargando preguntas...</p>
                    <div class="quiz-info">
                        <span id="contador"></span>
                        <span id="puntaje"></span>
                    </div>
                    <div class="barra" role="progressbar" aria-label="Progreso del quiz" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                        <div id="barra-progreso"></div>
                    </div>

                    <!-- Esta zona se anima en cada pregunta nueva -->
                    <div id="zona-pregunta">
                        <p id="tipo-pregunta" class="tipo-pregunta"></p>
                        <h2 id="pregunta"></h2>
                        <div id="opciones"></div>
                    </div>
                    <p id="mensaje" class="mensaje"></p>

                    <button id="btn-siguiente" class="boton" type="button" hidden>Siguiente</button>
                </div>

                <!-- Pantalla de resultado -->
                <div id="quiz-resultado" class="panel" hidden>
                    <h2>¡Quiz terminado!</h2>
                    <p id="puntaje-final"></p>
                    <p id="mensaje-final"></p>
                    <div class="quiz-acciones">
                        <button id="btn-reiniciar" class="boton" type="button">Intentar de nuevo</button>
                        <a class="boton boton_secundario" href="<?= url('pages/quiz.php') ?>">Elegir otro nivel</a>
                    </div>
                </div>
            </section>
<?php require __DIR__ . '/../includes/pie.php'; ?>

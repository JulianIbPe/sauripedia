<?php
// Comentarios: formulario (solo con sesión iniciada) y lista de comentarios publicados.
require_once __DIR__ . '/../includes/config.php';

$usuario = usuario_actual();

$pagina = [
    'titulo'      => 'Comentarios sobre dinosaurios y el sitio | Sauripedia',
    'descripcion' => 'Comparte tu opinión sobre los dinosaurios, el mundo prehistórico y la Sauripedia, y lee lo que escribieron otros visitantes.',
    'ruta'        => 'pages/comentarios.php',
    'seccion'     => 'comentarios',
    'css'         => ['formularios.css'],
    'scripts'     => ['comentarios.js'],
    'json_ld'     => json_ld_pagina('Comentarios', 'pages/comentarios.php', [
        ['Inicio', ''], ['Comentarios', 'pages/comentarios.php'],
    ]),
];

require __DIR__ . '/../includes/cabecera.php';
?>
            <h1 class="titulo_pagina">Comentarios</h1>

            <section class="panel formulario" aria-labelledby="subtitulo_comentario">
                <h2 id="subtitulo_comentario">Brinda tu opinión sobre los dinosaurios y el mundo prehistórico</h2>

<?php if ($usuario === null): ?>
                <!-- Sin sesión no se muestra un formulario deshabilitado: se explica qué hacer -->
                <p>Para comentar, <a href="<?= url('pages/login.php') ?>">inicia sesión</a> o <a href="<?= url('pages/registro.php') ?>">crea una cuenta</a>.</p>
<?php else: ?>
                <!-- El token viaja en un atributo data-*: comentarios.js lo manda en la cabecera X-CSRF-Token -->
                <form id="form_comentario" data-csrf="<?= token_csrf() ?>" novalidate>
                    <div class="campo">
                        <label for="comentario">Comentario como <strong><?= e($usuario) ?></strong></label>
                        <textarea id="comentario" name="comentario" rows="5" maxlength="500" required aria-describedby="contador_comentario"></textarea>
                        <small id="contador_comentario" class="contador">0 / 500</small>
                    </div>
                    <p id="aviso_comentario" class="aviso" role="status" aria-live="polite"></p>
                    <button type="submit" class="boton">Enviar comentario</button>
                </form>
<?php endif; ?>
            </section>

            <section class="lista_comentarios" aria-labelledby="titulo_lista_comentarios">
                <h2 id="titulo_lista_comentarios" class="solo_lectores">Comentarios publicados</h2>
                <div id="lista_comentarios" aria-live="polite">
                    <!-- Skeleton mientras llegan los comentarios (comentarios.js lo reemplaza) -->
                    <p class="solo_lectores">Cargando comentarios...</p>
<?php for ($i = 0; $i < 3; $i++): ?>
                    <div class="panel comentario_item" aria-hidden="true">
                        <span class="esqueleto esqueleto_linea corta"></span>
                        <span class="esqueleto esqueleto_linea"></span>
                        <span class="esqueleto esqueleto_linea media"></span>
                    </div>
<?php endfor; ?>
                </div>
            </section>
<?php require __DIR__ . '/../includes/pie.php'; ?>

<?php
// Formulario de inicio de sesión. Lo procesa php/login_usuario_be.php.
require_once __DIR__ . '/../includes/config.php';

// Si ya hay sesión, no tiene sentido mostrar el formulario
if (usuario_actual() !== null) {
    redirigir('index.php');
}

$flash = tomar_flash();
$valores = $flash['valores'] ?? [];

$pagina = [
    'titulo'      => 'Iniciar sesión | Sauripedia',
    'descripcion' => 'Inicia sesión en Sauripedia para dejar comentarios.',
    'ruta'        => 'pages/login.php',
    'css'         => ['formularios.css'],
    'indexar'     => false,
];

require __DIR__ . '/../includes/cabecera.php';
?>
            <section class="panel formulario formulario_cuenta">
                <h1>Iniciar sesión</h1>
<?php require __DIR__ . '/../includes/aviso.php'; ?>
                <form method="POST" action="<?= url('php/login_usuario_be.php') ?>" accept-charset="UTF-8">
                    <?= campo_csrf() ?>
                    <div class="campo">
                        <label for="usuario">Usuario</label>
                        <input type="text" id="usuario" name="usuario" value="<?= e($valores['usuario'] ?? '') ?>" placeholder="Tu nombre de usuario" required maxlength="50" autocomplete="username">
                    </div>
                    <div class="campo">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" placeholder="Tu contraseña" required maxlength="72" autocomplete="current-password">
                    </div>
                    <button type="submit" class="boton">Ingresar</button>
                </form>
                <p class="formulario_pie">¿No tienes cuenta? <a href="<?= url('pages/registro.php') ?>">Regístrate</a></p>
            </section>
<?php require __DIR__ . '/../includes/pie.php'; ?>

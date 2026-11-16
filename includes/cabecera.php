<?php
// ============================================================
// cabecera.php
// <head> + barra de navegación, iguales en todas las páginas.
// Antes este bloque estaba copiado en ~120 archivos .html; ahora vive acá.
// Patrón: Template View (plantilla con "huecos" que llena cada página).
//
// Cada página define $pagina ANTES de incluir este archivo:
//   $pagina = [
//     'titulo'      => 'Texto del <title>',            (obligatorio)
//     'descripcion' => 'Meta description',              (obligatorio)
//     'ruta'        => 'pages/mundo.php',               ruta canónica desde la raíz
//     'seccion'     => 'mundo',                         clave de SECCIONES a resaltar
//     'nivel'       => 0,                               0 = página de sección, 1 = subpágina
//     'css'         => ['mundo.css'],                   hojas propias de la página
//     'scripts'     => ['mapas.js'],                    scripts propios (van al final del body)
//     'fondo'       => 'fondo_jurassico',               clase del <body> con la foto de fondo
//     'og_tipo'     => 'website' | 'article',
//     'imagen'      => 'imagenes/x.webp',               imagen para redes sociales
//     'imagen_alt'  => 'Texto alternativo',
//     'json_ld'     => [...],                           datos estructurados (schema.org)
//     'indexar'     => true,                            false = noindex (login, registro, 404)
//   ];
// ============================================================

$pagina += [
    'ruta'       => '',
    'seccion'    => '',
    'nivel'      => 0,
    'css'        => [],
    'scripts'    => [],
    'fondo'      => 'fondo_jurassico',
    'og_tipo'    => 'website',
    'imagen'     => 'imagenes/fondo_jurassico_renderizado.webp',
    'imagen_alt' => 'Paisaje jurásico de Sauripedia',
    'json_ld'    => null,
    'indexar'    => true,
];

// Posición de la sección en el menú: la usa transiciones.js para decidir la dirección
$orden_seccion = array_search($pagina['seccion'], array_keys(SECCIONES), true);
if ($orden_seccion === false) {
    // Páginas fuera del menú (login, registro): se ubican a la derecha, donde están sus botones
    $orden_seccion = count(SECCIONES);
}

$usuario = usuario_actual();

/** Color fijo del círculo de perfil según el nombre (mismo nombre = mismo color). */
function color_perfil(string $nombre): string
{
    $colores = ['#1a73e8', '#c5221f', '#188038', '#b06000', '#8430ce', '#c2185b', '#007b83', '#5f6368'];
    $suma = 0;
    foreach (mb_str_split($nombre) as $letra) {
        $suma = ($suma * 31 + mb_ord($letra)) & 0xFFFFFFFF;
    }
    return $colores[$suma % count($colores)];
}
?>
<!DOCTYPE html>
<html lang="es" data-raiz="<?= e(url()) ?>" data-orden="<?= $orden_seccion ?>" data-nivel="<?= (int) $pagina['nivel'] ?>">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= e($pagina['titulo']) ?></title>
        <meta name="description" content="<?= e($pagina['descripcion']) ?>">
        <meta name="robots" content="<?= $pagina['indexar'] ? 'index, follow' : 'noindex, follow' ?>">
        <meta name="author" content="<?= SITIO_NOMBRE ?>">
        <link rel="canonical" href="<?= e(url_absoluta($pagina['ruta'])) ?>">
        <link rel="icon" href="<?= url('favicon.ico') ?>" sizes="48x48">
        <link rel="icon" href="<?= url('favicon.svg') ?>" type="image/svg+xml" sizes="any">
        <link rel="apple-touch-icon" href="<?= url('apple-touch-icon.png') ?>">
        <link rel="manifest" href="<?= url('site.webmanifest') ?>">
        <meta name="theme-color" content="#050a05">

        <!-- Open Graph / redes sociales -->
        <meta property="og:type" content="<?= e($pagina['og_tipo']) ?>">
        <meta property="og:locale" content="es_AR">
        <meta property="og:site_name" content="<?= SITIO_NOMBRE ?>">
        <meta property="og:title" content="<?= e($pagina['titulo']) ?>">
        <meta property="og:description" content="<?= e($pagina['descripcion']) ?>">
        <meta property="og:url" content="<?= e(url_absoluta($pagina['ruta'])) ?>">
        <meta property="og:image" content="<?= e(url_absoluta($pagina['imagen'])) ?>">
        <meta property="og:image:alt" content="<?= e($pagina['imagen_alt']) ?>">
        <meta name="twitter:card" content="summary_large_image">

        <!-- Estilos: base y tema en todas las páginas, luego los propios de la página -->
        <link rel="stylesheet" href="<?= url_version('css/base.css') ?>">
        <link rel="stylesheet" href="<?= url_version('css/tema_medianoche.css') ?>">
        <link rel="stylesheet" href="<?= url_version('css/transiciones.css') ?>">
<?php foreach ($pagina['css'] as $hoja): ?>
        <link rel="stylesheet" href="<?= url_version('css/' . $hoja) ?>">
<?php endforeach; ?>

        <!-- Va en el <head> (sin defer) para preparar la animación de entrada antes de pintar -->
        <script src="<?= url_version('javascript/transiciones.js') ?>"></script>

<?php if ($pagina['json_ld']): ?>
        <script type="application/ld+json"><?= json_encode($pagina['json_ld'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?></script>
<?php endif; ?>
    </head>
    <body class="<?= e($pagina['fondo']) ?>">
        <a class="saltar_contenido" href="#contenido">Saltar al contenido</a>
        <header class="cabecera">
            <nav class="paginas" aria-label="Navegación principal">
                <ul>
<?php foreach (SECCIONES as $clave => $seccion):
    $activa = $clave === $pagina['seccion'];
    // aria-current="page" solo en la página exacta; en subpáginas (una ficha) se resalta sin él
    $es_esta = $activa && $pagina['nivel'] === 0;
?>
                    <li><a href="<?= url($seccion['ruta']) ?>"<?= $activa ? ' class="opcion_seleccionada"' : '' ?><?= $es_esta ? ' aria-current="page"' : '' ?>><?= e($seccion['titulo']) ?></a></li>
<?php endforeach; ?>
                </ul>

                <div class="zona_sesion">
<?php if ($usuario === null): ?>
                    <!-- Enlaces y no botones: llevan a otra página (HTML semántico, funcionan sin JS) -->
                    <a class="boton_sesion" href="<?= url('pages/login.php') ?>">Iniciar sesión</a>
                    <a class="boton_sesion boton_sesion_destacado" href="<?= url('pages/registro.php') ?>">Registrarse</a>
<?php else: ?>
                    <div class="perfil_usuario">
                        <button type="button" class="perfil_avatar" style="background-color: <?= color_perfil($usuario) ?>"
                                aria-label="Menú de usuario: <?= e($usuario) ?>" aria-haspopup="true" aria-expanded="false" aria-controls="perfil_menu">
                            <?= e(mb_strtoupper(mb_substr(trim($usuario), 0, 1))) ?>
                        </button>
                        <div class="perfil_menu" id="perfil_menu" hidden>
                            <p class="perfil_nombre"><?= e($usuario) ?></p>
                            <!-- Cerrar sesión cambia el estado: va por POST y con token CSRF -->
                            <form method="POST" action="<?= url('php/cerrar_sesion.php') ?>">
                                <?= campo_csrf() ?>
                                <button type="submit" class="boton_sesion">Cerrar sesión</button>
                            </form>
                        </div>
                    </div>
<?php endif; ?>
                </div>
            </nav>
        </header>
        <main id="contenido">

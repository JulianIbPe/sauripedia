<?php
// ============================================================
// config.php
// Punto de entrada común: cada página y cada script de php/
// incluye SOLO este archivo, y él trae el resto (sesión, datos).
// Patrón: Front Controller "liviano" / configuración centralizada.
// Así una URL, un mail o una ruta se cambia en un único lugar (DRY).
// ============================================================

// Dominio público del sitio: se usa para canonical, Open Graph y sitemap.
// Reemplazar por el dominio real cuando se publique.
const SITIO_URL    = 'https://www.sauripedia.com/';
const SITIO_NOMBRE = 'Sauripedia';

// Correo de contacto genérico del proyecto (en lugar de mails personales,
// que quedaban expuestos a spam). Cambiarlo por la casilla real del grupo.
const CONTACTO_EMAIL = 'contacto@sauripedia.com';

require_once __DIR__ . '/sesion.php';
require_once __DIR__ . '/datos.php';

/**
 * Ruta relativa desde el script que se está ejecutando hasta la raíz del sitio.
 * Ej.: index.php -> ""   |   pages/mundo.php -> "../"
 * Se calcula comparando carpetas en disco, así funciona igual en
 * http://localhost/sauripedia/ (XAMPP) que en un dominio propio.
 */
function ruta_raiz(): string
{
    $raiz   = str_replace('\\', '/', realpath(dirname(__DIR__)));
    $script = str_replace('\\', '/', realpath(dirname($_SERVER['SCRIPT_FILENAME'])));

    // Lo que sobra de la ruta del script después de la raíz son las subcarpetas
    $resto = trim(substr($script, strlen($raiz)), '/');
    $niveles = $resto === '' ? 0 : substr_count($resto, '/') + 1;

    return str_repeat('../', $niveles);
}

$RAIZ = ruta_raiz();

/** URL relativa a un archivo del sitio, válida desde la página actual. */
function url(string $ruta = ''): string
{
    global $RAIZ;
    return $RAIZ . $ruta;
}

/**
 * Igual que url(), pero agrega ?v=<fecha de modificación> para que el navegador
 * descargue de nuevo el archivo cada vez que cambia (evita ver CSS/JS viejos en caché).
 */
function url_version(string $ruta): string
{
    $archivo = dirname(__DIR__) . '/' . $ruta;
    $version = is_file($archivo) ? filemtime($archivo) : 0;
    return url($ruta) . '?v=' . $version;
}

/** URL absoluta (con dominio) para canonical, og:url, sitemap, etc. */
function url_absoluta(string $ruta = ''): string
{
    return SITIO_URL . $ruta;
}

/** Escapa texto antes de imprimirlo en HTML (evita XSS). Nombre corto porque se usa mucho. */
function e(?string $texto): string
{
    return htmlspecialchars($texto ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Redirige a otra página del sitio y corta la ejecución. */
function redirigir(string $ruta): void
{
    header('Location: ' . url($ruta));
    exit();
}

/**
 * Datos estructurados (schema.org) de una página con su "miga de pan".
 * $migas: [['Inicio', ''], ['Dinosaurios', 'pages/dinosaurios.php'], ...]
 * $extra: campos adicionales para el nodo WebPage (ej. primaryImageOfPage).
 */
function json_ld_pagina(string $nombre, string $ruta, array $migas, array $extra = []): array
{
    $url = url_absoluta($ruta);
    $items = [];
    foreach ($migas as $i => [$titulo, $ruta_miga]) {
        $items[] = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $titulo, 'item' => url_absoluta($ruta_miga)];
    }

    return [
        '@context' => 'https://schema.org',
        '@graph'   => [
            [
                '@type'      => 'WebPage',
                '@id'        => $url,
                'url'        => $url,
                'name'       => $nombre,
                'inLanguage' => 'es',
                'isPartOf'   => ['@type' => 'WebSite', 'name' => SITIO_NOMBRE, 'url' => SITIO_URL],
                'breadcrumb' => ['@id' => $url . '#migas'],
            ] + $extra,
            ['@type' => 'BreadcrumbList', '@id' => $url . '#migas', 'itemListElement' => $items],
        ],
    ];
}

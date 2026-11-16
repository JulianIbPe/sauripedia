<?php
// ============================================================
// sitemap.php  ->  sitemap.xml para los buscadores (Google, Bing...).
// Se genera desde los mismos datos que el menú y las fichas, así
// nunca queda desactualizado (antes era un XML escrito a mano).
// El .htaccess hace que /sitemap.xml muestre este archivo.
// ============================================================
require_once __DIR__ . '/includes/config.php';

header('Content-Type: application/xml; charset=UTF-8');

// Fecha de última modificación: la del archivo de datos de las fichas
$fecha = date('Y-m-d', filemtime(__DIR__ . '/datos/dinosaurios.json'));

$rutas = array_column(SECCIONES, 'ruta');
$rutas[0] = ''; // la portada se publica como la raíz del dominio, no como /index.php
foreach (array_keys(NIVELES_QUIZ) as $nivel) {
    $rutas[] = ruta_quiz($nivel);
}
foreach (dinosaurios() as $dino) {
    $rutas[] = ruta_dinosaurio($dino['id']);
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($rutas as $ruta): ?>
    <url>
        <loc><?= e(url_absoluta($ruta)) ?></loc>
        <lastmod><?= $fecha ?></lastmod>
    </url>
<?php endforeach; ?>
</urlset>

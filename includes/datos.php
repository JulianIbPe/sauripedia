<?php
// ============================================================
// datos.php
// Contenido "fijo" del sitio en un solo lugar:
//   - secciones del menú (alimentan el nav, el mapa del sitio del pie y el sitemap.xml)
//   - niveles del quiz
//   - fichas de dinosaurios (se leen de datos/dinosaurios.json)
// Patrón: Repositorio. Las páginas piden datos con funciones y no saben
// de dónde salen; si mañana pasan a una tabla MySQL, solo cambia este archivo.
// ============================================================

/**
 * Secciones del menú principal, en el orden en que aparecen.
 * El orden también define hacia dónde se desliza la transición entre páginas.
 */
const SECCIONES = [
    'inicio'      => ['titulo' => 'Inicio',             'ruta' => 'index.php'],
    'dinosaurios' => ['titulo' => 'Dinosaurios',        'ruta' => 'pages/dinosaurios.php'],
    'mundo'       => ['titulo' => 'Mundo prehistórico', 'ruta' => 'pages/mundo.php'],
    'quiz'        => ['titulo' => 'Quiz',               'ruta' => 'pages/quiz.php'],
    'comentarios' => ['titulo' => 'Comentarios',        'ruta' => 'pages/comentarios.php'],
];

/** Niveles del quiz: la clave es la que se usa en la URL (?nivel=facil) y en preguntas.json */
const NIVELES_QUIZ = [
    'facil'   => ['titulo' => 'Fácil',   'descripcion' => 'preguntas básicas para empezar'],
    'medio'   => ['titulo' => 'Medio',   'descripcion' => 'preguntas de dificultad intermedia'],
    'dificil' => ['titulo' => 'Difícil', 'descripcion' => 'preguntas para expertos'],
];

/** Nombres legibles de cada dieta (la clave coincide con el campo "dieta" del JSON). */
const DIETAS = [
    'carnivoro' => 'Carnívoro',
    'herbivoro' => 'Herbívoro',
    'omnivoro'  => 'Omnívoro',
];

/** Todas las fichas, ordenadas alfabéticamente. Se lee el JSON una sola vez por petición. */
function dinosaurios(): array
{
    static $lista = null;
    if ($lista === null) {
        $json  = file_get_contents(dirname(__DIR__) . '/datos/dinosaurios.json');
        $lista = json_decode($json, true) ?: [];
    }
    return $lista;
}

/** Una ficha por su id (ej. "carnotauro"), o null si no existe. */
function dinosaurio(string $id): ?array
{
    foreach (dinosaurios() as $dino) {
        if ($dino['id'] === $id) {
            return $dino;
        }
    }
    return null;
}

/** Fichas agrupadas por la primera letra del nombre: ['A' => [...], 'B' => [...]] */
function dinosaurios_por_letra(): array
{
    $grupos = [];
    foreach (dinosaurios() as $dino) {
        $letra = mb_strtoupper(mb_substr($dino['nombre_lista'], 0, 1));
        $grupos[$letra][] = $dino;
    }
    ksort($grupos);
    return $grupos;
}

/** URL de la ficha de un dinosaurio (relativa a la raíz del sitio). */
function ruta_dinosaurio(string $id): string
{
    return 'pages/dinosaurio.php?id=' . rawurlencode($id);
}

/** URL de un nivel del quiz (relativa a la raíz del sitio). */
function ruta_quiz(string $nivel): string
{
    return 'pages/quiz_juego.php?nivel=' . rawurlencode($nivel);
}

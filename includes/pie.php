<?php
// ============================================================
// pie.php
// Cierra el <main> que abre cabecera.php, dibuja el pie de página
// (con el mapa del sitio) y carga los scripts.
// El mapa del sitio se arma desde SECCIONES y NIVELES_QUIZ: si se agrega
// una sección, aparece sola en el menú, en el pie y en el sitemap.xml.
// ============================================================
?>
        </main>
        <footer class="pie_pagina">
            <div class="pie_columna pie_marca">
                <p class="pie_logo"><?= SITIO_NOMBRE ?></p>
                <p>La enciclopedia de dinosaurios en español.</p>
                <p class="pie_copy">© <?= date('Y') ?> <?= SITIO_NOMBRE ?>. Todos los derechos reservados.</p>
            </div>

            <nav class="pie_columna pie_mapa" aria-label="Mapa del sitio">
                <h2 class="titulo_footer">Mapa del sitio</h2>
                <ul>
<?php foreach (SECCIONES as $clave => $seccion): ?>
                    <li>
                        <a href="<?= url($seccion['ruta']) ?>"><?= e($seccion['titulo']) ?></a>
<?php if ($clave === 'quiz'): ?>
                        <ul>
<?php foreach (NIVELES_QUIZ as $nivel => $datos_nivel): ?>
                            <li><a href="<?= url(ruta_quiz($nivel)) ?>">Nivel <?= e(mb_strtolower($datos_nivel['titulo'])) ?></a></li>
<?php endforeach; ?>
                        </ul>
<?php endif; ?>
                    </li>
<?php endforeach; ?>
                    <li><a href="<?= url('pages/login.php') ?>">Iniciar sesión</a></li>
                    <li><a href="<?= url('pages/registro.php') ?>">Registrarse</a></li>
                </ul>
            </nav>

            <address class="pie_columna">
                <h2 class="titulo_footer">Contacto</h2>
                <p><a href="mailto:<?= CONTACTO_EMAIL ?>"><?= CONTACTO_EMAIL ?></a></p>
                <p>¿Una sugerencia? <a href="<?= url('pages/comentarios.php') ?>">Déjanos un comentario</a>.</p>
            </address>

            <div class="pie_columna">
                <h2 class="titulo_footer">Redes sociales</h2>
                <ul>
                    <li><a href="https://www.facebook.com/sauripedia" target="_blank" rel="noopener noreferrer">Facebook</a></li>
                    <li><a href="https://www.twitter.com/sauripedia" target="_blank" rel="noopener noreferrer">Twitter</a></li>
                    <li><a href="https://www.instagram.com/sauripedia" target="_blank" rel="noopener noreferrer">Instagram</a></li>
                </ul>
            </div>
        </footer>

        <script src="<?= url('javascript/usuario.js') ?>" defer></script>
<?php foreach ($pagina['scripts'] as $script): ?>
        <script src="<?= url('javascript/' . $script) ?>" defer></script>
<?php endforeach; ?>
    </body>
</html>

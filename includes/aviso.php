<?php
// Muestra el mensaje "flash" pendiente (error o éxito) que dejó un script de php/.
// Uso: $flash = tomar_flash();  ...  require 'aviso.php';
if (!empty($flash)): ?>
                <p class="aviso aviso_<?= $flash['tipo'] === 'ok' ? 'ok' : 'error' ?>" role="<?= $flash['tipo'] === 'ok' ? 'status' : 'alert' ?>"><?= e($flash['mensaje']) ?></p>
<?php endif;

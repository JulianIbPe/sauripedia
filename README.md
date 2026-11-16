# Sauripedia

Enciclopedia de dinosaurios en español (PHP + MySQL, sin frameworks).

## Cómo correrlo

El sitio ahora es PHP en todas sus páginas, así que **no funciona con Live Server** de VS Code: hay que usar XAMPP.

1. Copiar la carpeta `sauripedia` dentro de `C:\xampp\htdocs\`.
2. Encender **Apache** y **MySQL** en el panel de XAMPP.
3. En phpMyAdmin:
   - Si es la primera vez: importar `sql/login_register_db.sql`.
   - Si ya tenían la base creada: ejecutar `sql/migracion_2026-11.sql` (agrega la tabla `intentos_login`).
4. Abrir `http://localhost/sauripedia/`.

## Estructura

```
index.php                 Portada
pages/
  dinosaurios.php         Lista A-Z con buscador
  dinosaurio.php?id=...   Ficha de una especie (una plantilla para las 109)
  mundo.php               Mapas por período
  quiz.php                Elección de nivel
  quiz_juego.php?nivel=   Partida (facil | medio | dificil)
  comentarios.php
  login.php / registro.php
php/                      Procesan formularios y la API de comentarios (no muestran HTML)
includes/                 Piezas compartidas (no accesibles desde el navegador)
  config.php              Dominio, mail de contacto, funciones url() y e()
  cabecera.php / pie.php  <head>, menú y pie de página de todas las páginas
  datos.php               Secciones del menú, niveles del quiz y acceso a las fichas
  sesion.php              Sesión, token CSRF y mensajes
  conexion.php            Conexión a la base de datos
datos/
  dinosaurios.json        Datos de las 109 fichas
  preguntas.json          Banco de preguntas del quiz
css/                      base + tema + transiciones + un archivo por tipo de página
javascript/               Un archivo por comportamiento
sql/                      Scripts de la base de datos
```

## Tareas comunes

- **Agregar un dinosaurio:** sumar un objeto en `datos/dinosaurios.json` y su imagen en `imagenes/`. Aparece solo en la lista, en el sitemap y en la navegación anterior/siguiente.
- **Cambiar un link del menú o del pie:** `SECCIONES` en `includes/datos.php`.
- **Cambiar los colores:** variables de `css/tema_medianoche.css`.
- **Cambiar el dominio o el mail de contacto:** constantes de `includes/config.php`.

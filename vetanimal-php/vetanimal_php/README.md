# Veterinaria VetAnimal — versión PHP + JS + CSS

Conversión completa del proyecto original (React + TypeScript + Express) a
**PHP puro (con PDO/MySQL) + JavaScript vanilla + CSS**, sin frameworks ni
paso de compilación. Podés editar cualquier archivo y recargar el navegador.

## 1. Requisitos

- PHP 8.x con extensión **PDO MySQL** habilitada
- MySQL o MariaDB
- Un servidor web (Apache, o el servidor embebido de PHP para probar local)

## 2. Instalación

### a) Crear la base de datos

Importá el archivo `schema.sql` (crea la base `vetanimal`, las tablas y los
datos de prueba):

```bash
mysql -u root -p < schema.sql
```

### b) Configurar la conexión

Editá `config.php` con los datos de tu servidor MySQL:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'vetanimal');
define('DB_USER', 'root');
define('DB_PASS', 'tu_password');
```

### c) Levantar el servidor

Para probar rápido en tu máquina, desde la carpeta del proyecto:

```bash
php -S localhost:8000
```

Y abrís `http://localhost:8000/index.php` en el navegador.

En un hosting real, apuntá el "document root" del sitio a esta carpeta.

## 3. Usuarios de prueba

Todas las contraseñas son **123456**.

| Rol         | Email                              |
|-------------|-------------------------------------|
| Veterinario | santiago.mendez@vetanimal.com       |
| Veterinario | martina.paz@vetanimal.com           |
| Cliente     | agustina.gomez@example.com          |

También hay botones de "Ingresar como Cliente / Vet" en la pantalla de login.

## 4. Estructura del proyecto

```
config.php              -> credenciales de la base de datos
schema.sql               -> esquema + datos de prueba

includes/
  db.php                  -> conexión PDO reutilizable
  auth.php                -> sesiones PHP (login, helpers require_login, etc.)
  header.php / footer.php -> nav y pie de página compartidos
  page-start.php / page-end.php -> apertura/cierre del HTML de cada página

api/                     -> endpoints JSON (uno por recurso), reemplazan
                             las rutas Express del server.ts original:
  auth.php        (login, register, logout, forgot-password)
  pets.php        (listar, crear, editar, borrar mascotas)
  services.php    (servicios de la clínica)
  turnos.php      (reservas: crear, listar, cambiar estado, calendario)
  consultas.php   (historial clínico)
  vacunas.php
  estudios.php
  products.php    (catálogo de la tienda)
  orders.php      (checkout / pedidos)
  admin_stats.php (estadísticas del panel veterinario)

assets/
  css/style.css   -> todo el diseño (colores, botones, layout, etc.)
  js/*.js         -> un archivo JS por página, hace fetch() a /api/

admin/            -> panel veterinario (dashboard, turnos, pacientes)

*.php (raíz)      -> una página por pantalla: index, login, register,
                     booking, historial, tienda, carrito, perfil, etc.
```

## 5. Cómo está armado (para entender el código)

- **Cada pantalla es un archivo `.php` independiente** (a diferencia del
  original, que era una sola app de una página con rutas de React). Por
  ejemplo `booking.php` es la pantalla de reservar turno.
- Esas páginas **PHP arman el HTML** y muestran el "cascarón" de cada
  pantalla (formularios, tablas vacías, etc.).
- El **contenido dinámico** (llenar la lista de mascotas, el calendario,
  la tabla de turnos, etc.) lo hace el archivo **JavaScript** correspondiente
  en `assets/js/`, que llama a los endpoints de `api/` con `fetch()`,
  recibe JSON y arma el HTML con JavaScript puro (sin librerías).
- El **login** usa sesiones de PHP (`$_SESSION`), es más seguro que guardar
  todo en el navegador como hacía la versión original.
- El **carrito de compras** sigue viviendo en el navegador
  (`localStorage`), igual que en el original — ver `assets/js/main.js`.

## 6. Notas / diferencias respecto al original

- Las contraseñas ahora se guardan **hasheadas** (`password_hash` de PHP)
  en vez de texto plano como en el `server.ts` original.
- Los endpoints admin (ver todas las mascotas, todos los turnos,
  estadísticas) están protegidos: solo un usuario con `rol = 'veterinario'`
  logueado puede usarlos.
- Si tu hosting no soporta los métodos HTTP `PUT`/`PATCH`/`DELETE` desde
  `fetch()` (poco común, pero pasa en algunos hostings compartidos viejos),
  avisame y lo adaptamos a que todo viaje por `POST`.

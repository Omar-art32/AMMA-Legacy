# AMMA Legacy

Migración del sistema SIGCE Legacy desde una versión antigua de PHP hacia un entorno moderno utilizando Docker.

## Estructura del proyecto

```
AMMA-Legacy/
│
├── docker-compose.yml
├── Dockerfile
├── .dockerignore
├── .gitignore
├── README.md
│
└── sigce53/
```

---

# Entorno de desarrollo

## Tecnologías utilizadas

- PHP 8.3
- Apache
- MariaDB 10.5
- Docker
- Docker Compose

---

# Iniciar el proyecto

Construir los contenedores:

```bash
docker compose up -d --build
```

Verificar contenedores activos:

```bash
docker ps
```

---

# Base de datos

Base de datos utilizada:

```
amma
```

Importar respaldo SQL:

```bash
docker exec -i mariadb101 mysql -uroot -proot amma < sigce53/amma.sql
```

---

# Conexión a MariaDB

Al utilizar Docker, los servicios se comunican mediante el nombre del servicio definido en `docker-compose.yml`.

Ejemplo anterior:

```php
$conexion = new mysqli(
    "localhost",
    "root",
    "",
    "amma"
);
```

Dentro de Docker debe utilizar:

```php
$conexion = new mysqli(
    "mariadb",
    "root",
    "root",
    "amma"
);
```

---

# Configuración PHP

Durante la migración se utilizará un archivo:

```
php.ini
```

Este archivo permite controlar la configuración de PHP dentro del contenedor.

Configuración recomendada para desarrollo:

```ini
display_errors = On
display_startup_errors = On
error_reporting = E_ALL
```

## ¿Qué hace cada opción?

### display_errors

Muestra errores directamente en el navegador.

Ejemplo:

```
Fatal error: Undefined function
```

---

### display_startup_errors

Muestra errores durante el inicio de PHP.

---

### error_reporting

Define qué errores serán mostrados.

Con:

```ini
error_reporting = E_ALL
```

se muestran:

- Errores fatales
- Warnings
- Notices
- Deprecated
- Errores de sintaxis

---

# Mostrar errores temporalmente desde PHP

Durante la migración también se pueden activar desde código:

```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

Ejemplo:

```php
<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
```

Esto ayuda a detectar problemas de compatibilidad con PHP 8.3.

---

# Revisar errores del contenedor

Logs del servidor:

```bash
docker logs amma-legacy-web
```

Últimos errores:

```bash
docker logs amma-legacy-web --tail 100
```

Entrar al contenedor:

```bash
docker exec -it amma-legacy-web bash
```

Logs de Apache:

```bash
tail -f /var/log/apache2/error.log
```

---
# AMMA-Legacy — Migración a PHP 8.3

Resumen sencillo de lo que llevamos hecho hasta ahora. Empezamos por el módulo
`acceso` (login) como primer paso de una migración modular del sistema completo.

---

## 1. Qué logramos

El módulo **`acceso` ya corre en PHP 8.3** sin errores fatales ni avisos de
deprecación, y de paso quedó más seguro que el original. Es el primer módulo del
sistema que cruza a 8.3; el resto sigue en código legado y se migrará después
con este mismo patrón.

---

## 2. Qué cambiamos (y por qué)

| Cambio | Antes | Ahora |
|---|---|---|
| **Inyección SQL** | Usuario y contraseña se metían crudos en la consulta | Prepared statements (`bind_param`) |
| **Errores de BD** | `mysqli` con `if ($result != false)` — en 8.3 reventaba en 500 | `try/catch` que devuelve el JSON de error |
| **Funciones obsoletas** | `FILTER_SANITIZE_STRING`, `utf8_encode` (deprecadas) | `htmlspecialchars`, `mb_convert_encoding` |
| **Logout roto** | `cerrar.php` mandaba a `/sigce/` → 404 | Ruta relativa a `login.php` |
| **Ruta del proyecto** | `/sigce53` escrito a mano en varios archivos | Centralizada en `config.php` (`APP_BASE_PATH`) |
| **jQuery** | `../js/jquery.min.js` (ruta frágil un nivel arriba) | jQuery local dentro del módulo |
| **Conexión a BD** | `die()` que en 8.3 ya no se ejecutaba; sin charset | `mysqli` con `try/catch` y `utf8mb4` |
| **`declare(strict_types)`** | Rompía si el archivo tenía un carácter invisible al inicio | Retirado, por robustez |

---

## 3. Qué archivos movimos

Los duplicados y versiones viejas se movieron a `acceso/_archivo/`. **No se
borraron** (por si hay que consultarlos), pero ya no forman parte del sistema.

| Archivo | Por qué se archivó |
|---|---|
| `login2.php` | Copia casi idéntica de `login.php` |
| `login_old.php` | Versión anterior del login |
| `entrar010223.php` | Copia vieja (fechada ~01/02/23) que apuntaba a `/sigce/` |
| `cfg_server.php.old` | Reemplazado por `config.php` |

**Archivos que ahora sí se usan (canónicos):** `login.php`, `entrar.php`,
`cerrar.php`.

**Archivo nuevo:** `common/config.php` (configuración de rutas centralizada).

**Puente de compatibilidad:** `common/cfg_server.php` se conservó pero ahora solo
reenvía a `config.php`, para que el código viejo que aún lo pide (como el
`index.php` de la raíz) no se rompa.

---

## 4. Idea de organización de carpetas

La regla es simple: **el código compartido vive en `common/`, cada módulo en su
propia carpeta, y lo obsoleto en un `_archivo/` dentro del módulo.**

```
sigce53/                        ← raíz del proyecto
│
├── common/                     ← código compartido por TODO el sistema
│   ├── config.php              ← configuración central (rutas, APP_BASE_PATH)
│   ├── conexion.php            ← conexión a la base de datos
│   ├── cfg_server.php          ← puente de compatibilidad → config.php
│   └── ExceptionCRM.php        ← manejo de excepciones
│
├── acceso/                     ← MÓDULO (login) — ya migrado a 8.3
│   ├── login.php               ← pantalla de login
│   ├── entrar.php              ← valida usuario y arma la sesión
│   ├── cerrar.php              ← logout
│   ├── css/  js/  scss/        ← recursos del módulo
│   ├── images/                 ← imágenes del módulo
│   └── _archivo/               ← versiones viejas (no se usan)
│
├── (otros módulos)             ← aún en PHP 5.6, pendientes de migrar
│   └── ...
│
└── index.php                   ← puerta de entrada (aún sin migrar del todo)
```

**Principios:**

1. **Nada de rutas escritas a mano.** Si un archivo necesita saber dónde está el
   proyecto, lo toma de `config.php`, no lo escribe literal.
2. **Un solo lugar para cada cosa.** La conexión a BD, la configuración y las
   rutas viven en `common/`; no se repiten en cada módulo.
3. **Migrar módulo por módulo.** Cada carpeta se migra completa y se prueba antes
   de pasar a la siguiente, sin tocar el resto del sistema.
4. **Lo viejo se archiva, no se borra a medias.** Cada módulo tiene su `_archivo/`
   para las versiones retiradas, hasta confirmar que la nueva funciona.

---

## 5. Pendientes en `acceso` (deuda conocida)

No son bloqueantes —el login funciona— pero conviene saldarlos:

- **Contraseñas en MD5.** Aún se hashean en el navegador y se comparan como MD5.
  Migrar a `password_hash()` requiere tocar cliente, servidor y la tabla a la vez.
- **ID de sesión en la URL** (`?d_s=`). Frágil; conviene modernizarlo.
- **Reglas de horario.** Falta confirmar si las reglas especiales por usuario que
  tenía `entrar010223.php` siguen vigentes.

---

## 6. Cómo migrar el siguiente módulo (patrón probado)

1. Levantar el módulo en el contenedor 8.3.
2. `php -l` sobre cada archivo para cazar errores de sintaxis.
3. Ejecutar con errores visibles para ver el fallo exacto:
   `php -d display_errors=1 archivo.php`
4. Tomar las rutas de `config.php` (no escribirlas a mano).
5. Al tocar la base de datos, usar prepared statements y `try/catch`.
6. Archivar duplicados en `_archivo/` y probar antes de continuar.

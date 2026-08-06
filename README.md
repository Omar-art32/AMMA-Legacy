# AMMA-Legacy — Migración a PHP 8.3

Migración del sistema **SIGCE Legacy** de PHP 5.6 a **PHP 8.3** sobre Docker,
de forma **modular**: se migra y prueba un módulo antes de pasar al siguiente,
sin apagar el sistema.

**Stack:** PHP 8.3 · Apache · MariaDB · Docker Compose

---

## Estado actual

**Ya migrado y funcionando en 8.3**
- `acceso/` — login, autenticación y logout. Más seguro: consultas preparadas
  (antes vulnerable a inyección SQL) y manejo de errores de BD.
- `index.php` (raíz) — página de inicio. El flujo **login → inicio → logout**
  ya está completo en 8.3.
- `common/` e `includes/` — conexión a BD, configuración y funciones comunes.
  Rutas centralizadas en `common/config.php`.

**Pendiente (diagnosticado, aquí paramos)**
- `nmaguey/` — registro de plantaciones y reportes PDF. Solo necesita reemplazar
  `utf8_decode` (105 veces, en 2 archivos de reportes).

**No se migra**
- `libs/`, `librerias/` y carpetas `plugins/` son **librerías de terceros**
  (FPDF, DataTables, etc.). Se reemplazan por Composer cuando toque, no se migran
  archivo por archivo.

---

## Levantar el entorno

```bash
docker compose up -d --build
docker ps
```

| Servicio | Qué es | Acceso |
|---|---|---|
| `web` (amma-legacy-web) | PHP 8.3 + Apache | http://localhost/sigce53/ |
| `mariadb` (mariadb101) | Base de datos | puerto 3307 |
| `phpmyadmin` | Admin visual de la BD | http://localhost:8082 |

> La URL del sistema es `http://localhost/sigce53/` (el código vive en la
> subcarpeta `sigce53`, no en la raíz).

---

## Base de datos

Base `amma` · usuario `root` / `root` · host interno `mariadb`.

```bash
# Importar respaldo
docker exec -i mariadb101 mysql -uroot -proot amma < sigce53/amma.sql

# Exportar respaldo
docker exec mariadb101 mysqldump -uroot -proot amma > amma.sql

# Clonar a una base de pruebas (recomendado antes de migrar un módulo)
docker exec mariadb101 mysqldump -uroot -proot amma > amma_backup.sql
docker exec -i mariadb101 mysql -uroot -proot -e "CREATE DATABASE amma_test"
docker exec -i mariadb101 mysql -uroot -proot amma_test < amma_backup.sql
```

Conexión desde el código (dentro de Docker el host es `mariadb`, no `localhost`):

```php
$conexion = new mysqli("mariadb", "root", "root", "amma");
```

---

## Ver errores (diagnóstico)

Para ver el error exacto de un archivo, se pueden activar los errores desde el
propio código de forma temporal:

```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

Logs del contenedor:

```bash
docker logs amma-legacy-web --tail 100
docker exec -it amma-legacy-web bash   # y dentro: tail -f /var/log/apache2/error.log
```

---

## Método para migrar cada módulo (probado)

1. **Verificar antes de tocar:** revisar el PHP propio (sin plugins) y buscar
   patrones obsoletos.
2. **Compilar** con `php -l archivo.php`.
3. **BD a mano:** consultas preparadas + `try/catch`.
4. **Archivar** duplicados viejos en `_archivo/` (no borrar de golpe).
5. **Probar** contra `amma_test` antes de cerrar.

---

## Estructura de carpetas

```
sigce53/
├── common/          config, conexion a BD, funciones   [migrado]
├── acceso/          login                               [migrado]
├── includes/        helpers compartidos                 [migrado]
├── nmaguey/         registro de plantaciones            [siguiente]
├── (otros modulos)  aun en PHP 5.6
├── libs/ librerias/ terceros -> Composer, NO se migran
└── index.php        pagina de inicio                    [migrado]
```

**Reglas:** rutas siempre desde `common/config.php` (nunca a mano) · cada cosa en
un solo lugar · migrar módulo por módulo · lo viejo se archiva.

---

## Deuda conocida en `acceso` (no bloquea)

- Contraseñas en MD5 (migrar a `password_hash()` toca cliente + servidor + tabla).
- ID de sesión viaja en la URL (`?d_s=`).
- Confirmar si las reglas de horario por usuario del viejo `entrar010223.php`
  siguen vigentes.

---

## Más adelante

- **Rector** (automatización de cambios repetitivos) para migrar `nmaguey` y los
  módulos grandes. Aún no se usa.
- Reemplazar las librerías de terceros duplicadas (`libs/` + `librerias/`) por
  Composer.

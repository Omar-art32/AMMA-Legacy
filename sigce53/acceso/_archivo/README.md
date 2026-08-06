# Archivo de versiones antiguas del módulo `acceso`

Estos archivos se retiraron del flujo activo durante la reestructuración a PHP 8.3.
Se conservan aquí por trazabilidad; **no deben incluirse ni referenciarse** desde
el código en producción. Una vez confirmado que el módulo nuevo funciona, esta
carpeta puede eliminarse por completo (el historial de Git ya la preserva).

| Archivo | Qué era | Por qué se archivó |
|---|---|---|
| `login2.php` | Variante de `login.php` (idioma `en`, otro fondo, orden de columnas distinto) | Duplicado casi idéntico al canónico. Se conserva `login.php`. |
| `login_old.php` | Versión anterior del login | Obsoleta. Además cargaba `css/style.css` dos veces (bug). |
| `entrar010223.php` | Copia de `entrar.php` fechada ~01/02/2023 | **Generaba rutas `/sigce/` (otro despliegue), no `/sigce53/`.** Tenía reglas de horario distintas embebidas en el SQL y usaba `utf8_decode()`. Corresponde a una instalación diferente. |
| `cfg_server.php.old` | Configuración de servidor original | Reemplazado por `common/config.php`, que centraliza la ruta base en `APP_BASE_PATH` y añade detección de protocolo. |

## Diferencia crítica entre `entrar.php` y `entrar010223.php`

Antes de descartar definitivamente `entrar010223.php`, confirma con el negocio
cuál juego de **reglas de horario** es el vigente:

- `entrar.php` (canónico) valida el horario en PHP, con ventanas por día
  (`horaInicial_l_v`, `horaInicial_s`, `horaInicial_d`).
- `entrar010223.php` metía el filtro de horario dentro del SQL y tenía reglas
  especiales para ciertos `id_us` (29, 34, 26, 25, 33) los fines de semana.

Si esas reglas especiales por usuario siguen siendo necesarias, hay que
portarlas al `entrar.php` nuevo antes de borrar este archivo.

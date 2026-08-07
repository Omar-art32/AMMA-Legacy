# AMMA-Legacy

Versión original del sistema AMMA ejecutándose mediante Docker.

## Tecnologías

- PHP 5.6
- Apache
- MariaDB 10.1
- phpMyAdmin

---

## Requisitos

- Docker
- Docker Compose

---

## Estructura

```
AMMA-Legacy/
│
├── docker-compose.yml
├── Dockerfile
├── README.md
└── sigce53/
```

---

## Levantar el proyecto

```bash
docker compose up -d
```

Verificar los contenedores.

```bash
docker ps
```

---

## Importar la base de datos

```bash
docker exec -i mariadb101 mysql -uroot -proot amma < sigce53/amma.sql
```

---

## Accesos

Sistema

```
http://localhost/sigce53
```

phpMyAdmin

```
http://localhost:8082
```

Servidor

```
localhost
```

Usuario

```
root
```

Contraseña

```
root
```

Base de datos

```
amma
```

---

## Detener el proyecto

```bash
docker compose down
```

---

## Eliminar completamente (incluyendo la base de datos)

```bash
docker compose down -v
```

---


## Avance de migración a PHP 8.3

Migración modular (un módulo a la vez). Ya migrado y funcionando en 8.3:

- `acceso/` — login/autenticación. Ahora con consultas preparadas y manejo de errores de BD.
- `index.php` (raíz) — página de inicio. El flujo login → inicio → logout está completo en 8.3.
- `common/` e `includes/` — conexión, configuración y funciones comunes.

Qué movimos:

- Rutas centralizadas en `common/config.php` (antes `/sigce53` estaba escrito a mano).
- `common/cfg_server.php` quedó como puente que reenvía a `config.php` (para no romper el código viejo).
- Duplicados y versiones viejas archivados en `acceso/_archivo/` (`login2.php`, `login_old.php`, `entrar010223.php`, `cfg_server.php.old`).

Pendiente: `nmaguey/ y demas modulos de negocio` .
Las librerías de terceros (`libs/`, `librerias/`, `plugins/`) no se migran: se reemplazan por Composer.

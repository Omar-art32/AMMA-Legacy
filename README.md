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

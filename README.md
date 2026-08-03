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

## Notas

Este repositorio representa la versión original del sistema.

No realizar cambios estructurales importantes.

Se utiliza únicamente para mantenimiento y referencia.

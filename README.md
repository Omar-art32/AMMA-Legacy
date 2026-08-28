# AMMA-Legacy (SIGCE)

Sistema de Gestión y Control de la Asociación de Maguey y Mezcal Artesanal.  
PHP 8.3 + MariaDB 10.11 + Docker.

---

## Instalación

### 1. Clonar el repositorio

```bash
git clone <URL_DEL_REPOSITORIO>
cd AMMA-Legacy
```

### 2. Levantar los contenedores

```bash
docker compose up -d --build
```

### 3. Importar la base de datos

```bash
# Windows (PowerShell):
Get-Content amma.sql -Raw | docker exec -i mariadb101 mariadb -uroot -proot --default-character-set=utf8mb4 amma

# Linux/Mac:
docker exec -i mariadb101 mariadb -uroot -proot --default-character-set=utf8mb4 amma < amma.sql
```

### 4. Instalar dependencias PHP

```bash
docker exec -w /var/www/html/sigce53 amma-legacy-web composer install --no-dev
```

### 5. Abrir el sistema

```
http://localhost/sigce53/
```

---

## Credenciales de la base de datos

| Parámetro | Valor |
|---|---|
| Host | mariadb (dentro de Docker) / localhost:3307 (externo) |
| Usuario | root |
| Contraseña | root |
| Base de datos | amma |
| phpMyAdmin | http://localhost:8082 |

---

## Detener

```bash
docker compose down
```

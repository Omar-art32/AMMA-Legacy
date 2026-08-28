# AMMA-Legacy (SIGCE)

Sistema de Gestión y Control de la Asociación de Maguey y Mezcal Artesanal.
PHP 8.3 + MariaDB 10.11 + Docker.

---

## Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/Omar-art32/AMMA-Legacy.git
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

### 4. Tablas adicionales requeridas

> **Importante:** el archivo `amma.sql` no contiene completas las tablas `localidades` y `rv_produccion_ensamble`.
>
> Los archivos localidades.sql y rv_produccion_ensamble.sql se encuentran dentro de la carpeta sigce53 del repositorio.

> Ejecuta los siguientes comandos desde la carpeta sigce53:

#### Tabla `localidades`

```bash
docker exec -i mariadb101 mariadb -uroot -proot amma -e "DROP TABLE IF EXISTS localidades;"

docker exec -i mariadb101 mariadb -uroot -proot --default-character-set=utf8mb4 amma < localidades.sql
```

#### Tabla `rv_produccion_ensamble`

```bash
docker exec -i mariadb101 mariadb -uroot -proot amma -e "DROP TABLE IF EXISTS rv_produccion_ensamble;"

docker exec -i mariadb101 mariadb -uroot -proot --default-character-set=utf8mb4 amma < rv_produccion_ensamble.sql
```

### 5. Instalar dependencias PHP

```bash
docker exec -w /var/www/html/sigce53 amma-legacy-web composer install --no-dev
```

### 6. Abrir el sistema

```text
http://localhost/sigce53/
```

---

## Credenciales de la base de datos

| Parámetro     | Valor                                                 |
| ------------- | ----------------------------------------------------- |
| Host          | mariadb (dentro de Docker) / localhost:3307 (externo) |
| Usuario       | root                                                  |
| Contraseña    | root                                                  |
| Base de datos | amma                                                  |
| phpMyAdmin    | http://localhost:8082                                 |

---

## Detener

```bash
docker compose down
```


# AMMA-Legacy (SIGCE)

Sistema de Gestión y Control de la Asociación de Maguey y Mezcal Artesanal.

**Tecnologías principales:**

* PHP 8.3
* MariaDB 10.11
* Docker
* Composer
* PhpSpreadsheet

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

Esto crea los contenedores necesarios para el sistema:

* `amma-legacy-web` — Apache + PHP 8.3
* `mariadb101` — MariaDB 10.11
* `amma-legacy-pma` — phpMyAdmin

### 3. Importar la base de datos

El archivo principal de la base de datos es `amma.sql`.

#### Windows (PowerShell)

Ejecutar desde la raíz del proyecto:

```powershell
Get-Content amma.sql -Raw | docker exec -i mariadb101 mariadb -uroot -proot --default-character-set=utf8mb4 amma
```

#### Linux / Mac

```bash
docker exec -i mariadb101 mariadb -uroot -proot --default-character-set=utf8mb4 amma < amma.sql
```

### 4. Importar tablas adicionales

> **Importante:** el archivo `amma.sql` no contiene completas las tablas `localidades` y `rv_produccion_ensamble`.
>
> Los archivos `localidades.sql` y `rv_produccion_ensamble.sql` se encuentran dentro de la carpeta `sigce53`.

Primero entra a la carpeta:

```bash
cd sigce53
```

#### Tabla `localidades`

```bash
docker exec -i mariadb101 mariadb -uroot -proot amma -e "DROP TABLE IF EXISTS localidades;"
```

Windows (PowerShell):

```powershell
Get-Content localidades.sql -Raw | docker exec -i mariadb101 mariadb -uroot -proot --default-character-set=utf8mb4 amma
```

Linux / Mac:

```bash
docker exec -i mariadb101 mariadb -uroot -proot --default-character-set=utf8mb4 amma < localidades.sql
```

#### Tabla `rv_produccion_ensamble`

```bash
docker exec -i mariadb101 mariadb -uroot -proot amma -e "DROP TABLE IF EXISTS rv_produccion_ensamble;"
```

Windows (PowerShell):

```powershell
Get-Content rv_produccion_ensamble.sql -Raw | docker exec -i mariadb101 mariadb -uroot -proot --default-character-set=utf8mb4 amma
```

Linux / Mac:

```bash
docker exec -i mariadb101 mariadb -uroot -proot --default-character-set=utf8mb4 amma < rv_produccion_ensamble.sql
```

### 5. Instalar dependencias PHP

Desde la raíz del proyecto:

```bash
docker exec -w /var/www/html/sigce53 amma-legacy-web composer install --no-dev
```

Composer instalará las dependencias definidas en `sigce53/composer.json` utilizando las versiones especificadas en `sigce53/composer.lock`.

### 6. Abrir el sistema

En el navegador:

```text
http://localhost/sigce53/
```

---


## Credenciales de la base de datos

| Parámetro             | Valor                   |
| --------------------- | ----------------------- |
| Host dentro de Docker | `mariadb`               |
| Host externo          | `localhost:3307`        |
| Usuario               | `root`                  |
| Contraseña            | `root`                  |
| Base de datos         | `amma`                  |
| phpMyAdmin            | `http://localhost:8082` |

### phpMyAdmin

Acceder desde:

```text
http://localhost:8082
```

---

## Detener el sistema

Para detener los contenedores:

```bash
docker compose down
```

Para volver a iniciarlos:

```bash
docker compose up -d
```

> Los datos de MariaDB se almacenan en el volumen Docker `mariadb_data`, por lo que `docker compose down` no elimina la base de datos.

---

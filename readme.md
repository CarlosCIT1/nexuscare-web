# NexusCare - Migración a PostgreSQL

Este proyecto corresponde a una **migración de la base de datos de MySQL a PostgreSQL** realizada para la materia **Integradora II**.

El proyecto original fue desarrollado utilizando MySQL y puede consultarse en el siguiente repositorio:

https://github.com/2023371168-MITL/Pagina-web-de-Metricas/tree/main

En esta versión se realizaron las modificaciones necesarias para trabajar con **PostgreSQL**, manteniendo el funcionamiento principal de la aplicación web NexusCare.

## Requisitos

Antes de ejecutar el proyecto se necesita tener instalado:

- XAMPP
- PHP
- PostgreSQL
- pgAdmin 4
- Extensión de PostgreSQL para PHP ("pdo_pgsql`")

## Instalación y ejecución

### 1. Crear la base de datos

Abrir **pgAdmin 4** y crear una nueva base de datos con el nombre:

**nexuscare**

Dentro de la carpeta `db` del repositorio se encuentra el archivo `database.sql`.

Una vez creada la base de datos, abrir el **Query Tool** de la base de datos `nexuscare` y ejecutar el archivo `database.sql`.

### 2. Insertar datos de prueba

Dentro de la carpeta `db` del repositorio se encuentra el archivo `datos.sql`.

Una vez creada la estructura de la base de datos, ejecutar este archivo sobre la base de datos `nexuscare`.

Este archivo contiene **datos de ejemplo** para poder probar el funcionamiento de la aplicación, como usuarios, roles, especialidades, servicios médicos y citas.

Este paso es opcional si se desea comenzar con una base de datos vacía.

### 3. Configurar PostgreSQL en XAMPP

Para que PHP pueda conectarse a PostgreSQL es necesario habilitar las extensiones correspondientes en XAMPP.

Abrir el archivo "php.ini" desde XAMPP y verificar que las extensiones de PostgreSQL estén habilitadas:

"ini"
extension=pdo_pgsql
extension=pgsql
Si aparecen comentadas con " ; ", quitar el punto y coma.

Después de realizar el cambio, reiniciar **Apache** desde el panel de XAMPP.

### 4. Colocar el proyecto web en XAMPP

Al descargar este repositorio, se obtiene la carpeta:

`nexuscare-web`

Dentro de ella existen dos carpetas principales:

- `nexuscare`: contiene el proyecto web.
- `db`: contiene los archivos SQL de la base de datos.

Para ejecutar el proyecto con XAMPP, **únicamente se debe copiar la carpeta `nexuscare` dentro de `C:\xampp\htdocs\`**.

No se debe copiar la carpeta completa `nexuscare-web` dentro de `htdocs`.

La estructura debe quedar de la siguiente manera:

`C:\xampp\htdocs\nexuscare\`

La carpeta `db` **no necesita colocarse dentro de `htdocs`**, ya que únicamente contiene los archivos SQL utilizados para crear y poblar la base de datos.

### 5. Configurar la conexión a PostgreSQL

Verificar el archivo:

`tools/mypathdb.php`

y configurar los datos correspondientes a la instalación local de PostgreSQL:

- Host
- Puerto
- Nombre de la base de datos: `nexuscare`
- Usuario
- Contraseña

La aplicación utiliza **PDO con PostgreSQL** para realizar la conexión y ejecutar las consultas.

### 6. Ejecutar el proyecto

Con **Apache** iniciado desde XAMPP, abrir el navegador y acceder a:

"http://localhost/nexuscare/login.php"
Desde esta página se puede iniciar sesión y acceder al sistema.
## Tecnologías utilizadas

* PHP
* PostgreSQL
* PDO
* HTML
* CSS
* JavaScript
* XAMPP
* pgAdmin 4

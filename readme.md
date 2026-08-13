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

Crear una base de datos en PostgreSQL utilizando **pgAdmin 4**.

Posteriormente, ejecutar el archivo "database.sql".

Este archivo contiene la creación de las tablas, relaciones, función y procedimiento almacenado necesarios para el funcionamiento del sistema.

También se puede abrir el archivo y copiar su contenido directamente en el **Query Tool de pgAdmin** para ejecutarlo.

### 2. Insertar datos de prueba

Una vez creada la estructura de la base de datos, ejecutar el archivo "datos.sql".

Este archivo contiene **datos de ejemplo** para poder probar el funcionamiento de la aplicación, como usuarios, roles, especialidades, servicios médicos y citas.

Este paso es opcional si se desea comenzar con una base de datos vacía.

### 3. Configurar PostgreSQL en XAMPP

Para que PHP pueda conectarse a PostgreSQL es necesario habilitar las extensiones correspondientes en XAMPP.

Abrir el archivo "php.ini" desde XAMPP y verificar que las extensiones de PostgreSQL estén habilitadas:

ini
extension=pdo_pgsql
extension=pgsql
Si aparecen comentadas con " ; ", quitar el punto y coma.

Después de realizar el cambio, reiniciar **Apache** desde el panel de XAMPP.

### 4. Colocar el proyecto en XAMPP

Copiar la carpeta del proyecto dentro de:

"C:\xampp\htdocs\"

La carpeta debe quedar con el nombre:

"C:\xampp\htdocs\nexuscare\"

No es necesario utilizar directamente la carpeta del repositorio descargado. Si la carpeta descargada tiene otro nombre, como "nexuscare-web" , puede renombrarse a "nexuscare".

### 5. Configurar la conexión a PostgreSQL

Verificar el archivo:

"tools/mypathdb.php"

y configurar los datos correspondientes a la instalación local de PostgreSQL:

* Host
* Puerto
* Nombre de la base de datos
* Usuario
* Contraseña

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

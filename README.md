
PHP Developer Test
========================
1. Instalar PHP 7.2
2. Instalar MySQL.
3. Instalar Symfony.
4. Instalar Composer.
5. Desde la raíz del proyecto, entrar en el directorio ./app/config/, duplicar el fichero parameters.yml.dist a uno llamado parameters.yml y rellenar con nuestras credenciales de MySQL.
6. Desde la raíz del proyecto, ejecutar php bin/console doctrine:database:create e importar en la base de datos creada el dump de la ruta ./sql/dump.sql.
7. Desde el directorio del proyecto, ejecutar: $ composer update
8. Ejecutar php bin/console server:start para arrancar el servidor.
9. Abrir en un navegador la URL http://127.0.0.1:8000 y navegar por la web.
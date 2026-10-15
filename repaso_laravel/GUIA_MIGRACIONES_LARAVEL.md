# Guia de Estudio: Migraciones en Laravel

Documento estructurado de preparacion para evaluacion practica y teorica sobre el sistema de migraciones de Laravel, basado en la documentacion oficial de Laravel.

---

## 1. Que son las migraciones

En Laravel, las migraciones son el **sistema de control de versiones para la base de datos**.

Asi como Git permite rastrear, ramificar y revertir cambios en el codigo fuente de una aplicacion, las migraciones permiten definir, modificar y versionar el esquema de la base de datos mediante codigo PHP, sin necesidad de escribir sentencias SQL manuales ni depender de interfaces graficas como phpMyAdmin.

Cada archivo de migracion representa un cambio incremental en el esquema (creacion de una tabla, adicion de columnas, eliminacion de indices o claves foraneas).

---

## 2. Para que sirven

Las migraciones resuelven los problemas criticos de sincronizacion y persistencia de bases de datos en equipos de desarrollo y entornos de despliegue:

- **Estandarizacion del esquema:** Permite que cualquier desarrollador clone el repositorio y obtenga exactamente la misma estructura de base de datos ejecutando un solo comando.
- **Historial de cambios:** Mantiene un registro cronologico y determinista de como ha evolucionado la base de datos a lo largo del tiempo.
- **Capacidad de reversion (Rollback):** Permite deshacer cambios previos en caso de error o ajuste de requerimientos, devolviendo la base de datos a un estado estable anterior.
- **Independencia del motor de base de datos:** El codigo PHP escrito con el constructor de esquemas de Laravel (`Schema`) es agnostico; funciona sin cambios en MySQL, PostgreSQL, SQLite o SQL Server.
- **Eliminacion de scripts SQL manuales:** Evita compartir archivos `.sql` desactualizados o ejecutar sentencias `ALTER TABLE` a ciegas en entornos de produccion o pruebas.

---

## 3. Como funcionan internamente

Cuando ejecutas las migraciones en Laravel:

1. Laravel consulta una tabla especial llamada `migrations` dentro de tu base de datos.
2. Si la tabla `migrations` no existe, Laravel la crea automaticamente en la primera ejecucion.
3. Esta tabla contiene columnas como `id`, `migration` (el nombre del archivo) y `batch` (el numero de lote en el que fue ejecutada).
4. Laravel compara los archivos fisicos en la carpeta `database/migrations/` con los registros de la tabla `migrations`.
5. Unicamente ejecuta los archivos que aun no figuren registrados en dicha tabla.

---

## 4. Estructura de un archivo de migracion

Los archivos de migracion se alojan en la ruta:
`database/migrations/`

El nombre del archivo sigue el patron:
`YYYY_MM_DD_HHMMSS_accion_nombretabla_table.php`

El prefijo de fecha y hora asegura que Laravel ejecute las migraciones en estricto orden cronologico.

### Anatomia del archivo (Clase anonima)

A partir de versiones modernas de Laravel, las migraciones se definen como clases anonimas que extienden `Illuminate\Database\Migrations\Migration`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones (Aplica los cambios).
     */
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 8, 2);
            $table->integer('stock')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Revierte las migraciones (Deshace los cambios).
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
```

### Reglas clave de los metodos:
- **`up()`:** Define las operaciones que se aplican al ejecutar `php artisan migrate` (ej. crear tabla, agregar columna).
- **`down()`:** Define exactamente la operacion inversa para revertir lo hecho en `up()` al ejecutar `php artisan migrate:rollback` (ej. eliminar tabla, borrar columna).

---

## 5. Tipos de datos mas comunes (Cheatsheet para el examen)

Dentro del callback `function (Blueprint $table) { ... }`, se invocan metodos sobre la instancia `$table`:

| Tipo de Dato / Metodo | Equivalente SQL tipico | Ejemplo de Uso | Descripcion |
| :--- | :--- | :--- | :--- |
| `$table->id()` | BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY | `$table->id();` | Clave primaria autoincremental estandar |
| `$table->string('col', [longitud])` | VARCHAR(longitud) | `$table->string('titulo', 150);` | Texto corto (por defecto longitud 255 si se omite) |
| `$table->text('col')` | TEXT | `$table->text('contenido');` | Texto extenso |
| `$table->longText('col')` | LONGTEXT | `$table->longText('payload');` | Texto muy extenso |
| `$table->integer('col')` | INTEGER | `$table->integer('cantidad');` | Numero entero con signo |
| `$table->unsignedInteger('col')` | INT UNSIGNED | `$table->unsignedInteger('votos');` | Numero entero positivo |
| `$table->bigInteger('col')` | BIGINT | `$table->bigInteger('contador');` | Entero grande |
| `$table->decimal('col', M, D)` | DECIMAL(M, D) | `$table->decimal('precio', 10, 2);` | Numero decimal exacto (10 digitos en total, 2 decimales) |
| `$table->float('col')` | FLOAT | `$table->float('calificacion');` | Numero de punto flotante |
| `$table->boolean('col')` | BOOLEAN / TINYINT(1) | `$table->boolean('activo');` | Verdadero / Falso (1 o 0) |
| `$table->date('col')` | DATE | `$table->date('fecha_nacimiento');` | Fecha (YYYY-MM-DD) |
| `$table->dateTime('col')` | DATETIME | `$table->dateTime('fecha_evento');` | Fecha y hora combinada |
| `$table->timestamp('col')` | TIMESTAMP | `$table->timestamp('publicado_en');` | Marca de tiempo |
| `$table->timestamps()` | TIMESTAMP `created_at` y `updated_at` | `$table->timestamps();` | Agrega automaticamente las dos columnas de auditoria de Eloquent |
| `$table->softDeletes()` | TIMESTAMP `deleted_at` | `$table->softDeletes();` | Soporte para borrado logico de Eloquent |

---

## 6. Modificadores de columnas (Restricciones y atributos)

Los modificadores se encadenan al metodo de la columna para agregar restricciones o valores por defecto:

```php
// Permite que la columna acepte valores NULL
$table->string('telefono', 20)->nullable();

// Impone indice de unicidad (no duplicados)
$table->string('email')->unique();

// Asigna un valor por defecto
$table->boolean('activo')->default(true);
$table->integer('intentos')->default(0);

// Ubica la columna despues de otra existente (solo al modificar tablas en MySQL)
$table->string('direccion')->after('email');

// Marca un entero como positivo exclusivamente
$table->integer('edad')->unsigned();

// Agrega comentario en la base de datos
$table->string('codigo')->comment('Codigo interno de inventario');
```

---

## 7. Claves foraneas y relaciones (Pregunta frecuente de examen)

Existen dos sintaxis en Laravel. La moderna y concisa (`foreignId`), y la clasica (`unsignedBigInteger` + `foreign`).

### Opcion A: Sintaxis moderna recomendada (Laravel 8, 9, 10, 11, 12, 13)

```php
// Crea la columna 'categoria_id', la marca como unsignedBigInteger,
// y genera la restriccion foranea hacia la tabla 'categorias' sobre la columna 'id'
$table->foreignId('categoria_id')
      ->constrained('categorias')
      ->onDelete('cascade'); // o cascadeOnDelete()
```

Metodos auxiliares rapidos:
- `->cascadeOnDelete()`: Si se borra la categoria, se borran los productos asociados (`CASCADE`).
- `->nullOnDelete()`: Si se borra la categoria, el campo queda en `NULL` (requiere `->nullable()`).
- `->restrictOnDelete()`: Impide borrar la categoria si tiene productos asociados.

```php
// Ejemplo con llave foranea opcional:
$table->foreignId('categoria_id')
      ->nullable()
      ->constrained('categorias')
      ->nullOnDelete();
```

### Opcion B: Sintaxis clasica o manual

```php
$table->unsignedBigInteger('categoria_id');

$table->foreign('categoria_id')
      ->references('id')
      ->on('categorias')
      ->onDelete('cascade');
```

### Regla critica de orden de migraciones (Importante para evitar fallos en el parcial):
La tabla padre (a la que se hace referencia, ej. `categorias`) **debe crearse antes en el tiempo** que la tabla hija que contiene la clave foranea (ej. `productos`).
Si la migracion de `productos` tiene una marca de tiempo anterior a `categorias`, la base de datos arrojara el error:
`Foreign key constraint is incorrectly formed`.

---

## 8. Modificar una tabla existente (Agregar o eliminar columnas)

Si en el parcial te piden "agrega un campo a una tabla ya existente sin recrearla":

### 1. Generar la migracion de alteracion:
```bash
php artisan make:migration add_telefono_to_personas_table --table=personas
```

### 2. Implementar `up()` y `down()` usando `Schema::table`:

```php
public function up(): void
{
    Schema::table('personas', function (Blueprint $table) {
        $table->string('telefono', 20)->nullable()->after('email');
    });
}

public function down(): void
{
    Schema::table('personas', function (Blueprint $table) {
        $table->dropColumn('telefono');
    });
}
```

---

## 9. Comandos Artisan indispensables

| Comando | Proposito | Cuando usarlo en el parcial |
| :--- | :--- | :--- |
| `php artisan make:migration create_alumnos_table` | Crea un archivo de migracion para nueva tabla | Al iniciar el diseno de una entidad |
| `php artisan make:model Alumno -m` | Crea el modelo y su migracion simultaneamente | Forma mas rapida y profesional de trabajar |
| `php artisan make:model Alumno -mcr` | Crea Modelo, Migracion y Controlador Resource | Si piden la API o CRUD completo |
| `php artisan migrate` | Ejecuta las migraciones pendientes | Despues de escribir las columnas en el archivo |
| `php artisan migrate:status` | Lista todas las migraciones y su estado (Ran / Pending) | Para verificar si se ejecutaron correctamente |
| `php artisan migrate:rollback` | Revierte el ultimo lote (batch) de migraciones | Si acabas de migrar y notaste un error |
| `php artisan migrate:rollback --step=1` | Revierte unicamente la ultima migracion | Para corregir un solo archivo |
| `php artisan migrate:fresh` | **Comando de rescate:** Borra todas las tablas y corre todo de cero | Si la base de datos se desincroniza durante el examen |
| `php artisan migrate:fresh --seed` | Borra todo, corre migraciones y ejecuta los seeders | Si el parcial exige cargar datos de prueba iniciales |

---

## 10. Guia paso a paso para el examen (Sin asistencia de IA)

Sigue esta secuencia exacta durante el parcial:

### Paso 1: Configurar la conexion en `.env`
Antes de ejecutar cualquier comando de Artisan, verifica que Laravel pueda comunicarse con tu motor de base de datos local.
Abre el archivo `.env` en la raiz del proyecto:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nombre_de_la_bd_del_parcial
DB_USERNAME=root
DB_PASSWORD=
```

Asegurate de:
1. Tener iniciado el servicio de MySQL (XAMPP, Laragon o servicio local).
2. Haber creado la base de datos en MySQL con el nombre exacto especificado en `DB_DATABASE`.

### Paso 2: Crear el modelo y la migracion
En la terminal (dentro de la carpeta del proyecto Laravel), ejecuta:

```bash
php artisan make:model Producto -m
```

Salida esperada:
```
INFO  Model [app/Models/Producto.php] created successfully.
INFO  Migration [database/migrations/2026_10_15_000000_create_productos_table.php] created successfully.
```

### Paso 3: Ubicar y editar la migracion
Navega a `database/migrations/` y abre el archivo recien creado.
Verifica que el metodo `up()` contenga `Schema::create`:

```php
public function up(): void
{
    Schema::create('productos', function (Blueprint $table) {
        $table->id();
        $table->string('codigo', 50)->unique();
        $table->string('nombre', 120);
        $table->text('descripcion')->nullable();
        $table->decimal('precio', 10, 2);
        $table->integer('stock')->default(0);
        $table->boolean('activo')->default(true);
        $table->timestamps();
    });
}
```

Verifica que el metodo `down()` elimine la tabla:
```php
public function down(): void
{
    Schema::dropIfExists('productos');
}
```

### Paso 4: Configurar el modelo Eloquent (`$fillable`)
Para que tu API o formulario pueda guardar estos datos, abre el modelo en `app/Models/Producto.php` y declara la propiedad `$fillable`:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'precio',
        'stock',
        'activo',
    ];
}
```

### Paso 5: Ejecutar la migracion
En la terminal corre:

```bash
php artisan migrate
```

Salida esperada:
```
INFO  Running migrations.
2026_10_15_000000_create_productos_table .................... RUNNING
2026_10_15_000000_create_productos_table ................. 12.45ms DONE
```

### Paso 6: Validar el estado
Ejecuta:
```bash
php artisan migrate:status
```
Comprueba que tu archivo marque `Ran: Yes` con su respectivo numero de lote.

---

## 11. Que hacer si cometes un error durante el examen

### Escenario A: Escribiste mal un campo y ya corriste `php artisan migrate`
**Solucion rapida (si el error fue en la ultima migracion):**
1. Ejecuta en consola:
   ```bash
   php artisan migrate:rollback
   ```
2. Modifica el archivo de la migracion corrigiendo el nombre o tipo de columna.
3. Vuelve a ejecutar:
   ```bash
   php artisan migrate
   ```

### Escenario B: Hiciste varios cambios, la base de datos dio error y quedo inconsistente
**Solucion total de rescate (entorno local de parcial):**
Si no tienes datos criticos que conservar y necesitas dejar la base de datos limpia y funcional:
```bash
php artisan migrate:fresh
```
Este comando borra todas las tablas de la base de datos y vuelve a correr cada migracion desde el principio.

### Escenario C: Error `Base table or view already exists: 1050 Table 'xxx' already exists`
- **Causa:** La tabla ya existe fisicamente en MySQL pero no esta registrada en la tabla `migrations` (tal vez creada manualmente o por un script previo).
- **Solucion:** Ejecutar `php artisan migrate:fresh` o eliminar manualmente la tabla conflictiva desde phpMyAdmin/consola MySQL y volver a correr `php artisan migrate`.

### Escenario D: Error `General error: 1005 Can't create table ... (errno: 150 "Foreign key constraint is incorrectly formed")`
- **Causa 1:** La tabla padre a la que apunta la clave foranea todavia no ha sido creada (la migracion de la tabla foranea tiene una fecha anterior a la tabla principal).
- **Causa 2:** El tipo de dato no coincide (por ejemplo, la clave primaria es `id()` que es BIGINT UNSIGNED, y la foranea se declaro como `integer()` que es INT normal).
- **Solucion:** Usar siempre `$table->foreignId('tabla_id')->constrained('tablas')` y asegurar el orden cronologico de creacion renombrando el timestamp del archivo si fuese necesario.

### Escenario E: Error `Access denied for user` o `Unknown database`
- **Causa:** Datos de acceso erroneos en el `.env` o la base de datos no ha sido creada en MySQL.
- **Solucion:** Crear la base de datos en MySQL con `CREATE DATABASE nombre_bd;` y revisar usuario/clave en `.env`. Si modificas `.env`, recuerda limpiar la cache con:
  ```bash
  php artisan config:clear
  ```

---

## 12. Plantilla rapida de memoria (Codigo para escribir a mano si es en papel)

Si el examen es escrito en papel o en un editor sin autocompletado, esta es la estructura minima obligatoria que debes memorizar:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nombre_tabla', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_columna');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nombre_tabla');
    }
};
```

# Resumen Examen - Migraciones en Laravel

---

## Las 2 preguntas tecnicas mas probables

**1. Que son las migraciones en Laravel?**

Son el sistema de control de versiones para la base de datos. Permiten definir y modificar el esquema de la BD mediante codigo PHP, sin escribir SQL manual. Se pueden revertir y garantizan que todos los desarrolladores tengan la misma estructura de BD.

**2. Como funcionan internamente?**

Laravel mantiene una tabla llamada `migrations` en la BD. Cada vez que corres `php artisan migrate`, Laravel compara los archivos en `database/migrations/` contra esa tabla y solo ejecuta los que aun no han corrido.

---

## Como realizar las migraciones (flujo de memoria)

```bash
# 1. Crear la BD en MySQL primero

# 2. Configurar .env
DB_DATABASE=nombre_bd
DB_USERNAME=root
DB_PASSWORD=

# 3. Crear modelo + migracion
php artisan make:model NombreModelo -mc

# 4. Editar la migracion en database/migrations/
#    Definir columnas en up() con Schema::create

# 5. Correr la migracion
php artisan migrate

# 6. Verificar
php artisan migrate:status
```

---

## Estructura minima de una migracion

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

---

## Si algo falla en el examen

| Error | Solucion |
|---|---|
| Escribi mal una columna | `php artisan migrate:rollback` → corregir → `php artisan migrate` |
| BD inconsistente | `php artisan migrate:fresh` |
| "Unknown database" | Crear la BD en MySQL primero |
| Cambias el `.env` | `php artisan config:clear` |

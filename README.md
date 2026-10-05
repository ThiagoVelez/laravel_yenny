# API REST de Personas - Laravel

Este repositorio contiene la implementación completa de la **Guía Práctica: Construcción de API REST en Laravel** (Programación Avanzada | Eloquent ORM & Postman).

---

## 🏛️ Arquitectura del Proyecto

El flujo de la aplicación sigue el patrón MVC y las convenciones de Laravel para APIs RESTful:

```
[ Cliente / Postman ]
       │  POST http://127.0.0.1:8000/api/personas
       ▼
[ routes/api.php ]
       │
       ▼
[ app/Http/Controllers/PersonaController.php ]
  ├── Validación de reglas (nombres, apellidos, email único, telefono)
  └── Inserción a través del ORM
       │
       ▼
[ app/Models/Persona.php ] (Eloquent ORM con $fillable)
       │
       ▼
[ MySQL Database: laravel_api_db ] (Tabla `personas`)
```

---

## 🛠️ Tecnologías Utilizadas

- **PHP**: 8.3+
- **Laravel Framework**: 11+
- **Base de Datos**: MySQL / MariaDB (puerto 3306)
- **Herramienta de Pruebas**: Postman & PHPUnit / Artisan Test

---

## 📋 Estructura de la Base de Datos

Migración: `database/migrations/YYYY_MM_DD_XXXXXX_create_personas_table.php`

| Campo | Tipo | Restricción |
|---|---|---|
| `id` | BigIncrements | Primary Key |
| `nombres` | String(100) | Requerido |
| `apellidos` | String(100) | Requerido |
| `email` | String | Requerido, Único |
| `telefono` | String(20) | Opcional (Nullable) |
| `created_at` / `updated_at` | Timestamps | Automático |

---

## 🚀 Puesta en Marcha Local

### 1. Clonar el repositorio
```bash
git clone https://github.com/ThiagoVelez/laravel_yenny.git
cd laravel_yenny/api-personas
```

### 2. Instalar dependencias
```bash
composer install
```

### 3. Configurar entorno y base de datos
Copia el archivo `.env.example` a `.env`:
```bash
cp .env.example .env
```
Configura los parámetros de tu MySQL en el archivo `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_api_db
DB_USERNAME=root
DB_PASSWORD=
```
Crea la base de datos `laravel_api_db` en tu gestor MySQL (phpMyAdmin, DBeaver, Laragon, MySQL Workbench, etc.).

### 4. Generar App Key y Ejecutar Migraciones
```bash
php artisan key:generate
php artisan migrate
```

### 5. Levantar el Servidor de Desarrollo
```bash
php artisan serve
```
El servidor estará disponible en `http://127.0.0.1:8000`.

---

## 📬 Pruebas con Postman

En la raíz del proyecto se incluye el archivo listo para importar en Postman:
- `Postman_Collection.json`

### Especificaciones del Endpoint:
- **Método**: `POST`
- **URL**: `http://127.0.0.1:8000/api/personas`
- **Headers**:
  - `Accept`: `application/json`
  - `Content-Type`: `application/json`
- **Body (raw JSON)**:
```json
{
  "nombres": "Ana María",
  "apellidos": "Gómez Pérez",
  "email": "ana.gomez@ejemplo.com",
  "telefono": "3001234567"
}
```

### Respuesta Exitosa (`Status 201 Created`):
```json
{
  "mensaje": "Persona registrada exitosamente",
  "data": {
    "nombres": "Ana María",
    "apellidos": "Gómez Pérez",
    "email": "ana.gomez@ejemplo.com",
    "telefono": "3001234567",
    "updated_at": "2026-10-05T14:18:08.000000Z",
    "created_at": "2026-10-05T14:18:08.000000Z",
    "id": 1
  }
}
```

---

## 🧪 Pruebas Automatizadas

El proyecto cuenta con pruebas de integración en `tests/Feature/PersonaApiTest.php`. Para ejecutarlas:
```bash
php artisan test --filter=PersonaApiTest
```

# Entorno de prueba con MySQL

## 1. Crear la base de datos

```sql
CREATE DATABASE control_taller_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## 2. Preparar variables de entorno

1. Copia `.env.testing.example` a `.env`.
2. Ajusta `DB_USERNAME` y `DB_PASSWORD` segun tu instalacion local de MySQL.

## 3. Instalar dependencias

```bash
composer install
npm install
```

## 4. Inicializar la aplicacion

```bash
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
```

## 5. Usuarios de prueba

Panel admin:

- `admin@taller.test` / `admin12345`
- `admin2@taller.test` / `admin22345`

Panel tecnico:

- `tecnico@taller.test` / `tecnico12345`
- `tecnico2@taller.test` / `tecnico22345`

Frontend:

- `Acceso principal` / `consulta12345`
- `Cliente VIP` / `clientevip123`

## 6. Datos demo sembrados

Se cargan trabajos en estos estados:

- terminado, pendiente de pago y visible en frontend;
- en proceso y visible para tecnicos;
- terminado con comentario y pago enviado;
- terminado y pagado;
- en proceso sin pagar.

## 7. Arranque local

```bash
composer run dev
```

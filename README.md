# Laravel 12 Query Debugger

A comprehensive Laravel 12 application demonstrating multiple methods to retrieve the last executed SQL query for debugging and learning purposes.

## Features

* Demonstrates 5 different query debugging methods
* Interactive dashboard with practical examples
* Real-time query logging
* Clean and well-documented code samples
* Suitable for learning and debugging use cases

## Requirements

* PHP 8.1 or higher
* Laravel 12.x
* MySQL 5.7+ or MariaDB 10.3+
* Composer

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/laravel-query-debugger.git
cd laravel-query-debugger
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Environment Setup

```bash
cp .env.example .env
php artisan key:generate
```

Configure your database in the `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_debug
DB_USERNAME=root
DB_PASSWORD=yourpassword
```

### 4. Run Migrations and Seeders

```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS laravel_debug;"
php artisan migrate
php artisan db:seed --class=ProductSeeder
```

### 5. Run the Application

```bash
php artisan serve
```

Visit: `http://localhost:8000/debug`

## Debugging Methods Included

### Method 1: DB::getQueryLog()

Best for debugging specific code blocks.

```php
DB::enableQueryLog();
// Execute queries
$queries = DB::getQueryLog();
$lastQuery = end($queries);
```

### Method 2: toSql() on Query Builder

Best for viewing SQL before execution.

```php
$query = Product::where('price', '>', 100);
$sql = $query->toSql();
```

### Method 3: DB::listen()

Best for global query monitoring.

```php
DB::listen(function ($query) {
    Log::info($query->sql, $query->bindings);
});
```

### Method 4: Middleware-Based Logging

Best for environment-based or production-safe debugging.

```php
// Queries are logged via custom middleware
```

### Method 5: Raw SQL Queries

Useful when working with raw SQL.

```php
DB::enableQueryLog();
DB::select('SELECT * FROM products WHERE quantity > ?', [20]);
$queries = DB::getQueryLog();
```

## Project Structure

```
laravel-query-debugger/
├── app/
│   ├── Http/Controllers/QueryDebugController.php
│   ├── Http/Middleware/QueryLogMiddleware.php
│   └── Models/Product.php
├── resources/views/debug/
│   ├── dashboard.blade.php
│   ├── method1.blade.php
│   ├── method2.blade.php
│   ├── method3.blade.php
│   ├── method4.blade.php
│   └── method5.blade.php
├── database/
│   ├── migrations/xxxx_create_products_table.php
│   └── seeders/ProductSeeder.php
└── routes/web.php
```

## Performance Notes

Query logging can affect performance. Follow these guidelines:

* Enable logging only in local or staging environments
* Disable query logs after use
* Use middleware with environment checks

## Troubleshooting

### Common Issues

**MassAssignmentException**

* Ensure `$fillable` is defined in the Product model

**Empty Query Log**

* Call `DB::enableQueryLog()` before running queries

**Middleware Not Logging**

* Verify environment conditions inside middleware

### Reset Application

```bash
php artisan migrate:fresh --seed
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

## Advanced Configuration

### Custom Query Log Channel

```php
'channels' => [
    'queries' => [
        'driver' => 'daily',
        'path' => storage_path('logs/queries.log'),
        'level' => 'info',
        'days' => 7,
    ],
],
```

### Environment-Based Logging

```php
if (app()->environment(['local', 'staging']) || config('app.debug')) {
    DB::enableQueryLog();
}
```

## Learning References

* Laravel Database Query Builder Documentation
* Laravel Eloquent ORM Documentation
* Laravel Debugbar Package


## Screenshots

<img width="1774" height="959" alt="image" src="https://github.com/user-attachments/assets/03135f7e-5b7c-46c7-b8c8-7534ec19ab49" />

<img width="1758" height="887" alt="image" src="https://github.com/user-attachments/assets/b8ac6f41-ff48-4ca1-8317-ee649ed9e039" />
<img width="1759" height="670" alt="image" src="https://github.com/user-attachments/assets/0c98654a-f604-4af3-beea-c5eed900fdc3" />




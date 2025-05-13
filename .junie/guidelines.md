# Laravel Starter Kit Development Guidelines

This document provides essential information for developers working on this Laravel Starter Kit project.

## Build/Configuration Instructions

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js and npm
- A database (SQLite, MySQL, PostgreSQL)

### Initial Setup
1. Clone the repository
2. Install PHP dependencies:
   ```bash
   composer install
   ```
3. Install JavaScript dependencies:
   ```bash
   npm install
   ```
4. Create environment file:
   ```bash
   cp .env.example .env
   ```
5. Generate application key:
   ```bash
   php artisan key:generate
   ```
6. Configure your database in the `.env` file
7. Run migrations:
   ```bash
   php artisan migrate
   ```
8. Sync modules:
   ```bash
   php artisan modules:sync
   ```

### Development Server
Run the development server with a single command that starts Laravel server, queue worker, logs, and Vite:
```bash
composer dev
```

For SSR (Server-Side Rendering):
```bash
composer dev:ssr
```

### Building for Production
```bash
npm run build
```

For SSR:
```bash
npm run build:ssr
```

## Testing Information

### Testing Framework
This project uses Pest PHP, a testing framework built on top of PHPUnit, with a more expressive syntax.

### Test Structure
Tests are organized in three main locations:
- `tests/Unit/` - Unit tests
- `tests/Feature/` - Feature tests
- `app-modules/*/tests/` - Module-specific tests

### Running Tests
Run all tests:
```bash
composer test
```

Run specific test file:
```bash
php artisan test path/to/test/file.php
```

Run tests with coverage report:
```bash
php artisan test --coverage
```

### Creating Tests
1. Create a new test file in the appropriate directory
2. Use the Pest syntax for writing tests:
   ```php
   <?php
   
   declare(strict_types=1);
   
   use Illuminate\Foundation\Testing\RefreshDatabase;
   
   uses(RefreshDatabase::class);
   
   test('feature works as expected', function (): void {
       // Arrange
       $data = [...];
       
       // Act
       $result = doSomething($data);
       
       // Assert
       expect($result)->toBe(true);
   });
   ```

3. Use factories to create test data:
   ```php
   $user = User::factory()->create();
   $workspace = Workspace::factory()->create(['user_id' => $user->id]);
   ```

4. Use the `expect()` function for assertions:
   ```php
   expect($result)->toBe(true);
   expect($collection)->toHaveCount(5);
   expect($model)->toBeInstanceOf(Model::class);
   ```

### Test Example
Here's a simple test for the Workspace model:

```php
<?php

declare(strict_types=1);

use Modules\Workspace\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('workspace has expected attributes', function (): void {
    // Arrange
    $name = 'Test Workspace';
    
    // Act
    $workspace = Workspace::factory()->create([
        'name' => $name,
    ]);
    
    // Assert
    expect($workspace->name)->toBe($name);
    expect($workspace->owner)->not()->toBeNull();
    expect($workspace->users)->toBeInstanceOf(\Illuminate\Database\Eloquent\Collection::class);
});
```

## Additional Development Information

### Code Style
This project follows the Laravel coding style with some additional rules:
- Strict types are required (`declare(strict_types=1);`)
- Class elements must be ordered according to the configuration in `pint.json`
- Void return types must be explicitly declared

### Code Formatting
Format code using Laravel Pint:
```bash
composer format
```

Format JavaScript and Vue files:
```bash
npm run format
```

Check JavaScript and Vue formatting without fixing:
```bash
npm run format:check
```

### Linting
Lint JavaScript and Vue files:
```bash
npm run lint
```

### IDE Helper
Generate IDE helper files for better autocompletion:
```bash
composer ide-helper
```

### Modular Structure
The project uses a modular structure with modules in the `app-modules` directory. Each module can have its own:
- Controllers
- Models
- Views
- Routes
- Tests
- Migrations
- Factories

### Frontend
- Vue.js 3 with TypeScript
- Inertia.js for server-side rendering
- Tailwind CSS for styling
- Vite for asset compilation

### Internationalization
The project supports multiple languages using Vue I18n. Translation files are located in:
- `lang/` directory for PHP translations
- `app-modules/*/lang/` directories for module-specific translations

### Workspace Module
The Workspace module provides multi-tenancy features:
- Users can create and manage workspaces
- Users can be invited to workspaces
- Users can switch between workspaces
- Resources can be scoped to workspaces

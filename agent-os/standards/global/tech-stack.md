## Tech stack

This application's technical stack with specific versions for consistency across development.

### Backend Framework & Runtime
- **Framework:** Laravel 12
- **Language:** PHP 8.4.13
- **Authentication:** Laravel Fortify v1
- **Permissions:** Spatie Laravel Permission v6
- **Settings:** Spatie Laravel Settings v3
- **Data Objects:** Spatie Laravel Data v4
- **Package Manager:** Composer

### Frontend
- **JavaScript Framework:** React 18
- **Language:** TypeScript 5
- **CSS Framework:** Tailwind CSS v4
- **UI Components:** shadcn/ui
- **SPA Framework:** Inertia.js v2 (@inertiajs/react)
- **Build Tool:** Vite 6
- **Code Formatter:** Prettier 3

### Database & ORM
- **ORM:** Laravel Eloquent
- **Query Builder:** Laravel Query Builder
- **Migrations:** Laravel Migrations

### Testing & Quality
- **Test Framework:** Pest v4 (PHPUnit v12)
- **PHP Linter:** Laravel Pint v1
- **Static Analysis:** Larastan v3 (PHPStan)
- **Refactoring:** Rector v2
- **Frontend Testing:** Pest v4 Browser Testing

### Development Tools
- **MCP Server:** Laravel MCP v0
- **Prompts:** Laravel Prompts v0
- **Wayfinder:** Laravel Wayfinder v0

### Architecture Patterns
- **Action Pattern:** Business logic in Action classes
- **Query Pattern:** Data access in Query classes
- **Data Transfer:** Spatie Laravel Data for DTOs
- **Form Validation:** Laravel Form Requests
- **Lean Models:** Models contain only relationships, casts, and simple helpers
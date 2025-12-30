# AGENTS.md - Development Guidelines for Agentic Coding

This file contains essential information for agentic coding agents working in this PeopleDear Laravel application.

## Build/Lint/Test Commands

### Essential Commands

- **Full test suite**: `composer test` (requires 100% coverage, linting, type checking)
- **Unit tests only**: `composer test:unit` (100% coverage required)
- **Type checking**: `composer test:types` (PHPStan + npm types)
- **Code formatting**: `vendor/bin/pint --dirty` (run before finalizing changes)
- **Linting**: `composer lint` (rector, pint, npm lint)

### Single Test Commands

- **Run specific test file**: `php artisan test tests/Feature/ExampleTest.php`
- **Run specific test by name**: `php artisan test --filter=testName`
- **Run with minimal coverage**: `php artisan test --filter=testName --coverage`

### Development Servers

- **All dev servers**: `composer dev` (serve, queue, pail, npm dev)
- **Frontend build**: `npm run build` (if UI changes not visible)
- **Frontend dev**: `npm run dev`

## Code Style Guidelines

### PHP Standards

- **Strict typing**: Always use `declare(strict_types=1)` in PHP files
- **Type declarations**: Always use explicit return types and parameter type hints
- **Constructor property promotion**: Use PHP 8+ constructor property promotion
- **No empty constructors**: Don't allow empty `__construct()` methods with zero parameters
- **Curly braces**: Always use curly braces for control structures, even single-line
- **Descriptive naming**: `isRegisteredForDiscounts` not `discount()`

### Laravel Conventions

- **Model queries**: Always use `Model::query()` for all queries
- **Model creation**: Use `php artisan make:model {Name} -mfs --no-interaction`
- **Controllers**: Use Form Request classes for validation, not inline validation
- **Actions**: Business logic lives in `app/Actions/` classes with `handle()` method
- **Contextual attributes**: Use `#[CurrentUser]` instead of injecting Request for user

### Import Organization

- **Alphabetical imports**: Organize imports alphabetically within groups
- **Import all classes**: Never use fully qualified names inline in tests
- **Type-only imports**: Use `import type` for TypeScript type-only imports

### Frontend (React + TypeScript)

- **Functional components only**: No class components
- **File naming**: lowercase with hyphens (`user-profile.tsx`)
- **Props interface**: Define props interface for every component
- **No `any` type**: Use proper TypeScript types
- **shadcn/ui**: Check `resources/js/components/ui/` before creating new components

### Testing (Pest v4)

- **Test structure**: Flat structure, no nested subdirectories
- **Type hinting**: Type hint everything: `test('example', function (): void { ... });`
- **PHPDoc for variables**: Use `/** @var User $user */` for factories and queries
- **Factory methods**: Use `createQuietly()` not `create()`
- **Browser tests**: Use `visit()` and `$this->actingAs($user)`

## Error Handling

### Exceptions

- **Use `->throws()`**: For exception testing in Pest
- **Action error handling**: Wrap complex operations in `DB::transaction()`
- **Validation errors**: Return proper validation responses from controllers

### Logging

- **Browser logs**: Use `browser-logs` tool for debugging frontend issues
- **Laravel logs**: Use `php artisan pail` for real-time log monitoring

## Architecture Patterns

### Action Pattern

- **Location**: `app/Actions/`
- **Naming**: No "Action" suffix (`CreateOrganization` not `CreateOrganizationAction`)
- **Method**: Single `handle()` method
- **Dependencies**: Inject via constructor using private properties

### Data Objects (DTOs)

- **Location**: `app/Data/`
- **Suffix**: Must end with `Data` (`UpdateOfficeData`)
- **Validation**: NOT for validation - use Form Requests
- **Properties**: Use `readonly` properties and `Optional` for updates

### Models

- **Type hints**: Add `@property` and `@property-read` PHPDoc annotations
- **Relationships**: Always add PHPDoc return type hints with PHPStan generics
- **Casts**: Use public `casts()` method, not `$casts` property
- **Lean models**: Keep models lean, business logic in Actions

### Query Pattern

- **Location**: `app/Queries/`
- **Method**: All Query classes must use `builder()` method to return Eloquent Builder
- **Pattern**:

```php
/**
 * @return Builder<Model>
 */
public function builder(): Builder
{
    return $this->builder; // or Model::query()
}
```

- **Consistency**: Never use `make()` - always use `builder()` for Builder access
- **Invokable**: Preferred pattern is invokable classes with `__invoke()` method for initialization

## Git Workflow

### Branching

- **Feature branches**: Always create `git checkout -b feature/descriptive-name`
- **Before branching**: `git fetch && git pull origin main`
- **Before commits**: Run `composer test:unit`, `vendor/bin/pint --dirty`, `composer test:types`

### Commits

- **Conventional commits**: Follow `type(scope): description` format
- **Types**: feat, fix, docs, style, refactor, perf, test, build, ci
- **Imperative mood**: "Add feature" not "Added feature"

## Tools & Resources

### Laravel Boost Tools

- **`search-docs`**: Use FIRST for Laravel ecosystem documentation
- **`list-artisan-commands`**: Verify Artisan command parameters
- **`tinker`**: Execute PHP for debugging
- **`database-query`**: Read-only database queries
- **`get-absolute-url`**: Generate correct URLs

### MCP Servers

#### Playwright MCP Server Setup

The Playwright MCP server provides browser automation capabilities for testing and web interaction.

**Global Installation (Recommended):**

```bash
npm install -g @playwright/mcp
```

**Configuration:**
Create `~/opencode.json` (global config):

```json
{
    "$schema": "https://opencode.ai/config.json",
    "mcp": {
        "playwright": {
            "type": "local",
            "command": ["npx", "@playwright/mcp@latest"],
            "enabled": true,
            "timeout": 30000
        }
    }
}
```

**Usage in OpenCode:**

```
Navigate to https://example.com and click the login button. use playwright
```

**Alternative Local Installation:**

```bash
npm install @playwright/mcp
```

Then update your MCP configuration files (`.mcp.json`, `.cursor/mcp.json`, etc.) with:

```json
{
    "mcpServers": {
        "playwright": {
            "command": "npx",
            "args": ["@playwright/mcp@latest"]
        }
    }
}
```

### Key Dependencies

- **PHP**: 8.4.13
- **Laravel**: v12
- **Frontend**: React 18, TypeScript 5, Inertia.js v2, Tailwind CSS v4
- **Testing**: Pest v4, PHPStan v3
- **Data**: Spatie Laravel Data v4

## Important Notes

- **No new base folders**: Don't create new directories without approval
- **No dependency changes**: Don't change dependencies without approval
- **Reuse components**: Check existing components before creating new ones
- **Test coverage**: Maintain 100% test coverage
- **Documentation**: Only create docs when explicitly requested
- **Security**: Never expose or log secrets/keys

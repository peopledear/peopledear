## Coding style standards

- **Strict Types in PHP**: Always use `declare(strict_types=1);` at the top of all PHP files
- **Explicit Return Types**: Always use explicit return type declarations for methods and functions in PHP
- **Type Hints for Parameters**: Use appropriate type hints for all method and function parameters
- **Curly Braces Required**: Always use curly braces for control structures, even single-line blocks
- **Method Chaining on New Lines**: Chain methods on new lines for better readability
- **PHP 8 Constructor Promotion**: Use PHP 8 constructor property promotion in `__construct()`
- **No Empty Constructors**: Do not allow empty `__construct()` methods with zero parameters
- **Run Laravel Pint**: ALWAYS run `vendor/bin/pint --dirty` before committing to format code
- **Descriptive Names**: Use descriptive names for variables and methods (e.g., `isRegisteredForDiscounts`, not `discount()`)
- **Check Existing Conventions**: When creating or editing files, check sibling files for correct structure, approach, and naming
- **Small Focused Functions**: Keep functions small and focused on a single task
- **Remove Dead Code**: Delete unused code, commented-out blocks, and imports
- **DRY Principle**: Avoid duplication by extracting common logic into reusable classes or methods

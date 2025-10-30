## Database migration standards

- **Always Delete down() Method**: NEVER implement the `down()` method - this application does not roll back migrations, always remove it entirely
- **Column Order CREATE TABLE**: ALWAYS use this exact order: `id()` first, then `timestamps()`, then all other columns
- **No after() in ALTER TABLE**: Do NOT use `after()` method when adding columns - breaks PostgreSQL compatibility
- **No Default Values**: Default values are business logic, NOT database constraints - implement in Model's `$attributes`, `booted()` method, or Action classes
- **Use foreignIdFor()**: ALWAYS use `$table->foreignIdFor(Model::class)` for foreign keys instead of manual `unsignedBigInteger()`
- **No Cascade Constraints**: NEVER add `->onDelete('cascade')` or `->onUpdate('cascade')` - handle deletions explicitly in Action classes to prevent unintended data loss
- **Column Modification Preserves All**: When modifying a column with `->change()`, MUST include ALL previously defined attributes or they will be dropped
- **Use migrate:fresh --seed**: Reset database with `php artisan migrate:fresh --seed` instead of rolling back migrations
- **Small Focused Changes**: Keep each migration focused on a single logical change for clarity and easier troubleshooting
- **Descriptive Names**: Use clear, descriptive migration names that indicate what the migration does
- **Version Control**: Always commit migrations to version control and never modify existing deployed migrations
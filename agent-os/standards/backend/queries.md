## Query class standards

- **Query Builder Pattern**: Create Query classes in `app/Queries/` that provide reusable query builders for complex database reads
- **Required builder() Method**: ALL Query classes MUST implement a `builder()` method that returns an Eloquent or Query Builder instance
- **Naming Convention**: Use singular model name + "Query" suffix (e.g., `CountryQuery`, `UserQuery`, not `GetUsersQuery`)
- **Return Type Hints**: Always use generic type hints for builder() method: `@return Builder<Model>`
- **Method Injection**: Inject Query classes at the method level in controllers, NOT in `__construct()`
- **Read Operations Only**: Queries handle SELECT operations - write operations (INSERT, UPDATE, DELETE) belong in Actions
- **No Business Logic**: Queries should not contain business logic, data transformation, or validation
- **Chainable Methods**: Return builder instances to allow method chaining in controllers
- **Always Use Model::query()**: NEVER use `Model::all()`, `Model::find()`, or `Model::where()` directly - always start with `Model::query()`
- **Prevent N+1 Queries**: Use eager loading to prevent N+1 query problems
- **Avoid DB Facade**: Prefer `Model::query()` over `DB::` facade to leverage Laravel's ORM capabilities

## Action class standards

- **Action Pattern**: Use Action classes in `app/Actions/` for ALL business logic - keep models and controllers lean
- **Naming Convention**: Name actions WITHOUT "Action" suffix - use descriptive names like `CreateOrganization`, `UpdateOffice`, `DeleteUser`
- **Single handle() Method**: Actions MUST implement a `handle()` method (NOT `__invoke()`) that performs the business logic
- **Create with Artisan**: Use `php artisan make:action "{Name}" --no-interaction` to create new actions
- **Constructor Injection**: Inject dependencies via constructor using private readonly properties for services and other actions
- **Method Parameters**: Accept Data objects and required models/context as handle() parameters
- **Update Actions Accept Model**: Update actions MUST accept the model being updated as a parameter, not query for it inside
- **Delete Actions Accept Model**: Delete actions MUST accept the model being deleted as a parameter, not accept ID and query
- **Create Actions Accept Data**: Create actions accept Data object and any required context (user, parent models, etc.)
- **Use toArray() with Optional**: Data objects automatically handle Optional fields - use `$data->toArray()` for clean updates
- **Transactions for Complex Operations**: Wrap multi-model operations in `DB::transaction()` when needed
- **Return Updated Models**: Return the model after updates, use `->refresh()` to get latest data
- **No Dependencies in Simple Actions**: Some actions don't require constructor dependencies - just the handle() method
- **Unit Test All Actions**: ALWAYS create comprehensive unit tests for Actions in `tests/Unit/Actions/`
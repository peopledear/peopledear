## Controller standards

- **Thin HTTP Adapters**: Controllers are thin adapters that validate, convert data, call actions, and return responses - NO business logic
- **Flat Hierarchy**: Controllers live directly in `app/Http/Controllers/` - NO nested Admin/ or other subdirectories
- **Single Action Controllers**: Use `__invoke()` for one specific action (e.g., `ActivateUserController`, `ResendInvitationController`)
- **Multi-Action Controllers**: Use named methods for related CRUD operations (e.g., `OfficeController` with `store()`, `update()`, `destroy()`)
- **Always Use Form Requests**: ALWAYS create dedicated Form Request classes for validation - NEVER inline validation in controllers
- **Method-Level Injection**: Inject Actions and Queries at method level, NOT in `__construct()`
- **Use CurrentUser Attribute**: Use `#[CurrentUser] User $user` instead of `Request::user()` for cleaner dependency injection
- **Data Object Creation**: Create Data objects from validated data using `::from($request->validated())`
- **Delegate to Actions**: Call Action's `handle()` method with Data objects and models - controller just orchestrates
- **Explicit Return Types**: Always use explicit return type hints (`RedirectResponse`, `Response`, `JsonResponse`)
- **Named Routes**: Prefer named routes with `route()` function for redirects and URL generation
- **Flash Messages**: Use `->with('success', 'message')` for user feedback on redirects
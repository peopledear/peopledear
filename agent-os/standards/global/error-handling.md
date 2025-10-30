## Error handling standards

- **Laravel Exception Handling**: Use Laravel's built-in exception handling in `bootstrap/app.php` for centralized error management
- **Form Request Validation Errors**: Validation errors automatically returned by Form Requests - frontend displays using `form.errors`
- **Action Layer Exceptions**: Throw specific exceptions from Action classes for business logic errors
- **User-Friendly Messages**: Provide clear, actionable error messages without exposing technical details or security information
- **Fail Fast**: Validate input and check preconditions early in Actions - fail with clear exceptions before processing
- **Specific Exception Types**: Use specific exception types (ValidationException, AuthorizationException, etc.) for targeted handling
- **No try-catch Everywhere**: Handle errors at appropriate boundaries (controllers, Actions) not scattered try-catch blocks
- **Transactions for Safety**: Use `DB::transaction()` in Actions for multi-model operations to ensure atomicity
- **Clean Up Resources**: Always clean up resources (file handles, connections) in finally blocks
- **Log Errors**: Use Laravel's logging for errors - check with `last-error` MCP tool or `read-log-entries`

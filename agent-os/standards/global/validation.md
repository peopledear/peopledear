## Validation standards

- **Form Requests for HTTP Validation**: ALWAYS create Form Request classes for validation - NEVER inline validation in controllers
- **Data Objects Are NOT Validators**: Data objects are DTOs for type-safe transfer - validation belongs in Form Requests
- **Separation of Concerns**: Form Requests validate, Data objects transfer, Actions contain business logic
- **Server-Side Validation**: Always validate on server; never trust client-side validation alone for security
- **Client-Side for UX**: Frontend displays validation errors from backend - use `form.errors` in Inertia
- **Specific Error Messages**: Provide clear, field-specific error messages in Form Request's `messages()` method
- **Array or String Rules**: Check sibling Form Requests to see if application uses array or string-based validation rules
- **Authorization in Form Requests**: Use Form Request's `authorize()` method for permission checks
- **Fail Early**: Validate input as early as possible and reject invalid data before processing
- **Type and Format Validation**: Check data types, formats, ranges, and required fields systematically
- **Sanitize Input**: Laravel automatically sanitizes to prevent injection attacks when using parameterized queries/ORM
- **Business Rule Validation**: Complex business rules belong in Action classes, not Form Requests

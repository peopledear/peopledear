## API endpoint standards

- **RESTful Design**: Follow REST principles with clear resource-based URLs and appropriate HTTP methods (GET, POST, PUT, PATCH, DELETE)
- **Use Eloquent API Resources**: Default to using Eloquent API Resources for transforming models to JSON responses for type-safe serialization
- **API Versioning**: Implement API versioning (URL path or headers) to manage breaking changes without disrupting existing clients
- **Consistent Naming**: Use lowercase, hyphenated or underscored naming consistently across all API endpoints
- **Plural Resource Names**: Use plural nouns for resource endpoints (e.g., `/users`, `/products`) for consistency
- **Limit Nesting Depth**: Keep resource nesting to 2-3 levels maximum for readability and maintainability
- **Query Parameters for Filtering**: Use query parameters for filtering, sorting, pagination, and search instead of separate endpoints
- **Appropriate Status Codes**: Return correct HTTP status codes (200 OK, 201 Created, 400 Bad Request, 404 Not Found, 500 Server Error)
- **Rate Limiting**: Include rate limit information in response headers to help clients manage usage
- **JSON Response Format**: Maintain consistent JSON structure across all endpoints with clear error messages

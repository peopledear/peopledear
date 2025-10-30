## Code commenting standards

- **Prefer PHPDoc Over Comments**: Use PHPDoc blocks over inline comments - never use comments within code unless very complex
- **PHPDoc for Type Hints**: Add PHPDoc annotations for properties, relationships, array shapes, and return types
- **Self-Documenting Code**: Write clear, descriptive code through structure and naming
- **Minimal Comments**: Add concise comments only to explain complex logic or "why" decisions
- **No Superfluous Annotations**: Don't include superfluous PHP annotations except ones starting with `@` for typing
- **Array Shape Documentation**: Add useful array shape type definitions for arrays in PHPDoc when appropriate
- **No Temporal Comments**: Do NOT leave comments about recent changes or temporary fixes - comments should be evergreen
- **Enum Keys TitleCase**: Enum keys should be TitleCase (e.g., `FavoritePerson`, `BestLake`, `Monthly`)

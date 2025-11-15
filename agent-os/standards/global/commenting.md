## Code commenting standards

- **NO Inline Comments**: NEVER use inline comments within code - the code should be self-documenting
- **Prefer PHPDoc Over Comments**: Use PHPDoc blocks for documentation - NOT inline comments
- **PHPDoc for Type Hints**: Add PHPDoc annotations for properties, relationships, array shapes, and return types
- **Self-Documenting Code**: Write clear, descriptive code through structure and naming that explains itself
- **No Superfluous Annotations**: Don't include superfluous PHP annotations except ones starting with `@` for typing
- **Array Shape Documentation**: Add useful array shape type definitions for arrays in PHPDoc when appropriate
- **No Temporal Comments**: Do NOT leave comments about recent changes or temporary fixes - comments should be evergreen
- **Enum Keys TitleCase**: Enum keys should be TitleCase (e.g., `FavoritePerson`, `BestLake`, `Monthly`)

### Exception: Very Complex Logic Only
Only in extremely rare cases where logic is unavoidably complex and cannot be refactored, a brief comment explaining "why" (not "what") may be acceptable. These cases should be extremely rare.

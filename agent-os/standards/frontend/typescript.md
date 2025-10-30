## TypeScript standards

- **Proper Typing**: Type all props, state, and function parameters explicitly
- **Interface Over Type**: Prefer `interface` for component props and object shapes
- **No any Type**: Avoid using `any` type - use proper types or `unknown` when type is truly unknown
- **Import Types**: Use `import type` for type-only imports to optimize bundle size
- **Type-Safe Forms**: Define form data interfaces for useForm hook usage
- **Explicit Return Types**: Add return type annotations to functions for clarity
- **Generic Types**: Use generic types for reusable components and utilities
- **Null Safety**: Handle nullable types explicitly with optional chaining and nullish coalescing
- **Enum Usage**: Use TypeScript enums or const objects for sets of related constants
- **Strict Mode**: Ensure TypeScript strict mode is enabled in tsconfig.json
- **Type Guards**: Use type guards for runtime type checking when needed
- **Utility Types**: Leverage TypeScript utility types (Partial, Pick, Omit, etc.) for type transformations
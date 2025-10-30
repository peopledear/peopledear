## React component standards

- **Functional Components Only**: Use functional components with hooks - NO class components
- **TypeScript Interfaces**: Define props interface for every component with proper typing
- **Check Existing Components**: ALWAYS check `resources/js/components/ui/` for shadcn/ui components before creating new ones
- **Reuse shadcn/ui**: Use existing shadcn/ui components (Button, Card, Input, Select, Dialog, Sheet, etc.) for consistency
- **File Naming**: Use lowercase with hyphens (e.g., `user-profile.tsx`, not `UserProfile.tsx`)
- **Export Default for Pages**: Page components use `export default`, reusable components can use named exports
- **Component Organization**: UI primitives in `ui/`, reusable components in `components/`, layouts in `layouts/`, pages in `pages/`
- **Flat Page Structure**: Pages live in `resources/js/pages/` with lowercase folders - NO deep nesting like `Admin/Users/Index.tsx`
- **Import from @/components/ui/**: Use path alias when importing shadcn/ui components
- **Single Responsibility**: Each component should have one clear, focused purpose
- **Props Interface**: Use `interface` over `type` for component props definitions
- **No any Type**: Avoid using `any` - use proper TypeScript types
- **Import Types**: Use `import type` for type-only imports to optimize bundle size

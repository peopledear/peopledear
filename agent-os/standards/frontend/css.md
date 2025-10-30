## Tailwind CSS standards

- **Use Tailwind v4**: Always use Tailwind CSS v4 - do not use deprecated utilities from v3
- **Import Statement**: Use `@import "tailwindcss";` in CSS files, NOT the old `@tailwind` directives
- **Replaced Utilities**: Use new utilities: `bg-black/*` not `bg-opacity-*`, `shrink-*` not `flex-shrink-*`, `text-ellipsis` not `overflow-ellipsis`
- **Check Existing Conventions**: Review and use existing Tailwind conventions within the project before writing your own
- **Gap for Spacing**: Use gap utilities for spacing in flex/grid layouts instead of margins
- **Dark Mode Support**: If existing pages support dark mode with `dark:` prefix, new pages must also support it consistently
- **Minimize Custom CSS**: Leverage Tailwind utilities to reduce custom CSS maintenance
- **Class Organization**: Think through class placement, order, and priority - remove redundant classes, group elements logically
- **Component Extraction**: Extract repeated Tailwind patterns into reusable React components
- **No corePlugins**: `corePlugins` configuration is not supported in Tailwind v4

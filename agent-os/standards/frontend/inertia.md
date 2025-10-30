## Inertia.js standards (React)

- **React with Inertia**: This application uses React with Inertia.js v2 - NOT Vue.js
- **Page Components**: Inertia pages are React components in `resources/js/pages/` directory with lowercase folder names
- **TSX Extension**: Page components use `.tsx` extension for TypeScript + JSX
- **Server-Side Routing**: Use `Inertia::render()` in controllers for server-side routing instead of traditional Blade views
- **Props Interface**: Define TypeScript interface for page component props
- **Head Component**: Use `<Head>` from `@inertiajs/react` for page titles and meta tags
- **useForm Hook**: ALWAYS use Inertia's `useForm` hook for form handling - provides processing state and errors
- **Type-Safe Forms**: Define form data interface and use with `useForm<FormData>()`
- **Error Display**: Use `form.errors` for displaying validation errors from Laravel
- **Loading States**: Use `form.processing` for submit button disabled state and loading indicators
- **Polling**: Use Inertia v2 polling feature for real-time data updates
- **Prefetching**: Use prefetching to preload data for faster page transitions
- **Deferred Props**: Use deferred props for lazy-loading data with loading skeletons
- **Infinite Scrolling**: Use merging props and `WhenVisible` for infinite scroll implementations
- **Backend Validation**: Validation happens in Laravel Form Requests - frontend displays errors from backend
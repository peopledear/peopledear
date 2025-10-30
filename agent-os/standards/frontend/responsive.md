## Responsive design standards

- **Tailwind Breakpoints**: Use Tailwind's responsive prefixes (sm:, md:, lg:, xl:, 2xl:) consistently across application
- **Mobile-First Development**: Start with mobile layout and progressively enhance with breakpoint prefixes
- **Test Across Devices**: Test UI changes across multiple screen sizes (mobile, tablet, desktop) for balanced experience
- **Fluid Layouts**: Use Tailwind's responsive width utilities and flexible containers that adapt to screen size
- **Relative Units**: Tailwind uses rem units by default - avoid fixed pixel values when possible
- **Touch-Friendly Design**: Ensure tap targets appropriately sized (minimum 44x44px) for mobile users
- **Pest Browser Testing**: Use Pest v4 browser testing with different viewports to test responsive behavior
- **Performance on Mobile**: Optimize images with proper sizing and lazy loading for mobile networks
- **Readable Typography**: Maintain readable font sizes across all breakpoints without requiring zoom
- **Content Priority**: Show most important content first on smaller screens through thoughtful layout
- **Dark Mode Responsive**: Ensure dark mode works across all breakpoints if implemented

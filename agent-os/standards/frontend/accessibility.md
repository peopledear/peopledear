## Accessibility standards

- **shadcn/ui Built-in Accessibility**: shadcn/ui components have built-in accessibility - use them for consistent a11y
- **Semantic HTML**: Use appropriate HTML elements (nav, main, button, article) that convey meaning to assistive technologies
- **Keyboard Navigation**: Ensure all interactive elements accessible via keyboard with visible focus indicators
- **Color Contrast**: Maintain sufficient contrast ratios (4.5:1 for normal text) - test with both light and dark modes
- **Alternative Text**: Provide descriptive alt text for images and meaningful labels for all form inputs
- **Form Labels**: Use shadcn/ui Label component properly associated with Input components via htmlFor
- **Screen Reader Testing**: Test views with screen reading devices to ensure proper experience
- **ARIA When Needed**: Use ARIA attributes to enhance complex components when semantic HTML insufficient
- **Logical Heading Structure**: Use heading levels (h1-h6) in proper order to create clear document outline
- **Focus Management**: Manage focus in modals (Dialog/Sheet components), dynamic content, and page transitions
- **Dark Mode Support**: If pages support dark mode, ensure accessibility maintained in both themes

# PeopleDear Tech Stack

## Overview

PeopleDear is built using modern Laravel best practices with a focus on developer experience, code quality, and performance. This document outlines all technologies, services, and tools used across the entire application stack.

---

## Backend Technologies

### Core Framework
| Technology | Version | Purpose |
|-----------|---------|---------|
| **PHP** | 8.4.12 | Primary programming language |
| **Laravel** | v12 | Web application framework |
| **PostgreSQL** | Latest | Primary database (production and development) |

### Key Laravel Packages
| Package | Version | Purpose |
|---------|---------|---------|
| **laravel/prompts** | v0 | Interactive CLI prompts |
| **laravel/horizon** | Latest | Redis queue monitoring and management |
| **laravel/sanctum** | Latest | API authentication |
| **laravel/nightwatch** | Latest | Application performance and error tracking |

### Laravel Architecture Features
- **Streamlined Structure**: Using Laravel 11+ simplified application structure
  - No `app/Http/Middleware/` kernel
  - Configuration in `bootstrap/app.php`
  - Service providers in `bootstrap/providers.php`
  - Console commands auto-register from `app/Console/Commands/`
- **Eloquent ORM**: Database interactions with relationships and query builder
- **Form Requests**: Validation handled in dedicated request classes
- **API Resources**: Eloquent API Resources for API responses
- **Jobs & Queues**: Background processing with `ShouldQueue` interface
- **Policies & Gates**: Authorization handled through Laravel's built-in system

---

## Frontend Technologies

### Core Stack
| Technology | Version | Purpose |
|-----------|---------|---------|
| **Inertia.js** | Latest | SPA framework connecting Laravel and Vue |
| **Vue.js** | 3.x | Frontend JavaScript framework |
| **Tailwind CSS** | v4 | Utility-first CSS framework |
| **Vite** | Latest | Frontend build tool and dev server |

### Frontend Features
- **Component Architecture**: Reusable Vue 3 components with Composition API
- **Reactive Forms**: Form handling with Inertia.js form helpers
- **Layouts**: Shared layouts for consistent UI (`AppLayout.vue`, `ProfileLayout.vue`)
- **Avatar System**: Custom avatar selector component with data casting
- **User Menu**: Dropdown navigation with profile access
- **Dark Mode Support**: Built-in dark mode using Tailwind's `dark:` variant

### Tailwind CSS v4 Features
- CSS `@import` syntax (not `@tailwind` directives)
- Modern utility classes with updated opacity syntax
- Custom configuration in CSS files
- Replaced deprecated utilities (e.g., `flex-shrink-*` → `shrink-*`)

---

## Testing & Quality Assurance

### Testing Framework
| Tool | Version | Purpose |
|------|---------|---------|
| **Pest** | v4 | Primary testing framework |
| **PHPUnit** | v12 | Underlying test runner |

### Pest v4 Features
- **Browser Testing**: End-to-end tests with real browser automation
  - Located in `tests/Browser/`
  - Supports Chrome, Firefox, Safari
  - Device and viewport testing (mobile, tablet, desktop)
  - Screenshots and debugging capabilities
- **Feature Tests**: Testing HTTP requests, authentication, database
  - Located in `tests/Feature/`
- **Unit Tests**: Testing individual classes and methods
  - Located in `tests/Unit/`
- **Test Datasets**: Reusable test data for validation and edge cases
- **Mocking**: Full mocking support with `mock()` and partial mocks

### Code Quality Tools
| Tool | Version | Purpose |
|------|---------|---------|
| **Laravel Pint** | v1 | Code formatter (PSR-12 compliant) |
| **Larastan** | v3 | Static analysis (PHPStan for Laravel) |
| **Rector** | v2 | Automated code refactoring and upgrades |

### Quality Assurance Practices
- **Static Analysis**: PHPStan Level 8 enforcement
- **Code Formatting**: Automated with `vendor/bin/pint --dirty`
- **Type Safety**: Explicit return types and type hints on all methods
- **Test Coverage**: Feature and unit tests for all core functionality
- **Browser Testing**: Critical user flows tested end-to-end

---

## Infrastructure & DevOps

### Hosting & Deployment
| Service | Purpose |
|---------|---------|
| **Hetzner VPS** | Cloud hosting provider |
| **Laravel Forge** | Server management and deployment automation |
| **Hetzner S3-Compatible Buckets** | Object storage for uploaded documents |

### Deployment Workflow
1. Code pushed to Git repository
2. Laravel Forge triggers deployment hook
3. Automated deployment script runs:
   - Pull latest code
   - Run `composer install --no-dev --optimize-autoloader`
   - Run `npm run build`
   - Run database migrations (`php artisan migrate --force`)
   - Clear and cache configuration (`php artisan config:cache`)
   - Restart queue workers (`php artisan horizon:terminate`)

---

## Third-Party Services

### Email & Notifications
| Service | Purpose |
|---------|---------|
| **Mailgun** | Transactional emails and notifications |

**Email Use Cases**:
- User registration and password reset
- Approval workflow notifications
- Time-off request confirmations
- Overtime approval alerts
- Expense report updates

### Monitoring & Observability
| Service | Purpose |
|---------|---------|
| **Laravel Nightwatch** | Application performance monitoring and error tracking |

**Monitoring Features**:
- Real-time error tracking
- Performance metrics (page load times, database query times)
- Exception logging and stack traces
- Custom event tracking

### Queue Management
| Service | Purpose |
|---------|---------|
| **Laravel Horizon** | Redis-backed queue monitoring |
| **Redis** | Queue driver and caching layer |

**Queue Use Cases**:
- Sending bulk email notifications
- Generating large reports
- Processing uploaded documents
- Calculating complex overtime scenarios

---

## Future Integrations (Planned)

### Payment Processing
- **Stripe**: International payment processing
- **Easypay**: Portuguese market payment gateway

### Payroll System Integrations
- **Primavera**: Portuguese ERP and payroll system
- **Sage PHC**: Accounting and payroll software
- **Centralgest**: Business management software

### Calendar Integrations
- **Google Calendar**: Sync approved time-off to employee calendars
- **Microsoft Outlook**: Calendar integration for Outlook users

### Communication Integrations
- **Slack**: Approval notifications in team channels
- **Microsoft Teams**: Notifications for enterprise customers

### Authentication
- **SSO (Single Sign-On)**: Enterprise authentication via SAML/OAuth2
- **2FA (Two-Factor Authentication)**: Enhanced security for sensitive accounts

---

## Development Tools

### Version Control & Collaboration
| Tool | Purpose |
|------|---------|
| **Git** | Version control system |
| **GitHub** or **GitLab** | Repository hosting and CI/CD |

### CI/CD Pipeline
**Planned Integration**: GitHub Actions or GitLab CI

**Pipeline Stages**:
1. **Lint**: Run Laravel Pint to check code formatting
2. **Static Analysis**: Run Larastan (PHPStan Level 8)
3. **Unit Tests**: Run `php artisan test --testsuite=Unit`
4. **Feature Tests**: Run `php artisan test --testsuite=Feature`
5. **Browser Tests**: Run `php artisan test --testsuite=Browser`
6. **Build Frontend**: Run `npm run build`
7. **Deploy**: Trigger Forge deployment on successful pipeline

### Local Development Environment
| Tool | Purpose |
|------|---------|
| **Laravel Herd** or **Laravel Sail** | Local development environment |
| **Composer** | PHP dependency management |
| **NPM** | JavaScript package management |
| **Redis** | Local queue and cache driver |

### Database Management
| Tool | Purpose |
|------|---------|
| **TablePlus** or **DBeaver** | Database GUI client |
| **Laravel Migrations** | Version-controlled schema changes |
| **Seeders & Factories** | Test data generation |

---

## Code Conventions & Standards

### PHP Conventions
- **PSR-12 Compliance**: Enforced via Laravel Pint
- **Constructor Property Promotion**: Use PHP 8 syntax in `__construct()`
- **Type Declarations**: Explicit return types and parameter types on all methods
- **PHPDoc Blocks**: Document array shapes and complex return types
- **No Empty Constructors**: Remove `__construct()` if it has zero parameters
- **Enum Key Format**: TitleCase (e.g., `FavoritePerson`, `Monthly`)

### Laravel Conventions
- **Form Requests**: Validation in dedicated request classes, not controllers
- **Eloquent Relationships**: Always use relationship methods with return types
- **Configuration**: Use `config()` helper, never `env()` outside config files
- **URL Generation**: Prefer `route()` with named routes over hardcoded URLs
- **Model Factories**: Use factories for test data, check for custom states
- **Model Casts**: Define in `casts()` method (not `$casts` property)

### Frontend Conventions
- **Component Reuse**: Check for existing components before creating new ones
- **Tailwind Classes**: Follow project conventions for spacing, dark mode, responsive design
- **Gap Utilities**: Use `gap-*` for spacing in flex/grid layouts, not margins
- **Dark Mode**: Support `dark:` variants consistently across all components

### Testing Conventions
- **Pest Syntax**: All tests written in Pest (not PHPUnit)
- **Test Location**:
  - Feature tests: `tests/Feature/`
  - Unit tests: `tests/Unit/`
  - Browser tests: `tests/Browser/`
- **Assertions**: Use specific methods (`assertSuccessful`, `assertForbidden`) over generic `assertStatus()`
- **Datasets**: Use datasets for validation testing and repeated data
- **Factories**: Always use model factories in tests

### File Structure
- **Follow Sibling Files**: Match naming, structure, and approach of adjacent files
- **No New Base Folders**: Stick to existing directory structure unless approved
- **Descriptive Names**: Use clear, descriptive names (e.g., `isRegisteredForDiscounts` not `discount()`)

---

## Security Practices

### Authentication & Authorization
- **Laravel Sanctum**: API token authentication
- **Policies & Gates**: Fine-grained authorization controls
- **CSRF Protection**: Enabled on all POST/PUT/PATCH/DELETE routes
- **Password Hashing**: Bcrypt with appropriate cost factor

### Data Protection
- **GDPR Compliance**: Personal data handling per EU regulations
- **Data Encryption**: Sensitive data encrypted at rest and in transit
- **File Upload Validation**: Strict file type and size validation
- **SQL Injection Prevention**: Eloquent ORM and parameterized queries
- **XSS Prevention**: Vue.js automatic escaping and CSP headers

### Multi-Tenancy Security
- **Tenant Isolation**: Database-level separation or scoped queries
- **Access Control**: Row-level security ensuring users only access their tenant data
- **Security Audits**: Regular penetration testing and code reviews

---

## Performance Optimization

### Caching Strategy
| Layer | Technology | Purpose |
|-------|-----------|---------|
| **Application Cache** | Redis | Cache frequently accessed data |
| **Configuration Cache** | File-based | Cache compiled configuration |
| **Route Cache** | File-based | Cache compiled routes |
| **View Cache** | File-based | Cache compiled Blade templates |
| **Query Cache** | Redis | Cache expensive database queries |

### Database Optimization
- **Eager Loading**: Prevent N+1 queries with `with()` and relationship methods
- **Indexes**: Strategic indexing on frequently queried columns
- **Read Replicas**: Planned for scaling read-heavy workloads
- **Connection Pooling**: Efficient database connection management

### Frontend Optimization
- **Vite Code Splitting**: Automatic bundle splitting for optimal loading
- **Lazy Loading**: Load components and routes on demand
- **Asset Optimization**: Minification and compression via Vite
- **CDN Integration**: Static assets served via CDN (planned)

---

## Documentation & Knowledge Base

### Code Documentation
- **PHPDoc Blocks**: All classes and public methods documented
- **Inline Comments**: Only for complex logic (prefer clear code over comments)
- **README.md**: Installation and setup instructions
- **CLAUDE.md**: AI assistant guidelines for code generation

### API Documentation
- **Scramble** or similar: Auto-generated API documentation from Laravel routes
- **OpenAPI/Swagger**: Planned for external integrations

### User Documentation
- **In-App Help**: Contextual help within the application
- **Knowledge Base**: External documentation site (planned)
- **Video Tutorials**: Screen recordings for common workflows (planned)

---

## Versioning & Release Strategy

### Semantic Versioning
- **Major**: Breaking changes, database migrations requiring downtime
- **Minor**: New features, backward-compatible changes
- **Patch**: Bug fixes, security patches

### Release Cadence
- **Phase 1 (MVP)**: Weekly releases with iterative improvements
- **Phase 2 (SaaS)**: Bi-weekly releases with feature additions
- **Phase 3 (Growth)**: Monthly releases with stabilization focus

### Branching Strategy
- **main**: Production-ready code
- **develop**: Integration branch for feature development
- **feature/***: Individual feature branches
- **hotfix/***: Emergency production fixes

---

## Scalability Considerations

### Current Architecture
- **Single Server**: Hetzner VPS running web, queue, and cache
- **Single Database**: PostgreSQL instance
- **File Storage**: Hetzner S3-compatible buckets

### Future Scaling Plan
- **Horizontal Scaling**: Add additional web servers behind load balancer
- **Database Scaling**: Implement read replicas for reporting queries
- **Queue Workers**: Dedicated queue worker servers
- **CDN**: Cloudflare or similar for static asset delivery
- **Monitoring**: Enhanced monitoring with alerts for performance degradation

---

## Summary

PeopleDear's tech stack is built on a foundation of modern, well-supported technologies:

- **Backend**: Laravel 12 with PHP 8.4 provides robust, maintainable server-side logic
- **Frontend**: Inertia.js + Vue 3 + Tailwind v4 delivers a modern, reactive user experience
- **Testing**: Pest v4 with browser testing ensures quality and reliability
- **Infrastructure**: Hetzner + Forge provides cost-effective, scalable hosting
- **Quality**: Pint, Larastan, and Rector maintain code quality automatically

This stack balances developer productivity, application performance, and cost efficiency while maintaining flexibility for future growth and integrations.

---

**Last Updated**: October 15, 2025
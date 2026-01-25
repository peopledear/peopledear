<h1 align="center">PeopleDear</h1>

<p align="center">
  <strong>People Management Made Simple</strong><br>
  Streamline overtime, time-off, and expenses with automated workflows that just work
</p>

<p align="center">
  <a href="#features">Features</a> •
  <a href="#tech-stack">Tech Stack</a> •
  <a href="#getting-started">Getting Started</a> •
  <a href="#documentation">Documentation</a>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=flat&logo=laravel" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.4-777BB4?style=flat&logo=php" alt="PHP 8.4">
  <img src="https://img.shields.io/badge/React-18-61DAFB?style=flat&logo=react" alt="React 18">
  <img src="https://img.shields.io/badge/TypeScript-5-3178C6?style=flat&logo=typescript" alt="TypeScript 5">
  <img src="https://img.shields.io/badge/Tailwind-4-38B2AC?style=flat&logo=tailwind-css" alt="Tailwind 4">
  <img src="https://img.shields.io/badge/PHPStan-Level%208-8A2BE2?style=flat" alt="PHPStan Level 8">
</p>

---

## 🎯 Why PeopleDear?

Managing people shouldn't be complicated. Between tracking overtime, approving time-off requests, handling expenses, and ensuring everything is properly documented—small businesses waste hours on administrative tasks that could be automated.

**PeopleDear simplifies people management** with smart automation:
- ✅ **Automated overtime calculations** based on your business rules
- ✅ **Smart time-off tracking** with conflict detection
- ✅ **Multi-level approval workflows** that actually work
- ✅ **Clean data exports** for seamless payroll integration

**What we're NOT:** We're not a payroll system. We focus on collecting accurate data and streamlining approvals, so you can export clean, verified information to your payroll processor.

---

## ✨ Features

### For Employees
- 📝 **Self-Service Portal** - Submit overtime, time-off requests, and expenses without emails
- 📊 **Real-Time Status** - Track approval progress and view request history
- 📱 **Mobile-Friendly** - Submit and check requests from anywhere
- 📎 **Document Upload** - Attach receipts and justifications directly

### For Managers
- ✅ **One-Click Approvals** - Review and approve requests in seconds
- 📈 **Team Insights** - Monitor overtime patterns and absence trends
- 🔔 **Smart Notifications** - Email alerts for pending approvals
- 📱 **Approve Anywhere** - Mobile-optimized approval interface

### For HR & Owners
- ⚙️ **Flexible Configuration** - Set schedules, policies, and approval chains
- 🏢 **Multi-Office Support** - Manage multiple locations with different rules
- 📊 **Compliance Reports** - Generate audit-ready documentation
- 👥 **Role-Based Access** - Granular permissions (Employee, Manager, HR Manager, Owner)

---

## 🛠 Tech Stack

### Backend
- **Laravel 12** with strict typing (`declare(strict_types=1)`)
- **PHP 8.4+** with full type coverage
- **PostgreSQL** database
- **PHPStan Level 8** for static analysis
- **Pest v4** testing framework (150+ tests)

### Frontend
- **React 18** with TypeScript
- **Inertia.js** for seamless SPA experience
- **Tailwind CSS v4** for styling
- **shadcn/ui** components with Lucide icons
- **Wayfinder** for type-safe routing

### Quality Tools
- **Laravel Pint** for code formatting
- **Larastan** (PHPStan for Laravel)
- **Spatie Laravel Activity Log** for audit trails
- **Laravel Horizon** for queue management

---

## 🚀 Getting Started

### Prerequisites
- PHP 8.4+
- Composer
- Bun (https://bun.sh)
- PostgreSQL
- Redis (optional, for queues)

### Quick Start
```bash
# Clone the repository
git clone https://github.com/peopledear/peopledear.git
cd peopledear

# Install dependencies
composer install
bun install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database in .env, then run migrations
php artisan migrate --seed

# Build frontend assets
bun run build

# Start development server
php artisan serve
```

Visit `http://localhost:8000` and login with seeded credentials.

---

## 📚 Documentation

- [Architecture Guidelines](CLAUDE.md) - Code style and development patterns
- [Tech Stack Details](docs/tech-stack.md) - Complete technology breakdown
- [Product Roadmap](docs/roadmap.md) - Feature timeline and priorities

---

## 🧪 Testing & Quality
```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run static analysis
composer analyse

# Format code
composer format
```

**Quality Metrics:**
- ✅ 100% test coverage (400+ tests)
- ✅ PHPStan Level Max compliance
- ✅ Type-safe React components
- ✅ Full TypeScript integration

---

## 🗺 Roadmap

### ✅ Completed
- [x] Core authentication & role-based access
- [x] Organization & employee management
- [x] Admin layouts and navigation
- [x] User invitation system

### 🚧 In Progress
- [ ] Overtime logging with auto-calculation
- [ ] Time-off request workflows
- [ ] Expense report management

### 📋 Coming Soon
- [ ] Manager approval dashboards
- [ ] Advanced reporting & analytics
- [ ] Mobile apps (iOS & Android)
- [ ] Multi-tenant SaaS architecture
- [ ] Calendar integrations (Google, Outlook)
- [ ] Payroll system integrations

---

## 🤝 Contributing

We welcome contributions! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

### Development Principles
- **Strict typing** - Every method has explicit type hints
- **Static analysis** - PHPStan Level 8 must pass
- **Test coverage** - New features require tests
- **Laravel conventions** - Follow framework best practices
- **Clean code** - Readable, maintainable, well-documented

---

## 📄 License

PeopleDear is open-sourced software licensed under the [MIT license](LICENSE.md).

---

## 💼 Support & Services

Need help setting up PeopleDear for your organization?  
Contact us at [hello@peopledear.com](mailto:hello@peopledear.com)

---

<p align="center">Built with ❤️ for small businesses everywhere</p>

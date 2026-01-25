# Developer Guide

Welcome to the PeopleDear developer guide. This guide covers architecture patterns, coding standards, and best practices for working with this Laravel application.

## Architecture Patterns

- [Action Pattern](./actions-pattern.md) - Comprehensive guide to the Action pattern for business logic
- [File Storage](./file-storage.md) - S3/MinIO storage configuration and the Disk enum

## Architecture Decisions

Detailed records of key architectural decisions and their rationale:

- [ADR-0001: Use Action Pattern for Business Logic](../architecture-decisions/0001-use-action-pattern.md)
- [ADR-0002: Separate Query Pattern for Data Retrieval](../architecture-decisions/0002-separate-query-pattern.md)
- [ADR-0003: Domain Invariants in Actions](../architecture-decisions/0003-domain-invariants-in-actions.md)
- [ADR-0004: Action Composition for Complex Processes](../architecture-decisions/0004-action-composition-for-complex-processes.md)

## Quick Reference

### Essential Commands

- **Full test suite**: `composer test` (requires 100% coverage, linting, type checking)
- **Unit tests only**: `composer test:unit` (100% coverage required)
- **Type checking**: `composer test:types` (PHPStan + npm types)
- **Code formatting**: `vendor/bin/pint --dirty` (run before finalizing changes)
- **Linting**: `composer lint` (rector, pint, npm lint)

### Key Patterns

- **Actions**: Business logic in `app/Actions/` with `handle()` method
- **Queries**: Data retrieval in `app/Queries/` with `builder()` method
- **Models**: Lean models with business logic in Actions
- **Data Objects**: DTOs in `app/Data/` for data transfer

### Code Style

- **Strict typing**: Always use `declare(strict_types=1)`
- **Type declarations**: Always use explicit return types and parameter type hints
- **Constructor property promotion**: Use PHP 8+ constructor property promotion
- **Readonly classes**: Mark Actions and Queries as `readonly`

## Related Documentation

- [AGENTS.md](../../AGENTS.md) - Development guidelines for agentic coding
- [README.md](../../README.md) - Project overview and setup

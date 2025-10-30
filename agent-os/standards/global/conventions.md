## General development conventions

- **Follow Existing Conventions**: MUST follow all existing code conventions - check sibling files for structure, approach, naming
- **Stick to Directory Structure**: Do not create new base folders without approval - stick to existing structure
- **No Dependency Changes**: Do not change application dependencies without approval
- **Use Artisan Commands**: Use `php artisan make:` commands to create new files (migrations, controllers, models, etc.)
- **Pass --no-interaction**: Always pass `--no-interaction` to Artisan commands to ensure they work without user input
- **Environment Variables in Config**: Use environment variables ONLY in config files - never use `env()` outside of config/ directory
- **Named Routes**: Prefer named routes and `route()` function when generating links
- **SessionKey Enum**: ALWAYS use `SessionKey` enum for session key management instead of magic strings
- **Version Control Best Practices**: Use clear commit messages, feature branches, and meaningful pull/merge requests
- **Never Commit Secrets**: Never commit secrets, API keys, or .env files to version control
- **Testing Before Commits**: ALWAYS run `php artisan test` and `vendor/bin/pint --dirty` before every commit
- **Feature Branches**: Always create new feature branch when starting new task - fetch and pull latest from main first
- **Branch Naming**: Use descriptive branch names like `feature/descriptive-name`

# Contributing to group.one Centralized License Service

Thank you for considering contributing to the group.one Centralized License Service! This document outlines the development workflow and standards.

## Branch Strategy

### Main Branches

- **`trunk`** - Production branch. Only merge after thorough testing.
- **`develop`** - Main development branch. All features merge here first.

### Feature Branches

All work must be done in feature branches following this naming convention:

```
<type>/<issue-number>-<short-description>
```

**Types:**
- `feature/` - New features
- `fix/` - Bug fixes
- `enhancement/` - Improvements to existing features
- `chore/` - Maintenance tasks, dependency updates

**Examples:**
```
feature/123-add-license-renewal
fix/456-activation-validation-error
enhancement/789-improve-logging
chore/101-update-dependencies
```

## Commit Conventions

### Commit Message Format

Use imperative mood: "This commit will..."

**Good examples:**
```
Add license renewal endpoint
Fix validation error in brand creation
Update README with Docker instructions
Refactor activation service for better performance
```

**Bad examples:**
```
Added feature
Fixed bug
Updates
WIP
```

### Commit Frequency

- Make small, frequent commits
- Each commit should represent a logical unit of work
- Commit messages should be descriptive

### Extended Descriptions (Optional)

For complex changes, add an extended description:

```
Add license renewal endpoint

This commit adds a new endpoint for renewing licenses.
It includes:
- New route POST /api/v1/licenses/{id}/renew
- Validation for renewal period
- Event logging for renewals
- Tests for the new functionality
```

## Pull Request Process

### 1. Create Your Branch

```bash
git checkout develop
git pull origin develop
git checkout -b feature/123-your-feature
```

### 2. Make Your Changes

- Write clean, readable code
- Follow PSR-12 coding standards
- Add/update tests for your changes
- Update documentation if needed

### 3. Run Quality Checks

```bash
# Run tests
sail artisan test

# Run PHPStan
sail composer phpstan

# Check code style
sail composer pint:test

# Fix code style
sail composer pint
```

### 4. Commit Your Changes

```bash
git add .
git commit -m "Add your descriptive commit message"
```

### 5. Push to GitHub

```bash
git push origin feature/123-your-feature
```

### 6. Create Pull Request

- Go to GitHub and create a PR from your branch to `develop`
- Fill out the PR template with:
  - Description of changes
  - Related issue number
  - Testing performed
  - Screenshots (if UI changes)

### 7. PR Requirements

Your PR must meet these requirements:

✅ All CI checks pass (linting, PHPStan, tests)
✅ Diff coverage ≥ 50%
✅ At least 1 approval from a team member
✅ Branch is up-to-date with `develop`
✅ No merge conflicts
✅ Descriptive PR description (minimum 20 characters)

### 8. Merge Strategy

- Use **squash and merge** for all PRs
- Delete the branch after merging
- The squashed commit message should summarize all changes

## Branch Protection Rules

### `develop` Branch

- ✅ Require pull request before merging
- ✅ Require 1 approval
- ✅ Require status checks to pass
- ✅ Require branch to be up-to-date
- ✅ Require conversation resolution
- ❌ Allow force pushes
- ❌ Allow deletions

### `trunk` Branch

- ✅ Require pull request before merging
- ✅ Require 2 approvals
- ✅ Require status checks to pass
- ✅ Require branch to be up-to-date
- ✅ Require conversation resolution
- ❌ Allow force pushes
- ❌ Allow deletions

## Code Standards

### PHP Standards

- Follow PSR-12 coding style
- Use strict types: `declare(strict_types=1);`
- Type hint all parameters and return types
- Use readonly properties where applicable
- No `var_dump()`, `dd()`, or `dump()` in production code

### Testing Standards

- Write tests for all new features
- Maintain or improve code coverage
- Use descriptive test method names
- Follow AAA pattern: Arrange, Act, Assert

### Documentation Standards

- Update README for new features
- Add PHPDoc blocks for all public methods
- Include Swagger/OpenAPI annotations for API endpoints
- Update CHANGELOG for significant changes

## Development Setup

### First Time Setup

```bash
# Clone repository
git clone <repo-url>
cd license-service

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Start Docker containers
sail up -d

# Run migrations
sail artisan migrate

# Seed database (optional)
sail artisan db:seed
```

### Daily Development

```bash
# Start containers
sail up -d

# Run tests
sail artisan test

# Stop containers
sail down
```

## Getting Help

- Check existing issues and PRs
- Read the documentation
- Ask in team chat
- Create a discussion on GitHub

## Code of Conduct

- Be respectful and professional
- Provide constructive feedback
- Help others learn and grow
- Follow the project's coding standards

Thank you for contributing! 🚀


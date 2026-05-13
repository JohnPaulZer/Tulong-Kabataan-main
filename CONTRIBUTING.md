# Contributing

Thanks for your interest in contributing.

We welcome contributions of all kinds: bug fixes, features, documentation, and suggestions.

---

## Getting Started

1. Fork the repository
2. Clone your fork locally
3. Create a new branch from `main`
4. Install dependencies and set up the project

---

## Branching Strategy

We follow a structured branching approach:

### Main Branches

- `main` → Production-ready code
- `develop` → Integration branch for ongoing development

### Supporting Branches

Use the following naming conventions:

- `feature/<short-description>` → New features
- `fix/<short-description>` → Bug fixes
- `chore/<short-description>` → Maintenance tasks
- `docs/<short-description>` → Documentation updates
- `refactor/<short-description>` → Code improvements without behavior change
- `test/<short-description>` → Adding or updating tests

Examples:

```
feature/add-authentication
fix/login-validation-error
docs/update-installation-guide
```

---

## Development Workflow

1. Create a branch from `develop` (unless it's a hotfix for production)
2. Make your changes in a focused branch
3. Follow the project's coding style and conventions
4. Add or update tests when applicable
5. Run local checks before submitting:

```bash
make test
make build
```

---

## Commit Messages (Conventional Commits)

We follow the **Conventional Commits** specification.

### Format

```
<type>(optional scope): <short description>
```

### Common Types

- `feat` → New feature
- `fix` → Bug fix
- `docs` → Documentation changes
- `style` → Formatting (no code logic changes)
- `refactor` → Code restructuring
- `test` → Adding/updating tests
- `chore` → Maintenance

### Examples

```
feat(auth): add JWT authentication
fix(api): handle null response in user service
docs(readme): update setup instructions
refactor(core): simplify validation logic
```

### Rules

- Use lowercase for type and description
- Keep messages concise and meaningful
- Use the body for additional context if needed

---

## Pull Request Process

1. Ensure your branch is up to date with `develop`
2. Verify all tests and checks pass
3. Open a pull request targeting `develop` (or `main` for hotfixes)
4. Clearly describe:
   - What changed
   - Why it was needed
   - Any relevant context
5. Use the PR template:
   - [`.github/PULL_REQUEST_TEMPLATE.md`](.github/PULL_REQUEST_TEMPLATE.md)

Optional:

- Include screenshots, logs, or examples if applicable

---

## Reporting Issues

When reporting bugs, please use the provided template:

- [`.github/ISSUE_TEMPLATE/bug_report.md`](.github/ISSUE_TEMPLATE/bug_report.md)

Include:

- Description of the problem
- Steps to reproduce
- Expected vs actual behavior
- Environment details (if relevant)

---

## Suggestions & Feature Requests

For feature requests and suggestions, please use:

- [`.github/ISSUE_TEMPLATE/feature_request.md`](.github/ISSUE_TEMPLATE/feature_request.md)

Be sure to include:

- The problem you're trying to solve
- Your proposed solution
- Any alternatives you've considered

---

## Code of Conduct

This project follows the guidelines defined in:

- [`CODE_OF_CONDUCT.md`](CODE_OF_CONDUCT.md)

Be respectful and constructive in all interactions.
Harassment or inappropriate behavior will not be tolerated.

---

## Notes

- Maintainers may request changes before merging
- Not all contributions may be accepted, but all will be reviewed

---

Thanks again for contributing.
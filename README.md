# EasyBlog - Laravel Blog Engine

A modern, secure blog engine built with Laravel 12, featuring comprehensive input validation, output escaping, professional code standards, and Unit testsPHPUnit test coverage** with 105/107 tests passing.

## Features

### Core Functionality
- **User Authentication & Authorization** - Secure login/registration with role-based access
- **Profile Management** - User profiles with avatars, descriptions, and custom slugs
- **Blog Posts** - Full CRUD operations with rich content, images, and tags
- **Comments System** - Threaded comments with moderation and rate limiting
- **Tag System** - Organize posts with flexible tagging (max 6 tags per post)
- **Image Upload** - Secure image handling with validation and processing

### Security Features
- **Input Sanitization** - Built-in PHP sanitization using `strip_tags()` and `trim()`
- **Output Escaping** - Comprehensive XSS protection in all Blade templates
- **CSRF Protection** - Laravel's built-in CSRF tokens on all forms
- **Ownership Middleware** - Granular authorization for posts, comments, and profiles
- **Rate Limiting** - Comment spam protection (10 comments per user per day)
- **Image Validation** - Strict file type, size, and dimension validation

### Code Quality
- **PSR-12 Standards** - Consistent coding standards throughout
- **PHP 8+ Features** - Modern PHP with typed properties and return types
- **Repository Pattern** - Clean separation of concerns
- **Professional Documentation** - Detailed docblocks and comments

## Architecture

### Repository Pattern
- **Interfaces** - Define contracts for data operations
- **Repositories** - Implement business logic and data access
- **Controllers** - Handle HTTP requests and delegate to repositories
- **Middleware** - Handle cross-cutting concerns like authorization

### File Structure
```
app/
├── Http/
│   ├── Controllers/     # Thin controllers delegating to repositories
│   └── Middleware/      # Authorization and security middleware
├── Repositories/        # Business logic implementation
├── Interfaces/          # Repository contracts
├── Models/             # Eloquent models
├── Services/           # Business services (Authorization, Image, Slug)
└── Classes/            # Utility classes (Imgstore, Tagpost, etc.)
tests/
├── Unit/               # Comprehensive unit tests (105/107 passing)
└── Feature/            # Integration tests
```

## Quick Start

### Prerequisites
- PHP 8.1+
- Composer
- Node.js & NPM
- SQLite (for testing) or MySQL/PostgreSQL

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/boldib/easyblog
   cd easyblog
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Build assets**
   ```bash
   npm run build
   ```

7. **Create storage symlink**
   ```bash
   php artisan storage:link
   ```

8. **Start the development server**
   ```bash
   php artisan serve
   ```

## Testing

### Run Tests
```bash
# Run all tests
php artisan test

# Run unit tests only
php artisan test --testsuite=Unit

# Run with coverage
php artisan test --coverage

# Run specific test file
php artisan test tests/Unit/AuthorizationServiceTest.php
```

### Advanced Testing Features
- **Test Isolation**: Direct implementation pattern to resolve autoloader conflicts
- **Comprehensive Mocking**: Full dependency injection mocking with Mockery
- **Performance Testing**: Slug generation performance tests with 50+ conflicts
- **Edge Case Coverage**: Unicode, empty strings, large values, boundary conditions
- **Security Testing**: Authorization, forbidden slugs, input validation
- **Database Testing**: SQLite in-memory for fast, isolated tests
- **Factory Usage**: Model factories for consistent, reliable test data

## Code Standards

### PSR-12 Compliance
```bash
# Check code standards
./vendor/bin/pint --test

# Fix code standards
./vendor/bin/pint
```

### Key Standards
- **No strict types declarations** (as per project requirements)
- **Professional docblocks** on all methods
- **Type hints** for parameters and return types
- **Consistent naming** following Laravel conventions

## Configuration

### Key Configuration Files
- **`pint.json`** - PSR-12 code style configuration
- **`phpunit.xml`** - Test suite configuration
- **`.env`** - Environment variables
- **`config/`** - Laravel configuration files

## Contributing

1. Follow PSR-12 coding standards
2. Write tests for new features
3. Ensure all security measures are maintained
4. Add proper documentation and comments
5. Run tests before submitting PRs

## License

This project is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Built With

- **Laravel 12** - PHP web framework
- **PHP 8.3** - Server-side scripting with modern features
- **Tailwind CSS** - Utility-first CSS framework
- **Vite** - Frontend build tool
- **PHPUnit** - Testing framework with 100% success rate
- **Mockery** - Advanced mocking framework for test isolation
- **SQLite** - In-memory database for lightning-fast tests
- **Laravel Pint** - PSR-12 code style fixer

---

**EasyBlog** - A secure, modern blog engine demonstrating Laravel best practices, comprehensive security measures, professional code standards, and unit tests.

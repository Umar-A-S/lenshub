# Project: LensHub

This is a Laravel 13 application designed for rental management.

## Project Overview

- **Framework:** Laravel 13 (PHP 8.3+)
- **Architecture:** Traditional Laravel MVC with Blade templating.
- **Frontend:** Vite, Tailwind CSS, Alpine.js.
- **Key Dependencies:** Laravel Breeze for authentication.

## Building and Running

The project includes standard composer scripts for common workflows:

- **Initial Setup:** `composer setup` (installs dependencies, generates key, runs migrations, builds assets).
- **Development Server:** `composer dev` (starts development server, queue listener, logs watcher, and Vite).
- **Testing:** `composer test` (clears config and runs PHPUnit tests).

Use `php artisan` for all CLI interactions (e.g., `php artisan make:controller`, `php artisan migrate`).

## Development Conventions

- **Coding Standard:** Adhere to PSR-4 autoloading as defined in `composer.json`.
- **Testing:** Unit and Feature tests are located in the `tests/` directory. Use PHPUnit (`composer test`).
- **Styling:** Use Tailwind CSS for styling and Alpine.js for interactive components in Blade views.
- **Git:** Use conventional commit messages. Do not commit `.env` or sensitive files.

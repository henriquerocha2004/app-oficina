# Testing Multi-Tenancy with Central Database Migrations

## Overview

This project uses a multi-tenant architecture with two separate databases:
- **Central Database**: Stores admin users, impersonation logs, tenants, domains, etc.
- **Tenant Database**: Stores tenant-specific data (users, roles, clients, vehicles, etc.)

Laravel's `RefreshDatabase` trait handles migrations for the default database (central) but does not automatically run migrations in subdirectories like `database/migrations/central/`.

## Setup for Testing

Before running tests that require central database tables (like `admin_users`, `impersonation_logs`), you must manually run the central migrations **once**:

```bash
DB_DATABASE=app_oficina_central_test php artisan migrate --database=central --path=database/migrations/central --force
```

This creates the required tables:
- `admin_users` - Admin user accounts for impersonation
- `impersonation_logs` - Audit trail of impersonation sessions

## Why Manual Migration?

The `RefreshDatabase` trait:
1. Only runs `migrate:fresh` once at the start of the test suite (controlled by `RefreshDatabaseState::$migrated`)
2. Uses database transactions for subsequent tests (no fresh migrations)
3. Drops ALL tables including central when running `migrate:fresh`
4. Does not automatically discover migrations in subdirectories

Since central migrations are in a subfolder (`database/migrations/central/`), they must be run manually after the main `migrate:fresh` completes.

## Running Tests

```bash
# Run central migrations (once per environment setup)
DB_DATABASE=app_oficina_central_test php artisan migrate --database=central --path=database/migrations/central --force

# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/Admin/ImpersonationControllerTest.php
```

## Central Migrations

Current central migrations:
- `2026_01_04_000001_create_admin_users_table.php` - Admin authentication
- `2026_01_04_000003_create_impersonation_logs_table.php` - Audit logging

To add new central migrations, create them in `database/migrations/central/` and run the manual migration command above.

# Kacita - Project Workflow

## Overview

Kacita is a membership and bursary management platform built with Laravel 12, Livewire 4, and Flux UI. It manages market trader memberships via QR campaign registration, subscription billing, school/student records, and bursary applications.

## Tech Stack

- **Framework:** Laravel 12 (PHP 8.2+)
- **Frontend:** Livewire 4 + Flux UI components
- **Auth:** Laravel Fortify (with 2FA support)
- **Testing:** Pest 4
- **Linting:** Laravel Pint
- **Dev tooling:** Laravel Boost, Laravel Sail, Laravel Pail
- **Database:** MySQL (production), SQLite (testing)

## Commands

| Task | Command |
|------|---------|
| Run all tests | `php artisan test` |
| Run specific test file | `php artisan test --filter=ModelTest` |
| Lint code | `composer lint` |
| Check lint without fixing | `composer test:lint` |
| Full test suite (lint + tests) | `composer test` |
| Fresh migrate + seed | `php artisan migrate:fresh --seed` |
| Dev server (all services) | `composer dev` |
| Setup project | `composer setup` |

## Project Structure

```
app/
├── Models/              # Eloquent models (10 models)
│   ├── User.php         # Auth user with roles (admin/member)
│   ├── Member.php       # Market trader membership
│   ├── Subscription.php # Member billing plans
│   ├── QrCampaign.php   # QR registration campaigns
│   ├── School.php       # Partner schools
│   ├── Student.php      # Student records
│   ├── BursaryApplication.php
│   ├── Document.php     # Polymorphic file attachments
│   ├── Message.php      # SMS/email/push notifications
│   └── ActivityLog.php  # Audit trail
├── Observers/
│   └── QrCampaignObserver.php  # Maintains registrations_count on Member events
└── Providers/
    └── AppServiceProvider.php

database/
├── factories/           # 10 factories (one per model)
├── migrations/          # Timestamped migrations
└── seeders/
    └── DatabaseSeeder.php  # Coherent dev scenario

tests/
├── Feature/
│   ├── Models/ModelTest.php  # 67 model-layer tests
│   ├── Auth/                 # Authentication tests
│   ├── Settings/             # Profile/password/2FA tests
│   └── DashboardTest.php
└── Unit/
```

## Model Conventions

### Mass Assignment

All models use explicit `$fillable` arrays. System-generated fields are excluded:

- `member_number`, `student_number`, `reference_number` — auto-generated identifiers
- `code` (QrCampaign) — system-generated
- `registrations_count` — observer-managed counter
- `reviewed_by`, `reviewed_at`, `amount_approved`, `rejection_reason` — admin-only
- `delivery_count`, `sent_at` — dispatch system
- `ip_address` — server-side captured

### Soft Deletes

All models except `Message` and `ActivityLog` use `SoftDeletes`.

### Route Keys

- `Member` routes by `member_number`
- `Student` routes by `student_number`
- `QrCampaign` routes by `code`
- `BursaryApplication` routes by `reference_number`

### Query Scopes

| Model | Scopes |
|-------|--------|
| Member | `active()`, `pending()` |
| Subscription | `active()`, `pending()`, `expired()` |
| Student | `active()`, `pending()` |
| BursaryApplication | `pending()`, `approved()`, `rejected()` |
| Message | `draft()`, `sent()` |

### Polymorphic Relationships

`Document` is polymorphic via `documentable`. Currently used by:
- `Member` (morphMany)
- `Student` (morphMany)
- `BursaryApplication` (morphMany)

### Observer

`QrCampaignObserver` observes `Member` model events to maintain `QrCampaign.registrations_count`:
- `created` — increment if member has `qr_campaign_id`
- `deleted` — decrement
- `restored` — increment

Registered in `AppServiceProvider::boot()`.

## Factory States

| Factory | States |
|---------|--------|
| User | `admin()`, `inactive()`, `unverified()`, `withTwoFactor()` |
| Member | `active()`, `pending()`, `fromQrCampaign(?QrCampaign)` |
| Subscription | `active()`, `pending()`, `expired()` |
| Student | `active()`, `pending()`, `withUser(?User)` |
| BursaryApplication | `pending()`, `approved()`, `rejected()` |
| Message | `draft()`, `sent()` |
| Document | `forMember(?Member)` |

## User Roles

- `admin` — full platform access
- `member` — default role for market traders

## Status Fields

Most models use a `status` string column:

- **User:** `active`, `inactive`
- **Member:** `pending`, `active`
- **Subscription:** `pending`, `active`, `expired`
- **Student:** `pending`, `active`
- **BursaryApplication:** `pending`, `approved`, `rejected`
- **Message:** `draft`, `sent`

## Testing

- Tests use SQLite in-memory via `RefreshDatabase`
- Pest 4 is the test framework (no PHPUnit test classes)
- Model tests live in `tests/Feature/Models/ModelTest.php`
- Auth/settings tests are in `tests/Feature/Auth/` and `tests/Feature/Settings/`
- Run `php artisan test` to execute all 100 tests

## Code Style

- Laravel Pint enforces PSR-12 + Laravel preset
- Run `composer lint` before committing
- No docblocks on simple methods; use type hints instead
- Use `fake()` helper (not `$this->faker`) in factories

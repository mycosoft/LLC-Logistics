# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

LLC Express Logistics is a Laravel 12 logistics management system for tracking shipments, managing clients, handling invoices/payments, and communicating via WhatsApp. The application uses AdminLTE for the admin panel and supports both Meta WhatsApp Cloud API and local WhatsApp providers.

## Development Commands

### Full Stack Development
```bash
composer dev        # Start Laravel server, queue worker, logs, and Vite in parallel
php artisan serve   # Start Laravel server only
npm run dev         # Start Vite dev server
npm run build       # Build assets for production
```

### Testing
```bash
composer test       # Run tests (clears config cache first)
php artisan test    # Run tests directly
php artisan test --filter ShipmentEnhancementTest  # Run specific test class
```

### Database
```bash
php artisan migrate:fresh --seed  # Fresh migration with seeders
php artisan migrate               # Run pending migrations
php artisan db:seed               # Run seeders
```

### Queue & Jobs
```bash
php artisan queue:listen --tries=1  # Process queue jobs (used by composer dev)
php artisan queue:work              # Alternative queue worker
php artisan queue:flush             # Clear failed jobs
```

### Cache & Config
```bash
php artisan config:clear    # Clear config cache (required before running tests)
php artisan cache:clear     # Clear application cache
php artisan route:clear     # Clear route cache
php artisan view:clear      # Clear compiled views
```

## Architecture

### WhatsApp Notification System

The application uses a **provider-based architecture** for WhatsApp messaging:

- **WhatsAppService** (`app/Services/WhatsAppService.php`): Main service class that delegates to providers
- **WhatsAppProviderInterface** (`app/Services/WhatsApp/WhatsAppProviderInterface.php`): Interface for providers
- **MetaWhatsAppProvider** (`app/Services/WhatsApp/MetaWhatsAppProvider.php`): Official Meta Cloud API
- **LocalWhatsAppProvider** (`app/Services/WhatsApp/LocalWhatsAppProvider.php`): Local go-whatsapp-web-multidevice API

Configure provider in `.env`:
```env
WHATSAPP_PROVIDER=meta           # or 'local'
WHATSAPP_ACCESS_TOKEN=xxx        # For Meta provider
WHATSAPP_PHONE_NUMBER_ID=xxx     # For Meta provider
WHATSAPP_LOCAL_API_URL=http://... # For local provider
```

### Notification Architecture

- **Notifications**: Use Laravel's notification system via `app/Notifications/`
  - `ShipmentStatusChanged`: Email + WhatsApp for status updates
  - `InvoiceSent`, `ReceiptSent`: Invoice/payment notifications
  - `WhatsAppMessage`: Generic WhatsApp message notification

- **Channels**: Custom `WhatsAppChannel` in `app/Notifications/Channels/WhatsAppChannel.php`

- **Jobs** (Rate-limited to prevent WhatsApp bans):
  - `SendBatchShipmentNotificationJob`: Sends shipment status updates
  - `SendBulkNotificationsJob`: Broadcast messages with staggered 3-5 second delays
  - `SendDelayedWhatsAppJob`: Individual WhatsApp message with delay

### Key Models

- **Shipment**: Core model with auto-generated tracking numbers (format: `BRY-YYYYMMDD-NNNNNN`)
  - Relationships: `client()`, `receiver()`, `invoice()`, `invoices()`, `statusUpdates()`, `batch()`
- **Client**: Clients/senders with notification preferences
- **ShipmentBatch**: Groups shipments by cargo type (air/sea)
- **Invoice**: Shipments can have multiple invoices
- **Payment**: Linked to invoices with auto-generated receipt numbers
- **Setting**: Key-value configuration storage (uses `Setting::get('key', default)`)

### Event System

- **ShipmentStatusUpdatedEvent**: Fired when shipment status changes
- **NotifyClientListener**: Listens for status updates and sends notifications

### Role-Based Access Control

Uses `spatie/laravel-permission` package:
- Roles and permissions stored in database
- `CheckRole` middleware for route protection
- Default roles seeded: admin, staff, etc.

## Frontend Structure

- **AdminLTE**: Configured in `config/adminlte.php`
- **Styling**: Tailwind CSS + Bootstrap (AdminLTE uses Bootstrap)
- **Build Tool**: Vite with `laravel-vite-plugin`
- **Views**: `resources/views/` organized by feature (shipments, clients, invoices, etc.)

## Testing

- **PHPUnit**: Configured in `phpunit.xml`
- **Test Database**: Uses SQLite in-memory (`:memory:`) for tests
- **Test Suites**: Unit (`tests/Unit/`) and Feature (`tests/Feature/`)
- **Key Test Files**:
  - `tests/Feature/Auth/`: Authentication tests (Laravel Breeze)
  - `tests/Feature/ShipmentEnhancementTest.php`: Shipment feature tests

## Configuration Files

- `config/services.php`: Third-party service configs including WhatsApp settings
- `config/permission.php`: Spatie permission package configuration
- `config/adminlte.php`: AdminLTE panel configuration

## Important Implementation Notes

1. **Tracking Numbers**: Auto-generated on Shipment creation via model boot event
2. **WhatsApp Rate Limiting**: Bulk notifications use staggered delays (3-5 seconds) to prevent bans
3. **Settings Model**: Uses `Setting::get('key', default)` pattern for runtime configuration
4. **Queue Jobs**: All notification jobs implement `ShouldQueue` with retry logic (`$tries = 3`, `$backoff = 10`)
5. **Currency**: Default currency is UGX (Ugandan Shilling) - defined in settings/migrations

## Default Credentials

After seeding: `admin@admin.com` / `password`

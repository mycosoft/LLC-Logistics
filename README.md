# Bryanz Logistics Management System

A comprehensive Laravel-based logistics management system for tracking shipments, managing clients, handling invoices and payments, and communicating with customers via WhatsApp.

## 🚚 Features

### Shipment Management
- **Air Cargo & Sea Cargo** - Track shipments by cargo type (air and sea freight)
- **Real-time Tracking** - Track shipments with unique tracking numbers
- **Status Updates** - Update shipment status with history tracking
- **Batch Management** - Group shipments into batches for organized handling
- **Packing Lists** - Generate packing lists for air and sea cargo
- **Delivery Labels** - Print delivery labels for shipments
- **CBM Calculation** - Calculate Cubic Meter measurements for cargo

### Client Management
- **Client Database** - Manage client information (name, phone, email, address)
- **Import/Export** - Import clients from CSV files
- **Client Reports** - View client activity and shipment history

### Invoicing & Payments
- **Invoice Generation** - Create invoices for shipments
- **Payment Tracking** - Record and track payments
- **Receipt Generation** - Generate payment receipts with tracking numbers
- **Outstanding Reports** - Track unpaid invoices

### Reports & Analytics
- **Revenue Reports** - Track income and financial performance
- **Shipment Reports** - Monitor shipment status and trends
- **Client Reports** - Client activity analytics
- **Outstanding Balances** - Track overdue payments
- **PDF Export** - Export reports to PDF format

### Notifications
- **Email Notifications** - Send shipment status updates via email
- **WhatsApp Integration** - Send notifications via WhatsApp
- **Bulk Messaging** - Broadcast messages to multiple clients
- **Multi-Provider Support** - Support for Meta WhatsApp API and local providers

### User Management
- **Role-Based Access** - AdminLTE admin panel with role permissions
- **User Authentication** - Login, registration, password management
- **Profile Management** - Users can update their profiles

### System Settings
- **Company Settings** - Configure company information
- **Notification Settings** - Configure email and WhatsApp settings

## 🛠️ Tech Stack

- **Framework:** Laravel 11
- **Admin Panel:** AdminLTE 3
- **Database:** MySQL/SQLite
- **Authentication:** Laravel Breeze/Jetstream
- **Notifications:** Mail & WhatsApp
- **Styling:** Tailwind CSS + Bootstrap

## 📋 Prerequisites

- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL or SQLite

## 🚀 Installation

1. Clone the repository:
```bash
git clone https://github.com/mycosoft/Bryan-Logistics.git
```

2. Install dependencies:
```bash
composer install
npm install
```

3. Copy environment file:
```bash
cp .env.example .env
```

4. Generate app key:
```bash
php artisan key:generate
```

5. Run migrations:
```bash
php artisan migrate --seed
```

6. Build assets:
```bash
npm run build
```

7. Start the server:
```bash
php artisan serve
```

## 📱 Default Login

After seeding, you can login with:
- **Email:** admin@admin.com
- **Password:** password

## 📁 Project Structure

```
Bryanz Logistics/
├── app/
│   ├── Console/Commands/     # Custom artisan commands
│   ├── Http/Controllers/     # Application controllers
│   ├── Models/               # Eloquent models
│   ├── Notifications/        # Email & WhatsApp notifications
│   ├── Services/             # Business logic services
│   └── Providers/            # Service providers
├── database/
│   ├── migrations/           # Database migrations
│   └── seeders/              # Database seeders
├── resources/views/          # Blade templates
│   ├── shipments/            # Shipment management views
│   ├── clients/              # Client management views
│   ├── invoices/             # Invoice views
│   ├── payments/             # Payment views
│   ├── reports/              # Report views
│   └── batches/              # Batch management views
└── routes/                   # Application routes
```

## 🔧 Configuration

### WhatsApp Setup

The system supports WhatsApp notifications via:
- **Meta WhatsApp API** - Official Meta Business API
- **Local WhatsApp Provider** - Local server-based solution

Configure in `.env`:
```env
WHATSAPP_PROVIDER=meta
META_ACCESS_TOKEN=your_token
PHONE_NUMBER_ID=your_phone_id
```

### Email Settings

Configure SMTP settings in `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
```

## 📊 Database Schema

Key tables:
- `users` - System users with roles
- `clients` - Client information
- `shipments` - Shipment records
- `shipment_batches` - Batch groupings
- `shipment_status_updates` - Status history
- `invoices` - Invoice records
- `invoice_items` - Line items
- `payments` - Payment records
- `settings` - System configuration

## 🎯 Usage

### Creating a Shipment
1. Navigate to Shipments → Create New
2. Fill in sender, receiver, and cargo details
3. Assign to a batch if needed
4. Save to generate tracking number

### Tracking a Shipment
1. Go to Tracking page
2. Enter tracking number
3. View current status and history

### Managing Batches
1. Navigate to Batches
2. Create new batch with cargo type (Air/Sea)
3. Add shipments to batch
4. Generate packing list

### Sending Notifications
1. Go to Broadcast
2. Select recipients
3. Type message
4. Send via WhatsApp

## 📄 License

This project is proprietary software.

## 📞 Support

For support, please contact the development team.

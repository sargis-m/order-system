# Order Management System

A comprehensive order management system built with Laravel 12 and Filament 4, designed to handle order processing between partners, customers, and administrators.

## 📋 Table of Contents

- [Goal of the Application](#goal-of-the-application)
- [Features](#features)
- [Technology Stack](#technology-stack)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Configuration](#configuration)
- [Running the Application](#running-the-application)
- [How the Application Works](#how-the-application-works)
- [User Roles and Permissions](#user-roles-and-permissions)
- [Default Test Accounts](#default-test-accounts)
- [Project Structure](#project-structure)

---

## 🎯 Goal of the Application

The Order Management System is designed to facilitate order processing in a multi-tenant business environment where:

- **Partners** (business partners/vendors) can register and manage their customers
- **Customers** can place orders that need approval
- **Administrators** can review, accept, or reject orders

The system ensures proper access control, where each user type can only access and manage data relevant to their role, maintaining data isolation and security.

---

## ✨ Features

- **Multi-Panel Interface**: Separate admin, partner, and customer dashboards using Filament panels
- **Role-Based Access Control**: Fine-grained permissions using Spatie Laravel Permission
- **Order Management**: Complete order lifecycle from creation to approval/rejection
- **Customer Management**: Partners can register and manage their customers
- **Data Isolation**: Users can only view and manage their own data (or data they're authorized to see)
- **Modern UI**: Built with Filament 4 for a beautiful, responsive admin interface

---

## 🛠 Technology Stack

- **Framework**: Laravel 12
- **Admin Panel**: Filament 4
- **PHP**: 8.2+
- **Database**: MySQL
- **Authentication**: Laravel's built-in authentication
- **Authorization**: Spatie Laravel Permission
- **Frontend**: Filament's built-in components (Alpine.js, Tailwind CSS)

---

## 📦 Prerequisites

Before you begin, ensure you have the following installed:

- PHP 8.2 or higher
- Composer
- Node.js and npm
- A database (SQLite, MySQL, or PostgreSQL)

---

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/sargis-m/order-system.git
cd order-system
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Environment Setup

```bash
# Copy the environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure Database

Edit the `.env` file and configure your database:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
```

Or for MySQL/PostgreSQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=order_system
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 5. Run Migrations and Seeders

```bash
# Create database file (if using SQLite)
touch database/database.sqlite

# Run migrations
php artisan migrate

# Seed roles and permissions
php artisan db:seed --class=RolesAndPermissionsSeeder

# Seed test users
php artisan db:seed --class=UsersSeeder
```

### 6. Build Frontend Assets

```bash
npm run build
```

Or for development with hot reload:

```bash
npm run dev
```

---

## ⚙️ Configuration

### Filament Panels

The application uses three Filament panels:

- **Admin Panel**: `/admin` - For administrators
- **Partner Panel**: `/partner` - For business partners
- **Customer Panel**: `/customer` - For customers

Each panel is configured in:
- `app/Providers/Filament/AdminPanelProvider.php`
- `app/Providers/Filament/PartnerPanelProvider.php`
- `app/Providers/Filament/CustomerPanelProvider.php`

### Custom Login Pages

Each panel has a custom login page with appropriate branding:
- `app/Filament/Auth/Pages/AdminLogin.php`
- `app/Filament/Auth/Pages/PartnerLogin.php`
- `app/Filament/Auth/Pages/CustomerLogin.php`

---

## 🏃 Running the Application

### Development Server

```bash
# Start Laravel development server
php artisan serve

# In another terminal, start Vite for frontend assets (if needed)
npm run dev
```

The application will be available at `http://localhost:8000`

### Using Composer Scripts

```bash
# Setup everything (install dependencies, migrate, build)
composer run setup

# Run development server with queue, logs, and vite
composer run dev
```

### Production

```bash
# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Build frontend assets
npm run build
```

---

## 🔄 How the Application Works

### 1. **User Authentication & Authorization**

The system uses Laravel's authentication with Spatie Laravel Permission for role-based access control:

- Users are assigned one of three roles: `admin`, `partner`, or `customer`
- Each role has specific permissions that control what actions they can perform
- Filament panels automatically check user roles and redirect unauthorized users

### 2. **Order Lifecycle**

```
1. Order Creation
   ├─ Partner: Creates order for one of their customers
   └─ Customer: Creates their own order
   
2. Order Status: "pending"
   └─ Order is created with status "pending"
   
3. Admin Review
   ├─ Admin views all orders
   ├─ Can Accept → Status: "accepted"
   └─ Can Reject → Status: "rejected"
```

### 3. **Data Access Control**

The system implements strict data isolation:

- **Admins**: Can view all orders but cannot edit them (only accept/reject)
- **Partners**: 
  - Can view only orders where they are the partner
  - Can create orders for their registered customers
  - Can register new customers
- **Customers**: 
  - Can view only their own orders
  - Can create their own orders
  - Cannot see other customers' data

### 4. **Order Management Flow**

#### For Partners:
1. Login to `/partner`
2. Register customers (if needed) via "Customers" menu
3. Create orders for registered customers
4. View and edit their orders
5. Cannot change order status (only admins can)

#### For Customers:
1. Login to `/customer`
2. Create their own orders
3. View and edit their own orders
4. Cannot change order status

#### For Admins:
1. Login to `/admin`
2. View all orders in the system
3. Accept or reject pending orders
4. Cannot edit order details (only view and status changes)

### 5. **Customer Registration**

Partners can register customers:
- When creating a customer, the system automatically:
  - Assigns the customer role
  - Links the customer to the partner (`partner_id`)
  - Sets up the customer account with email and password

### 6. **Order Status Management**

Order statuses are managed through constants (`app/Models/Constants/OrderStatus.php`):
- `pending`: Initial status when order is created
- `accepted`: Order approved by admin
- `rejected`: Order rejected by admin

Status changes are handled through model methods:
- `Order::accept()` - Validates and accepts an order
- `Order::reject()` - Validates and rejects an order

---

## 👥 User Roles and Permissions

### Admin Role
**Permissions:**
- `view all orders` - Can view all orders in the system
- `accept orders` - Can accept pending orders
- `reject orders` - Can reject pending orders

**Capabilities:**
- View all orders regardless of partner/customer
- Accept or reject orders
- Cannot edit order details
- Cannot create orders

### Partner Role
**Permissions:**
- `view own orders` - Can view orders where they are the partner
- `place order` - Can create orders for their customers
- `register customer` - Can register new customers

**Capabilities:**
- Register and manage customers
- Create orders for registered customers
- View and edit their own orders
- Cannot change order status
- Cannot see other partners' orders

### Customer Role
**Permissions:**
- `view own orders` - Can view only their own orders
- `place order` - Can create their own orders

**Capabilities:**
- Create their own orders
- View and edit their own orders
- Cannot change order status
- Cannot see other customers' orders

---

## 🔑 Default Test Accounts

After running the seeders, you can use these test accounts:

### Admin
- **Email**: `admin@test.com`
- **Password**: `admin123`
- **URL**: `http://localhost:8000/admin`

### Partners
- **Email**: `partner1@test.com`, `partner2@test.com`, `partner3@test.com`
- **Password**: `partner123`
- **URL**: `http://localhost:8000/partner`

### Customers
- **Email**: `customer_p1_1@test.com`, `customer_p1_2@test.com`, etc.
- **Password**: `customer123`
- **URL**: `http://localhost:8000/customer`

Or use the standalone customer:
- **Email**: `customer@test.com`
- **Password**: `customer123`

---

## 📁 Project Structure

```
order-system_1/
├── app/
│   ├── Concerns/
│   │   └── HasUserAuthorization.php      # Authorization trait
│   ├── Filament/
│   │   ├── Auth/Pages/                    # Custom login pages
│   │   ├── Resources/
│   │   │   ├── Customers/                 # Customer resource
│   │   │   └── Orders/                    # Order resource
│   │   └── Providers/                     # Filament panel providers
│   ├── Models/
│   │   ├── Constants/                     # Role, Permission, OrderStatus constants
│   │   ├── Order.php                      # Order model
│   │   └── User.php                       # User model
│   └── Providers/
├── database/
│   ├── migrations/                        # Database migrations
│   └── seeders/
│       ├── RolesAndPermissionsSeeder.php # Seeds roles and permissions
│       └── UsersSeeder.php                # Seeds test users
├── resources/
│   ├── views/
│   │   ├── index.blade.php                # Landing page
│   │   └── filament/
│   │       └── auth/
│   │           └── home-link.blade.php    # Home link in login pages
│   └── ...
└── routes/
    └── web.php                            # Web routes
```

### Key Components

**Models:**
- `Order`: Represents an order with relationships to partner and customer
- `User`: Represents users (can be admin, partner, or customer)

**Resources:**
- `OrderResource`: Manages order CRUD operations with role-based access
- `CustomerResource`: Manages customer registration and management

**Constants:**
- `Role`: Defines user roles (admin, partner, customer)
- `Permission`: Defines system permissions
- `OrderStatus`: Defines order statuses (pending, accepted, rejected)

**Traits:**
- `HasUserAuthorization`: Provides helper methods for authorization checks

---

## 🔒 Security Features

- Role-based access control (RBAC)
- Permission-based authorization
- Data isolation between users
- Secure password hashing
- CSRF protection (Laravel default)
- SQL injection protection (Eloquent ORM)
- XSS protection (Filament default)

---

## 🧪 Testing

```bash
# Run tests
php artisan test

# Or use composer script
composer run test
```

---

## 📝 Additional Notes

- The application uses eager loading to prevent N+1 query problems
- Constants are used instead of magic strings for better maintainability
- Authorization logic is centralized in the `HasUserAuthorization` trait
- Order status changes are handled through model methods with validation
- All panels have custom login pages with appropriate branding

---

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 🆘 Troubleshooting

### Common Issues

**Issue**: "Class not found" errors
- **Solution**: Run `composer dump-autoload`

**Issue**: Database connection errors
- **Solution**: Check `.env` file database configuration

**Issue**: Permission denied errors
- **Solution**: Ensure storage and bootstrap/cache directories are writable:
  ```bash
  chmod -R 775 storage bootstrap/cache
  ```

**Issue**: Assets not loading
- **Solution**: Run `npm run build` or `npm run dev`

---

---

*Last updated: 2025*

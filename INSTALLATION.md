# CRM Installation & Setup Guide

## Quick Start Commands

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Build frontend assets
npm run build

# Run migrations with seed data
php artisan migrate --seed

# Start development server
php artisan serve
```

Then open: http://127.0.0.1:8000

---

## Default Login Credentials

### Admin Account
- Email: `admin@example.com`
- Password: `password`
- Role: Administrator (full access)

### Staff Account  
- Email: `staff@example.com`
- Password: `password`
- Role: Staff (cannot manage users)

---

## Key Features Implemented

### 1. Authentication & Roles
- Laravel Breeze (Blade stack)
- Role-based middleware (admin/staff)
- Arabic RTL interface

### 2. Database Structure
- **Users** table with role enum
- **Campaigns** table (platforms, ad types, sources)
- **Leads** table with auto-generated codes (LEAD-000001)
- **Orders** table linked to leads
- **Order Items** table for product details
- Foreign keys with cascade deletes
- Indexed fields for performance

### 3. Business Logic
- Auto-generated lead codes after creation
- Automatic line_total calculation (quantity × unit_price)
- Automatic order total calculation (sum of items)
- Automatic lead expected_value calculation (sum of orders)
- Observer-based recalculations (no recursion loops)

### 4. CRUD Operations
- **Leads**: Create, Read, Update, Delete
  - Search by name/phone/code
  - Filter by platform/status/date
  - View details with orders list
  
- **Orders**: Full CRUD
  - Create orders under leads (nested routes)
  - Dynamic item rows (add/remove)
  - Real-time total calculation
  
- **Campaigns**: Full CRUD
- **Users**: Full CRUD (admin only)

### 5. Dashboard
- Today's new leads count
- Leads by status breakdown
- Revenue (last 7 & 30 days)
- Top platforms by leads
- Recent leads list

### 6. Validations
- Phone required with format check
- Email optional with format validation
- Quantity must be >= 1
- Unit price must be >= 0
- Status enum validation
- Date validation for follow_up_date

### 7. Views
- All pages in Arabic (RTL)
- Tailwind CSS styling
- Responsive design
- Success/error flash messages
- Navigation menu with role-based visibility

---

## File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── DashboardController.php
│   │   ├── LeadController.php
│   │   ├── OrderController.php
│   │   ├── CampaignController.php
│   │   └── UserController.php
│   ├── Middleware/
│   │   ├── IsAdmin.php
│   │   └── IsStaff.php
│   └── Requests/
│       ├── StoreLeadRequest.php
│       ├── UpdateLeadRequest.php
│       ├── StoreOrderRequest.php
│       ├── UpdateOrderRequest.php
│       ├── StoreCampaignRequest.php
│       └── UpdateCampaignRequest.php
├── Models/
│   ├── User.php
│   ├── Campaign.php
│   ├── Lead.php
│   ├── Order.php
│   └── OrderItem.php
├── Observers/
│   └── OrderItemObserver.php
└── Providers/
    └── BladeServiceProvider.php

database/
├── migrations/
│   ├── 2024_01_22_000001_add_role_to_users_table.php
│   ├── 2024_01_22_000002_create_campaigns_table.php
│   ├── 2024_01_22_000003_create_leads_table.php
│   ├── 2024_01_22_000004_create_orders_table.php
│   └── 2024_01_22_000005_create_order_items_table.php
└── seeders/
    └── DatabaseSeeder.php

resources/views/
├── layouts/
│   └── app.blade.php (RTL layout)
├── dashboard.blade.php
├── leads/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
├── orders/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
├── campaigns/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
└── users/
    ├── index.blade.php
    ├── create.blade.php
    └── edit.blade.php
```

---

## Technical Notes

### Observer Implementation
The `OrderItemObserver` handles recalculation safely:
- Recalculates order total when item created/updated/deleted
- Recalculates lead expected_value after order update
- Uses `saveQuietly()` to prevent infinite loops

### Lead Code Generation
- Code format: `LEAD-000001`
- Generated in model boot event after creation
- Uses lead ID for sequential numbering

### Total Calculations
- Line Total: Automatically calculated in `OrderItem` model before save
- Order Total: Sum of all items' line_total
- Lead Expected Value: Sum of all orders' total_value

### Role-Based Access
- Admin: Can access all features including user management
- Staff: Can manage leads, orders, campaigns but NOT users
- Middleware: `is_admin` and `is_staff`

---

## Sample Data Included

- 2 users (admin + staff)
- 7 campaigns (one per platform)
- 30 leads with various statuses and dates
- Multiple orders per lead
- Multiple order items per order
- Products: Coffee, Green Tea, Honey, Dates, Olive Oil
- Cities: Riyadh, Jeddah, Makkah, Madinah, Dammam
- Dates spread over 60 days for dashboard stats

---

## Testing Flow

1. Login as admin
2. Create a campaign (optional)
3. Create a lead
4. Verify lead code auto-generated (LEAD-000001)
5. Add an order to the lead
6. Add multiple items with different variants
7. Verify totals calculated correctly
8. Check dashboard stats updated
9. Test search and filter functionality

---

## Troubleshooting

### If migrations fail:
```bash
php artisan migrate:fresh
```

### If frontend assets missing:
```bash
npm install && npm run build
```

### If database connection issues:
Check `.env` file:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

---

## Next Steps

The system is production-ready with:
- ✅ Complete authentication system
- ✅ Role-based access control
- ✅ Full CRUD operations
- ✅ Automatic calculations
- ✅ Arabic RTL interface
- ✅ Sample data for testing
- ✅ Validations & error handling
- ✅ Responsive design with Tailwind

Ready to deploy and use!

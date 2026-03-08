# MADO POS - Quick Reference Guide

## 🚀 Installation (5 minutes)

```bash
cd mado-pos
composer install
npm install
php artisan migrate:fresh --seed
npm run build
```

## 🏃 Running (2 terminals)

**Terminal 1:**
```bash
php artisan serve
```

**Terminal 2:**
```bash
npm run dev
```

Access: http://localhost:8000

## 🔐 Login
- Email: `admin@example.com`
- Password: `password`

## 📋 Menu Structure

```
Dashboard
├── Products
│   └── Detail (BOM Management)
├── Raw Materials
│   └── Detail (Stock Movements)
├── Stock Management
│   ├── Stock Input
│   └── Stock Opname
└── Transactions
```

## 🔄 Workflow

### 1. Setup (First Time)
1. Go to **Raw Materials** → Add raw materials (e.g., flour, sugar, butter)
2. Go to **Products** → Add products (e.g., cake, bread)
3. Go to **Products** → Click Detail → Add materials to BOM
   - Example: Cake needs 2kg flour, 1kg sugar, 0.5kg butter

### 2. Stock Management
1. Go to **Stock Input** → Add stock for raw materials
   - Stock will increase automatically
   - Recorded as PURCHASE

2. Go to **Stock Opname** → Adjust stock if needed
   - Positive qty = Adjustment (increase)
   - Negative qty = Waste (decrease)

### 3. Transactions
1. Go to **Transactions** → New Transaction
2. Click products to add to cart
3. Adjust quantity if needed
4. Enter discount (optional)
5. Enter paid amount (optional, auto-calculated)
6. Click Save
   - Stock will decrease based on BOM
   - Invoice generated automatically

### 4. View Stock History
1. Go to **Raw Materials** → Click Detail on any material
2. See all stock movements with date filter
3. View type (PURCHASE, SALE, ADJUSTMENT, WASTE)

## 💡 Key Features

### Stock Reduction Logic
When you sell 1 Cake that needs:
- 2kg flour
- 1kg sugar
- 0.5kg butter

The system automatically reduces:
- Flour: -2kg
- Sugar: -1kg
- Butter: -0.5kg

### Transaction Status
- **PAID**: Normal transaction
- **VOID**: Deleted transaction (stock restored)
- **REFUND**: Refunded transaction (stock unchanged)

### Stock Movement Types
- **PURCHASE**: From stock input
- **SALE**: From transaction
- **ADJUSTMENT**: From stock opname (positive)
- **WASTE**: From stock opname (negative)

## 🛠️ Common Tasks

### Add New Product
1. Products → Add Product
2. Enter name and price
3. Click Save
4. Click Detail on the product
5. Add materials to BOM

### Adjust Stock
1. Stock Opname → Add Stock Opname
2. Select raw material
3. Enter qty (+ or -)
4. Click Save

### View Stock History
1. Raw Materials → Click Detail
2. Set date range
3. View all movements

### Refund Transaction
1. Transactions → Find transaction
2. Click Refund button
3. Status changes to REFUND

### Delete Transaction
1. Transactions → Find transaction
2. Click Delete
3. Status changes to VOID
4. Stock is restored

## 📊 Dashboard Info

- **Total Products**: Count of all products
- **Raw Materials**: Count of all raw materials
- **Today Sales**: Number of transactions today
- **Today Revenue**: Total sales amount today
- **Recent Transactions**: Last 10 transactions

## 🔍 Search & Filter

### Products
- Search by product name

### Raw Materials
- Search by material name
- View stock movements with date filter

### Stock Input
- Search by raw material name

### Stock Opname
- Search by raw material name

### Transactions
- Search by invoice number

## ⚙️ Settings

### Database
- Using SQLite (database.sqlite)
- Auto-created on first migration

### Authentication
- Email/password based
- Session-based (120 minutes)

### Timezone
- Set in config/app.php (default: UTC)

## 🐛 Troubleshooting

### Port 8000 already in use
```bash
php artisan serve --port=8001
```

### Database error
```bash
php artisan config:clear
php artisan migrate:fresh --seed
```

### CSS not loading
```bash
npm run build
```

### Livewire not working
- Make sure npm run dev is running
- Clear browser cache
- Refresh page

## 📱 Responsive Design

- Works on desktop, tablet, mobile
- Sidebar collapses on small screens
- Touch-friendly buttons

## 🔒 Security

- All routes protected with auth middleware
- Passwords hashed with bcrypt
- CSRF protection enabled
- SQL injection prevention via Eloquent ORM

## 📝 Notes

- All data changes are tracked (created_by, updated_by)
- Soft deletes enabled (deleted data can be recovered)
- Stock movements are immutable audit trail
- Invoice numbers are unique and auto-generated

## 🎓 Learning Resources

- Laravel: https://laravel.com/docs
- Livewire: https://livewire.laravel.com
- Tailwind CSS: https://tailwindcss.com
- SQLite: https://www.sqlite.org

## 📞 Support

For issues or questions:
1. Check SETUP.md for installation help
2. Check README.md for feature overview
3. Check PROJECT_SUMMARY.md for technical details

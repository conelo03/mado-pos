# MADO POS - System Overview

## Introduction

MADO POS is a Point of Sale (POS) system built with Laravel and Livewire, designed to manage inventory and sales transactions efficiently. The system uses a unified Items management approach that consolidates Products and Raw Materials into a single, flexible data model.

## Core Architecture

### Database Structure

The system is built on four main tables:

#### 1. Items Table
Unified table for all inventory items (Products and Raw Materials).

**Fields:**
- `id` - Primary key
- `name` - Item name
- `type` - PRODUCT or RAW_MATERIAL
- `unit` - Unit of measurement (pcs, kg, liter, etc.)
- `price` - Item price (for products)
- `stock` - Current stock quantity
- `minimum_stock` - Minimum stock threshold
- `is_active` - Active/inactive status
- `is_track_stock` - Boolean flag for stock tracking behavior
- `created_by` - User who created the item
- `updated_by` - User who last updated the item
- `created_at`, `updated_at`, `deleted_at` - Timestamps

**Key Concept:**
- `is_track_stock = true`: Stock is tracked directly on the item
- `is_track_stock = false`: Stock is tracked through Bill of Materials (BOM)

#### 2. ItemBom Table
Bill of Materials - defines composition of products using raw materials.

**Fields:**
- `id` - Primary key
- `product_id` - Foreign key to Items (the product)
- `material_id` - Foreign key to Items (the raw material)
- `qty` - Quantity of material needed per product unit
- `created_at`, `updated_at`, `deleted_at` - Timestamps

**Usage:**
- Links products to their component materials
- Used for stock calculation when `is_track_stock = false`

#### 3. StockMovement Table
Audit trail for all stock changes across the system.

**Fields:**
- `id` - Primary key
- `item_id` - Foreign key to Items
- `type` - IN or OUT
- `qty` - Quantity moved
- `reference_id` - ID of the transaction/operation
- `reference_type` - PURCHASE, SALE, ADJUSTMENT, or WASTE
- `date` - Date of movement
- `note` - Optional notes
- `created_by` - User who created the movement
- `updated_by` - User who updated the movement
- `created_at`, `updated_at` - Timestamps

**Reference Types:**
- `PURCHASE` - Stock input (Stock Input module)
- `SALE` - Sales transaction
- `ADJUSTMENT` - Stock adjustment (Stock Opname)
- `WASTE` - Stock waste/loss (Stock Opname)

#### 4. Sales & SaleItems Tables
Transaction records.

**Sales Fields:**
- `id` - Primary key
- `invoice_no` - Unique invoice number
- `total_price` - Total transaction amount
- `paid_amount` - Amount paid by customer
- `change_amount` - Change given to customer
- `discount` - Discount applied
- `status` - PAID, VOID, or REFUND
- `created_by`, `updated_by` - User tracking
- `created_at`, `updated_at`, `deleted_at` - Timestamps

**SaleItems Fields:**
- `id` - Primary key
- `sale_id` - Foreign key to Sales
- `item_id` - Foreign key to Items
- `price` - Price at time of sale
- `qty` - Quantity sold
- `subtotal` - Price × Quantity
- `created_at`, `updated_at`, `deleted_at` - Timestamps

## Stock Management Logic

### Stock Tracking Modes

#### Mode 1: Direct Stock Tracking (is_track_stock = true)
When a product has `is_track_stock = true`:
- Stock is reduced directly from the item
- One stock movement record is created per sale
- Example: Simple products without components

#### Mode 2: BOM-Based Stock Tracking (is_track_stock = false)
When a product has `is_track_stock = false`:
- System checks the product's BOM (ItemBom records)
- Stock is reduced from each material according to BOM quantities
- Multiple stock movement records are created (one per material)
- Example: Assembled products made from raw materials

### Stock Flow

#### Sales Transaction
1. User creates a sale with items
2. For each item in the sale:
   - If `is_track_stock = true`: Reduce item stock directly
   - If `is_track_stock = false`: For each BOM entry, reduce material stock by (BOM qty × sale qty)
3. Create stock movement records with `reference_type = SALE`
4. All operations wrapped in database transaction for consistency

#### Stock Input (Purchase)
1. User records incoming stock
2. Item stock is incremented
3. Stock movement created with `reference_type = PURCHASE`

#### Stock Opname (Adjustment/Waste)
1. User records stock adjustment or waste
2. Item stock is adjusted accordingly
3. Stock movement created with `reference_type = ADJUSTMENT` or `WASTE`

#### Transaction Deletion/Void
1. When a sale is deleted:
   - Stock is restored (incremented) for all items
   - Stock movement records are deleted (they're transaction records, not history)
   - Sale status is set to VOID

## Module Overview

### 1. Items Management
- **Route**: `/items`
- **Components**: `Items/Index.php`, `Items/Detail.php`
- **Features**:
  - List all items (products and raw materials)
  - Create, edit, delete items
  - For PRODUCT type: Manage Bill of Materials
  - For RAW_MATERIAL type: View stock movements
  - Filter stock movements by date range

### 2. Transactions
- **Route**: `/transactions`
- **Components**: `Transactions/Index.php`
- **Features**:
  - Create new sales transactions
  - Edit existing transactions
  - Delete/void transactions
  - Refund transactions
  - Print receipts
  - Real-time stock calculation
  - Automatic stock reduction based on item type

### 3. Stock Management
- **Routes**: `/stock-input`, `/stock-opname`
- **Components**: `StockManagement/StockInput.php`, `StockManagement/StockOpname.php`
- **Features**:
  - Record incoming stock (purchases)
  - Record stock adjustments and waste
  - Edit and delete stock records
  - View stock movement history

### 4. Reports
- **Routes**: `/reports/by-products`, `/reports/by-transactions`
- **Components**: `Reports/ByProducts.php`, `Reports/ByTransactions.php`
- **Features**:
  - Sales report by product
  - Sales report by transaction
  - Date range filtering
  - Revenue calculation

### 5. User Management
- **Route**: `/users`
- **Components**: `Users/Index.php`
- **Features**:
  - Create and manage users
  - Assign roles (ADMIN, USER)
  - Soft delete users

## User Roles

### ADMIN
- Full access to all modules
- Can manage items, stock, users, and reports
- Can view and manage all transactions

### USER
- Can only create and view transactions
- Cannot access admin modules
- Cannot manage inventory or users

## Key Features

### 1. Database Transactions
All critical operations (save, delete) are wrapped in `DB::transaction()` to ensure data consistency.

### 2. Soft Deletes
All main entities support soft deletes for data recovery.

### 3. Audit Trail
- `created_by` and `updated_by` fields track user actions
- Stock movements provide complete audit trail
- All timestamps recorded

### 4. Real-time Calculations
- Stock levels updated immediately
- Totals calculated in real-time
- Change amount calculated automatically

### 5. Confirmation Dialogs
- Delete operations require confirmation
- Refund operations require confirmation
- Prevents accidental data loss

## Technology Stack

- **Framework**: Laravel 11
- **UI Framework**: Livewire 3 + Alpine.js
- **CSS**: Tailwind CSS + DaisyUI
- **Database**: SQLite (default, configurable)
- **Authentication**: Laravel Breeze

## File Structure

```
app/
├── Livewire/
│   ├── Dashboard.php
│   ├── ChangePassword.php
│   ├── Items/
│   │   ├── Index.php
│   │   └── Detail.php
│   ├── Transactions/
│   │   └── Index.php
│   ├── StockManagement/
│   │   ├── StockInput.php
│   │   └── StockOpname.php
│   ├── Reports/
│   │   ├── ByProducts.php
│   │   └── ByTransactions.php
│   └── Users/
│       └── Index.php
├── Models/
│   ├── Item.php
│   ├── ItemBom.php
│   ├── StockMovement.php
│   ├── Sale.php
│   ├── SaleItem.php
│   ├── User.php
│   └── ...
└── Http/
    └── Controllers/
        └── TransactionPrintController.php

database/
├── migrations/
│   ├── 2024_01_01_000011_create_items_table.php
│   ├── 2024_01_01_000012_create_item_boms_table.php
│   ├── 2024_01_01_000013_create_stock_movements_table.php
│   └── ...
└── seeders/

resources/
└── views/
    ├── components/
    │   ├── app-layout.blade.php
    │   ├── confirm-dialog.blade.php
    │   └── icon/
    └── livewire/
        ├── items/
        ├── transactions/
        ├── stock-management/
        ├── reports/
        └── users/
```

## Getting Started

1. **Setup**: Follow SETUP.md for installation and configuration
2. **Database**: Run migrations to create tables
3. **Users**: Create admin user through seeder or manually
4. **Items**: Add products and raw materials
5. **Transactions**: Start recording sales

## Next Steps

- See SETUP.md for installation instructions
- See QUICK_REFERENCE.md for common tasks
- See TROUBLESHOOTING.md for common issues

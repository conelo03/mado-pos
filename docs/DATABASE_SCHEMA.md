# Database Schema

## Overview

The MADO POS system uses a relational database with the following main tables:

```
users
├── sales
│   └── sale_items
│       └── items
│           ├── item_boms
│           │   └── items (materials)
│           ├── item_price_lists
│           │   └── price_list_types
│           └── stock_movements
├── price_list_types
│   ├── customers
│   └── item_price_lists
└── stock_movements
```

---

## Tables

### 1. users
Stores user account information.

```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('ADMIN', 'USER') DEFAULT 'USER',
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);
```

**Fields:**
- `id` - Unique identifier
- `name` - User's full name
- `email` - User's email address (unique)
- `email_verified_at` - Email verification timestamp
- `password` - Hashed password
- `role` - User role (ADMIN or USER)
- `remember_token` - Remember me token
- `created_at` - Creation timestamp
- `updated_at` - Last update timestamp
- `deleted_at` - Soft delete timestamp

**Indexes:**
- PRIMARY KEY: id
- UNIQUE: email

**Relationships:**
- HasMany: sales (created_by)
- HasMany: stock_movements (created_by)

---

### 2. items
Unified table for products and raw materials.

```sql
CREATE TABLE items (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    type ENUM('PRODUCT', 'RAW_MATERIAL') NOT NULL,
    unit VARCHAR(50) NOT NULL,
    price DECIMAL(12, 2) DEFAULT 0,
    stock DECIMAL(12, 2) DEFAULT 0,
    minimum_stock DECIMAL(12, 2) DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    is_track_stock BOOLEAN DEFAULT TRUE,
    created_by BIGINT NOT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id)
);
```

**Fields:**
- `id` - Unique identifier
- `name` - Item name
- `type` - PRODUCT or RAW_MATERIAL
- `unit` - Unit of measurement (pcs, kg, liter, etc.)
- `price` - Item price (mainly for products)
- `stock` - Current stock quantity
- `minimum_stock` - Minimum stock threshold for alerts
- `is_active` - Whether item is available for use
- `is_track_stock` - Stock tracking mode:
  - true: Direct stock tracking
  - false: BOM-based stock tracking
- `created_by` - User who created the item
- `updated_by` - User who last updated the item
- `created_at` - Creation timestamp
- `updated_at` - Last update timestamp
- `deleted_at` - Soft delete timestamp

**Indexes:**
- PRIMARY KEY: id
- FOREIGN KEY: created_by
- FOREIGN KEY: updated_by

**Relationships:**
- HasMany: item_boms (product_id)
- HasMany: stock_movements
- BelongsTo: users (created_by)
- BelongsTo: users (updated_by)

---

### 3. item_boms
Bill of Materials - defines product composition.

```sql
CREATE TABLE item_boms (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    product_id BIGINT NOT NULL,
    material_id BIGINT NOT NULL,
    qty DECIMAL(12, 2) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (product_id) REFERENCES items(id) ON DELETE CASCADE,
    FOREIGN KEY (material_id) REFERENCES items(id) ON DELETE CASCADE
);
```

**Fields:**
- `id` - Unique identifier
- `product_id` - Foreign key to items (the product)
- `material_id` - Foreign key to items (the raw material)
- `qty` - Quantity of material needed per product unit
- `created_at` - Creation timestamp
- `updated_at` - Last update timestamp
- `deleted_at` - Soft delete timestamp

**Indexes:**
- PRIMARY KEY: id
- FOREIGN KEY: product_id
- FOREIGN KEY: material_id

**Relationships:**
- BelongsTo: items (product_id)
- BelongsTo: items (material_id)

**Constraints:**
- product_id must reference an item with type = PRODUCT
- material_id must reference an item with type = RAW_MATERIAL
- qty must be > 0

---

### 4. stock_movements
Audit trail for all stock changes.

```sql
CREATE TABLE stock_movements (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    item_id BIGINT NOT NULL,
    type ENUM('IN', 'OUT') NOT NULL,
    qty DECIMAL(12, 2) NOT NULL,
    reference_id BIGINT NULL,
    reference_type ENUM('PURCHASE', 'SALE', 'ADJUSTMENT', 'WASTE') NULL,
    date DATE NOT NULL,
    note TEXT NULL,
    created_by BIGINT NOT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id)
);
```

**Fields:**
- `id` - Unique identifier
- `item_id` - Foreign key to items
- `type` - IN (stock increase) or OUT (stock decrease)
- `qty` - Quantity moved (always positive)
- `reference_id` - ID of related transaction/operation
- `reference_type` - Type of reference:
  - PURCHASE: Stock input
  - SALE: Sales transaction
  - ADJUSTMENT: Stock adjustment
  - WASTE: Stock waste/loss
- `date` - Date of movement
- `note` - Optional notes
- `created_by` - User who created the movement
- `updated_by` - User who updated the movement
- `created_at` - Creation timestamp
- `updated_at` - Last update timestamp

**Indexes:**
- PRIMARY KEY: id
- FOREIGN KEY: item_id
- FOREIGN KEY: created_by
- FOREIGN KEY: updated_by
- INDEX: (item_id, date) - For efficient date range queries

**Relationships:**
- BelongsTo: items
- BelongsTo: users (created_by)
- BelongsTo: users (updated_by)

**Constraints:**
- qty must be > 0
- type must be IN or OUT
- reference_type must match the operation type

---

### 5. price_list_types
Defines different pricing tiers for items.

```sql
CREATE TABLE price_list_types (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    type ENUM('RETAIL', 'GROSIR', 'MEMBER', 'RESELLER') NOT NULL,
    description TEXT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
);
```

**Fields:**
- `id` - Unique identifier
- `name` - Price list type name (e.g., "Retail Price", "Wholesale Price")
- `type` - Type of pricing:
  - RETAIL: Standard retail pricing
  - GROSIR: Wholesale/bulk pricing
  - MEMBER: Member-exclusive pricing
  - RESELLER: Reseller pricing
- `description` - Optional description of the price list type
- `created_by` - User who created the price list type
- `updated_by` - User who last updated the price list type
- `created_at` - Creation timestamp
- `updated_at` - Last update timestamp
- `deleted_at` - Soft delete timestamp

**Indexes:**
- PRIMARY KEY: id
- FOREIGN KEY: created_by
- FOREIGN KEY: updated_by

**Relationships:**
- HasMany: customers
- HasMany: item_price_lists
- BelongsTo: users (created_by)
- BelongsTo: users (updated_by)

---

### 6. customers
Customer information with assigned price list type.

```sql
CREATE TABLE customers (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    price_list_type_id BIGINT NOT NULL,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NULL,
    address TEXT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (price_list_type_id) REFERENCES price_list_types(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
);
```

**Fields:**
- `id` - Unique identifier
- `price_list_type_id` - Foreign key to price_list_types
- `name` - Customer name
- `phone` - Customer phone number (optional)
- `address` - Customer address (optional)
- `created_by` - User who created the customer record
- `updated_by` - User who last updated the customer record
- `created_at` - Creation timestamp
- `updated_at` - Last update timestamp
- `deleted_at` - Soft delete timestamp

**Indexes:**
- PRIMARY KEY: id
- FOREIGN KEY: price_list_type_id
- FOREIGN KEY: created_by
- FOREIGN KEY: updated_by

**Relationships:**
- BelongsTo: price_list_types
- BelongsTo: users (created_by)
- BelongsTo: users (updated_by)

---

### 7. item_price_lists
Item-specific pricing for different price list types.

```sql
CREATE TABLE item_price_lists (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    item_id BIGINT NOT NULL,
    price_list_type_id BIGINT NOT NULL,
    price DECIMAL(12, 2) NOT NULL,
    created_by BIGINT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    UNIQUE KEY unique_item_price_list (item_id, price_list_type_id),
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE,
    FOREIGN KEY (price_list_type_id) REFERENCES price_list_types(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
);
```

**Fields:**
- `id` - Unique identifier
- `item_id` - Foreign key to items
- `price_list_type_id` - Foreign key to price_list_types
- `price` - Price for this item in this price list type
- `created_by` - User who created the price list entry
- `updated_by` - User who last updated the price list entry
- `created_at` - Creation timestamp
- `updated_at` - Last update timestamp
- `deleted_at` - Soft delete timestamp

**Indexes:**
- PRIMARY KEY: id
- UNIQUE: (item_id, price_list_type_id) - Ensures one price per item per type
- FOREIGN KEY: item_id
- FOREIGN KEY: price_list_type_id
- FOREIGN KEY: created_by
- FOREIGN KEY: updated_by

**Relationships:**
- BelongsTo: items
- BelongsTo: price_list_types
- BelongsTo: users (created_by)
- BelongsTo: users (updated_by)

**Constraints:**
- price must be >= 0
- Each item can have only one price per price_list_type

---

### 8. sales
Sales transaction records.

```sql
CREATE TABLE sales (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    invoice_no VARCHAR(255) UNIQUE NOT NULL,
    total_price DECIMAL(12, 2) NOT NULL,
    paid_amount DECIMAL(12, 2) NOT NULL,
    change_amount DECIMAL(12, 2) NOT NULL,
    discount DECIMAL(12, 2) DEFAULT 0,
    status ENUM('PAID', 'VOID', 'REFUND') DEFAULT 'PAID',
    created_by BIGINT NOT NULL,
    updated_by BIGINT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id)
);
```

**Fields:**
- `id` - Unique identifier
- `invoice_no` - Unique invoice number (format: INV-YYYYMMDDHHmmss)
- `total_price` - Total transaction amount after discount
- `paid_amount` - Amount paid by customer
- `change_amount` - Change given to customer
- `discount` - Discount amount applied
- `status` - Transaction status:
  - PAID: Completed transaction
  - VOID: Cancelled transaction
  - REFUND: Refunded transaction
- `created_by` - User who created the transaction
- `updated_by` - User who updated the transaction
- `created_at` - Creation timestamp
- `updated_at` - Last update timestamp
- `deleted_at` - Soft delete timestamp

**Indexes:**
- PRIMARY KEY: id
- UNIQUE: invoice_no
- FOREIGN KEY: created_by
- FOREIGN KEY: updated_by

**Relationships:**
- HasMany: sale_items
- BelongsTo: users (created_by)
- BelongsTo: users (updated_by)

**Constraints:**
- total_price = subtotal - discount
- change_amount = paid_amount - total_price
- status must be PAID, VOID, or REFUND

---

### 9. sale_items
Individual items in a sales transaction.

```sql
CREATE TABLE sale_items (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    sale_id BIGINT NOT NULL,
    item_id BIGINT NOT NULL,
    price DECIMAL(12, 2) NOT NULL,
    qty DECIMAL(12, 2) NOT NULL,
    subtotal DECIMAL(12, 2) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE
);
```

**Fields:**
- `id` - Unique identifier
- `sale_id` - Foreign key to sales
- `item_id` - Foreign key to items
- `price` - Price per unit at time of sale
- `qty` - Quantity sold
- `subtotal` - price × qty
- `created_at` - Creation timestamp
- `updated_at` - Last update timestamp
- `deleted_at` - Soft delete timestamp

**Indexes:**
- PRIMARY KEY: id
- FOREIGN KEY: sale_id
- FOREIGN KEY: item_id

**Relationships:**
- BelongsTo: sales
- BelongsTo: items

**Constraints:**
- subtotal = price × qty
- qty must be > 0
- price must be >= 0

---

## Data Flow

### Sales Transaction Flow

```
1. User creates sale
   ↓
2. For each item in sale:
   ├─ If is_track_stock = true:
   │  ├─ Decrement item.stock by qty
   │  └─ Create stock_movement (type=OUT, reference_type=SALE)
   │
   └─ If is_track_stock = false:
      ├─ For each BOM entry:
      │  ├─ Decrement material.stock by (bom.qty × sale.qty)
      │  └─ Create stock_movement (type=OUT, reference_type=SALE)
      │
      └─ Create sale_item record
   ↓
3. Create sale record with status=PAID
   ↓
4. Transaction complete
```

### Stock Input Flow

```
1. User records stock input
   ↓
2. Increment item.stock by qty
   ↓
3. Create stock_movement (type=IN, reference_type=PURCHASE)
   ↓
4. Stock input complete
```

### Stock Opname Flow

```
1. User records stock adjustment
   ↓
2. If qty > 0 (adjustment):
   ├─ Increment item.stock by qty
   └─ Create stock_movement (type=IN, reference_type=ADJUSTMENT)
   
   If qty < 0 (waste):
   ├─ Decrement item.stock by |qty|
   └─ Create stock_movement (type=OUT, reference_type=WASTE)
   ↓
3. Stock opname complete
```

### Transaction Deletion Flow

```
1. User deletes sale
   ↓
2. For each sale_item:
   ├─ If is_track_stock = true:
   │  └─ Increment item.stock by qty
   │
   └─ If is_track_stock = false:
      └─ For each BOM entry:
         └─ Increment material.stock by (bom.qty × sale.qty)
   ↓
3. Delete all stock_movements for this sale
   ↓
4. Update sale.status = VOID
   ↓
5. Soft delete sale record
   ↓
6. Transaction deletion complete
```

---

## Queries

### Get stock movements for an item (last 30 days)

```sql
SELECT * FROM stock_movements
WHERE item_id = ?
  AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
ORDER BY created_at DESC;
```

### Get sales by product (date range)

```sql
SELECT 
    i.id,
    i.name,
    SUM(si.qty) as total_qty,
    SUM(si.subtotal) as total_revenue
FROM sale_items si
JOIN items i ON si.item_id = i.id
JOIN sales s ON si.sale_id = s.id
WHERE s.status = 'PAID'
  AND s.created_at BETWEEN ? AND ?
GROUP BY i.id, i.name
ORDER BY total_revenue DESC;
```

### Get items below minimum stock

```sql
SELECT * FROM items
WHERE stock < minimum_stock
  AND is_active = TRUE
ORDER BY stock ASC;
```

### Get BOM for a product

```sql
SELECT 
    ib.id,
    ib.qty,
    m.id as material_id,
    m.name as material_name,
    m.stock as material_stock
FROM item_boms ib
JOIN items m ON ib.material_id = m.id
WHERE ib.product_id = ?
ORDER BY m.name;
```

### Get daily sales summary

```sql
SELECT 
    DATE(s.created_at) as date,
    COUNT(s.id) as transaction_count,
    SUM(s.total_price) as total_revenue,
    SUM(s.discount) as total_discount
FROM sales s
WHERE s.status = 'PAID'
  AND s.created_at >= ?
GROUP BY DATE(s.created_at)
ORDER BY date DESC;
```

### Get stock movement audit trail

```sql
SELECT 
    sm.id,
    sm.date,
    i.name as item_name,
    sm.type,
    sm.qty,
    sm.reference_type,
    u.name as created_by_name
FROM stock_movements sm
JOIN items i ON sm.item_id = i.id
JOIN users u ON sm.created_by = u.id
WHERE sm.item_id = ?
ORDER BY sm.created_at DESC;
```

---

## Constraints & Validations

### Item Constraints
- `name` - Required, max 255 characters
- `type` - Required, must be PRODUCT or RAW_MATERIAL
- `unit` - Required, max 50 characters
- `price` - Must be >= 0
- `stock` - Must be >= 0
- `minimum_stock` - Must be >= 0
- `is_active` - Boolean
- `is_track_stock` - Boolean

### ItemBom Constraints
- `product_id` - Required, must reference PRODUCT type item
- `material_id` - Required, must reference RAW_MATERIAL type item
- `qty` - Required, must be > 0

### StockMovement Constraints
- `item_id` - Required
- `type` - Required, must be IN or OUT
- `qty` - Required, must be > 0
- `date` - Required, must be valid date
- `reference_type` - Must match operation type

### Sale Constraints
- `invoice_no` - Required, unique
- `total_price` - Required, must be >= 0
- `paid_amount` - Required, must be >= 0
- `change_amount` - Calculated, must be >= 0
- `discount` - Must be >= 0
- `status` - Must be PAID, VOID, or REFUND

### SaleItem Constraints
- `sale_id` - Required
- `item_id` - Required
- `price` - Required, must be >= 0
- `qty` - Required, must be > 0
- `subtotal` - Calculated, must equal price × qty

---

## Indexes

### Performance Indexes

```sql
-- Stock movements by item and date
CREATE INDEX idx_stock_movements_item_date 
ON stock_movements(item_id, date);

-- Sales by date
CREATE INDEX idx_sales_created_at 
ON sales(created_at);

-- Sale items by sale
CREATE INDEX idx_sale_items_sale_id 
ON sale_items(sale_id);

-- Items by type
CREATE INDEX idx_items_type 
ON items(type);

-- Items by active status
CREATE INDEX idx_items_is_active 
ON items(is_active);
```

---

## Soft Deletes

The following tables support soft deletes:
- `users` - deleted_at
- `items` - deleted_at
- `item_boms` - deleted_at
- `sales` - deleted_at
- `sale_items` - deleted_at

Soft deleted records are excluded from queries by default in Eloquent.

To include soft deleted records:
```php
Model::withTrashed()->get();
```

To get only soft deleted records:
```php
Model::onlyTrashed()->get();
```

To permanently delete:
```php
Model::forceDelete();
```

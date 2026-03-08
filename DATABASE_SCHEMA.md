# MADO POS - Database Schema

## Tables Overview

### 1. users
User authentication dan tracking

```sql
id (PK)
name
email (UNIQUE)
email_verified_at
password
remember_token
created_at
updated_at
deleted_at (soft delete)
```

### 2. products
Master data produk

```sql
id (PK)
name
price (decimal 12,2)
is_active (boolean, default: true)
created_by (FK → users.id)
updated_by (FK → users.id)
created_at
updated_at
deleted_at (soft delete)
```

**Relationships:**
- HasMany: boms
- HasMany: sale_items
- BelongsToMany: raw_materials (through boms)

### 3. raw_materials
Master data bahan baku

```sql
id (PK)
name
unit (e.g., kg, liter, pcs)
stock (decimal 12,2, default: 0)
minimum_stock (decimal 12,2, default: 0)
created_by (FK → users.id)
updated_by (FK → users.id)
created_at
updated_at
deleted_at (soft delete)
```

**Relationships:**
- HasMany: boms
- HasMany: stock_movements
- HasMany: stock_inputs
- HasMany: stock_opnames
- BelongsToMany: products (through boms)

### 4. boms (Bill of Materials)
Komposisi bahan baku per produk

```sql
id (PK)
product_id (FK → products.id)
raw_material_id (FK → raw_materials.id)
qty (decimal 12,2) - quantity per product
created_at
updated_at
deleted_at (soft delete)
```

**Relationships:**
- BelongsTo: product
- BelongsTo: raw_material

**Example:**
- Product: Cake, Raw Material: Flour, Qty: 2 (means 1 cake needs 2kg flour)

### 5. sales
Transaksi penjualan

```sql
id (PK)
invoice_no (UNIQUE) - format: INV-YYYYMMDDHHmmss-XXXX
total_price (decimal 12,2)
paid_amount (decimal 12,2, nullable)
change_amount (decimal 12,2, nullable)
discount (decimal 12,2, default: 0)
status (enum: PAID, VOID, REFUND)
created_by (FK → users.id)
updated_by (FK → users.id)
created_at
updated_at
deleted_at (soft delete)
```

**Relationships:**
- HasMany: items (sale_items)

**Status:**
- PAID: Normal transaction
- VOID: Deleted transaction
- REFUND: Refunded transaction

### 6. sale_items
Detail item dalam transaksi

```sql
id (PK)
sale_id (FK → sales.id)
product_id (FK → products.id)
price (decimal 12,2) - price at time of sale
qty (decimal 12,2)
subtotal (decimal 12,2) - price × qty
created_at
updated_at
deleted_at (soft delete)
```

**Relationships:**
- BelongsTo: sale
- BelongsTo: product

### 7. raw_material_stock_movements
Audit trail perubahan stok

```sql
id (PK)
raw_material_id (FK → raw_materials.id)
type (enum: PURCHASE, SALE, ADJUSTMENT, WASTE)
qty (decimal 12,2) - absolute quantity
reference_id (string, nullable) - ID dari source (sale_id, stock_input_id, etc)
reference_type (enum: PURCHASE, SALE, ADJUSTMENT, WASTE, nullable)
created_by (FK → users.id)
updated_by (FK → users.id)
created_at
updated_at
deleted_at (soft delete)
```

**Relationships:**
- BelongsTo: raw_material
- BelongsTo: created_by (user)

**Type Mapping:**
- PURCHASE: From stock_input
- SALE: From transaction
- ADJUSTMENT: From stock_opname (positive)
- WASTE: From stock_opname (negative)

### 8. raw_material_stock_inputs
Input stok bahan baku

```sql
id (PK)
raw_material_id (FK → raw_materials.id)
qty (decimal 12,2)
date (date)
note (text, nullable)
created_by (FK → users.id)
updated_by (FK → users.id)
created_at
updated_at
deleted_at (soft delete)
```

**Relationships:**
- BelongsTo: raw_material

**Flow:**
1. Create stock_input
2. raw_material.stock += qty
3. Create stock_movement (type: PURCHASE, reference_id: stock_input.id)

### 9. raw_material_stock_opnames
Opname/adjustment stok

```sql
id (PK)
raw_material_id (FK → raw_materials.id)
qty (decimal 12,2) - positive or negative
date (date)
note (text, nullable)
created_by (FK → users.id)
updated_by (FK → users.id)
created_at
updated_at
deleted_at (soft delete)
```

**Relationships:**
- BelongsTo: raw_material

**Flow:**
1. Create stock_opname with qty (+ or -)
2. raw_material.stock += qty
3. Create stock_movement (type: ADJUSTMENT if qty > 0, WASTE if qty < 0)

## Data Flow Diagrams

### Stock Input Flow
```
Stock Input Created
    ↓
raw_material.stock += qty
    ↓
stock_movement created (PURCHASE)
    ↓
Edit: stock adjusted, movement updated
    ↓
Delete: stock reversed, movement deleted
```

### Transaction Flow
```
Transaction Created
    ↓
For each sale_item:
    - Get product BOM
    - For each BOM material:
        - raw_material.stock -= (bom.qty × sale_item.qty)
        - stock_movement created (SALE)
    ↓
Edit: stock adjusted based on qty changes
    ↓
Delete: status = VOID, stock restored, movements deleted
    ↓
Refund: status = REFUND, stock unchanged
```

### Stock Opname Flow
```
Stock Opname Created (qty: +5 or -3)
    ↓
raw_material.stock += qty
    ↓
stock_movement created (ADJUSTMENT if +, WASTE if -)
    ↓
Edit: stock adjusted, movement updated
    ↓
Delete: stock reversed, movement deleted
```

## Indexes

For performance optimization:

```sql
-- Foreign keys (auto-indexed)
products.created_by
products.updated_by
raw_materials.created_by
raw_materials.updated_by
boms.product_id
boms.raw_material_id
sales.created_by
sales.updated_by
sale_items.sale_id
sale_items.product_id
raw_material_stock_movements.raw_material_id
raw_material_stock_movements.created_by
raw_material_stock_inputs.raw_material_id
raw_material_stock_inputs.created_by
raw_material_stock_opnames.raw_material_id
raw_material_stock_opnames.created_by

-- Search indexes
products.name
raw_materials.name
sales.invoice_no
```

## Constraints

### Foreign Keys
- All FK have CASCADE ON DELETE (except user references which are NULL ON DELETE)
- Ensures referential integrity

### Unique Constraints
- users.email
- sales.invoice_no

### Check Constraints
- products.price >= 0
- raw_materials.stock >= 0
- raw_materials.minimum_stock >= 0
- sales.total_price >= 0
- sale_items.qty > 0
- raw_material_stock_movements.qty > 0

## Soft Deletes

All tables except cache and jobs have soft deletes:
- deleted_at column (nullable timestamp)
- Deleted records are excluded from queries by default
- Can be restored if needed

## Audit Trail

All data tables track:
- created_by: User who created the record
- updated_by: User who last updated the record
- created_at: When record was created
- updated_at: When record was last updated
- deleted_at: When record was deleted (soft delete)

## Example Queries

### Get all active products
```sql
SELECT * FROM products WHERE is_active = 1 AND deleted_at IS NULL
```

### Get BOM for a product
```sql
SELECT rm.*, b.qty 
FROM boms b
JOIN raw_materials rm ON b.raw_material_id = rm.id
WHERE b.product_id = ? AND b.deleted_at IS NULL
```

### Get stock movements for a material (last 30 days)
```sql
SELECT * FROM raw_material_stock_movements
WHERE raw_material_id = ? 
  AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
  AND deleted_at IS NULL
ORDER BY created_at DESC
```

### Get today's revenue
```sql
SELECT SUM(total_price) as revenue
FROM sales
WHERE DATE(created_at) = CURDATE()
  AND status = 'PAID'
  AND deleted_at IS NULL
```

### Get low stock materials
```sql
SELECT * FROM raw_materials
WHERE stock < minimum_stock
  AND deleted_at IS NULL
```

## Notes

- All decimal fields use DECIMAL(12,2) for currency precision
- Timestamps are in UTC (configurable in Laravel)
- Soft deletes allow data recovery
- Stock movements are immutable (only created/deleted, not updated)
- Invoice numbers are unique and auto-generated

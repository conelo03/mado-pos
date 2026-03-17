# Price List Management System

## Overview

The Price List Management System allows you to define multiple pricing tiers for items and manage customer assignments. This enables flexible pricing strategies for different customer segments (retail, wholesale, members, resellers).

## Features

### 1. Price List Types
Define different pricing categories with specific types:
- **RETAIL** - Standard retail pricing for walk-in customers
- **GROSIR** - Wholesale pricing for bulk purchases
- **MEMBER** - Special pricing for registered members
- **RESELLER** - Pricing for authorized resellers

**Access:** Admin+ only
**Route:** `/price-list-types`

#### Operations
- Create new price list types
- Edit existing price list types
- Delete price list types
- Search and filter by name
- View description and type information

### 2. Customers
Manage customer information with assigned price list types.

**Access:** Admin+ only
**Route:** `/customers`

#### Operations
- Create new customers
- Edit customer information
- Delete customers
- Assign price list types to customers
- Store optional phone and address information
- Search customers by name

### 3. Item Price Lists
Set specific prices for items within each price list type.

**Access:** Admin+ (via item detail page)
**Location:** Item Detail Page → Price Lists section

#### Operations
- Add prices for items in different price list types
- Edit item prices
- Delete item prices
- View all prices for an item
- Prevent duplicate price assignments

## Database Schema

### price_list_types Table
```
id              - Unique identifier
name            - Price list type name
type            - RETAIL | GROSIR | MEMBER | RESELLER
description     - Optional description
created_by      - User who created
updated_by      - User who last updated
created_at      - Creation timestamp
updated_at      - Last update timestamp
deleted_at      - Soft delete timestamp
```

### customers Table
```
id                  - Unique identifier
price_list_type_id  - Foreign key to price_list_types
name                - Customer name
phone               - Optional phone number
address             - Optional address
created_by          - User who created
updated_by          - User who last updated
created_at          - Creation timestamp
updated_at          - Last update timestamp
deleted_at          - Soft delete timestamp
```

### item_price_lists Table
```
id                  - Unique identifier
item_id             - Foreign key to items
price_list_type_id  - Foreign key to price_list_types
price               - Price for this item in this type
created_by          - User who created
updated_by          - User who last updated
created_at          - Creation timestamp
updated_at          - Last update timestamp
deleted_at          - Soft delete timestamp
```

## Model Relationships

### PriceListType
```php
- hasMany(Customer::class)
- hasMany(ItemPriceList::class)
- belongsTo(User::class, 'created_by')
- belongsTo(User::class, 'updated_by')
```

### Customer
```php
- belongsTo(PriceListType::class)
- belongsTo(User::class, 'created_by')
- belongsTo(User::class, 'updated_by')
```

### ItemPriceList
```php
- belongsTo(Item::class)
- belongsTo(PriceListType::class)
- belongsTo(User::class, 'created_by')
- belongsTo(User::class, 'updated_by')
```

### Item
```php
- hasMany(ItemPriceList::class, 'priceLists')
```

## Usage Examples

### Create a Price List Type

```php
$priceListType = PriceListType::create([
    'name' => 'Wholesale Price',
    'type' => 'GROSIR',
    'description' => 'Wholesale pricing for bulk purchases',
    'created_by' => auth()->id(),
]);
```

### Create a Customer

```php
$customer = Customer::create([
    'price_list_type_id' => $priceListType->id,
    'name' => 'PT Maju Jaya',
    'phone' => '08123456789',
    'address' => 'Jl. Merdeka No. 123',
    'created_by' => auth()->id(),
]);
```

### Set Item Price for a Price List Type

```php
$itemPrice = ItemPriceList::create([
    'item_id' => $item->id,
    'price_list_type_id' => $priceListType->id,
    'price' => 50000,
    'created_by' => auth()->id(),
]);
```

### Get All Prices for an Item

```php
$prices = $item->priceLists()
    ->with('priceListType')
    ->get();

foreach ($prices as $price) {
    echo $price->priceListType->name . ': Rp ' . number_format($price->price);
}
```

### Get Price for Specific Price List Type

```php
$price = ItemPriceList::where('item_id', $item->id)
    ->where('price_list_type_id', $priceListType->id)
    ->first();

if ($price) {
    echo 'Price: Rp ' . number_format($price->price);
}
```

### Get All Customers of a Price List Type

```php
$customers = $priceListType->customers()->get();

foreach ($customers as $customer) {
    echo $customer->name . ' - ' . $customer->phone;
}
```

## Livewire Components

### PriceListTypes\Index
Manages price list types with full CRUD operations.

**Features:**
- Search by name
- Create/Edit/Delete operations
- Modal-based forms
- Validation and error handling
- Pagination (10 items per page)
- Audit trail (created_by, updated_by)

**View:** `resources/views/livewire/price-list-types/index.blade.php`
**Component:** `app/Livewire/PriceListTypes/Index.php`

### Customers\Index
Manages customers with price list type assignment.

**Features:**
- Search by name
- Create/Edit/Delete operations
- Price list type assignment
- Optional phone and address fields
- Modal-based forms
- Validation and error handling
- Pagination (10 items per page)
- Audit trail (created_by, updated_by)

**View:** `resources/views/livewire/customers/index.blade.php`
**Component:** `app/Livewire/Customers/Index.php`

### Items\PriceListManager
Manages item prices for different price list types.

**Features:**
- Add prices for items
- Edit item prices
- Delete item prices
- Prevent duplicate assignments
- Display prices in table format
- Modal-based forms
- Validation and error handling
- Audit trail (created_by, updated_by)

**View:** `resources/views/livewire/items/price-list-manager.blade.php`
**Component:** `app/Livewire/Items/PriceListManager.php`

## Validation Rules

### PriceListType
```php
'name' => 'required|string|max:255',
'type' => 'required|in:RETAIL,GROSIR,MEMBER,RESELLER',
'description' => 'nullable|string',
```

### Customer
```php
'price_list_type_id' => 'required|exists:price_list_types,id',
'name' => 'required|string|max:255',
'phone' => 'nullable|string|max:20',
'address' => 'nullable|string',
```

### ItemPriceList
```php
'price_list_type_id' => 'required|exists:price_list_types,id',
'price' => 'required|numeric|min:0',
```

## Default Price List Types

The system comes with 4 default price list types:

1. **Retail Price** (RETAIL)
   - Standard retail pricing for walk-in customers

2. **Wholesale Price** (GROSIR)
   - Wholesale pricing for bulk purchases

3. **Member Price** (MEMBER)
   - Special pricing for registered members

4. **Reseller Price** (RESELLER)
   - Pricing for authorized resellers

These are created during database seeding.

## Navigation

The Price List Management features are accessible from the admin sidebar:

- **Price List Types** - Manage pricing tiers
- **Customers** - Manage customer information

Both are visible only to Admin+ users.

## Audit Trail

All operations are tracked with:
- `created_by` - User who created the record
- `updated_by` - User who last updated the record
- `created_at` - Creation timestamp
- `updated_at` - Last update timestamp
- `deleted_at` - Soft delete timestamp (for audit purposes)

## Soft Deletes

All tables support soft deletes:
- Records are not permanently deleted
- Deleted records are excluded from queries by default
- Audit trail is preserved for compliance

To restore a deleted record:
```php
$priceListType = PriceListType::withTrashed()->find($id);
$priceListType->restore();
```

## Future Enhancements

Potential features for future development:
- Bulk price updates
- Price history tracking
- Automatic price adjustments based on rules
- Price comparison reports
- Customer-specific discounts
- Seasonal pricing
- Integration with sales transactions

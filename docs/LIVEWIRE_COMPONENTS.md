# MADO POS - Livewire Components Documentation

## Overview

Aplikasi menggunakan Livewire 4 untuk real-time interactivity tanpa page reload. Setiap fitur utama memiliki component yang menangani CRUD operations.

## Components List

### 1. Products/Index
**Path:** `app/Livewire/Products/Index.php`
**View:** `resources/views/livewire/products/index.blade.php`

**Properties:**
- `$search` - Search query
- `$showModal` - Toggle modal visibility
- `$editingId` - ID of product being edited
- `$name` - Product name
- `$price` - Product price
- `$is_active` - Active status

**Methods:**
- `render()` - Render component with paginated products
- `openModal()` - Open add/edit modal
- `closeModal()` - Close modal
- `save()` - Create or update product
- `edit($id)` - Load product for editing
- `delete($id)` - Delete product

**Features:**
- Search by product name (live)
- Pagination (10 per page)
- Add/Edit/Delete products
- Status toggle (active/inactive)
- Link to product detail page

---

### 2. Products/Detail
**Path:** `app/Livewire/Products/Detail.php`
**View:** `resources/views/livewire/products/detail.blade.php`

**Properties:**
- `$productId` - Current product ID
- `$product` - Product model with BOM
- `$showBomModal` - Toggle BOM modal
- `$raw_material_id` - Selected raw material
- `$qty` - Quantity for BOM
- `$editingBomId` - ID of BOM being edited

**Methods:**
- `mount($id)` - Initialize component with product
- `render()` - Render product detail with BOM
- `openBomModal()` - Open add/edit BOM modal
- `closeBomModal()` - Close modal
- `saveBom()` - Create or update BOM
- `editBom($id)` - Load BOM for editing
- `deleteBom($id)` - Delete BOM

**Features:**
- View product details
- Manage Bill of Materials
- Add/Edit/Delete materials for product
- Show quantity per product

---

### 3. RawMaterials/Index
**Path:** `app/Livewire/RawMaterials/Index.php`
**View:** `resources/views/livewire/raw-materials/index.blade.php`

**Properties:**
- `$search` - Search query
- `$showModal` - Toggle modal visibility
- `$editingId` - ID of material being edited
- `$name` - Material name
- `$unit` - Unit (kg, liter, pcs, etc)
- `$minimum_stock` - Minimum stock level

**Methods:**
- `render()` - Render component with paginated materials
- `openModal()` - Open add/edit modal
- `closeModal()` - Close modal
- `save()` - Create or update material
- `edit($id)` - Load material for editing
- `delete($id)` - Delete material

**Features:**
- Search by material name (live)
- Pagination (10 per page)
- Add/Edit/Delete materials
- View current stock
- Link to detail page for stock movements

---

### 4. RawMaterials/Detail
**Path:** `app/Livewire/RawMaterials/Detail.php`
**View:** `resources/views/livewire/raw-materials/detail.blade.php`

**Properties:**
- `$materialId` - Current material ID
- `$material` - Material model
- `$dateFrom` - Filter start date
- `$dateTo` - Filter end date

**Methods:**
- `mount($id)` - Initialize component with material
- `render()` - Render material detail with stock movements
- `resetFilter()` - Reset date filter to last 30 days

**Features:**
- View material details
- View current stock and minimum stock
- Stock status indicator (Low Stock / Stock OK)
- View all stock movements with date filter
- Show movement type (PURCHASE, SALE, ADJUSTMENT, WASTE)
- Show reference and created by user
- Pagination (20 per page)

---

### 5. StockInputs/Index
**Path:** `app/Livewire/StockInputs/Index.php`
**View:** `resources/views/livewire/stock-inputs/index.blade.php`

**Properties:**
- `$search` - Search query
- `$showModal` - Toggle modal visibility
- `$editingId` - ID of stock input being edited
- `$raw_material_id` - Selected raw material
- `$qty` - Quantity to add
- `$date` - Date of input
- `$note` - Optional note

**Methods:**
- `mount()` - Initialize with today's date
- `render()` - Render component with paginated inputs
- `openModal()` - Open add/edit modal
- `closeModal()` - Close modal
- `save()` - Create or update stock input
- `edit($id)` - Load stock input for editing
- `delete($id)` - Delete stock input

**Features:**
- Search by raw material name (live)
- Pagination (10 per page)
- Add/Edit/Delete stock inputs
- Auto increment raw material stock
- Record as PURCHASE in stock movements
- Date and note fields
- Edit with stock adjustment
- Delete with stock reversal

---

### 6. StockOpnames/Index
**Path:** `app/Livewire/StockOpnames/Index.php`
**View:** `resources/views/livewire/stock-opnames/index.blade.php`

**Properties:**
- `$search` - Search query
- `$showModal` - Toggle modal visibility
- `$editingId` - ID of opname being edited
- `$raw_material_id` - Selected raw material
- `$qty` - Adjustment quantity (+ or -)
- `$date` - Date of opname
- `$note` - Optional note

**Methods:**
- `mount()` - Initialize with today's date
- `render()` - Render component with paginated opnames
- `openModal()` - Open add/edit modal
- `closeModal()` - Close modal
- `save()` - Create or update stock opname
- `edit($id)` - Load opname for editing
- `delete($id)` - Delete opname

**Features:**
- Search by raw material name (live)
- Pagination (10 per page)
- Add/Edit/Delete stock opnames
- Support positive (adjustment) and negative (waste) quantities
- Auto adjust raw material stock
- Record as ADJUSTMENT or WASTE in stock movements
- Date and note fields
- Edit with stock adjustment
- Delete with stock reversal

---

### 7. Transactions/Index
**Path:** `app/Livewire/Transactions/Index.php`
**View:** `resources/views/livewire/transactions/index.blade.php`

**Properties:**
- `$search` - Search query
- `$showModal` - Toggle modal visibility
- `$editingId` - ID of transaction being edited
- `$items` - Array of sale items
- `$discount` - Discount amount
- `$paid_amount` - Amount paid by customer
- `$total_price` - Total transaction price
- `$change_amount` - Change to give customer

**Methods:**
- `render()` - Render component with paginated transactions
- `openModal()` - Open new transaction modal
- `closeModal()` - Close modal
- `addItem($productId)` - Add product to cart
- `removeItem($index)` - Remove item from cart
- `updateQty($index, $qty)` - Update item quantity
- `calculateTotal()` - Recalculate totals
- `updateDiscount($value)` - Update discount and recalculate
- `updatePaidAmount($value)` - Update paid amount and calculate change
- `save()` - Create transaction and reduce stock
- `edit($id)` - Load transaction for editing
- `delete($id)` - Delete transaction (VOID status)
- `refund($id)` - Refund transaction (REFUND status)
- `reduceRawMaterialStock($productId, $qty, $saleId)` - Reduce stock based on BOM
- `restoreRawMaterialStock($productId, $qty, $saleId)` - Restore stock on delete

**Features:**
- Search by invoice number (live)
- Pagination (10 per page)
- Create new transaction
- Add products to cart
- Auto calculate subtotal and total
- Optional discount input
- Optional paid amount input
- Auto calculate change amount
- Auto reduce raw material stock based on BOM
- Record stock movements as SALE
- Edit transaction with stock adjustment
- Delete transaction (VOID status, stock restored)
- Refund transaction (REFUND status, stock unchanged)
- View transaction details

---

## Component Lifecycle

### Create/Edit Flow
```
1. User clicks "Add" or "Edit"
2. openModal() called
3. Modal displayed with form
4. User fills form
5. User clicks "Save"
6. save() validates input
7. Model created/updated
8. Stock movements recorded (if applicable)
9. closeModal() called
10. Component re-renders
11. Success notification
```

### Delete Flow
```
1. User clicks "Delete"
2. Confirmation dialog shown
3. User confirms
4. delete() called
5. Related records updated (stock, movements)
6. Model deleted
7. Component re-renders
8. Success notification
```

## Livewire Features Used

### Wire Directives
- `wire:model` - Two-way data binding
- `wire:model.live` - Real-time search
- `wire:click` - Click event handling
- `wire:change` - Change event handling
- `wire:submit` - Form submission

### Pagination
- `WithPagination` trait
- `paginate()` method
- `{{ $items->links() }}` in view

### Validation
- `validate()` method
- Error display with `@error` directive

### Notifications
- `dispatch('notify')` for success messages

## Best Practices

1. **Validation**: Always validate input before saving
2. **Relationships**: Use eager loading with `with()` to avoid N+1 queries
3. **Pagination**: Use pagination for large datasets
4. **Search**: Use `like` queries for search functionality
5. **Transactions**: Use database transactions for complex operations
6. **Error Handling**: Provide clear error messages to users
7. **Confirmation**: Ask for confirmation before destructive operations

## Performance Tips

1. Use pagination for large lists
2. Eager load relationships
3. Use indexes on frequently searched columns
4. Avoid unnecessary re-renders
5. Use `wire:key` for dynamic lists
6. Cache frequently accessed data

## Testing

Each component can be tested with:
```php
Livewire::test(ComponentName::class)
    ->call('methodName', $args)
    ->assertSee('expected text')
```

## Troubleshooting

### Component not updating
- Check if `wire:model` is correctly bound
- Verify method name is correct
- Check browser console for errors

### Validation not working
- Ensure `validate()` is called before save
- Check validation rules
- Verify error messages are displayed

### Stock not updating
- Check if BOM is correctly configured
- Verify stock reduction logic
- Check stock movements table

### Performance issues
- Use pagination for large datasets
- Eager load relationships
- Check database queries with Laravel Debugbar

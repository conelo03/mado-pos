# Quick Reference Guide

## Routes

### Public Routes
- `GET /` - Dashboard
- `GET /login` - Login page
- `POST /login` - Login submission
- `POST /logout` - Logout

### Authenticated Routes
- `GET /change-password` - Change password page
- `GET /transactions` - Transactions list
- `GET /transactions/{id}/print` - Print receipt

### Admin Routes
- `GET /users` - Users management
- `GET /items` - Items list
- `GET /items/{id}` - Item details
- `GET /stock-input` - Stock input list
- `GET /stock-opname` - Stock opname list
- `GET /reports/by-products` - Sales by products report
- `GET /reports/by-transactions` - Sales by transactions report

---

## Models & Relationships

### Item
```php
$item->boms()              // HasMany ItemBom
$item->stockMovements()    // HasMany StockMovement
$item->createdBy()         // BelongsTo User
$item->updatedBy()         // BelongsTo User
```

### ItemBom
```php
$bom->product()            // BelongsTo Item (product_id)
$bom->material()           // BelongsTo Item (material_id)
```

### StockMovement
```php
$movement->item()          // BelongsTo Item
$movement->createdBy()     // BelongsTo User
$movement->updatedBy()     // BelongsTo User
```

### Sale
```php
$sale->items()             // HasMany SaleItem
```

### SaleItem
```php
$saleItem->sale()          // BelongsTo Sale
$saleItem->item()          // BelongsTo Item
```

---

## Common Queries

### Get all active products
```php
Item::where('type', 'PRODUCT')
    ->where('is_active', true)
    ->get();
```

### Get all raw materials
```php
Item::where('type', 'RAW_MATERIAL')->get();
```

### Get items below minimum stock
```php
Item::where('stock', '<', DB::raw('minimum_stock'))
    ->where('is_active', true)
    ->get();
```

### Get BOM for a product
```php
$product = Item::find($id);
$bom = $product->boms()->with('material')->get();
```

### Get stock movements for an item
```php
$item = Item::find($id);
$movements = $item->stockMovements()
    ->orderBy('created_at', 'desc')
    ->paginate(10);
```

### Get sales for a date range
```php
Sale::whereBetween('created_at', [$startDate, $endDate])
    ->where('status', 'PAID')
    ->with('items.item')
    ->get();
```

### Get sales by product
```php
SaleItem::with('item', 'sale')
    ->whereHas('sale', function ($q) {
        $q->where('status', 'PAID');
    })
    ->get()
    ->groupBy('item_id');
```

### Get total revenue for a date
```php
Sale::whereDate('created_at', $date)
    ->where('status', 'PAID')
    ->sum('total_price');
```

---

## Livewire Properties & Methods

### Items Index
```php
// Properties
$search              // Search query
$showModal           // Modal visibility
$editingId           // ID of item being edited
$name, $type, $unit, $price, $stock, $minimum_stock, $is_active, $is_track_stock

// Methods
openModal()          // Open create/edit modal
closeModal()         // Close modal
save()               // Create or update item
edit($id)            // Load item for editing
confirmDelete($id)   // Show delete confirmation
delete($id)          // Delete item
```

### Transactions Index
```php
// Properties
$search              // Search by invoice
$showModal           // Modal visibility
$editingId           // ID of transaction being edited
$items               // Array of items in transaction
$discount            // Discount amount
$paid_amount         // Amount paid
$total_price         // Total amount
$change_amount       // Change amount
$productSearch       // Search for products

// Methods
openModal()          // Open transaction modal
closeModal()         // Close modal
addItem($id)         // Add item to transaction
removeItem($index)   // Remove item from transaction
updateQty($index, $qty)  // Update item quantity
incrementQty($index) // Increase quantity
decrementQty($index) // Decrease quantity
calculateTotal()     // Recalculate totals
save()               // Create or update transaction
edit($id)            // Load transaction for editing
confirmDelete($id)   // Show delete confirmation
delete($id)          // Delete transaction
confirmRefund($id)   // Show refund confirmation
refund($id)          // Refund transaction
```

### Items Detail
```php
// Properties
$itemId              // Current item ID
$item                // Item model
$showBomModal        // BOM modal visibility
$editingBomId        // ID of BOM being edited
$material_id         // Material selection
$bom_qty             // BOM quantity
$dateFrom, $dateTo   // Date filters

// Methods
openBomModal()       // Open BOM modal
closeBomModal()      // Close BOM modal
saveBom()            // Create or update BOM
editBom($id)         // Load BOM for editing
confirmDeleteBom($id) // Show BOM delete confirmation
deleteBom($id)       // Delete BOM
resetFilter()        // Clear date filters
```

### Stock Input
```php
// Properties
$search              // Search by item name
$showModal           // Modal visibility
$editingId           // ID being edited
$item_id             // Selected item
$qty                 // Quantity
$date                // Date
$note                // Notes

// Methods
openModal()          // Open modal
closeModal()         // Close modal
save()               // Create or update
edit($id)            // Load for editing
confirmDelete($id)   // Show delete confirmation
delete($id)          // Delete
```

### Stock Opname
```php
// Properties
$search              // Search by item name
$showModal           // Modal visibility
$editingId           // ID being edited
$item_id             // Selected item
$qty                 // Quantity (positive/negative)
$date                // Date
$note                // Notes

// Methods
openModal()          // Open modal
closeModal()         // Close modal
save()               // Create or update
edit($id)            // Load for editing
confirmDelete($id)   // Show delete confirmation
delete($id)          // Delete
```

---

## Blade Components

### app-layout
```blade
<x-app-layout>
    <x-slot name="title">Page Title</x-slot>
    <!-- Page content -->
</x-app-layout>
```

### confirm-dialog
```blade
<x-confirm-dialog 
    title="Delete Item"
    message="Are you sure?"
    confirmText="Delete"
    cancelText="Cancel"
    isDangerous="true"
    onConfirm="$wire.delete({{ $id }})"
/>
```

### confirm-refund-dialog
```blade
<x-confirm-refund-dialog 
    title="Refund Transaction"
    message="Are you sure?"
    confirmText="Refund"
    cancelText="Cancel"
    onConfirm="$wire.refund({{ $id }})"
/>
```

### Icons
```blade
<x-icon.home />
<x-icon.package />
<x-icon.users />
<x-icon.shopping-cart />
<x-icon.chart-bar />
<x-icon.plus />
<x-icon.pencil />
<x-icon.trash />
<x-icon.eye />
<x-icon.eye-off />
<!-- And many more... -->
```

---

## Database Transactions

All critical operations use database transactions:

```php
DB::transaction(function () {
    // Create sale
    $sale = Sale::create([...]);
    
    // Create sale items
    foreach ($items as $item) {
        SaleItem::create([...]);
        
        // Reduce stock
        $this->reduceItemStock($item['item_id'], $item['qty'], $sale->id);
    }
});
```

---

## Stock Tracking Logic

### Direct Tracking (is_track_stock = true)
```php
// Reduce stock
$item->decrement('stock', $qty);

// Create movement
StockMovement::create([
    'item_id' => $itemId,
    'type' => 'OUT',
    'qty' => $qty,
    'reference_type' => 'SALE',
    'created_by' => auth()->id(),
]);
```

### BOM-Based Tracking (is_track_stock = false)
```php
// For each BOM entry
foreach ($item->boms as $bom) {
    $requiredQty = $bom->qty * $qty;
    
    // Reduce material stock
    $bom->material->decrement('stock', $requiredQty);
    
    // Create movement
    StockMovement::create([
        'item_id' => $bom->material_id,
        'type' => 'OUT',
        'qty' => $requiredQty,
        'reference_type' => 'SALE',
        'created_by' => auth()->id(),
    ]);
}
```

---

## Validation Rules

### Item
```php
'name' => 'required|string|max:255',
'type' => 'required|in:PRODUCT,RAW_MATERIAL',
'unit' => 'required|string|max:50',
'price' => 'required|numeric|min:0',
'stock' => 'required|numeric|min:0',
'minimum_stock' => 'required|numeric|min:0',
'is_active' => 'boolean',
'is_track_stock' => 'boolean',
```

### ItemBom
```php
'material_id' => 'required|exists:items,id',
'bom_qty' => 'required|numeric|min:0.01',
```

### Stock Movement
```php
'item_id' => 'required|exists:items,id',
'qty' => 'required|numeric',
'date' => 'required|date',
```

### Sale
```php
'items' => 'required|array|min:1',
'discount' => 'nullable|numeric|min:0',
'paid_amount' => 'nullable|numeric|min:0',
```

---

## Common Calculations

### Transaction Total
```php
$subtotal = array_sum(array_column($items, 'subtotal'));
$total = $subtotal - $discount;
$change = $paid_amount - $total;
```

### BOM Stock Requirement
```php
$requiredQty = $bom->qty * $saleQty;
```

### Daily Revenue
```php
$revenue = Sale::whereDate('created_at', $date)
    ->where('status', 'PAID')
    ->sum('total_price');
```

### Stock Movement Type
```php
$type = $qty > 0 ? 'IN' : 'OUT';
$referenceType = $qty > 0 ? 'ADJUSTMENT' : 'WASTE';
```

---

## Debugging Tips

### Check Stock Movements
```php
// In tinker
StockMovement::where('item_id', $itemId)->latest()->get();
```

### Check Item Stock
```php
Item::find($itemId)->stock;
```

### Check BOM
```php
Item::find($productId)->boms()->with('material')->get();
```

### Check Sale Details
```php
Sale::with('items.item')->find($saleId);
```

### Check User Transactions
```php
Sale::where('created_by', auth()->id())->latest()->get();
```

---

## Environment Variables

Key environment variables in `.env`:

```
APP_NAME=MADO POS
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost

DB_CONNECTION=sqlite
DB_DATABASE=/path/to/database.sqlite

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@madopos.com
```

---

## File Locations

### Key Files
- **Models**: `app/Models/`
- **Livewire Components**: `app/Livewire/`
- **Views**: `resources/views/`
- **Migrations**: `database/migrations/`
- **Routes**: `routes/web.php`
- **Config**: `config/`

### Important Files
- `app/Models/Item.php` - Item model
- `app/Models/StockMovement.php` - Stock movement model
- `app/Livewire/Transactions/Index.php` - Transaction logic
- `resources/views/components/app-layout.blade.php` - Main layout
- `routes/web.php` - Route definitions

---

## Useful Commands

### Artisan Commands
```bash
# Run migrations
php artisan migrate

# Create new migration
php artisan make:migration create_table_name

# Create new Livewire component
php artisan make:livewire component-name

# Run tinker (interactive shell)
php artisan tinker

# Clear cache
php artisan cache:clear

# Seed database
php artisan db:seed
```

### Database Commands
```bash
# Access SQLite database
sqlite3 database/database.sqlite

# Backup database
cp database/database.sqlite database/database.backup.sqlite

# Reset database
php artisan migrate:refresh
```

---

## Performance Tips

1. **Use Pagination**: Always paginate large result sets
2. **Eager Load**: Use `with()` to avoid N+1 queries
3. **Index Queries**: Add indexes for frequently queried columns
4. **Cache Results**: Cache reports and statistics
5. **Batch Operations**: Use batch inserts for multiple records

### Example: Eager Loading
```php
// Bad - N+1 query
$sales = Sale::all();
foreach ($sales as $sale) {
    echo $sale->items->count(); // Query per sale
}

// Good - Eager loading
$sales = Sale::with('items')->get();
foreach ($sales as $sale) {
    echo $sale->items->count(); // No additional queries
}
```

---

## Security Best Practices

1. **Always Validate Input**: Use validation rules
2. **Use Soft Deletes**: Don't permanently delete data
3. **Track Changes**: Use created_by and updated_by
4. **Hash Passwords**: Use Hash::make()
5. **Check Authorization**: Use middleware for role checks
6. **Escape Output**: Use {{ }} in Blade templates
7. **Use Transactions**: Wrap critical operations

---

## Troubleshooting

### Stock Not Reducing
- Check `is_track_stock` value
- Verify BOM is set up correctly
- Check material stock availability

### Cannot Add Item to Sale
- Verify item is active
- Check item type is PRODUCT
- Ensure item exists

### Stock Movement Not Showing
- Check date range filter
- Verify stock movement was created
- Check item_id is correct

### Transaction Won't Delete
- Check user has admin role
- Verify transaction exists
- Ensure stock is available to restore

### BOM Not Working
- Verify product has `is_track_stock = false`
- Check BOM entries exist
- Verify materials have stock

# Troubleshooting Guide

## Common Issues and Solutions

### 1. Stock Not Reducing on Sale

**Problem**: When creating a sale, the item stock is not being reduced.

**Possible Causes**:

#### Cause A: Item has is_track_stock = false but no BOM
- **Check**: Go to Items → Select item → Check "Is Track Stock" value
- **Solution**: 
  - If false, add BOM entries for the product
  - Or set "Is Track Stock" to true for direct tracking

#### Cause B: BOM entries are missing or incorrect
- **Check**: Go to Items → Select product → View BOM section
- **Solution**:
  - Add missing BOM entries
  - Verify material quantities are correct
  - Ensure materials have sufficient stock

#### Cause C: Material stock is insufficient
- **Check**: Go to Items → Select material → Check stock quantity
- **Solution**:
  - Add stock input for the material
  - Or reduce sale quantity

#### Cause D: Item is not active
- **Check**: Go to Items → Select item → Check "Is Active" checkbox
- **Solution**: Enable the item by checking "Is Active"

**Debug Steps**:
```php
// In tinker
$item = Item::find($itemId);
echo $item->is_track_stock;  // Should be true or false
echo $item->stock;            // Should be > 0
$item->boms()->get();         // Should show BOM entries if is_track_stock = false
```

---

### 2. Cannot Add Item to Sale

**Problem**: Item doesn't appear in the product search or cannot be added to transaction.

**Possible Causes**:

#### Cause A: Item type is not PRODUCT
- **Check**: Go to Items → Select item → Check "Type" field
- **Solution**: Change type to "PRODUCT"

#### Cause B: Item is not active
- **Check**: Go to Items → Select item → Check "Is Active" checkbox
- **Solution**: Enable the item

#### Cause C: Item doesn't exist
- **Check**: Go to Items → Search for item
- **Solution**: Create the item first

**Debug Steps**:
```php
// In tinker
Item::where('type', 'PRODUCT')->where('is_active', true)->get();
Item::find($itemId);
```

---

### 3. Stock Movement Not Showing

**Problem**: Stock movements are not visible in Items Detail page.

**Possible Causes**:

#### Cause A: Date filter is too restrictive
- **Check**: Items Detail → Check date filters
- **Solution**: Clear date filters or adjust date range

#### Cause B: Stock movement was not created
- **Check**: Database directly
- **Solution**: Verify transaction was saved correctly

#### Cause C: Wrong item selected
- **Check**: Verify you're viewing the correct item
- **Solution**: Select the correct item

**Debug Steps**:
```php
// In tinker
StockMovement::where('item_id', $itemId)->latest()->get();
StockMovement::where('reference_type', 'SALE')->get();
```

---

### 4. Cannot Delete Transaction

**Problem**: Delete button is disabled or delete fails.

**Possible Causes**:

#### Cause A: User doesn't have admin role
- **Check**: User profile → Check role
- **Solution**: Change user role to ADMIN

#### Cause B: Stock cannot be restored
- **Check**: Item stock levels
- **Solution**: Manually adjust stock if needed

#### Cause C: Transaction doesn't exist
- **Check**: Verify transaction ID
- **Solution**: Refresh page and try again

**Debug Steps**:
```php
// In tinker
Sale::find($saleId);
auth()->user()->role;  // Should be ADMIN
```

---

### 5. BOM Not Working

**Problem**: When selling a product with BOM, materials are not being reduced.

**Possible Causes**:

#### Cause A: Product has is_track_stock = true
- **Check**: Items Detail → Check "Is Track Stock" value
- **Solution**: Set to false for BOM-based tracking

#### Cause B: BOM entries are missing
- **Check**: Items Detail → View BOM section
- **Solution**: Add BOM entries for all materials

#### Cause C: Material items don't exist
- **Check**: Items Detail → BOM section → Check material names
- **Solution**: Create missing material items first

#### Cause D: Material stock is insufficient
- **Check**: Items Detail → Select material → Check stock
- **Solution**: Add stock input for materials

**Debug Steps**:
```php
// In tinker
$product = Item::find($productId);
echo $product->is_track_stock;
$product->boms()->with('material')->get();
```

---

### 6. Duplicate Invoice Numbers

**Problem**: Two transactions have the same invoice number.

**Possible Causes**:

#### Cause A: System clock is incorrect
- **Check**: Server time
- **Solution**: Correct server time

#### Cause B: Concurrent transactions
- **Check**: Transaction creation times
- **Solution**: Transactions should be created sequentially

**Debug Steps**:
```php
// In tinker
Sale::where('invoice_no', 'INV-..')->get();
DB::table('sales')->select('invoice_no')->groupBy('invoice_no')->havingRaw('count(*) > 1')->get();
```

---

### 7. Stock Negative

**Problem**: Item stock shows negative value.

**Possible Causes**:

#### Cause A: Sale was created without sufficient stock
- **Check**: Stock movements
- **Solution**: Add stock input to correct the balance

#### Cause B: Manual database modification
- **Check**: Stock movement history
- **Solution**: Verify stock movements are correct

**Debug Steps**:
```php
// In tinker
Item::where('stock', '<', 0)->get();
StockMovement::where('item_id', $itemId)->sum('qty');
```

**Fix**:
```php
// In tinker
$item = Item::find($itemId);
$item->update(['stock' => 0]);  // Or correct value
```

---

### 8. Cannot Change Password

**Problem**: Password change fails or shows validation error.

**Possible Causes**:

#### Cause A: Current password is incorrect
- **Check**: Verify you're entering correct current password
- **Solution**: Try again with correct password

#### Cause B: New passwords don't match
- **Check**: Verify password and confirmation match
- **Solution**: Ensure both fields are identical

#### Cause C: Password too short
- **Check**: Password length
- **Solution**: Use password with at least 8 characters

**Debug Steps**:
```php
// In tinker
$user = auth()->user();
Hash::check('password', $user->password);  // Should return true
```

---

### 9. Report Shows No Data

**Problem**: Sales report is empty even though transactions exist.

**Possible Causes**:

#### Cause A: Date range is incorrect
- **Check**: Report date filters
- **Solution**: Adjust date range to include transactions

#### Cause B: Transactions have status other than PAID
- **Check**: Transaction status
- **Solution**: Only PAID transactions appear in reports

#### Cause C: Product filter is too restrictive
- **Check**: Product filter selection
- **Solution**: Clear product filter or select correct product

**Debug Steps**:
```php
// In tinker
Sale::where('status', 'PAID')->whereBetween('created_at', [$start, $end])->count();
SaleItem::with('item')->get();
```

---

### 10. Slow Performance

**Problem**: System is slow or pages take long to load.

**Possible Causes**:

#### Cause A: Too many records in database
- **Check**: Database size
- **Solution**: Archive old data or optimize queries

#### Cause B: Missing indexes
- **Check**: Database indexes
- **Solution**: Add indexes to frequently queried columns

#### Cause C: N+1 query problem
- **Check**: Livewire component queries
- **Solution**: Use eager loading with `with()`

#### Cause D: Large pagination
- **Check**: Pagination size
- **Solution**: Reduce items per page

**Debug Steps**:
```php
// Enable query logging
DB::enableQueryLog();
// Run your code
dd(DB::getQueryLog());
```

---

### 11. User Cannot Login

**Problem**: Login fails with invalid credentials.

**Possible Causes**:

#### Cause A: User account doesn't exist
- **Check**: Users list
- **Solution**: Create user account

#### Cause B: User is soft deleted
- **Check**: Database directly
- **Solution**: Restore user or create new account

#### Cause C: Password is incorrect
- **Check**: Verify password
- **Solution**: Reset password or create new user

#### Cause D: Email is incorrect
- **Check**: User email
- **Solution**: Use correct email address

**Debug Steps**:
```php
// In tinker
User::where('email', 'user@example.com')->first();
User::withTrashed()->where('email', 'user@example.com')->first();
```

---

### 12. Stock Input Not Saving

**Problem**: Stock input record is not created.

**Possible Causes**:

#### Cause A: Validation error
- **Check**: Form validation messages
- **Solution**: Fill all required fields correctly

#### Cause B: Item doesn't exist
- **Check**: Item selection
- **Solution**: Select valid item

#### Cause C: Quantity is invalid
- **Check**: Quantity value
- **Solution**: Enter positive number

**Debug Steps**:
```php
// In tinker
StockMovement::where('reference_type', 'PURCHASE')->latest()->get();
```

---

### 13. BOM Delete Confirmation Not Working

**Problem**: BOM delete confirmation dialog doesn't appear.

**Possible Causes**:

#### Cause A: JavaScript not loaded
- **Check**: Browser console for errors
- **Solution**: Refresh page

#### Cause B: Alpine.js not initialized
- **Check**: Browser console
- **Solution**: Check that Alpine.js is loaded

**Debug Steps**:
```javascript
// In browser console
console.log(Alpine);  // Should show Alpine object
```

---

### 14. Discount Not Calculating

**Problem**: Discount is not applied to total.

**Possible Causes**:

#### Cause A: Discount field is empty
- **Check**: Discount input
- **Solution**: Enter discount amount

#### Cause B: Livewire not updating
- **Check**: Browser console
- **Solution**: Refresh page

**Debug Steps**:
```php
// In tinker
$total = 1000;
$discount = 100;
$result = $total - $discount;  // Should be 900
```

---

### 15. Cannot View Item Details

**Problem**: Item detail page shows 404 error.

**Possible Causes**:

#### Cause A: Item doesn't exist
- **Check**: Item ID in URL
- **Solution**: Use valid item ID

#### Cause B: Item is soft deleted
- **Check**: Database
- **Solution**: Restore item or use different item

#### Cause C: Route not defined
- **Check**: routes/web.php
- **Solution**: Verify route is defined

**Debug Steps**:
```php
// In tinker
Item::find($itemId);
Item::withTrashed()->find($itemId);
```

---

## Database Issues

### Database Locked
**Problem**: "Database is locked" error

**Solution**:
```bash
# Restart application
php artisan cache:clear

# Or check for long-running queries
sqlite3 database/database.sqlite
> .tables
```

### Corrupted Database
**Problem**: Database appears corrupted

**Solution**:
```bash
# Backup current database
cp database/database.sqlite database/database.backup.sqlite

# Reset database
php artisan migrate:refresh

# Restore from backup if needed
cp database/database.backup.sqlite database/database.sqlite
```

---

## Performance Optimization

### Slow Queries
```php
// Enable query logging
DB::enableQueryLog();

// Run your code
$items = Item::with('boms.material')->get();

// Check queries
dd(DB::getQueryLog());
```

### Add Indexes
```php
// In migration
Schema::table('stock_movements', function (Blueprint $table) {
    $table->index(['item_id', 'created_at']);
});
```

### Cache Results
```php
// Cache report data
$report = Cache::remember('sales_report_' . $date, 3600, function () {
    return Sale::where('status', 'PAID')
        ->whereDate('created_at', $date)
        ->sum('total_price');
});
```

---

## Getting Help

### Check Logs
```bash
# View application logs
tail -f storage/logs/laravel.log

# Or in tinker
Log::info('Debug message');
```

### Database Inspection
```php
// In tinker
// Check item
Item::find($id);

// Check stock movements
StockMovement::where('item_id', $id)->latest()->get();

// Check sales
Sale::with('items.item')->latest()->get();

// Check BOM
ItemBom::where('product_id', $id)->with('material')->get();
```

### Common Commands
```bash
# Clear cache
php artisan cache:clear

# Clear config cache
php artisan config:clear

# Restart queue
php artisan queue:restart

# Check database connection
php artisan tinker
> DB::connection()->getPdo();
```

---

## Reporting Issues

When reporting an issue, include:

1. **Error Message**: Exact error text
2. **Steps to Reproduce**: How to trigger the issue
3. **Expected Behavior**: What should happen
4. **Actual Behavior**: What actually happens
5. **Screenshots**: If applicable
6. **Database State**: Relevant data
7. **Logs**: Error logs from storage/logs/

### Example Issue Report
```
Title: Stock not reducing on sale

Steps to Reproduce:
1. Create product with is_track_stock = false
2. Add BOM entry (1 material, qty 2)
3. Create sale with 1 unit of product
4. Check material stock

Expected: Material stock reduced by 2
Actual: Material stock unchanged

Error Log: [paste relevant logs]
```

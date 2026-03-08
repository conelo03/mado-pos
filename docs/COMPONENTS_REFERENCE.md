# Components Reference

## Livewire Components

### Dashboard
**Path**: `app/Livewire/Dashboard.php`
**Route**: `/`

**Purpose**: Display system overview and recent transactions.

**Public Properties**:
- None (all data passed to view)

**Methods**:
- `render()` - Renders dashboard with statistics

**Data Passed to View**:
- `totalProducts` - Count of items with type PRODUCT
- `totalRawMaterials` - Count of items with type RAW_MATERIAL
- `todaySales` - Count of PAID sales today
- `todayRevenue` - Sum of total_price for PAID sales today
- `recentTransactions` - Last 10 sales

---

### Items Index
**Path**: `app/Livewire/Items/Index.php`
**Route**: `/items`

**Purpose**: List all items with search and filtering.

**Public Properties**:
- `search` - Search query for item name
- `showModal` - Toggle create/edit modal
- `editingId` - ID of item being edited
- `name` - Item name input
- `type` - Item type (PRODUCT or RAW_MATERIAL)
- `unit` - Unit of measurement
- `price` - Item price
- `stock` - Current stock
- `minimum_stock` - Minimum stock threshold
- `is_active` - Active status
- `is_track_stock` - Stock tracking mode
- `deleteId` - ID of item to delete

**Methods**:
- `render()` - Display items list
- `openModal()` - Open create/edit modal
- `closeModal()` - Close modal
- `save()` - Create or update item
- `edit($id)` - Load item for editing
- `confirmDelete($id)` - Show delete confirmation
- `delete($id)` - Delete item

**Validation Rules**:
- `name` - required, string
- `type` - required, in:PRODUCT,RAW_MATERIAL
- `unit` - required, string
- `price` - required, numeric, min:0
- `stock` - required, numeric, min:0
- `minimum_stock` - required, numeric, min:0

---

### Items Detail
**Path**: `app/Livewire/Items/Detail.php`
**Route**: `/items/{id}`

**Purpose**: View item details, manage BOM, and view stock movements.

**Public Properties**:
- `itemId` - Current item ID
- `item` - Item model instance
- `showBomModal` - Toggle BOM modal
- `editingBomId` - ID of BOM being edited
- `material_id` - Material selection for BOM
- `bom_qty` - Quantity for BOM entry
- `deletingBomId` - ID of BOM to delete
- `dateFrom` - Stock movement filter start date
- `dateTo` - Stock movement filter end date

**Methods**:
- `mount($id)` - Load item
- `render()` - Display item details
- `openBomModal()` - Open BOM modal
- `closeBomModal()` - Close BOM modal
- `saveBom()` - Create or update BOM entry
- `editBom($id)` - Load BOM for editing
- `confirmDeleteBom($id)` - Show BOM delete confirmation
- `deleteBom($id)` - Delete BOM entry
- `resetFilter()` - Clear date filters

**Validation Rules**:
- `material_id` - required, exists:items,id
- `bom_qty` - required, numeric, min:0.01

**Features**:
- For PRODUCT type: Shows BOM management interface
- For RAW_MATERIAL type: Shows stock movements
- Date range filtering for stock movements
- Pagination (10 per page)

---

### Transactions Index
**Path**: `app/Livewire/Transactions/Index.php`
**Route**: `/transactions`

**Purpose**: Manage sales transactions with real-time stock calculation.

**Public Properties**:
- `search` - Search by invoice number
- `showModal` - Toggle transaction modal
- `editingId` - ID of transaction being edited
- `items` - Array of items in current transaction
- `discount` - Discount amount
- `paid_amount` - Amount paid by customer
- `total_price` - Total transaction amount
- `change_amount` - Change to give customer
- `productSearch` - Search for products to add
- `deleteId` - ID of transaction to delete
- `refundId` - ID of transaction to refund

**Methods**:
- `render()` - Display transactions list
- `openModal()` - Open transaction modal
- `closeModal()` - Close modal
- `addItem($productId)` - Add item to transaction
- `removeItem($index)` - Remove item from transaction
- `updateQty($index, $qty)` - Update item quantity
- `incrementQty($index)` - Increase quantity by 1
- `decrementQty($index)` - Decrease quantity by 1
- `calculateTotal()` - Recalculate totals
- `updatedDiscount()` - Recalculate when discount changes
- `updatedPaidAmount()` - Recalculate when paid amount changes
- `save()` - Create or update transaction
- `edit($id)` - Load transaction for editing
- `confirmDelete($id)` - Show delete confirmation
- `confirmRefund($id)` - Show refund confirmation
- `delete($id)` - Delete transaction and restore stock
- `refund($id)` - Mark transaction as refunded

**Private Methods**:
- `reduceItemStock($itemId, $qty, $saleId)` - Reduce stock based on item type
- `restoreItemStock($itemId, $qty, $saleId)` - Restore stock on delete

**Stock Logic**:
- If `is_track_stock = true`: Reduce item stock directly
- If `is_track_stock = false`: Reduce materials from BOM
- All changes recorded in stock_movements with reference_type = SALE

**Features**:
- Real-time product search
- Automatic total calculation
- Change amount calculation
- Database transaction wrapping for consistency
- Stock restoration on delete
- Confirmation dialogs for delete/refund

---

### Stock Input
**Path**: `app/Livewire/StockManagement/StockInput.php`
**Route**: `/stock-input`

**Purpose**: Record incoming stock (purchases).

**Public Properties**:
- `search` - Search by item name
- `showModal` - Toggle input modal
- `editingId` - ID of movement being edited
- `item_id` - Selected item
- `qty` - Quantity input
- `date` - Date of input
- `note` - Optional notes
- `deleteId` - ID of movement to delete

**Methods**:
- `mount()` - Initialize date to today
- `render()` - Display stock inputs
- `openModal()` - Open input modal
- `closeModal()` - Close modal
- `save()` - Create or update stock input
- `edit($id)` - Load input for editing
- `confirmDelete($id)` - Show delete confirmation
- `delete($id)` - Delete stock input and reverse stock

**Validation Rules**:
- `item_id` - required, exists:items,id
- `qty` - required, numeric
- `date` - required, date

**Features**:
- Increment item stock
- Create stock movement with reference_type = PURCHASE
- Edit existing inputs
- Delete with stock reversal

---

### Stock Opname
**Path**: `app/Livewire/StockManagement/StockOpname.php`
**Route**: `/stock-opname`

**Purpose**: Record stock adjustments and waste.

**Public Properties**:
- `search` - Search by item name
- `showModal` - Toggle opname modal
- `editingId` - ID of movement being edited
- `item_id` - Selected item
- `qty` - Quantity (positive for adjustment, negative for waste)
- `date` - Date of opname
- `note` - Optional notes
- `deleteId` - ID of movement to delete

**Methods**:
- `mount()` - Initialize date to today
- `render()` - Display stock opnames
- `openModal()` - Open opname modal
- `closeModal()` - Close modal
- `save()` - Create or update stock opname
- `edit($id)` - Load opname for editing
- `confirmDelete($id)` - Show delete confirmation
- `delete($id)` - Delete stock opname and reverse stock

**Validation Rules**:
- `item_id` - required, exists:items,id
- `qty` - required, numeric
- `date` - required, date

**Features**:
- Positive qty: ADJUSTMENT (stock increase)
- Negative qty: WASTE (stock decrease)
- Create stock movement with reference_type = ADJUSTMENT or WASTE
- Edit existing records
- Delete with stock reversal

---

### Reports - By Products
**Path**: `app/Livewire/Reports/ByProducts.php`
**Route**: `/reports/by-products`

**Purpose**: Sales report grouped by product.

**Public Properties**:
- `startDate` - Report start date (default: first day of month)
- `endDate` - Report end date (default: today)
- `productId` - Filter by specific product

**Methods**:
- `mount()` - Initialize date range
- `render()` - Display report

**Data Passed to View**:
- `report` - Grouped sales data by product
- `totalQty` - Total quantity sold
- `totalSubtotal` - Total revenue
- `products` - List of products for filtering

**Report Data Structure**:
```php
[
    'product_name' => string,
    'qty' => decimal,
    'subtotal' => decimal,
]
```

---

### Reports - By Transactions
**Path**: `app/Livewire/Reports/ByTransactions.php`
**Route**: `/reports/by-transactions`

**Purpose**: Sales report by individual transaction.

**Public Properties**:
- `startDate` - Report start date
- `endDate` - Report end date

**Methods**:
- `mount()` - Initialize date range
- `render()` - Display report

**Data Passed to View**:
- `transactions` - List of transactions in date range
- `totalRevenue` - Total revenue
- `totalTransactions` - Count of transactions

---

### Users Index
**Path**: `app/Livewire/Users/Index.php`
**Route**: `/users`

**Purpose**: Manage system users.

**Public Properties**:
- `search` - Search by name or email
- `showModal` - Toggle user modal
- `editingId` - ID of user being edited
- `name` - User name
- `email` - User email
- `password` - User password
- `role` - User role (ADMIN or USER)
- `deleteId` - ID of user to delete

**Methods**:
- `render()` - Display users list
- `openModal()` - Open create/edit modal
- `closeModal()` - Close modal
- `save()` - Create or update user
- `edit($id)` - Load user for editing
- `confirmDelete($id)` - Show delete confirmation
- `delete($id)` - Soft delete user

---

### Change Password
**Path**: `app/Livewire/ChangePassword.php`
**Route**: `/change-password`

**Purpose**: Allow users to change their password.

**Public Properties**:
- `current_password` - Current password input
- `password` - New password input
- `password_confirmation` - Password confirmation
- `showPassword` - Toggle password visibility

**Methods**:
- `render()` - Display change password form
- `togglePasswordVisibility()` - Toggle password visibility
- `save()` - Update password

**Validation Rules**:
- `current_password` - required, current_password
- `password` - required, string, min:8, confirmed

**Features**:
- Validates current password
- Requires password confirmation
- Password visibility toggle with eye icon
- Uses Hash::make() for security

---

## View Components

### app-layout.blade.php
Main layout component with sidebar navigation.

**Slots**:
- `title` - Page title (default: Dashboard)
- `slot` - Page content

**Features**:
- Responsive sidebar (drawer)
- User dropdown menu
- Navigation menu with role-based access
- Logout functionality

### confirm-dialog.blade.php
Confirmation dialog component for delete operations.

**Props**:
- `title` - Dialog title
- `message` - Confirmation message
- `confirmText` - Confirm button text
- `cancelText` - Cancel button text
- `isDangerous` - Boolean for danger styling
- `onConfirm` - Livewire method to call on confirm

### confirm-refund-dialog.blade.php
Confirmation dialog for refund operations.

**Props**:
- `title` - Dialog title
- `message` - Confirmation message
- `confirmText` - Confirm button text
- `cancelText` - Cancel button text
- `onConfirm` - Livewire method to call on confirm

---

## Models

### Item
**Relationships**:
- `boms()` - HasMany ItemBom (product_id)
- `stockMovements()` - HasMany StockMovement
- `createdBy()` - BelongsTo User
- `updatedBy()` - BelongsTo User

### ItemBom
**Relationships**:
- `product()` - BelongsTo Item (product_id)
- `material()` - BelongsTo Item (material_id)

### StockMovement
**Relationships**:
- `item()` - BelongsTo Item
- `createdBy()` - BelongsTo User
- `updatedBy()` - BelongsTo User

### Sale
**Relationships**:
- `items()` - HasMany SaleItem

### SaleItem
**Relationships**:
- `sale()` - BelongsTo Sale
- `item()` - BelongsTo Item

### User
Standard Laravel User model with role support.

---

## Controllers

### TransactionPrintController
**Path**: `app/Http/Controllers/TransactionPrintController.php`

**Methods**:
- `show($id)` - Display printable receipt for transaction

**Route**: `GET /transactions/{id}/print`

**Returns**: Blade view with transaction details formatted for printing

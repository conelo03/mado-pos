# Common Workflows

## 1. Setting Up a New Product

### Scenario
You want to add a new product to the system that will be sold to customers.

### Steps

1. **Navigate to Items**
   - Go to `/items` (Admin only)
   - Click "New Item" button

2. **Fill in Product Details**
   - Name: Enter product name (e.g., "Laptop")
   - Type: Select "PRODUCT"
   - Unit: Select unit (e.g., "pcs" for pieces)
   - Price: Enter selling price
   - Stock: Enter initial stock quantity
   - Minimum Stock: Set reorder point
   - Is Active: Check to enable
   - Is Track Stock: Choose tracking mode

3. **Choose Stock Tracking Mode**

   **Option A: Direct Tracking (is_track_stock = true)**
   - Use for simple products without components
   - Stock is reduced directly from the item
   - Example: Pre-made laptops

   **Option B: BOM-Based Tracking (is_track_stock = false)**
   - Use for assembled products
   - Stock is reduced from component materials
   - Requires setting up BOM (see next section)
   - Example: Custom-built computers

4. **Save Product**
   - Click "Save" button
   - Product is now available for sales

### If Using BOM-Based Tracking

5. **Add Bill of Materials**
   - Click on the product in the items list
   - Go to "BOM" section
   - Click "Add BOM" button
   - Select material (raw material item)
   - Enter quantity needed per product unit
   - Click "Save"
   - Repeat for each material component

6. **Verify BOM**
   - Check that all materials are listed
   - Verify quantities are correct
   - Ensure all materials have sufficient stock

---

## 2. Recording a Sale

### Scenario
A customer wants to buy products.

### Steps

1. **Navigate to Transactions**
   - Go to `/transactions`
   - Click "New Transaction" button

2. **Add Items to Sale**
   - Search for product in the search box
   - Click on product to add it
   - Product appears in the items list
   - Adjust quantity using +/- buttons or input field
   - Add more items as needed

3. **Apply Discount (Optional)**
   - Enter discount amount in "Discount" field
   - Total updates automatically

4. **Enter Payment**
   - Enter amount paid by customer in "Paid Amount" field
   - Change amount calculates automatically
   - If no amount entered, defaults to total price

5. **Review Summary**
   - Check subtotal, discount, total, and change
   - Verify all items and quantities

6. **Save Transaction**
   - Click "Save" button
   - Transaction is recorded
   - Stock is automatically reduced
   - Stock movements are created

### Stock Reduction Details

**For Direct Tracking Products (is_track_stock = true):**
- Product stock is reduced by sale quantity
- One stock movement created per product

**For BOM-Based Products (is_track_stock = false):**
- Each material in BOM is reduced by (BOM qty × sale qty)
- Multiple stock movements created (one per material)

### Example
- Product: "Custom Computer" (is_track_stock = false)
- BOM: 1 CPU, 2 RAM, 1 SSD
- Sale: 2 computers
- Stock reduction:
  - CPU: -2 (1 × 2)
  - RAM: -4 (2 × 2)
  - SSD: -2 (1 × 2)

---

## 3. Editing a Transaction

### Scenario
You need to modify a sale that was just recorded.

### Steps

1. **Navigate to Transactions**
   - Go to `/transactions`
   - Find the transaction in the list

2. **Click Edit Button**
   - Click the pencil icon for the transaction
   - Transaction modal opens with current items

3. **Modify Items**
   - Add new items using the search box
   - Remove items by clicking trash icon
   - Adjust quantities as needed
   - Update discount or paid amount

4. **Review Changes**
   - Check that totals are correct
   - Verify all changes

5. **Save Changes**
   - Click "Save" button
   - Old stock is restored
   - New stock is reduced
   - Stock movements are updated

---

## 4. Deleting/Voiding a Transaction

### Scenario
A sale needs to be cancelled and stock restored.

### Steps

1. **Navigate to Transactions**
   - Go to `/transactions`
   - Find the transaction to delete

2. **Click Delete Button**
   - Click the trash icon for the transaction
   - Confirmation dialog appears

3. **Confirm Deletion**
   - Read the warning message
   - Click "Delete" to confirm
   - Transaction is marked as VOID
   - Stock is automatically restored
   - Stock movements are deleted

### What Happens
- Sale status changes to VOID
- All items' stock is incremented back
- Stock movement records are deleted (they're transaction records, not history)
- Transaction can be recovered from soft delete if needed

---

## 5. Recording Stock Input (Purchase)

### Scenario
New stock arrives from supplier.

### Steps

1. **Navigate to Stock Management**
   - Go to `/stock-input` (Admin only)

2. **Click "New Stock Input"**
   - Modal opens for new input

3. **Fill in Details**
   - Select Item: Choose the item being received
   - Quantity: Enter quantity received
   - Date: Date of receipt (defaults to today)
   - Note: Optional notes (e.g., supplier name, invoice number)

4. **Save**
   - Click "Save" button
   - Item stock is incremented
   - Stock movement created with reference_type = PURCHASE

### Editing Stock Input

1. **Click Edit Button**
   - Click pencil icon for the input
   - Modal opens with current data

2. **Modify Details**
   - Change quantity, date, or notes
   - Stock difference is calculated and applied

3. **Save Changes**
   - Click "Save" button
   - Stock is adjusted by the difference

### Deleting Stock Input

1. **Click Delete Button**
   - Click trash icon for the input
   - Confirmation dialog appears

2. **Confirm Deletion**
   - Click "Delete" to confirm
   - Stock is reversed (decremented)
   - Stock movement is deleted

---

## 6. Recording Stock Opname (Adjustment/Waste)

### Scenario
You need to adjust stock due to inventory count discrepancy or record waste.

### Steps

1. **Navigate to Stock Management**
   - Go to `/stock-opname` (Admin only)

2. **Click "New Stock Opname"**
   - Modal opens for new opname

3. **Fill in Details**
   - Select Item: Choose the item to adjust
   - Quantity: 
     - Positive number: Stock adjustment (increase)
     - Negative number: Waste/loss (decrease)
   - Date: Date of opname (defaults to today)
   - Note: Reason for adjustment (e.g., "Inventory count", "Damaged goods")

4. **Save**
   - Click "Save" button
   - Item stock is adjusted
   - Stock movement created with:
     - reference_type = ADJUSTMENT (if positive)
     - reference_type = WASTE (if negative)

### Example Scenarios

**Scenario A: Inventory Count Discrepancy**
- Physical count shows 50 units
- System shows 45 units
- Quantity to enter: +5
- Result: Stock increased by 5

**Scenario B: Damaged Goods**
- 3 units found damaged
- Quantity to enter: -3
- Result: Stock decreased by 3, recorded as WASTE

---

## 7. Viewing Stock Movements

### Scenario
You want to see the history of stock changes for an item.

### Steps

1. **Navigate to Items**
   - Go to `/items`

2. **Click on Item**
   - Click on the item name to view details

3. **View Stock Movements**
   - Scroll to "Stock Movements" section
   - Shows all stock changes for this item

4. **Filter by Date (Optional)**
   - Enter "From Date" to filter from a specific date
   - Enter "To Date" to filter until a specific date
   - Click "Reset" to clear filters

### Stock Movement Details
Each movement shows:
- Type: IN or OUT
- Quantity: Amount changed
- Reference Type: PURCHASE, SALE, ADJUSTMENT, or WASTE
- Date: When the movement occurred
- Note: Additional information
- Created By: User who created the movement

---

## 8. Viewing Bill of Materials

### Scenario
You want to see what materials are used in a product.

### Steps

1. **Navigate to Items**
   - Go to `/items`

2. **Click on Product**
   - Click on a product with type "PRODUCT"

3. **View BOM Section**
   - Shows list of materials used in the product
   - Each row shows:
     - Material name
     - Quantity needed per product unit
     - Edit and delete buttons

4. **Edit BOM Entry**
   - Click pencil icon
   - Modal opens with current data
   - Modify material or quantity
   - Click "Save"

5. **Delete BOM Entry**
   - Click trash icon
   - Confirmation dialog appears
   - Click "Delete" to confirm

---

## 9. Generating Sales Reports

### Scenario
You want to analyze sales performance.

### Steps

1. **Navigate to Reports**
   - Go to `/reports/by-products` or `/reports/by-transactions`

2. **Set Date Range**
   - Enter "Start Date" (defaults to first day of month)
   - Enter "End Date" (defaults to today)

3. **Filter (Optional)**
   - For "By Products" report: Select specific product to filter
   - Click "Filter" or wait for auto-update

4. **View Report**
   - See sales data grouped by product or transaction
   - Shows quantity sold and revenue
   - Totals displayed at bottom

### Report Types

**By Products Report:**
- Groups sales by product
- Shows total quantity sold per product
- Shows total revenue per product
- Useful for identifying best-selling products

**By Transactions Report:**
- Lists individual transactions
- Shows transaction details and amounts
- Useful for daily reconciliation

---

## 10. Managing Users

### Scenario
You need to add a new cashier to the system.

### Steps

1. **Navigate to Users**
   - Go to `/users` (Admin only)

2. **Click "New User"**
   - Modal opens for new user

3. **Fill in Details**
   - Name: User's full name
   - Email: User's email address
   - Password: Initial password
   - Role: Select "USER" for cashier, "ADMIN" for manager

4. **Save**
   - Click "Save" button
   - User account is created
   - User can now login

### Editing User

1. **Click Edit Button**
   - Click pencil icon for the user
   - Modal opens with current data

2. **Modify Details**
   - Change name, email, or role
   - Leave password blank to keep current password
   - Enter new password to change it

3. **Save Changes**
   - Click "Save" button

### Deleting User

1. **Click Delete Button**
   - Click trash icon for the user
   - Confirmation dialog appears

2. **Confirm Deletion**
   - Click "Delete" to confirm
   - User is soft deleted (can be recovered)
   - User cannot login anymore

---

## 11. Printing Receipt

### Scenario
Customer needs a receipt for their purchase.

### Steps

1. **Navigate to Transactions**
   - Go to `/transactions`

2. **Find Transaction**
   - Locate the transaction in the list

3. **Click Print Button**
   - Click the printer icon
   - Receipt opens in new window
   - Auto-print dialog appears

4. **Print Receipt**
   - Select printer
   - Click "Print" button
   - Or click "Close" to close without printing

### Receipt Contents
- Store name (MADO POS)
- Invoice number and date/time
- Item details (name, price, quantity, subtotal)
- Subtotal, discount, total, paid amount, change
- Thank you message

---

## 12. Changing Password

### Scenario
User wants to change their login password.

### Steps

1. **Click User Menu**
   - Click avatar in top-right corner

2. **Select "Change Password"**
   - Click "Change Password" option
   - Change password page opens

3. **Enter Passwords**
   - Current Password: Enter current password
   - New Password: Enter new password (min 8 characters)
   - Confirm Password: Re-enter new password
   - Use eye icon to toggle password visibility

4. **Save**
   - Click "Save" button
   - Password is updated
   - User is logged out and must login with new password

---

## Troubleshooting Common Issues

### Stock Not Reducing on Sale
- Check if product has `is_track_stock = true`
- If false, verify BOM is set up correctly
- Check that materials have sufficient stock

### Cannot Add Item to Sale
- Verify item is marked as "is_active = true"
- Check that item type is "PRODUCT"
- Ensure item exists in the system

### Stock Movement Not Showing
- Verify stock movement was created (check database)
- Check date range filter in Items Detail
- Ensure item_id is correct

### Cannot Delete Transaction
- Verify you have admin role
- Check that transaction exists
- Ensure stock is available to restore

### BOM Not Working
- Verify product has `is_track_stock = false`
- Check that BOM entries exist
- Verify materials have sufficient stock
- Ensure material items exist in system

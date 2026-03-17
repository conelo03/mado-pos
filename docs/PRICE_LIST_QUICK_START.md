# Price List Management - Quick Start Guide

## 🚀 Getting Started

### Prerequisites
- Admin or Superadmin role
- Access to MADO POS system

### Default Price List Types
The system comes with 4 pre-configured price list types:
1. **Retail Price** - Standard retail pricing
2. **Wholesale Price** - Bulk/wholesale pricing
3. **Member Price** - Member-exclusive pricing
4. **Reseller Price** - Reseller pricing

## 📋 Step-by-Step Guide

### Step 1: Create a Price List Type (Optional)
If you need additional pricing tiers beyond the defaults:

1. Go to **Price List Types** from sidebar
2. Click **Add Price List Type**
3. Fill in:
   - **Name**: e.g., "VIP Customer Price"
   - **Type**: Select from RETAIL, GROSIR, MEMBER, RESELLER
   - **Description**: Optional notes
4. Click **Save**

### Step 2: Create a Customer
1. Go to **Customers** from sidebar
2. Click **Add Customer**
3. Fill in:
   - **Price List Type**: Select the pricing tier for this customer
   - **Name**: Customer name
   - **Phone**: Optional phone number
   - **Address**: Optional address
4. Click **Save**

### Step 3: Set Item Prices
1. Go to **Items** from sidebar
2. Click on an item to view details
3. Scroll to **Price Lists** section
4. Click **Add Price**
5. Fill in:
   - **Price List Type**: Select pricing tier
   - **Price**: Enter the price for this tier
6. Click **Save**

Repeat for each price list type you want to set prices for.

## 🎯 Common Tasks

### View All Prices for an Item
1. Go to **Items** → Select item
2. Scroll to **Price Lists** section
3. All configured prices are displayed in a table

### Update a Customer's Price List Type
1. Go to **Customers**
2. Find the customer
3. Click **Edit**
4. Change **Price List Type**
5. Click **Save**

### Change an Item's Price
1. Go to **Items** → Select item
2. Scroll to **Price Lists** section
3. Click **Edit** on the price you want to change
4. Update the price
5. Click **Save**

### Delete a Price
1. Go to **Items** → Select item
2. Scroll to **Price Lists** section
3. Click **Delete** on the price
4. Confirm deletion

### Search Customers
1. Go to **Customers**
2. Type customer name in search box
3. Results update automatically

### Search Price List Types
1. Go to **Price List Types**
2. Type name in search box
3. Results update automatically

## 📊 Data Structure

### Price List Types
- **Name**: Display name (e.g., "Retail Price")
- **Type**: Category (RETAIL, GROSIR, MEMBER, RESELLER)
- **Description**: Optional notes about the pricing tier

### Customers
- **Name**: Customer name
- **Price List Type**: Which pricing tier they use
- **Phone**: Contact number (optional)
- **Address**: Delivery address (optional)

### Item Prices
- **Item**: Which product/material
- **Price List Type**: Which pricing tier
- **Price**: The price for this combination

## ✅ Best Practices

### Organizing Price List Types
- Use clear, descriptive names
- Include type in the name if helpful (e.g., "Retail - Standard")
- Add descriptions for internal reference

### Managing Customers
- Assign appropriate price list types
- Keep contact information updated
- Use consistent naming conventions

### Setting Item Prices
- Set prices for all relevant price list types
- Review prices regularly for accuracy
- Consider bulk discounts in wholesale pricing
- Document any special pricing rules

## 🔍 Troubleshooting

### Can't see Price List Types menu?
- Ensure you're logged in as Admin or Superadmin
- Check user role in Users management

### Can't add a price for an item?
- Ensure the price list type exists
- Check that you haven't already set a price for this item/type combination
- Verify the price is a valid number

### Customer not appearing in search?
- Check spelling of customer name
- Ensure customer hasn't been deleted
- Try clearing search and scrolling through list

### Price not updating?
- Refresh the page after saving
- Check that you clicked Save button
- Verify no validation errors appeared

## 📱 Mobile Access

All features are mobile-responsive:
- Tables adapt to smaller screens
- Forms are touch-friendly
- Navigation works on mobile devices

## 🔐 Permissions

| Feature | Admin | Superadmin | Cashier |
|---------|-------|-----------|---------|
| View Price List Types | ✓ | ✓ | ✗ |
| Create Price List Type | ✓ | ✓ | ✗ |
| Edit Price List Type | ✓ | ✓ | ✗ |
| Delete Price List Type | ✓ | ✓ | ✗ |
| View Customers | ✓ | ✓ | ✗ |
| Create Customer | ✓ | ✓ | ✗ |
| Edit Customer | ✓ | ✓ | ✗ |
| Delete Customer | ✓ | ✓ | ✗ |
| Manage Item Prices | ✓ | ✓ | ✗ |

## 💡 Tips & Tricks

### Bulk Price Updates
Currently, prices must be updated individually. For bulk updates:
1. Go to each item
2. Update prices in the Price Lists section
3. Consider creating a spreadsheet for reference

### Price Comparison
To compare prices across types:
1. Go to an item
2. View all prices in the Price Lists section
3. Prices are displayed in a clear table format

### Audit Trail
All changes are tracked:
- Who created/updated each record
- When changes were made
- Deleted records are preserved for compliance

## 📞 Support

For issues or questions:
1. Check the full documentation in `docs/PRICE_LIST_MANAGEMENT.md`
2. Review database schema in `docs/DATABASE_SCHEMA.md`
3. Check implementation details in `docs/IMPLEMENTATION_SUMMARY.md`

## 🎓 Learning Resources

### Understanding Price List Types
- **RETAIL**: Standard prices for regular customers
- **GROSIR**: Discounted prices for bulk purchases
- **MEMBER**: Special prices for registered members
- **RESELLER**: Wholesale prices for resellers

### When to Use Each Type
- Use RETAIL for walk-in customers
- Use GROSIR for large orders
- Use MEMBER for loyalty program members
- Use RESELLER for business partners

### Pricing Strategy
- Set RETAIL as your base price
- GROSIR should be 10-20% lower than RETAIL
- MEMBER can be 5-15% lower than RETAIL
- RESELLER should be 20-30% lower than RETAIL

## 🔄 Workflow Example

### Scenario: New Wholesale Customer

1. **Create Price List Type** (if needed)
   - Name: "Wholesale - Bulk Orders"
   - Type: GROSIR
   - Description: "For orders over 100 units"

2. **Create Customer**
   - Name: "PT Distributor Maju"
   - Price List Type: "Wholesale - Bulk Orders"
   - Phone: "08123456789"
   - Address: "Jl. Industri No. 45"

3. **Set Item Prices**
   - For each product, set wholesale price
   - Example: Retail Rp 10,000 → Wholesale Rp 7,500

4. **Ready to Use**
   - Customer can now be assigned to sales transactions
   - Prices will be applied based on their price list type

## 📈 Next Steps

After setting up price lists:
1. Train staff on using the system
2. Review prices regularly
3. Monitor sales by price list type
4. Adjust pricing as needed
5. Consider seasonal adjustments

---

**Last Updated:** March 17, 2026
**Version:** 1.0

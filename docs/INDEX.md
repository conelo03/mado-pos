# MADO POS Documentation Index

Welcome to the MADO POS documentation. This guide will help you understand and use the system effectively.

## 📚 Documentation Structure

### Getting Started
- **[SYSTEM_OVERVIEW.md](SYSTEM_OVERVIEW.md)** - Complete system architecture and overview
  - Core architecture and database structure
  - Module overview
  - User roles and permissions
  - Technology stack

### Learning & Reference
- **[QUICK_REFERENCE.md](QUICK_REFERENCE.md)** - Quick lookup guide
  - Routes and endpoints
  - Models and relationships
  - Common queries
  - Validation rules
  - Useful commands

- **[COMPONENTS_REFERENCE.md](COMPONENTS_REFERENCE.md)** - Detailed component documentation
  - Livewire components with properties and methods
  - Blade components
  - Models and relationships
  - Controllers

### How-To Guides
- **[WORKFLOWS.md](WORKFLOWS.md)** - Step-by-step workflows
  - Setting up products
  - Recording sales
  - Managing stock
  - Generating reports
  - Managing users

### Technical Documentation
- **[DATABASE_SCHEMA.md](DATABASE_SCHEMA.md)** - Database structure
  - Table definitions
  - Relationships
  - Constraints
  - Indexes
  - Sample queries

### Support
- **[TROUBLESHOOTING.md](TROUBLESHOOTING.md)** - Problem solving
  - Common issues and solutions
  - Debug steps
  - Performance optimization
  - Getting help

---

## 🚀 Quick Start

### For New Users
1. Start with [SYSTEM_OVERVIEW.md](SYSTEM_OVERVIEW.md) to understand the system
2. Follow [WORKFLOWS.md](WORKFLOWS.md) for step-by-step guides
3. Use [QUICK_REFERENCE.md](QUICK_REFERENCE.md) for quick lookups

### For Developers
1. Read [SYSTEM_OVERVIEW.md](SYSTEM_OVERVIEW.md) for architecture
2. Review [DATABASE_SCHEMA.md](DATABASE_SCHEMA.md) for data structure
3. Check [COMPONENTS_REFERENCE.md](COMPONENTS_REFERENCE.md) for component details
4. Use [QUICK_REFERENCE.md](QUICK_REFERENCE.md) for code snippets

### For Troubleshooting
1. Check [TROUBLESHOOTING.md](TROUBLESHOOTING.md) for your issue
2. Follow debug steps provided
3. Use [QUICK_REFERENCE.md](QUICK_REFERENCE.md) for database inspection commands

---

## 📖 Documentation by Topic

### Items Management
- [SYSTEM_OVERVIEW.md - Items Table](SYSTEM_OVERVIEW.md#1-items-table)
- [WORKFLOWS.md - Setting Up a New Product](WORKFLOWS.md#1-setting-up-a-new-product)
- [COMPONENTS_REFERENCE.md - Items Index](COMPONENTS_REFERENCE.md#items-index)
- [COMPONENTS_REFERENCE.md - Items Detail](COMPONENTS_REFERENCE.md#items-detail)
- [DATABASE_SCHEMA.md - Items Table](DATABASE_SCHEMA.md#2-items)

### Sales & Transactions
- [SYSTEM_OVERVIEW.md - Sales & SaleItems Tables](SYSTEM_OVERVIEW.md#4-sales--saleitems-tables)
- [WORKFLOWS.md - Recording a Sale](WORKFLOWS.md#2-recording-a-sale)
- [WORKFLOWS.md - Editing a Transaction](WORKFLOWS.md#3-editing-a-transaction)
- [WORKFLOWS.md - Deleting/Voiding a Transaction](WORKFLOWS.md#4-deletingvoiding-a-transaction)
- [COMPONENTS_REFERENCE.md - Transactions Index](COMPONENTS_REFERENCE.md#transactions-index)
- [DATABASE_SCHEMA.md - Sales & SaleItems Tables](DATABASE_SCHEMA.md#5-sales)

### Stock Management
- [SYSTEM_OVERVIEW.md - Stock Management Logic](SYSTEM_OVERVIEW.md#stock-management-logic)
- [WORKFLOWS.md - Recording Stock Input](WORKFLOWS.md#5-recording-stock-input-purchase)
- [WORKFLOWS.md - Recording Stock Opname](WORKFLOWS.md#6-recording-stock-opname-adjustmentwaste)
- [WORKFLOWS.md - Viewing Stock Movements](WORKFLOWS.md#7-viewing-stock-movements)
- [COMPONENTS_REFERENCE.md - Stock Input](COMPONENTS_REFERENCE.md#stock-input)
- [COMPONENTS_REFERENCE.md - Stock Opname](COMPONENTS_REFERENCE.md#stock-opname)
- [DATABASE_SCHEMA.md - StockMovement Table](DATABASE_SCHEMA.md#4-stock_movements)

### Bill of Materials (BOM)
- [SYSTEM_OVERVIEW.md - ItemBom Table](SYSTEM_OVERVIEW.md#2-itembom-table)
- [WORKFLOWS.md - Setting Up BOM](WORKFLOWS.md#1-setting-up-a-new-product)
- [WORKFLOWS.md - Viewing BOM](WORKFLOWS.md#8-viewing-bill-of-materials)
- [DATABASE_SCHEMA.md - ItemBom Table](DATABASE_SCHEMA.md#3-item_boms)

### Reports
- [WORKFLOWS.md - Generating Sales Reports](WORKFLOWS.md#9-generating-sales-reports)
- [COMPONENTS_REFERENCE.md - Reports](COMPONENTS_REFERENCE.md#reports---by-products)
- [QUICK_REFERENCE.md - Common Queries](QUICK_REFERENCE.md#common-queries)

### User Management
- [WORKFLOWS.md - Managing Users](WORKFLOWS.md#10-managing-users)
- [COMPONENTS_REFERENCE.md - Users Index](COMPONENTS_REFERENCE.md#users-index)
- [SYSTEM_OVERVIEW.md - User Roles](SYSTEM_OVERVIEW.md#user-roles)

### Security & Passwords
- [WORKFLOWS.md - Changing Password](WORKFLOWS.md#12-changing-password)
- [COMPONENTS_REFERENCE.md - Change Password](COMPONENTS_REFERENCE.md#change-password)

---

## 🔍 Finding Information

### By Task
| Task | Document | Section |
|------|----------|---------|
| Add a new product | WORKFLOWS.md | Setting Up a New Product |
| Record a sale | WORKFLOWS.md | Recording a Sale |
| Manage stock | WORKFLOWS.md | Stock Input / Stock Opname |
| View stock history | WORKFLOWS.md | Viewing Stock Movements |
| Generate report | WORKFLOWS.md | Generating Sales Reports |
| Add user | WORKFLOWS.md | Managing Users |
| Change password | WORKFLOWS.md | Changing Password |
| Print receipt | WORKFLOWS.md | Printing Receipt |

### By Component
| Component | Document | Section |
|-----------|----------|---------|
| Items | COMPONENTS_REFERENCE.md | Items Index / Items Detail |
| Transactions | COMPONENTS_REFERENCE.md | Transactions Index |
| Stock Input | COMPONENTS_REFERENCE.md | Stock Input |
| Stock Opname | COMPONENTS_REFERENCE.md | Stock Opname |
| Reports | COMPONENTS_REFERENCE.md | Reports |
| Users | COMPONENTS_REFERENCE.md | Users Index |

### By Database Table
| Table | Document | Section |
|-------|----------|---------|
| items | DATABASE_SCHEMA.md | Items Table |
| item_boms | DATABASE_SCHEMA.md | ItemBom Table |
| stock_movements | DATABASE_SCHEMA.md | StockMovement Table |
| sales | DATABASE_SCHEMA.md | Sales Table |
| sale_items | DATABASE_SCHEMA.md | SaleItems Table |
| users | DATABASE_SCHEMA.md | Users Table |

---

## 🛠️ Common Tasks

### I want to...

**...add a new product**
→ See [WORKFLOWS.md - Setting Up a New Product](WORKFLOWS.md#1-setting-up-a-new-product)

**...record a sale**
→ See [WORKFLOWS.md - Recording a Sale](WORKFLOWS.md#2-recording-a-sale)

**...manage inventory**
→ See [WORKFLOWS.md - Recording Stock Input](WORKFLOWS.md#5-recording-stock-input-purchase)

**...view sales reports**
→ See [WORKFLOWS.md - Generating Sales Reports](WORKFLOWS.md#9-generating-sales-reports)

**...add a user**
→ See [WORKFLOWS.md - Managing Users](WORKFLOWS.md#10-managing-users)

**...understand the database**
→ See [DATABASE_SCHEMA.md](DATABASE_SCHEMA.md)

**...find a code example**
→ See [QUICK_REFERENCE.md](QUICK_REFERENCE.md)

**...fix a problem**
→ See [TROUBLESHOOTING.md](TROUBLESHOOTING.md)

---

## 📋 Key Concepts

### Stock Tracking Modes
- **Direct Tracking** (is_track_stock = true): Stock reduced directly from item
- **BOM-Based Tracking** (is_track_stock = false): Stock reduced from materials via BOM

See [SYSTEM_OVERVIEW.md - Stock Tracking Modes](SYSTEM_OVERVIEW.md#stock-tracking-modes)

### Item Types
- **PRODUCT**: Items sold to customers
- **RAW_MATERIAL**: Materials used to make products

See [SYSTEM_OVERVIEW.md - Items Table](SYSTEM_OVERVIEW.md#1-items-table)

### Stock Movement Types
- **PURCHASE**: Stock input from supplier
- **SALE**: Stock reduction from sales
- **ADJUSTMENT**: Stock adjustment/increase
- **WASTE**: Stock loss/waste

See [SYSTEM_OVERVIEW.md - Stock Movement Table](SYSTEM_OVERVIEW.md#3-stockmovement-table)

### User Roles
- **ADMIN**: Full access to all modules
- **USER**: Can only create and view transactions

See [SYSTEM_OVERVIEW.md - User Roles](SYSTEM_OVERVIEW.md#user-roles)

---

## 🔗 Related Resources

### External Documentation
- [Laravel Documentation](https://laravel.com/docs)
- [Livewire Documentation](https://livewire.laravel.com)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [DaisyUI Documentation](https://daisyui.com)

### Project Files
- Source Code: `app/`, `resources/`, `database/`
- Configuration: `config/`
- Routes: `routes/web.php`
- Database: `database/database.sqlite`

---

## 📞 Support

### Getting Help
1. Check [TROUBLESHOOTING.md](TROUBLESHOOTING.md) for your issue
2. Search documentation using Ctrl+F
3. Review [QUICK_REFERENCE.md](QUICK_REFERENCE.md) for code examples
4. Check application logs in `storage/logs/`

### Reporting Issues
Include:
- Error message
- Steps to reproduce
- Expected vs actual behavior
- Screenshots if applicable
- Relevant logs

See [TROUBLESHOOTING.md - Reporting Issues](TROUBLESHOOTING.md#reporting-issues)

---

## 📝 Documentation Versions

- **Current Version**: 1.0
- **Last Updated**: 2024
- **System Version**: MADO POS v1.0

---

## 🎯 Learning Path

### Beginner
1. [SYSTEM_OVERVIEW.md](SYSTEM_OVERVIEW.md) - Understand the system
2. [WORKFLOWS.md](WORKFLOWS.md) - Learn basic tasks
3. [QUICK_REFERENCE.md](QUICK_REFERENCE.md) - Quick lookups

### Intermediate
1. [COMPONENTS_REFERENCE.md](COMPONENTS_REFERENCE.md) - Component details
2. [DATABASE_SCHEMA.md](DATABASE_SCHEMA.md) - Data structure
3. [TROUBLESHOOTING.md](TROUBLESHOOTING.md) - Problem solving

### Advanced
1. [DATABASE_SCHEMA.md](DATABASE_SCHEMA.md) - Advanced queries
2. [QUICK_REFERENCE.md](QUICK_REFERENCE.md) - Code snippets
3. Source code in `app/`, `resources/`, `database/`

---

## 📚 Document Map

```
docs/
├── INDEX.md (this file)
├── SYSTEM_OVERVIEW.md
├── QUICK_REFERENCE.md
├── COMPONENTS_REFERENCE.md
├── WORKFLOWS.md
├── DATABASE_SCHEMA.md
└── TROUBLESHOOTING.md
```

---

## ✅ Checklist for New Users

- [ ] Read SYSTEM_OVERVIEW.md
- [ ] Follow a workflow in WORKFLOWS.md
- [ ] Bookmark QUICK_REFERENCE.md
- [ ] Save TROUBLESHOOTING.md for later
- [ ] Review DATABASE_SCHEMA.md if needed
- [ ] Check COMPONENTS_REFERENCE.md for details

---

## 🎓 Tips for Using This Documentation

1. **Use Ctrl+F** to search within documents
2. **Follow links** to related sections
3. **Check examples** in QUICK_REFERENCE.md
4. **Review workflows** for step-by-step guides
5. **Consult troubleshooting** when stuck
6. **Reference database schema** for data structure

---

**Happy using MADO POS! 🎉**

For questions or feedback, refer to the troubleshooting section or check the application logs.

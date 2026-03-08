# MADO POS - Point of Sale System

Aplikasi POS (Point of Sale) yang dibangun dengan Laravel 12, Livewire 4, Tailwind CSS, dan SQLite.

## Fitur

### 1. Master Data
- **Products**: Kelola produk dengan harga dan status aktif/tidak aktif
- **Raw Materials**: Kelola bahan baku dengan unit dan stok minimum
- **Bill of Materials (BOM)**: Tentukan komposisi bahan baku untuk setiap produk

### 2. Manajemen Stok
- **Stock Input**: Tambah stok bahan baku (tercatat sebagai PURCHASE)
- **Stock Opname**: Adjustment stok (ADJUSTMENT untuk plus, WASTE untuk minus)
- **Stock Movements**: Lihat riwayat perubahan stok dengan filter date range

### 3. Transaksi
- **Penjualan**: Buat transaksi kasir dengan:
  - Input produk dan qty otomatis
  - Perhitungan subtotal otomatis
  - Discount dan paid amount (opsional)
  - Automatic change calculation
  - Automatic stock reduction berdasarkan BOM
- **Edit Transaksi**: Ubah transaksi dan stok akan di-adjust
- **Delete Transaksi**: Hapus transaksi dan ubah status menjadi VOID
- **Refund**: Refund transaksi tanpa mengubah stok

### 4. Dashboard
- Total produk, bahan baku, transaksi hari ini
- Revenue hari ini
- Riwayat transaksi terbaru

## Setup

### Requirements
- PHP 8.2+
- Composer
- Node.js & npm

### Installation

```bash
# Clone atau extract project
cd mado-pos

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate:fresh --seed

# Build assets
npm run build
```

### Running

```bash
# Terminal 1: Start Laravel dev server
php artisan serve

# Terminal 2: Start Vite dev server
npm run dev
```

Akses aplikasi di `http://localhost:8000`

**Login Credentials:**
- Email: admin@example.com
- Password: password

## Database Schema

### Tables
- `users` - User authentication
- `products` - Master produk
- `raw_materials` - Master bahan baku
- `boms` - Bill of Materials (relasi produk & bahan baku)
- `sales` - Transaksi penjualan
- `sale_items` - Detail item dalam transaksi
- `raw_material_stock_movements` - Riwayat perubahan stok
- `raw_material_stock_inputs` - Input stok bahan baku
- `raw_material_stock_opnames` - Opname stok bahan baku

## Struktur Project

```
mado-pos/
├── app/
│   ├── Http/
│   │   └── Controllers/Auth/
│   ├── Livewire/
│   │   ├── Products/
│   │   ├── RawMaterials/
│   │   ├── StockInputs/
│   │   ├── StockOpnames/
│   │   └── Transactions/
│   └── Models/
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
│       ├── auth/
│       ├── components/
│       └── livewire/
└── routes/
```

## Workflow

### 1. Setup Awal
1. Buat raw materials dengan init stock 0
2. Buat products dan tentukan BOM-nya

### 3. Stock Input
1. Buat stock input untuk raw material
2. Stok akan bertambah otomatis
3. Tercatat di stock movements sebagai PURCHASE

### 4. Transaksi Penjualan
1. Buat transaksi baru
2. Pilih produk dan qty
3. Subtotal dihitung otomatis
4. Input discount (opsional)
5. Input paid amount (opsional, auto dari total - discount)
6. Stok raw material berkurang sesuai BOM
7. Tercatat di stock movements sebagai SALE

### 5. Edit/Delete Transaksi
- **Edit**: Stok akan di-adjust sesuai perubahan
- **Delete**: Status berubah VOID, stok dikembalikan
- **Refund**: Status berubah REFUND, stok tetap

### 6. Stock Opname
1. Buat opname dengan qty (+ untuk adjustment, - untuk waste)
2. Stok akan di-adjust
3. Tercatat di stock movements sebagai ADJUSTMENT atau WASTE

## Notes

- Semua tabel menggunakan soft deletes
- Semua perubahan data tercatat dengan created_by dan updated_by
- Stock movements memiliki reference_id dan reference_type untuk tracking
- Aplikasi menggunakan Livewire untuk real-time updates
- Tailwind CSS untuk styling dengan @tailwindcss/vite plugin

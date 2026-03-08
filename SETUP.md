# Setup & Running Guide

## Prerequisites
- PHP 8.2 atau lebih tinggi
- Composer
- Node.js 16+ dan npm
- SQLite (sudah included di PHP)

## Installation Steps

### 1. Install PHP Dependencies
```bash
cd mado-pos
composer install
```

### 2. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Database Setup
```bash
php artisan migrate:fresh --seed
```

Ini akan membuat database SQLite dan seed user dummy:
- Email: `admin@example.com`
- Password: `password`

### 4. Install Node Dependencies
```bash
npm install
```

### 5. Build Assets
```bash
npm run build
```

## Running the Application

### Development Mode

**Terminal 1 - Laravel Server:**
```bash
php artisan serve
```
Server akan berjalan di `http://localhost:8000`

**Terminal 2 - Vite Dev Server:**
```bash
npm run dev
```
Ini akan watch file changes dan hot reload CSS/JS

### Production Mode

```bash
npm run build
php artisan serve
```

## Login

Buka `http://localhost:8000` dan login dengan:
- Email: `admin@example.com`
- Password: `password`

## Troubleshooting

### Database Error
Jika ada error database, jalankan:
```bash
php artisan config:clear
php artisan migrate:fresh --seed
```

### Port Already in Use
Jika port 8000 sudah digunakan:
```bash
php artisan serve --port=8001
```

### Node Version Issue
Jika ada error tentang Node version, pastikan menggunakan Node 16+:
```bash
node --version
```

## First Steps

1. **Login** dengan credentials di atas
2. **Buat Raw Materials** di menu "Raw Materials"
3. **Buat Products** di menu "Products"
4. **Setup BOM** dengan klik "Detail" pada product
5. **Input Stock** di menu "Stock Input"
6. **Buat Transaksi** di menu "Transactions"

## Features Overview

- **Dashboard**: Overview statistik
- **Products**: Kelola produk dan BOM
- **Raw Materials**: Kelola bahan baku
- **Stock Input**: Tambah stok bahan baku
- **Stock Opname**: Adjustment stok
- **Transactions**: Kelola penjualan

Setiap fitur memiliki CRUD lengkap (Create, Read, Update, Delete).

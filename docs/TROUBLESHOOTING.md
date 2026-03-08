# MADO POS - Troubleshooting Guide

## Installation Issues

### Composer Install Fails
**Problem:** `composer install` returns error

**Solutions:**
1. Check PHP version (need 8.2+)
   ```bash
   php --version
   ```

2. Update Composer
   ```bash
   composer self-update
   ```

3. Clear Composer cache
   ```bash
   composer clear-cache
   ```

4. Try with no-dev flag
   ```bash
   composer install --no-dev
   ```

---

### NPM Install Fails
**Problem:** `npm install` returns error

**Solutions:**
1. Check Node version (need 16+)
   ```bash
   node --version
   npm --version
   ```

2. Clear npm cache
   ```bash
   npm cache clean --force
   ```

3. Delete node_modules and package-lock.json
   ```bash
   rm -rf node_modules package-lock.json
   npm install
   ```

4. Try with legacy peer deps
   ```bash
   npm install --legacy-peer-deps
   ```

---

## Database Issues

### Database Connection Error
**Problem:** `SQLSTATE[HY000] [1049] Unknown database`

**Solutions:**
1. Clear config cache
   ```bash
   php artisan config:clear
   ```

2. Check .env file
   ```bash
   cat .env | grep DB_
   ```

3. Ensure SQLite database exists
   ```bash
   touch database/database.sqlite
   ```

4. Run migrations
   ```bash
   php artisan migrate:fresh --seed
   ```

---

### Migration Fails
**Problem:** Migration returns SQL error

**Solutions:**
1. Rollback migrations
   ```bash
   php artisan migrate:rollback
   ```

2. Fresh migration
   ```bash
   php artisan migrate:fresh
   ```

3. Check migration files for syntax errors
   ```bash
   php artisan migrate:status
   ```

4. Seed database
   ```bash
   php artisan db:seed
   ```

---

### Seeder Fails
**Problem:** `php artisan db:seed` returns error

**Solutions:**
1. Check DatabaseSeeder.php for errors
2. Run specific seeder
   ```bash
   php artisan db:seed --class=DatabaseSeeder
   ```

3. Create user manually
   ```bash
   php artisan tinker
   > User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => Hash::make('password')])
   ```

---

## Server Issues

### Port 8000 Already in Use
**Problem:** `Address already in use` when running `php artisan serve`

**Solutions:**
1. Use different port
   ```bash
   php artisan serve --port=8001
   ```

2. Kill process using port 8000
   ```bash
   # macOS/Linux
   lsof -ti:8000 | xargs kill -9
   
   # Windows
   netstat -ano | findstr :8000
   taskkill /PID <PID> /F
   ```

3. Use Valet (if installed)
   ```bash
   valet link
   valet open
   ```

---

### Vite Dev Server Not Working
**Problem:** CSS/JS not loading, `npm run dev` shows errors

**Solutions:**
1. Kill existing Vite process
   ```bash
   # Find and kill Vite process
   ps aux | grep vite
   kill -9 <PID>
   ```

2. Rebuild assets
   ```bash
   npm run build
   ```

3. Clear Vite cache
   ```bash
   rm -rf node_modules/.vite
   npm run dev
   ```

4. Check vite.config.js
   ```bash
   cat vite.config.js
   ```

---

## Authentication Issues

### Can't Login
**Problem:** Login fails with "credentials do not match"

**Solutions:**
1. Check user exists
   ```bash
   php artisan tinker
   > User::where('email', 'admin@example.com')->first()
   ```

2. Reset password
   ```bash
   php artisan tinker
   > $user = User::find(1)
   > $user->password = Hash::make('password')
   > $user->save()
   ```

3. Create new user
   ```bash
   php artisan tinker
   > User::create(['name' => 'Test', 'email' => 'test@example.com', 'password' => Hash::make('password')])
   ```

---

### Session Not Working
**Problem:** Logged in but redirected to login page

**Solutions:**
1. Clear session
   ```bash
   php artisan session:table
   php artisan migrate
   ```

2. Check SESSION_DRIVER in .env
   ```bash
   grep SESSION_DRIVER .env
   ```

3. Clear cache
   ```bash
   php artisan cache:clear
   ```

---

## Livewire Issues

### Component Not Updating
**Problem:** Livewire component doesn't respond to clicks

**Solutions:**
1. Check browser console for errors
   - Open DevTools (F12)
   - Check Console tab

2. Verify Livewire is loaded
   ```bash
   # Check if @livewireScripts is in layout
   grep -r "@livewireScripts" resources/views/
   ```

3. Clear Livewire cache
   ```bash
   php artisan livewire:publish --assets
   ```

4. Check component exists
   ```bash
   php artisan livewire:list
   ```

---

### Modal Not Showing
**Problem:** Modal button clicked but modal doesn't appear

**Solutions:**
1. Check `$showModal` property
   ```php
   // In component
   public $showModal = false;
   ```

2. Verify modal HTML in view
   ```blade
   @if($showModal)
       <!-- Modal content -->
   @endif
   ```

3. Check CSS for modal visibility
   ```bash
   # Check if Tailwind classes are applied
   grep -r "fixed inset-0" resources/views/
   ```

---

### Form Validation Not Working
**Problem:** Form submits without validation

**Solutions:**
1. Check validate() method
   ```php
   $this->validate([
       'name' => 'required|string',
   ]);
   ```

2. Verify error display
   ```blade
   @error('name') <span>{{ $message }}</span> @enderror
   ```

3. Check wire:submit
   ```blade
   <form wire:submit="save">
   ```

---

## Stock Management Issues

### Stock Not Reducing
**Problem:** After transaction, stock doesn't decrease

**Solutions:**
1. Check BOM is configured
   ```bash
   php artisan tinker
   > Product::find(1)->boms
   ```

2. Verify stock reduction logic
   ```bash
   # Check stock movements
   > RawMaterialStockMovement::where('reference_type', 'SALE')->get()
   ```

3. Check raw material stock
   ```bash
   > RawMaterial::find(1)->stock
   ```

---

### Stock Movements Not Recording
**Problem:** Stock movements table is empty

**Solutions:**
1. Check stock_movements table exists
   ```bash
   php artisan tinker
   > DB::table('raw_material_stock_movements')->count()
   ```

2. Verify stock movement creation in component
   ```php
   RawMaterialStockMovement::create([...])
   ```

3. Check for errors in transaction save
   ```bash
   # Check Laravel logs
   tail -f storage/logs/laravel.log
   ```

---

## Performance Issues

### Application Slow
**Problem:** Pages load slowly

**Solutions:**
1. Check database queries
   ```bash
   # Enable query logging in .env
   DB_LOG=true
   ```

2. Use Laravel Debugbar
   ```bash
   composer require barryvdh/laravel-debugbar --dev
   ```

3. Check for N+1 queries
   ```php
   // Use eager loading
   Product::with('boms.rawMaterial')->get()
   ```

4. Clear cache
   ```bash
   php artisan cache:clear
   php artisan config:clear
   ```

---

### High Memory Usage
**Problem:** Application uses too much memory

**Solutions:**
1. Reduce pagination size
   ```php
   ->paginate(10) // instead of 50
   ```

2. Use chunking for large operations
   ```php
   Product::chunk(100, function ($products) {
       // Process products
   });
   ```

3. Clear logs
   ```bash
   rm storage/logs/laravel.log
   ```

---

## CSS/Styling Issues

### Tailwind CSS Not Applied
**Problem:** Styles not showing, page looks unstyled

**Solutions:**
1. Build CSS
   ```bash
   npm run build
   ```

2. Check CSS import in app.css
   ```bash
   grep "@import 'tailwindcss'" resources/css/app.css
   ```

3. Verify Vite is running
   ```bash
   # Terminal 2 should show Vite running
   npm run dev
   ```

4. Clear browser cache
   - Hard refresh: Ctrl+Shift+R (Windows) or Cmd+Shift+R (Mac)

---

### Responsive Design Not Working
**Problem:** Layout breaks on mobile

**Solutions:**
1. Check viewport meta tag
   ```blade
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   ```

2. Test responsive design
   - Open DevTools (F12)
   - Click device toggle (Ctrl+Shift+M)

3. Check Tailwind breakpoints
   ```bash
   grep -r "md:" resources/views/
   ```

---

## File Upload Issues

### Files Not Saving
**Problem:** Uploaded files disappear

**Solutions:**
1. Check storage permissions
   ```bash
   chmod -R 775 storage/
   chmod -R 775 bootstrap/cache/
   ```

2. Check FILESYSTEM_DISK in .env
   ```bash
   grep FILESYSTEM_DISK .env
   ```

3. Create storage link
   ```bash
   php artisan storage:link
   ```

---

## Email Issues

### Emails Not Sending
**Problem:** Emails not received

**Solutions:**
1. Check MAIL_MAILER in .env
   ```bash
   grep MAIL_MAILER .env
   ```

2. For development, use log driver
   ```bash
   MAIL_MAILER=log
   ```

3. Check mail logs
   ```bash
   tail -f storage/logs/laravel.log | grep -i mail
   ```

---

## General Debugging

### Enable Debug Mode
```bash
# In .env
APP_DEBUG=true
```

### Check Logs
```bash
# Real-time log viewing
tail -f storage/logs/laravel.log

# Search for errors
grep -i error storage/logs/laravel.log
```

### Use Tinker
```bash
php artisan tinker

# Check data
> Product::all()
> RawMaterial::find(1)
> Sale::latest()->first()

# Test relationships
> Product::find(1)->boms
> RawMaterial::find(1)->stockMovements
```

### Database Inspection
```bash
php artisan tinker

# Check table structure
> DB::select('PRAGMA table_info(products)')

# Count records
> DB::table('products')->count()

# Check specific record
> DB::table('products')->where('id', 1)->first()
```

---

## Getting Help

1. **Check Documentation**
   - README.md - Overview
   - SETUP.md - Installation
   - QUICK_REFERENCE.md - Quick guide
   - DATABASE_SCHEMA.md - Database structure

2. **Check Logs**
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Use Tinker**
   ```bash
   php artisan tinker
   ```

4. **Check Laravel Docs**
   - https://laravel.com/docs
   - https://livewire.laravel.com

5. **Check Tailwind Docs**
   - https://tailwindcss.com/docs

---

## Common Error Messages

### "Class not found"
- Check namespace in file
- Verify file is in correct directory
- Run `composer dump-autoload`

### "Method not found"
- Check method name spelling
- Verify method exists in model/component
- Check method visibility (public/private)

### "Undefined variable"
- Check variable is defined in component
- Verify variable is passed to view
- Check variable name spelling

### "SQLSTATE error"
- Check database connection
- Verify table exists
- Check column names
- Run migrations

### "Route not found"
- Check route is defined in routes/web.php
- Verify route name is correct
- Check route parameters
- Run `php artisan route:list`

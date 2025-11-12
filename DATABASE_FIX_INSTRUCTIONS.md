# Database Fix Instructions

## Issue
The `business_link` column is missing from the `items` table in your database.

**Error**: `SQLSTATE[42S22]: Column not found: 1054 Unknown column 'business_link' in 'where clause'`

## Solution
Run the migration to add the missing column:

```bash
php artisan migrate
```

This will run the migration:
`2025_11_12_014849_ensure_business_link_exists_in_items_table.php`

The migration will:
- Check if the `business_link` column exists in the `items` table
- Add it if it's missing
- Skip if it already exists (safe to run multiple times)

## After Running Migration
Once the migration completes successfully, your subdomain routing will work:
- `airvend-res.localhost:3000` → Shows vendor menu with hero, logo, and items

## Verification
You can verify the column was added by running:
```bash
php artisan tinker
```

Then:
```php
Schema::hasColumn('items', 'business_link')
// Should return: true
```

Or check directly in your database:
```sql
DESCRIBE items;
```

You should see `business_link` listed as a `varchar` column.

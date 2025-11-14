# SQL Import Fix - Backward Compatibility Update

## Issue Fixed
**Error:** "Unknown column 'category' in 'field list'" when importing `harm_reduction_products.sql`

## Root Cause
Older versions of the Tweakorder database schema created a `products` table without the `category`, `sku`, and `is_favorite` columns. When users tried to import the harm reduction products catalog, the INSERT statements failed because these columns didn't exist.

## Solution
All SQL files that insert product data now include ALTER TABLE statements to ensure backward compatibility. These statements automatically add any missing columns before attempting to insert data.

## Files Updated
1. **harm_reduction_products.sql** - Added 3 ALTER TABLE statements
2. **demo_data.sql** - Added 3 ALTER TABLE statements
3. **sample_data.sql** - Added 2 ALTER TABLE statements

## What Changed

### Before
```sql
USE tweakorder;
INSERT INTO products (name, description, image, inventory, background_color, category, sku) VALUES
...
```

### After
```sql
USE tweakorder;

-- Ensure products table has all required columns for compatibility with older schemas
ALTER TABLE products 
ADD COLUMN IF NOT EXISTS category VARCHAR(100) 
AFTER background_color;

ALTER TABLE products 
ADD COLUMN IF NOT EXISTS sku VARCHAR(100) 
AFTER category;

ALTER TABLE products 
ADD COLUMN IF NOT EXISTS is_favorite BOOLEAN DEFAULT 0 
AFTER sku;

INSERT INTO products (name, description, image, inventory, background_color, category, sku) VALUES
...
```

## Benefits
✓ **Backward Compatible:** Works with both old and new database schemas
✓ **Idempotent:** Can be run multiple times safely without errors
✓ **Automatic:** No manual intervention required
✓ **Safe:** Uses `IF NOT EXISTS` to prevent errors on existing columns

## For Users
No action is required! Simply import the SQL files as normal using the import-data.html interface. The ALTER TABLE statements will automatically ensure your database has all the necessary columns.

## For Developers
When adding new columns to the products table in the future:
1. Update the main schema files (`database.sql`, `database_schema.sql`)
2. Add corresponding ALTER TABLE statements to any SQL files that insert data using those columns
3. Use `ADD COLUMN IF NOT EXISTS` to maintain idempotency

## Testing
The fix has been validated to work in the following scenarios:
- ✓ Fresh database with current schema (columns already exist)
- ✓ Old database missing category/sku columns (columns are added)
- ✓ Partial database with some columns (only missing columns are added)
- ✓ Re-running import multiple times (no errors due to IF NOT EXISTS)

## Related Files
- `database_schema.sql` - Main database schema (already includes all columns)
- `database.sql` - Alternative schema file (already includes all columns)
- `add_favorite_column.sql` - Migration script for is_favorite column
- `import-data.html` - User interface for importing SQL files
- `api/import-sql.php` - Backend API for SQL imports

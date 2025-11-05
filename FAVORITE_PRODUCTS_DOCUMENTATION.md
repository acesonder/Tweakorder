# Favorite Products Feature Documentation

## Overview
The Favorite Products feature allows administrators and staff to mark certain products as favorites. This makes it easier for all users (admins, staff, and clients) to quickly find and select commonly used products when creating orders.

## Features

### 1. Mark Products as Favorite
- When adding a new product, check the "Mark as Favorite" checkbox
- Products marked as favorite will be easily accessible during order creation

### 2. Filter Products by Favorites
- In the order creation page, two filter buttons are available:
  - **Show All**: Displays all available products
  - **★ Show Favorites**: Displays only products marked as favorite
- Favorite products are indicated with a ★ symbol next to their name

## Database Schema

### Products Table
A new `is_favorite` column has been added to the products table:

```sql
ALTER TABLE products 
ADD COLUMN is_favorite BOOLEAN DEFAULT 0;
```

- Type: BOOLEAN (TINYINT in MySQL)
- Default: 0 (not a favorite)
- Values: 0 = not favorite, 1 = favorite

## API Changes

### GET /api/products.php
Returns all products including the `is_favorite` field:

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Naloxone Kit",
      "description": "Emergency naloxone kit",
      "image": "assets/uploads/naloxone.jpg",
      "inventory": 50,
      "background_color": "gradient-1",
      "is_favorite": 1,
      "created_at": "2024-01-15 10:30:00"
    }
  ]
}
```

### POST /api/products.php
Accepts `is_favorite` parameter when creating a product:

```
POST /api/products.php
Content-Type: multipart/form-data

name=Naloxone Kit
description=Emergency naloxone kit
inventory=50
background_color=gradient-1
is_favorite=1
```

### PUT /api/products.php
Accepts `is_favorite` parameter when updating a product:

```
PUT /api/products.php
Content-Type: application/x-www-form-urlencoded

id=1
name=Naloxone Kit
description=Emergency naloxone kit
inventory=50
background_color=gradient-1
is_favorite=1
```

## User Interface

### Add Product Page (add-product.html)
- A checkbox labeled "Mark as Favorite" appears between the Inventory field and Background Color selector
- Check this box to mark the product as a favorite
- The checkbox value is submitted with the form data

### Create Order Page (create-order.html)
- Two filter buttons appear at the top of Step 1:
  - "Show All" (default active)
  - "★ Show Favorites"
- Click "★ Show Favorites" to see only favorite products
- Favorite products display with a ★ symbol next to their name
- The filter persists while selecting products
- Selected product counts are maintained when switching between filters

## Migration for Existing Databases

If you have an existing database, run the migration script:

```bash
mysql -u root -p tweakorder < add_favorite_column.sql
```

Or manually execute:

```sql
USE tweakorder;
ALTER TABLE products 
ADD COLUMN IF NOT EXISTS is_favorite BOOLEAN DEFAULT 0 
AFTER sku;
```

## Usage Examples

### Example 1: Create a Favorite Product
1. Navigate to "Add Product" page
2. Fill in product details (name, description, etc.)
3. Check the "Mark as Favorite" checkbox
4. Click "Add Product"
5. The product is now marked as a favorite

### Example 2: Create an Order with Favorites
1. Navigate to "Create Order" page
2. Click "★ Show Favorites" button
3. Only favorite products are displayed with ★ symbols
4. Select desired products by clicking on them
5. Continue with the normal order creation process

### Example 3: Update a Product to be a Favorite
Products can be updated via the API to change their favorite status:

```javascript
// Update product via API
const formData = new URLSearchParams();
formData.append('id', 1);
formData.append('name', 'Updated Product Name');
formData.append('is_favorite', 1); // Mark as favorite

fetch('api/products.php', {
    method: 'PUT',
    body: formData.toString(),
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
    }
});
```

## Best Practices

1. **Strategic Favorites**: Mark only frequently ordered products as favorites to keep the favorites list manageable
2. **Regular Review**: Periodically review and update favorite status based on ordering patterns
3. **User Training**: Educate users about the favorites feature to improve order creation efficiency
4. **Clear Naming**: Use clear, descriptive product names so favorites are easy to identify

## Technical Details

### JavaScript Implementation
The filtering logic is implemented in `create-order.html`:

```javascript
// Filter products based on showOnlyFavorites flag
let filteredProducts = showOnlyFavorites 
    ? products.filter(p => p.is_favorite === 1) 
    : products;
```

### Button State Management
The active button state is toggled using CSS classes:

```javascript
function showFavoriteProducts() {
    showOnlyFavorites = true;
    displayProducts();
    document.getElementById('showFavoritesBtn').classList.add('btn-secondary');
    document.getElementById('showFavoritesBtn').classList.remove('btn');
    document.getElementById('showAllBtn').classList.remove('btn-secondary');
    document.getElementById('showAllBtn').classList.add('btn');
}
```

## Troubleshooting

### Favorite Products Not Showing
- Ensure the database migration has been run
- Verify products have `is_favorite` set to 1 in the database
- Check browser console for JavaScript errors
- Clear browser cache and reload

### Checkbox Not Appearing
- Verify `add-product.html` has been updated with the checkbox
- Check if JavaScript is enabled in the browser
- Inspect HTML to ensure the checkbox element exists

### Filter Not Working
- Check browser console for JavaScript errors
- Verify `create-order.html` has the updated JavaScript code
- Ensure products are being loaded from the API correctly

## Future Enhancements

Potential improvements for the favorite products feature:

1. **Bulk Favorite Management**: Admin page to manage favorites for multiple products at once
2. **User-Specific Favorites**: Allow different users to maintain their own favorite lists
3. **Favorite Categories**: Group favorites into categories (e.g., "Emergency Supplies", "Daily Use")
4. **Usage Analytics**: Track which favorites are actually used most often
5. **Quick Add**: One-click order creation for favorite product combinations
6. **Favorite Sort Order**: Custom sorting of favorite products

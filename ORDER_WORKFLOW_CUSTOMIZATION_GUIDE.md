# Order Workflow Customization Guide

## Overview

The Tweak Order Online system now includes comprehensive order workflow customization features that allow administrators and staff members to personalize their order creation experience. This guide explains how to use these features.

## Features

### 1. Product Image Management

#### Upload Product Images
- Navigate to **Add Product** or **Edit Product** pages
- Choose between two image options:
  - **Upload New Image**: Upload an image file from your computer (JPG, PNG, GIF, WebP, SVG supported)
  - **Use Local Image Path**: Reference an image already stored on the server

#### Harm Reduction Product Images
All harm reduction product images are stored in `images/harmreduction/` directory:
- Images are in SVG format with transparent backgrounds
- White color scheme for easy visibility
- Named after the product (e.g., `naloxone.svg`, `syringe.svg`, etc.)

Example local image paths:
- `images/harmreduction/naloxone.svg`
- `images/harmreduction/syringe.svg`
- `images/harmreduction/fentanyl-test.svg`

### 2. Order Workflow Customization

#### Accessing Workflow Settings
1. Navigate to **Order Workflow Settings** from the main menu
2. Or click the ⚙️ icon on the create order page

#### Available Workflow Templates

**Standard Order Workflow** (Default)
- Complete workflow with all steps
- Includes product selection, client selection, location, scheduling, notes, status, and confirmation
- Best for comprehensive order processing

**Quick Order Workflow**
- Simplified 3-step workflow for experienced staff
- Product selection with favorites, client selection, and status completion
- Optimized for speed and efficiency

**Detailed Training Workflow**
- Comprehensive workflow with guidance and help text
- Includes all steps with detailed explanations
- Perfect for new staff members or training purposes

### 3. Product Display Options

#### Display Modes
- **Grid View**: Products displayed in a grid layout with images
- **List View**: Products in a vertical list format
- **Compact View**: Smaller product cards for more items per screen
- **Card View**: Larger cards with more details
- **Favorites First**: Automatically shows favorite products at the top

#### Sort Options
- **Name (A-Z)**: Alphabetical order
- **Recently Used**: Most recently ordered products first
- **Most Popular**: Most frequently ordered products first
- **Category**: Grouped by product category
- **Custom Order**: User-defined ordering (drag and drop)

#### Display Settings
- **Show Product Images**: Toggle product image visibility
- **Show Product Descriptions**: Toggle description text below products

### 4. Workflow Behavior Options

#### Quick Order Mode
- Enable single-page order creation
- All information on one screen
- Faster for experienced staff

#### Auto-Advance Steps
- Automatically move to the next step after completing required fields
- Reduces clicks and speeds up workflow

#### Default Order Status
- Set your preferred default status when creating orders
- Options: Processing, Fulfilled, Waiting for Supplies, Ready for Pickup

### 5. Product Customization Features

#### Pin Products
- Pin frequently used products to the top of the list
- Indicated by a 📌 icon
- Remains at the top regardless of sort order

#### Hide Products
- Temporarily hide products you don't use
- They remain in the system but won't appear in your order form

#### Custom Labels
- Add personal notes or custom labels to products
- Helps identify products quickly in your workflow

### 6. Quick Order Templates

#### Save Order Templates
- Save common orders as templates
- Include client, products, and default settings
- One-click order creation for repeat orders

#### Template Features
- Track usage count
- See when last used
- Easily delete unused templates

## Using the Customizable Order Form

### Standard Workflow

1. **Access the Custom Order Form**
   - Navigate to `create-order-custom.html`
   - Your saved preferences will automatically load

2. **Product Selection**
   - Search products using the search bar
   - Filter by category
   - Toggle favorites view
   - Click products to add them to your order
   - Products show a quantity badge when selected

3. **Client Selection**
   - Search for existing clients
   - Or add a new client on the fly
   - Recently used clients appear first

4. **Order Completion**
   - Review your order summary
   - Set the order status
   - Add any special notes or instructions
   - Create the order

### Quick Order Mode

1. **Enable Quick Order Mode**
   - Go to Order Workflow Settings
   - Enable "Quick Order Mode"
   - Save settings

2. **Use Quick Order**
   - All options appear on one screen
   - Select products and client simultaneously
   - Complete order with one click

## Staff-Specific Customization

Each staff member has their own customization profile:
- Settings are saved per user
- Does not affect other staff members
- Can be reset to defaults at any time

## Client Order Preferences

Clients can also have customized experiences:
- Preferred pickup method
- Favorite products
- Preferred location
- Display mode preferences

These preferences are automatically applied when creating orders for that client.

## Database Schema

### Main Tables

**user_workflow_preferences**
- Stores staff member workflow preferences
- Template selection, display modes, sort orders
- Workflow behavior options

**order_workflow_templates**
- Defines available workflow templates
- Step configurations
- Global template settings

**order_workflow_steps**
- Individual steps in each template
- Step order, visibility, settings
- Required vs optional steps

**user_product_display**
- User-specific product customization
- Display order, pinned status, hidden status
- Custom labels and notes

**order_quick_templates**
- Saved order templates for quick reuse
- Includes products, client, status
- Usage tracking

**client_order_preferences**
- Client-specific order preferences
- Favorite products, preferred locations
- Display preferences

## API Endpoints

### GET Requests

**Get User Preferences**
```
GET api/order-workflow.php?action=get_preferences
```

**Get Available Templates**
```
GET api/order-workflow.php?action=get_templates
```

**Get Template Steps**
```
GET api/order-workflow.php?action=get_template_steps&template_id=1
```

**Get Product Display Customization**
```
GET api/order-workflow.php?action=get_product_display
```

**Get Quick Templates**
```
GET api/order-workflow.php?action=get_quick_templates
```

### POST Requests

**Update Preferences**
```
POST api/order-workflow.php
action=update_preferences
template_id=1
product_display_mode=grid
product_sort_order=name
show_product_images=1
show_product_descriptions=1
quick_order_enabled=0
auto_advance_steps=0
default_order_status=processing
```

**Update Product Display**
```
POST api/order-workflow.php
action=update_product_display
product_id=1
display_order=0
is_pinned=1
is_hidden=0
custom_label=My Custom Label
```

**Save Quick Template**
```
POST api/order-workflow.php
action=save_quick_template
template_name=Weekly Supply Order
description=Standard weekly harm reduction supplies
client_id=5
product_items=[{"product_id":1,"quantity":10},{"product_id":2,"quantity":5}]
default_status=processing
```

## Installation

1. **Run SQL Schema**
   ```sql
   source order_workflow_customization.sql
   ```

2. **Verify Tables Created**
   - order_workflow_templates
   - order_workflow_steps
   - user_workflow_preferences
   - user_product_display
   - order_quick_templates
   - client_order_preferences
   - order_custom_fields

3. **Default Data**
   - Three workflow templates are created automatically
   - Default preferences are set for existing users
   - Default workflow steps are configured

## Best Practices

### For Administrators
- Review and customize workflow templates for your organization
- Create specialized templates for different staff roles
- Monitor which templates are most popular
- Regularly review and clean up unused quick templates

### For Staff Members
- Start with the default workflow and adjust as needed
- Use Quick Order Mode once you're comfortable with the system
- Pin your most frequently used products
- Create quick templates for repeat orders
- Regularly update your preferences as your workflow evolves

### For Training
- Start new staff with the "Detailed Training Workflow"
- Gradually transition to "Standard Order Workflow"
- Allow experienced staff to use "Quick Order Workflow"
- Encourage customization once staff are comfortable

## Troubleshooting

**Preferences Not Saving**
- Check that you're logged in
- Verify database connection
- Check browser console for errors

**Products Not Displaying**
- Verify products exist in the database
- Check if products are hidden in your settings
- Try resetting to default preferences

**Quick Templates Not Working**
- Ensure template has valid product IDs
- Check that client still exists
- Verify JSON format of product_items

**Workflow Steps Not Appearing**
- Check template is selected in preferences
- Verify workflow steps exist for template
- Try switching to default template

## Future Enhancements

Potential additions to the workflow system:
- Drag-and-drop product ordering
- Custom workflow step creation
- Advanced filtering options
- Batch order templates
- Analytics on workflow efficiency
- Mobile-optimized layouts
- Voice-activated order creation
- Integration with barcode scanners

## Support

For issues or questions:
1. Check this documentation
2. Review the API documentation
3. Contact system administrator
4. Submit a support ticket

## Security Considerations

- All API endpoints use prepared statements to prevent SQL injection
- User IDs are validated from session data
- File uploads are restricted to image types only
- Local image paths are sanitized
- User preferences are isolated per user
- No sensitive data is logged

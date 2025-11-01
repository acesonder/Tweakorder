# Harm Reduction Products Implementation

## Overview
This implementation adds a comprehensive harm reduction product catalog to the Tweakorder system, designed for outreach programs serving people experiencing addiction, homelessness, and related challenges.

## What Was Added

### 1. Custom SVG Icons (25 unique icons)
Created custom SVG icons for all harm reduction product categories:
- **Location**: `/assets/uploads/harm-reduction-icons/`
- **Icons Created**: 25 unique SVG files with distinct colors and designs
- **Categories Covered**: 
  - Naloxone & overdose prevention
  - Injection supplies
  - Safer sex products
  - Testing supplies
  - Smoking/inhalation supplies
  - Wound care & first aid
  - Personal protective equipment
  - Vitamins & nutrition
  - Disposal & safety
  - Educational materials

### 2. Comprehensive Product Database (71 Products)
Created SQL file with 71 harm reduction products: `harm_reduction_products.sql`

All fields populated for each product:
- **Name**: Clear, professional product names
- **Description**: Detailed descriptions explaining purpose and harm reduction benefits
- **Image**: Custom SVG icon for visual identification
- **Inventory**: Realistic stock levels based on typical demand patterns
- **Background Color**: Unique gradient color (gradient-1 through gradient-15) for visual categorization
- **Category**: All marked as "Harm Reduction" for filtering
- **SKU**: Systematic SKU codes (HR-XXX-###) for inventory management

### 3. Product Categories Included

#### Naloxone & Overdose Prevention (3 products)
- Naloxone nasal spray kits
- Injectable naloxone kits
- Overdose response training cards

#### Injection Supplies (12 products)
- Sterile syringes (multiple sizes)
- Sterile needles (multiple gauges)
- Cookers/spoons
- Cotton filters
- Alcohol prep pads
- Tourniquets
- Sharps containers (multiple sizes)
- Sterile water vials

#### Safer Sex & Sexual Health (6 products)
- Condoms (multiple types and sizes)
- Lubricant packets
- Dental dams

#### Testing Supplies (5 products)
- Fentanyl test strips
- Drug checking test strips
- Xylazine test strips
- HIV self-test kits
- Hepatitis C test kits

#### Smoking/Inhalation Supplies (6 products)
- Complete safe smoking kits
- Glass stems/pipes
- Metal and brass screens
- Rubber mouthpieces
- Push sticks

#### Wound Care & First Aid (7 products)
- Basic and advanced wound care kits
- Antibiotic ointment
- Sterile gauze
- Medical tape
- Adhesive bandages
- Burn gel

#### Personal Protective Equipment (6 products)
- Nitrile gloves (multiple sizes)
- Disposable face masks
- Hand sanitizer (multiple sizes)

#### Vitamin & Nutrition Support (3 products)
- Vitamin C tablets
- Multivitamin packets
- Electrolyte powder

#### Disposal & Safety (2 products)
- Biohazard disposal bags
- Needle clippers

#### Educational Materials (7 products)
- Safer injection guides
- Safer smoking guides
- Safer sex guides
- Overdose prevention cards
- Local resources cards
- Vein care guides
- Abscess prevention cards

#### Miscellaneous Supplies (8 products)
- Lip balm
- Tissues
- Menstrual products
- Pregnancy tests
- Emergency contraception
- Sunscreen
- Insect repellent

#### Kits & Bundles (6 products)
- Starter harm reduction kit
- Injection supplies bundle
- Safer sex bundle
- Smoking supplies bundle
- Personal care bundle
- Overdose response kit

## How to Install

### Option 1: Using MySQL Command Line
```bash
mysql -u root -p tweakorder < harm_reduction_products.sql
```

### Option 2: Using phpMyAdmin or Similar
1. Open phpMyAdmin
2. Select the `tweakorder` database
3. Go to "Import" tab
4. Choose `harm_reduction_products.sql`
5. Click "Go"

## Features

### All Fields Populated
Every product has complete information:
- ✅ Professional product name
- ✅ Detailed description with harm reduction context
- ✅ Custom icon/image
- ✅ Realistic inventory count
- ✅ Color-coded gradient background
- ✅ Category classification
- ✅ Unique SKU code

### Visual Design
- 25 unique custom SVG icons with distinct colors
- Icons designed for clarity and easy recognition
- Professional gradient backgrounds (15 color options)
- Consistent visual style across all products

### Inventory Management
- Stock levels reflect typical demand patterns
- High-demand items (educational materials, disposables): 400-600 units
- Standard supplies (syringes, condoms): 200-500 units
- Specialized items (naloxone, test kits): 100-300 units
- Bundled kits: 100-250 units

### SKU System
Systematic SKU codes for easy inventory tracking:
- `HR-NAL-###`: Naloxone products
- `HR-SYR-###`: Syringes
- `HR-NDL-###`: Needles
- `HR-CON-###`: Condoms
- `HR-TST-###`: Testing supplies
- `HR-SMK-###`: Smoking supplies
- `HR-WND-###`: Wound care
- `HR-PPE-###`: Personal protective equipment
- `HR-VIT-###`: Vitamins/nutrition
- `HR-DIS-###`: Disposal items
- `HR-EDU-###`: Educational materials
- `HR-MSC-###`: Miscellaneous
- `HR-KIT-###`: Bundled kits

## Testing

After installation, verify the products:

1. **Check product count**:
```sql
SELECT COUNT(*) FROM products WHERE category = 'Harm Reduction';
```
Should return: 71 products

2. **View sample products**:
```sql
SELECT name, sku, inventory FROM products WHERE category = 'Harm Reduction' LIMIT 10;
```

3. **Test in UI**:
- Navigate to the product add/view pages
- Products should display with custom icons
- Gradient backgrounds should be visible
- All descriptions should be complete

## Benefits

### For Outreach Workers
- Complete catalog ready to use
- Professional presentation builds trust
- Educational descriptions help explain products to clients
- Easy inventory tracking with SKUs

### For Clients
- Clear product information
- Visual icons help with product recognition
- Comprehensive descriptions explain benefits
- Complete selection addresses multiple needs

### For Program Management
- Organized by category for reporting
- Realistic inventory levels for planning
- SKU system for tracking distribution
- Flexible bundling options

## Customization

### Adjusting Inventory
Edit the SQL file and modify inventory values before importing:
```sql
-- Example: Change naloxone kit inventory to 150
('Naloxone Nasal Spray Kit', '...', '...', 150, '...', '...', 'HR-NAL-001'),
```

### Adding More Products
Follow the established pattern:
```sql
INSERT INTO products (name, description, image, inventory, background_color, category, sku) VALUES
('New Product', 'Description here', 'harm-reduction-icons/icon.svg', 100, 'gradient-1', 'Harm Reduction', 'HR-XXX-###');
```

## Future Enhancements
- Track product expiration dates
- Add supplier information
- Create product usage statistics
- Implement automated reordering
- Add multi-language descriptions
- Create client education links per product

## Support
Products are based on evidence-based harm reduction practices and align with public health guidelines. Descriptions emphasize safety and health benefits to support informed decision-making.

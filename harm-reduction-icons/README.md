# Harm Reduction Product Icons

This directory contains SVG icons for harm reduction products used in the Tweakorder system.

## Icon List

The following icons are referenced in `harm_reduction_products.sql`:

- naloxone.svg - Naloxone/overdose prevention supplies
- syringe.svg - Syringes and needles
- cooker.svg - Cookers/spoons for injection preparation
- cotton-filters.svg - Cotton filters
- alcohol-swabs.svg - Alcohol prep pads
- tourniquet.svg - Tourniquets
- sharps-container.svg - Sharps disposal containers
- water-bottle.svg - Sterile water vials
- condoms.svg - Condoms and safer sex supplies
- lubricant.svg - Personal lubricant
- fentanyl-test.svg - Fentanyl test strips
- test-strips.svg - Drug checking test strips
- safe-smoke-kit.svg - Safer smoking supplies
- mouthpiece.svg - Pipe mouthpieces
- wound-care.svg - Wound care supplies
- bandages.svg - Bandages and first aid
- gauze.svg - Gauze pads
- first-aid-kit.svg - First aid kits
- gloves.svg - Nitrile gloves
- face-mask.svg - Disposable face masks
- hand-sanitizer.svg - Hand sanitizer
- vitamin-c.svg - Vitamins and nutrition
- biohazard-bag.svg - Biohazard disposal bags
- safer-use-info.svg - Educational materials
- resource-card.svg - Resource cards and information

## Icon Specifications

- Format: SVG (Scalable Vector Graphics)
- Size: 512x512 pixels viewBox
- Color: Use the gradient colors from the database or neutral colors
- Style: Simple, clear, professional icons suitable for medical/health context

## Usage

Icons are referenced in the database as:
```
'harm-reduction-icons/[icon-name].svg'
```

These are displayed in product listings, order forms, and inventory management.

## Creating Icons

If you need to create custom icons:

1. Use a vector graphics editor (Inkscape, Adobe Illustrator, Figma)
2. Design icons at 512x512 pixels
3. Keep designs simple and recognizable
4. Export as optimized SVG
5. Test in the product listing pages

## Placeholder Icons

Currently, this directory contains placeholder SVG files. You can replace them with custom icons that match your organization's branding.

For accessibility:
- Include `<title>` tags in SVG for screen readers
- Use appropriate color contrast
- Ensure icons are recognizable at small sizes (32x32 pixels)

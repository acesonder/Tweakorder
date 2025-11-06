#!/bin/bash
# Generate placeholder SVG icons for harm reduction products

ICON_DIR="harm-reduction-icons"

# Create directory if it doesn't exist
mkdir -p "$ICON_DIR"

# Array of icon names needed
icons=(
    "naloxone"
    "syringe"
    "cooker"
    "cotton-filters"
    "alcohol-swabs"
    "tourniquet"
    "sharps-container"
    "water-bottle"
    "condoms"
    "lubricant"
    "fentanyl-test"
    "test-strips"
    "safe-smoke-kit"
    "mouthpiece"
    "wound-care"
    "bandages"
    "gauze"
    "first-aid-kit"
    "gloves"
    "face-mask"
    "hand-sanitizer"
    "vitamin-c"
    "biohazard-bag"
    "safer-use-info"
    "resource-card"
)

# Function to create a simple placeholder SVG
create_icon() {
    local name=$1
    local file="$ICON_DIR/${name}.svg"
    
    # Create a simple SVG with the icon name
    cat > "$file" << EOF
<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="512" height="512">
  <title>${name}</title>
  <defs>
    <linearGradient id="grad-${name}" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" style="stop-color:#667eea;stop-opacity:1" />
      <stop offset="100%" style="stop-color:#764ba2;stop-opacity:1" />
    </linearGradient>
  </defs>
  <rect width="512" height="512" rx="50" fill="url(#grad-${name})" opacity="0.1"/>
  <circle cx="256" cy="200" r="80" fill="url(#grad-${name})" opacity="0.3"/>
  <rect x="156" y="280" width="200" height="150" rx="20" fill="url(#grad-${name})" opacity="0.3"/>
  <text x="256" y="480" font-family="Arial, sans-serif" font-size="24" fill="#667eea" text-anchor="middle">${name}</text>
</svg>
EOF
    
    echo "Created: $file"
}

# Create all icons
for icon in "${icons[@]}"; do
    create_icon "$icon"
done

echo ""
echo "✓ Generated ${#icons[@]} placeholder SVG icons in $ICON_DIR/"
echo "  These are simple placeholders - replace with custom icons as needed"

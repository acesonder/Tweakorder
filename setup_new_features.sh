#!/bin/bash

# Setup script for new mobile and case management features
# This script initializes the database with new tables and templates

set -e

echo "========================================="
echo "Tweakorder - New Features Setup"
echo "========================================="
echo ""

# Database credentials
DB_USER="root"
DB_PASS=""
DB_NAME="tweakorder"

# Color codes
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Function to print status
print_status() {
    echo -e "${BLUE}>>>${NC} $1"
}

print_success() {
    echo -e "${GREEN}✓${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

# Check if MySQL is running
print_status "Checking MySQL connection..."
if ! mysql -u "$DB_USER" -p"$DB_PASS" -e "USE $DB_NAME;" 2>/dev/null; then
    print_error "Cannot connect to MySQL database '$DB_NAME'"
    echo "Please ensure:"
    echo "  1. MySQL service is running"
    echo "  2. Database '$DB_NAME' exists"
    echo "  3. Credentials in config/database.php are correct"
    exit 1
fi
print_success "MySQL connection successful"

# Update database schema
print_status "Updating database schema with new tables..."
if mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < database.sql 2>/dev/null; then
    print_success "Database schema updated"
else
    print_error "Failed to update database schema"
    exit 1
fi

# Load case templates
print_status "Loading 50 case templates..."
if mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < case_templates_data.sql 2>/dev/null; then
    print_success "Case templates loaded"
else
    print_error "Failed to load case templates"
    exit 1
fi

# Verify tables
print_status "Verifying new tables..."

tables=("error_logs" "case_templates" "case_notes" "worker_preferences")
all_tables_exist=true

for table in "${tables[@]}"; do
    if mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "DESCRIBE $table;" >/dev/null 2>&1; then
        print_success "Table '$table' exists"
    else
        print_error "Table '$table' not found"
        all_tables_exist=false
    fi
done

if [ "$all_tables_exist" = false ]; then
    print_error "Some tables are missing. Please check the errors above."
    exit 1
fi

# Count templates
template_count=$(mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -sN -e "SELECT COUNT(*) FROM case_templates;")
print_success "Found $template_count case templates"

# Verify clients table updates
print_status "Verifying clients table updates..."
if mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "DESCRIBE clients phone;" >/dev/null 2>&1; then
    print_success "Clients table has contact fields"
else
    print_error "Clients table missing contact fields"
fi

echo ""
echo "========================================="
echo -e "${GREEN}Setup Complete!${NC}"
echo "========================================="
echo ""
echo "New features available:"
echo "  📱 Mobile Order System (mobile-order.html)"
echo "  🎨 5 Alternative UI Styles"
echo "  📋 Case Management (case-management.html)"
echo "  ⚠️  Error Logging (error-logs.html)"
echo ""
echo "Template Categories:"
echo "  - Addiction Support (15 templates)"
echo "  - Homelessness Services (15 templates)"
echo "  - Mental Health (20 templates)"
echo ""
echo "Access the main dashboard at: http://localhost:8000/index.html"
echo ""
echo "For detailed documentation, see: MOBILE_FEATURES_GUIDE.md"
echo ""

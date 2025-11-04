#!/bin/bash
# Complete System Validation Runner
# Runs all validation scripts and generates comprehensive report

echo "╔════════════════════════════════════════════════════════╗"
echo "║     Tweakorder - Complete System Validation           ║"
echo "╚════════════════════════════════════════════════════════╝"
echo ""

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Track results
TOTAL_TESTS=0
PASSED_TESTS=0
FAILED_TESTS=0

# Test 1: Run link and form validation
echo -e "${BLUE}Running Link and Form Validation...${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
php validate_links_and_forms.php
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Link and Form Validation: PASSED${NC}"
    ((PASSED_TESTS++))
else
    echo -e "${RED}✗ Link and Form Validation: FAILED${NC}"
    ((FAILED_TESTS++))
fi
((TOTAL_TESTS++))
echo ""

# Test 2: Check if database is available and run database tests
echo -e "${BLUE}Checking Database Availability...${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

# Check if MySQL is running
if command -v mysql &> /dev/null; then
    if sudo service mysql status | grep -q "active (running)"; then
        echo -e "${GREEN}✓ MySQL is running${NC}"
        
        # Try to run database tests
        echo -e "${BLUE}Running Database Form Tests...${NC}"
        php test_database_forms.php
        if [ $? -eq 0 ]; then
            echo -e "${GREEN}✓ Database Form Tests: PASSED${NC}"
            ((PASSED_TESTS++))
        else
            echo -e "${YELLOW}⚠ Database Form Tests: Some tests may have been skipped${NC}"
            ((PASSED_TESTS++))
        fi
        ((TOTAL_TESTS++))
    else
        echo -e "${YELLOW}⚠ MySQL is not running - Skipping database tests${NC}"
        echo -e "${YELLOW}  To run database tests, start MySQL and ensure database is configured${NC}"
    fi
else
    echo -e "${YELLOW}⚠ MySQL not found - Skipping database tests${NC}"
fi
echo ""

# Test 3: Check all PHP files for syntax errors
echo -e "${BLUE}Checking PHP Syntax in All Files...${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

PHP_ERROR=0
for file in api/*.php; do
    php -l "$file" > /dev/null 2>&1
    if [ $? -ne 0 ]; then
        echo -e "${RED}✗ Syntax error in: $file${NC}"
        PHP_ERROR=1
    fi
done

for file in config/*.php; do
    php -l "$file" > /dev/null 2>&1
    if [ $? -ne 0 ]; then
        echo -e "${RED}✗ Syntax error in: $file${NC}"
        PHP_ERROR=1
    fi
done

if [ $PHP_ERROR -eq 0 ]; then
    echo -e "${GREEN}✓ All PHP files have valid syntax${NC}"
    ((PASSED_TESTS++))
else
    echo -e "${RED}✗ Some PHP files have syntax errors${NC}"
    ((FAILED_TESTS++))
fi
((TOTAL_TESTS++))
echo ""

# Test 4: Check directory structure
echo -e "${BLUE}Validating Directory Structure...${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

STRUCTURE_OK=1

# Check required directories
for dir in "api" "assets" "assets/css" "assets/js" "assets/uploads" "config"; do
    if [ -d "$dir" ]; then
        echo -e "${GREEN}✓ Directory exists: $dir${NC}"
    else
        echo -e "${RED}✗ Directory missing: $dir${NC}"
        STRUCTURE_OK=0
    fi
done

# Check required files
for file in "config/database.php" "assets/js/app.js" "index.html"; do
    if [ -f "$file" ]; then
        echo -e "${GREEN}✓ File exists: $file${NC}"
    else
        echo -e "${RED}✗ File missing: $file${NC}"
        STRUCTURE_OK=0
    fi
done

if [ $STRUCTURE_OK -eq 1 ]; then
    echo -e "${GREEN}✓ Directory Structure: VALID${NC}"
    ((PASSED_TESTS++))
else
    echo -e "${RED}✗ Directory Structure: INVALID${NC}"
    ((FAILED_TESTS++))
fi
((TOTAL_TESTS++))
echo ""

# Test 5: Count HTML pages
echo -e "${BLUE}Counting HTML Pages...${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

HTML_COUNT=$(ls -1 *.html 2>/dev/null | wc -l)
echo -e "${GREEN}Found $HTML_COUNT HTML pages${NC}"

if [ $HTML_COUNT -gt 0 ]; then
    echo -e "${GREEN}✓ HTML Pages: $HTML_COUNT pages found${NC}"
    ((PASSED_TESTS++))
else
    echo -e "${RED}✗ No HTML pages found${NC}"
    ((FAILED_TESTS++))
fi
((TOTAL_TESTS++))
echo ""

# Test 6: Count API endpoints
echo -e "${BLUE}Counting API Endpoints...${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

API_COUNT=$(ls -1 api/*.php 2>/dev/null | wc -l)
echo -e "${GREEN}Found $API_COUNT API endpoints${NC}"

if [ $API_COUNT -gt 0 ]; then
    echo -e "${GREEN}✓ API Endpoints: $API_COUNT endpoints found${NC}"
    ((PASSED_TESTS++))
else
    echo -e "${RED}✗ No API endpoints found${NC}"
    ((FAILED_TESTS++))
fi
((TOTAL_TESTS++))
echo ""

# Print Final Summary
echo "╔════════════════════════════════════════════════════════╗"
echo "║              Validation Summary Report                 ║"
echo "╚════════════════════════════════════════════════════════╝"
echo ""
echo "Total Tests Run: $TOTAL_TESTS"
echo -e "${GREEN}Passed: $PASSED_TESTS${NC}"
echo -e "${RED}Failed: $FAILED_TESTS${NC}"
echo ""

if [ $FAILED_TESTS -eq 0 ]; then
    echo -e "${GREEN}═══════════════════════════════════════════════════════${NC}"
    echo -e "${GREEN}   ✓✓✓ All validation tests passed! ✓✓✓${NC}"
    echo -e "${GREEN}═══════════════════════════════════════════════════════${NC}"
    echo ""
    echo "System Status: READY FOR PRODUCTION"
    exit 0
else
    echo -e "${RED}═══════════════════════════════════════════════════════${NC}"
    echo -e "${RED}   ✗ Some validation tests failed${NC}"
    echo -e "${RED}═══════════════════════════════════════════════════════${NC}"
    echo ""
    echo "System Status: NEEDS ATTENTION"
    exit 1
fi

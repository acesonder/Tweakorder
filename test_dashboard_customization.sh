#!/bin/bash
# Test script for customizable dashboard feature

echo "========================================"
echo "Customizable Dashboard Test Suite"
echo "========================================"
echo ""

# Color codes
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Test counter
TESTS_PASSED=0
TESTS_FAILED=0

# Function to print test results
print_result() {
    if [ $1 -eq 0 ]; then
        echo -e "${GREEN}✓ PASS${NC}: $2"
        ((TESTS_PASSED++))
    else
        echo -e "${RED}✗ FAIL${NC}: $2"
        ((TESTS_FAILED++))
    fi
}

# Test 1: Check PHP syntax for dashboard API
echo "Test 1: Checking PHP syntax for dashboard API..."
php -l api/dashboard.php > /dev/null 2>&1
print_result $? "Dashboard API syntax check"
echo ""

# Test 2: Check if dashboard HTML file exists
echo "Test 2: Checking dashboard HTML file exists..."
[ -f dashboard-custom.html ]
print_result $? "Dashboard HTML file exists"
echo ""

# Test 3: Check if SQL schema file exists
echo "Test 3: Checking SQL schema file exists..."
[ -f dashboard_customization.sql ]
print_result $? "Dashboard SQL schema file exists"
echo ""

# Test 4: Check if documentation exists
echo "Test 4: Checking documentation exists..."
[ -f DASHBOARD_CUSTOMIZATION_GUIDE.md ]
print_result $? "Dashboard documentation exists"
echo ""

# Test 5: Validate SQL syntax (basic check)
echo "Test 5: Validating SQL schema..."
grep -q "CREATE TABLE.*dashboard_layouts" dashboard_customization.sql
RESULT1=$?
grep -q "CREATE TABLE.*dashboard_widgets" dashboard_customization.sql
RESULT2=$?
if [ $RESULT1 -eq 0 ] && [ $RESULT2 -eq 0 ]; then
    print_result 0 "SQL schema contains required tables"
else
    print_result 1 "SQL schema missing required tables"
fi
echo ""

# Test 6: Check for Sortable.js CDN in HTML
echo "Test 6: Checking for drag-and-drop library..."
grep -q "sortablejs" dashboard-custom.html
print_result $? "Sortable.js library included"
echo ""

# Test 7: Check for Chart.js CDN in HTML
echo "Test 7: Checking for chart library..."
grep -q "chart.js" dashboard-custom.html
print_result $? "Chart.js library included"
echo ""

# Test 8: Check API endpoints are defined
echo "Test 8: Checking API endpoints..."
grep -q "get_layout" api/dashboard.php
RESULT1=$?
grep -q "save_widgets" api/dashboard.php
RESULT2=$?
grep -q "reset_layout" api/dashboard.php
RESULT3=$?
if [ $RESULT1 -eq 0 ] && [ $RESULT2 -eq 0 ] && [ $RESULT3 -eq 0 ]; then
    print_result 0 "All required API endpoints defined"
else
    print_result 1 "Missing required API endpoints"
fi
echo ""

# Test 9: Check for authentication in API
echo "Test 9: Checking for authentication checks..."
grep -q "session_start()" api/dashboard.php
RESULT1=$?
grep -q "user_id" api/dashboard.php
RESULT2=$?
if [ $RESULT1 -eq 0 ] && [ $RESULT2 -eq 0 ]; then
    print_result 0 "Authentication checks present"
else
    print_result 1 "Authentication checks missing"
fi
echo ""

# Test 10: Check for SQL injection protection
echo "Test 10: Checking for SQL injection protection..."
grep -q "prepare" api/dashboard.php
RESULT1=$?
grep -q "bind_param" api/dashboard.php
RESULT2=$?
if [ $RESULT1 -eq 0 ] && [ $RESULT2 -eq 0 ]; then
    print_result 0 "Prepared statements used for SQL safety"
else
    print_result 1 "SQL injection protection may be inadequate"
fi
echo ""

# Test 11: Check widget types are implemented
echo "Test 11: Checking widget implementations..."
grep -q "quick_stats" api/dashboard.php
RESULT1=$?
grep -q "recent_orders" api/dashboard.php
RESULT2=$?
grep -q "activity_feed" api/dashboard.php
RESULT3=$?
if [ $RESULT1 -eq 0 ] && [ $RESULT2 -eq 0 ] && [ $RESULT3 -eq 0 ]; then
    print_result 0 "Core widget types implemented"
else
    print_result 1 "Some widget types missing"
fi
echo ""

# Test 12: Check for smooth animations in CSS
echo "Test 12: Checking for animation styles..."
grep -q "transition" dashboard-custom.html
RESULT1=$?
grep -q "@keyframes" dashboard-custom.html
RESULT2=$?
if [ $RESULT1 -eq 0 ] && [ $RESULT2 -eq 0 ]; then
    print_result 0 "Animation styles present"
else
    print_result 1 "Animation styles may be incomplete"
fi
echo ""

# Test 13: Check staff dashboard has link to custom dashboard
echo "Test 13: Checking navigation links..."
grep -q "dashboard-custom.html" staff-dashboard.html
print_result $? "Link from staff dashboard exists"
echo ""

# Print summary
echo "========================================"
echo "Test Summary"
echo "========================================"
echo "Total Tests: $((TESTS_PASSED + TESTS_FAILED))"
echo -e "${GREEN}Passed: $TESTS_PASSED${NC}"
echo -e "${RED}Failed: $TESTS_FAILED${NC}"
echo ""

if [ $TESTS_FAILED -eq 0 ]; then
    echo -e "${GREEN}All tests passed! ✓${NC}"
    exit 0
else
    echo -e "${RED}Some tests failed. Please review the results above.${NC}"
    exit 1
fi

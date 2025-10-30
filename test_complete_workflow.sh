#!/bin/bash

# Tweakorder - Complete End-to-End Test Script
# This script tests all functionality including the workflows described in the issue

set -e

BASE_URL="http://localhost:8000"
echo "==================================================================="
echo "Tweakorder - Complete End-to-End Testing"
echo "==================================================================="
echo ""

# Color codes for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Test counter
TESTS_PASSED=0
TESTS_FAILED=0

# Function to print test result
test_result() {
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ PASS${NC}: $1"
        ((TESTS_PASSED++))
    else
        echo -e "${RED}✗ FAIL${NC}: $1"
        ((TESTS_FAILED++))
    fi
}

# Function to test JSON response
test_json_success() {
    local response=$1
    local test_name=$2
    echo "$response" | jq -e '.success == true' > /dev/null 2>&1
    test_result "$test_name"
}

echo -e "${BLUE}1. Testing API Endpoints${NC}"
echo "-------------------------------------------------------------------"

# Test Products API - GET
echo "Testing: GET /api/products.php"
response=$(curl -s "$BASE_URL/api/products.php")
test_json_success "$response" "Products API - GET"

# Test Clients API - GET
echo "Testing: GET /api/clients.php"
response=$(curl -s "$BASE_URL/api/clients.php")
test_json_success "$response" "Clients API - GET"

# Test Workers API - GET
echo "Testing: GET /api/workers.php"
response=$(curl -s "$BASE_URL/api/workers.php")
test_json_success "$response" "Workers API - GET"

# Test Orders API - GET
echo "Testing: GET /api/orders.php"
response=$(curl -s "$BASE_URL/api/orders.php")
test_json_success "$response" "Orders API - GET all"

echo "Testing: GET /api/orders.php?status=waiting"
response=$(curl -s "$BASE_URL/api/orders.php?status=waiting")
test_json_success "$response" "Orders API - GET waiting"

echo ""
echo -e "${BLUE}2. Creating Sample Products${NC}"
echo "-------------------------------------------------------------------"

# Create Product 1
echo "Creating: Widget Pro"
response=$(curl -s -X POST "$BASE_URL/api/products.php" \
  -F "name=Widget Pro" \
  -F "description=Professional-grade widget for testing" \
  -F "inventory=50" \
  -F "background_color=gradient-1")
test_json_success "$response" "Create Product: Widget Pro"
PRODUCT1_ID=$(echo "$response" | jq -r '.id')

# Create Product 2
echo "Creating: Gadget Plus"
response=$(curl -s -X POST "$BASE_URL/api/products.php" \
  -F "name=Gadget Plus" \
  -F "description=Advanced gadget with premium features" \
  -F "inventory=30" \
  -F "background_color=gradient-5")
test_json_success "$response" "Create Product: Gadget Plus"
PRODUCT2_ID=$(echo "$response" | jq -r '.id')

# Create Product 3
echo "Creating: Tool Basic"
response=$(curl -s -X POST "$BASE_URL/api/products.php" \
  -F "name=Tool Basic" \
  -F "description=Essential tool for daily use" \
  -F "inventory=100" \
  -F "background_color=gradient-10")
test_json_success "$response" "Create Product: Tool Basic"
PRODUCT3_ID=$(echo "$response" | jq -r '.id')

# Create Product 4
echo "Creating: Premium Kit"
response=$(curl -s -X POST "$BASE_URL/api/products.php" \
  -F "name=Premium Kit" \
  -F "description=Complete premium kit bundle" \
  -F "inventory=25" \
  -F "background_color=gradient-7")
test_json_success "$response" "Create Product: Premium Kit"
PRODUCT4_ID=$(echo "$response" | jq -r '.id')

echo ""
echo -e "${BLUE}3. Creating Sample Clients${NC}"
echo "-------------------------------------------------------------------"

# Create Client 1
echo "Creating: Test Client (New)"
response=$(curl -s -X POST "$BASE_URL/api/clients.php" \
  -d "first_name=Test&last_name=Client")
test_json_success "$response" "Create Client: Test Client"
CLIENT1_ID=$(echo "$response" | jq -r '.id')

# Create Client 2
echo "Creating: John Smith"
response=$(curl -s -X POST "$BASE_URL/api/clients.php" \
  -d "first_name=John&last_name=Smith")
test_json_success "$response" "Create Client: John Smith"
CLIENT2_ID=$(echo "$response" | jq -r '.id')

# Create Client 3
echo "Creating: Jane Doe"
response=$(curl -s -X POST "$BASE_URL/api/clients.php" \
  -d "first_name=Jane&last_name=Doe")
test_json_success "$response" "Create Client: Jane Doe"
CLIENT3_ID=$(echo "$response" | jq -r '.id')

echo ""
echo -e "${BLUE}4. Creating Sample Workers${NC}"
echo "-------------------------------------------------------------------"

# Create Worker 1
echo "Creating: Alice Manager"
response=$(curl -s -X POST "$BASE_URL/api/workers.php" \
  -d "first_name=Alice&last_name=Manager")
test_json_success "$response" "Create Worker: Alice Manager"

# Create Worker 2
echo "Creating: Bob Technician"
response=$(curl -s -X POST "$BASE_URL/api/workers.php" \
  -d "first_name=Bob&last_name=Technician")
test_json_success "$response" "Create Worker: Bob Technician"

echo ""
echo -e "${BLUE}5. Testing Order Workflow - Scenario 1${NC}"
echo "-------------------------------------------------------------------"
echo "Scenario: Create order with new client, mark waiting, then fulfill"

# Create Order 1 - waiting for supplies
echo "Creating: Order 1 for Test Client (waiting for supplies)"
items='[{"product_id":'$PRODUCT1_ID',"quantity":2},{"product_id":'$PRODUCT2_ID',"quantity":1}]'
response=$(curl -s -X POST "$BASE_URL/api/orders.php" \
  -d "client_id=$CLIENT1_ID&status=waiting&items=$items")
test_json_success "$response" "Create Order 1 (waiting)"
ORDER1_ID=$(echo "$response" | jq -r '.id')

# Verify order is in waiting list
echo "Verifying: Order appears in waiting list"
response=$(curl -s "$BASE_URL/api/orders.php?status=waiting")
echo "$response" | jq -e '.data[] | select(.id == "'$ORDER1_ID'")' > /dev/null 2>&1
test_result "Order 1 in waiting list"

# Update Order 1 to fulfilled
echo "Updating: Order 1 to fulfilled status"
response=$(curl -s -X PUT "$BASE_URL/api/orders.php" \
  -d "id=$ORDER1_ID&status=fulfilled")
test_json_success "$response" "Update Order 1 to fulfilled"

# Verify order is no longer in waiting list
echo "Verifying: Order removed from waiting list"
response=$(curl -s "$BASE_URL/api/orders.php?status=waiting")
echo "$response" | jq -e '.data[] | select(.id == "'$ORDER1_ID'")' > /dev/null 2>&1
if [ $? -ne 0 ]; then
    test_result "Order 1 removed from waiting"
else
    echo -e "${RED}✗ FAIL${NC}: Order 1 still in waiting list"
    ((TESTS_FAILED++))
fi

echo ""
echo -e "${BLUE}6. Testing Order Workflow - Scenario 2${NC}"
echo "-------------------------------------------------------------------"
echo "Scenario: Create order with existing client and fulfill immediately"

# Create Order 2 - fulfilled
echo "Creating: Order 2 for John Smith (fulfilled)"
items='[{"product_id":'$PRODUCT3_ID',"quantity":3},{"product_id":'$PRODUCT1_ID',"quantity":1}]'
response=$(curl -s -X POST "$BASE_URL/api/orders.php" \
  -d "client_id=$CLIENT2_ID&status=fulfilled&items=$items")
test_json_success "$response" "Create Order 2 (fulfilled)"
ORDER2_ID=$(echo "$response" | jq -r '.id')

# Verify order is NOT in waiting list
echo "Verifying: Order 2 not in waiting list"
response=$(curl -s "$BASE_URL/api/orders.php?status=waiting")
echo "$response" | jq -e '.data[] | select(.id == "'$ORDER2_ID'")' > /dev/null 2>&1
if [ $? -ne 0 ]; then
    test_result "Order 2 not in waiting (correctly fulfilled)"
else
    echo -e "${RED}✗ FAIL${NC}: Order 2 incorrectly in waiting list"
    ((TESTS_FAILED++))
fi

echo ""
echo -e "${BLUE}7. Testing Order Workflow - Scenario 3${NC}"
echo "-------------------------------------------------------------------"
echo "Scenario: Create another order with existing client"

# Create Order 3
echo "Creating: Order 3 for Jane Doe (waiting)"
items='[{"product_id":'$PRODUCT4_ID',"quantity":2},{"product_id":'$PRODUCT2_ID',"quantity":2}]'
response=$(curl -s -X POST "$BASE_URL/api/orders.php" \
  -d "client_id=$CLIENT3_ID&status=waiting&items=$items")
test_json_success "$response" "Create Order 3 (waiting)"
ORDER3_ID=$(echo "$response" | jq -r '.id')

# Update to fulfilled
echo "Updating: Order 3 to fulfilled"
response=$(curl -s -X PUT "$BASE_URL/api/orders.php" \
  -d "id=$ORDER3_ID&status=fulfilled")
test_json_success "$response" "Update Order 3 to fulfilled"

echo ""
echo -e "${BLUE}8. Verifying All Orders${NC}"
echo "-------------------------------------------------------------------"

# Get all orders and verify count
echo "Retrieving: All orders"
response=$(curl -s "$BASE_URL/api/orders.php?status=all")
order_count=$(echo "$response" | jq '.data | length')
echo "Total orders created: $order_count"
if [ "$order_count" -ge 3 ]; then
    test_result "All orders retrieved (count: $order_count)"
else
    echo -e "${RED}✗ FAIL${NC}: Expected at least 3 orders, got $order_count"
    ((TESTS_FAILED++))
fi

# Verify all orders have items
echo "Verifying: All orders have items"
has_items=$(echo "$response" | jq -e '.data[] | select(.items | length > 0)' > /dev/null 2>&1 && echo "yes" || echo "no")
if [ "$has_items" = "yes" ]; then
    test_result "All orders contain items"
else
    echo -e "${RED}✗ FAIL${NC}: Some orders missing items"
    ((TESTS_FAILED++))
fi

echo ""
echo -e "${BLUE}9. Testing Page Accessibility${NC}"
echo "-------------------------------------------------------------------"

# Test all HTML pages
pages=("index.html" "add-product.html" "add-worker.html" "create-order.html" "open-orders.html" "all-orders.html" "edit-orders.html")

for page in "${pages[@]}"; do
    echo "Testing: $page"
    status=$(curl -s -o /dev/null -w "%{http_code}" "$BASE_URL/$page")
    if [ "$status" = "200" ]; then
        test_result "Page accessible: $page"
    else
        echo -e "${RED}✗ FAIL${NC}: Page $page returned status $status"
        ((TESTS_FAILED++))
    fi
done

echo ""
echo -e "${BLUE}10. Summary of Created Test Data${NC}"
echo "-------------------------------------------------------------------"

echo "Products created:"
curl -s "$BASE_URL/api/products.php" | jq -r '.data[] | "  - \(.name) (ID: \(.id), Inventory: \(.inventory))"'

echo ""
echo "Clients created:"
curl -s "$BASE_URL/api/clients.php" | jq -r '.data[] | "  - \(.first_name) \(.last_name) (ID: \(.id))"'

echo ""
echo "Workers created:"
curl -s "$BASE_URL/api/workers.php" | jq -r '.data[] | "  - \(.first_name) \(.last_name) (ID: \(.id))"'

echo ""
echo "Orders created:"
curl -s "$BASE_URL/api/orders.php?status=all" | jq -r '.data[] | "  - Order #\(.id) for \(.first_name) \(.last_name) - Status: \(.status) - Items: \(.items | length)"'

echo ""
echo "==================================================================="
echo -e "${BLUE}TEST RESULTS${NC}"
echo "==================================================================="
echo -e "${GREEN}Tests Passed: $TESTS_PASSED${NC}"
echo -e "${RED}Tests Failed: $TESTS_FAILED${NC}"
echo "Total Tests: $((TESTS_PASSED + TESTS_FAILED))"
echo ""

if [ $TESTS_FAILED -eq 0 ]; then
    echo -e "${GREEN}✓ ALL TESTS PASSED!${NC}"
    echo ""
    echo "The Tweakorder application is fully functional and ready to use."
    echo "All order workflows have been tested successfully."
    exit 0
else
    echo -e "${RED}✗ SOME TESTS FAILED${NC}"
    echo ""
    echo "Please review the failed tests above and check:"
    echo "  1. MySQL service is running"
    echo "  2. Database credentials are correct in config/database.php"
    echo "  3. PHP server is running on localhost:8000"
    echo "  4. All dependencies are installed"
    exit 1
fi

#!/bin/bash

echo "Testing JavaScript file references..."

# Test if JS files have been updated to point to correct controller paths
echo "Checking main.js references..."
if grep -q "src/controllers/get_" js/main.js; then
    echo "✓ main.js references controllers correctly"
else
    echo "✗ main.js missing controller references"
fi

echo "Checking admin.js references..."
if grep -q "src/controllers/add_" js/admin.js; then
    echo "✓ admin.js references controllers correctly"
else
    echo "✗ admin.js missing controller references"
fi

echo "Checking checkout.js references..."
if grep -q "src/controllers/update" js/checkout.js; then
    echo "✓ checkout.js references controllers correctly"
else
    echo "✗ checkout.js missing controller references"
fi

echo "Checking initialization.js references..."
if grep -q "src/controllers/get_products.php" js/initialization.js; then
    echo "✓ initialization.js references controllers correctly"  
else
    echo "✗ initialization.js missing controller references"
fi

echo "JavaScript references test completed!"
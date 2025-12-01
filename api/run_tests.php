<?php
/**
 * Automated API Test Script
 * 
 * Script ini akan menjalankan test otomatis untuk semua API endpoint
 * dan menampilkan hasil test dalam format yang mudah dibaca
 */

// Configuration
$BASE_URL = 'http://localhost/belajar-web/toko-online-php/api';
$VERBOSE = true; // Set false untuk output ringkas

// Test results
$tests_passed = 0;
$tests_failed = 0;
$test_results = [];

// Colors for terminal output
$COLOR_GREEN = "\033[32m";
$COLOR_RED = "\033[31m";
$COLOR_YELLOW = "\033[33m";
$COLOR_BLUE = "\033[34m";
$COLOR_RESET = "\033[0m";

/**
 * Make HTTP request
 */
function makeRequest($url, $method = 'GET', $data = []) {
    $ch = curl_init();
    
    if ($method === 'GET' && !empty($data)) {
        $url .= '?' . http_build_query($data);
    }
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    }
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'code' => $http_code,
        'body' => json_decode($response, true),
        'raw' => $response
    ];
}

/**
 * Run a test
 */
function runTest($name, $url, $method, $data, $expectedCode, $expectedKeys = []) {
    global $BASE_URL, $tests_passed, $tests_failed, $test_results, $VERBOSE;
    global $COLOR_GREEN, $COLOR_RED, $COLOR_YELLOW, $COLOR_BLUE, $COLOR_RESET;
    
    echo $COLOR_BLUE . "Testing: $name" . $COLOR_RESET . "\n";
    
    $response = makeRequest($BASE_URL . $url, $method, $data);
    
    $passed = true;
    $errors = [];
    
    // Check HTTP status code
    if ($response['code'] !== $expectedCode) {
        $passed = false;
        $errors[] = "Expected HTTP $expectedCode, got {$response['code']}";
    }
    
    // Check expected keys in response
    if (!empty($expectedKeys) && is_array($response['body'])) {
        foreach ($expectedKeys as $key) {
            if (!isset($response['body'][$key])) {
                $passed = false;
                $errors[] = "Missing expected key: $key";
            }
        }
    }
    
    // Record result
    if ($passed) {
        $tests_passed++;
        echo $COLOR_GREEN . "✓ PASSED" . $COLOR_RESET . "\n";
    } else {
        $tests_failed++;
        echo $COLOR_RED . "✗ FAILED" . $COLOR_RESET . "\n";
        foreach ($errors as $error) {
            echo $COLOR_RED . "  - $error" . $COLOR_RESET . "\n";
        }
    }
    
    if ($VERBOSE) {
        echo "  Response: " . json_encode($response['body'], JSON_PRETTY_PRINT) . "\n";
    }
    
    echo "\n";
    
    $test_results[] = [
        'name' => $name,
        'passed' => $passed,
        'errors' => $errors,
        'response' => $response
    ];
    
    return $response;
}

/**
 * Print section header
 */
function printSection($title) {
    global $COLOR_YELLOW, $COLOR_RESET;
    echo "\n" . $COLOR_YELLOW . str_repeat("=", 60) . $COLOR_RESET . "\n";
    echo $COLOR_YELLOW . $title . $COLOR_RESET . "\n";
    echo $COLOR_YELLOW . str_repeat("=", 60) . $COLOR_RESET . "\n\n";
}

/**
 * Print summary
 */
function printSummary() {
    global $tests_passed, $tests_failed, $COLOR_GREEN, $COLOR_RED, $COLOR_YELLOW, $COLOR_RESET;
    
    $total = $tests_passed + $tests_failed;
    $percentage = $total > 0 ? round(($tests_passed / $total) * 100, 2) : 0;
    
    echo "\n" . $COLOR_YELLOW . str_repeat("=", 60) . $COLOR_RESET . "\n";
    echo $COLOR_YELLOW . "TEST SUMMARY" . $COLOR_RESET . "\n";
    echo $COLOR_YELLOW . str_repeat("=", 60) . $COLOR_RESET . "\n\n";
    
    echo "Total Tests: $total\n";
    echo $COLOR_GREEN . "Passed: $tests_passed" . $COLOR_RESET . "\n";
    echo $COLOR_RED . "Failed: $tests_failed" . $COLOR_RESET . "\n";
    echo "Success Rate: $percentage%\n\n";
}

// ============================================================================
// START TESTS
// ============================================================================

echo $COLOR_BLUE . "\n";
echo "╔══════════════════════════════════════════════════════════╗\n";
echo "║         TOKO ONLINE API - AUTOMATED TEST SUITE          ║\n";
echo "╚══════════════════════════════════════════════════════════╝\n";
echo $COLOR_RESET . "\n";

// ============================================================================
// 1. AUTHENTICATION TESTS
// ============================================================================
printSection("1. AUTHENTICATION TESTS");

runTest(
    "Member Login - Success",
    "/auth.php",
    "POST",
    ['action' => 'login', 'email' => 'andi@example.com', 'password' => 'pass123'],
    200,
    ['status', 'data']
);

runTest(
    "Member Login - Wrong Password",
    "/auth.php",
    "POST",
    ['action' => 'login', 'email' => 'andi@example.com', 'password' => 'wrongpass'],
    401,
    ['status', 'message']
);

runTest(
    "Admin Login - Success",
    "/auth.php",
    "POST",
    ['action' => 'admin_login', 'username' => 'admin1@gmail.com', 'password' => 'adminpass'],
    200,
    ['status', 'data']
);

// ============================================================================
// 2. PRODUCT TESTS
// ============================================================================
printSection("2. PRODUCT TESTS");

runTest(
    "Get All Products",
    "/product.php",
    "GET",
    [],
    200,
    ['status', 'data']
);

runTest(
    "Get Single Product",
    "/product.php",
    "GET",
    ['id' => 1],
    200,
    ['status', 'data']
);

runTest(
    "Get Non-Existent Product",
    "/product.php",
    "GET",
    ['id' => 9999],
    404,
    ['status', 'message']
);

// ============================================================================
// 3. CATEGORY TESTS
// ============================================================================
printSection("3. CATEGORY TESTS");

runTest(
    "Get All Categories",
    "/category.php",
    "GET",
    [],
    200,
    ['status', 'data']
);

runTest(
    "Get Single Category",
    "/category.php",
    "GET",
    ['id' => 1],
    200,
    ['status', 'data']
);

// ============================================================================
// 4. CART TESTS
// ============================================================================
printSection("4. SHOPPING CART TESTS");

runTest(
    "Get Cart Items",
    "/cart.php",
    "GET",
    ['id_member' => 1],
    200,
    ['status', 'data', 'total']
);

runTest(
    "Add to Cart - Success",
    "/cart.php",
    "POST",
    ['id_member' => 1, 'id_barang' => 3, 'qty' => 1],
    201,
    ['status', 'message']
);

runTest(
    "Add to Cart - Insufficient Stock",
    "/cart.php",
    "POST",
    ['id_member' => 1, 'id_barang' => 3, 'qty' => 10000],
    400,
    ['status', 'message']
);

// ============================================================================
// 5. SALES/ORDER TESTS
// ============================================================================
printSection("5. SALES/ORDER TESTS");

runTest(
    "Get All Orders",
    "/penjualan.php",
    "GET",
    [],
    200,
    ['status', 'data']
);

runTest(
    "Get Orders by Member",
    "/penjualan.php",
    "GET",
    ['id_member' => 1],
    200,
    ['status', 'data']
);

runTest(
    "Get Orders by Status",
    "/penjualan.php",
    "GET",
    ['status' => 'completed'],
    200,
    ['status', 'data']
);

runTest(
    "Get Order Details",
    "/penjualan.php",
    "GET",
    ['id' => 1],
    200,
    ['status', 'data']
);

// ============================================================================
// 6. PAYMENT TESTS
// ============================================================================
printSection("6. PAYMENT TESTS");

runTest(
    "Get All Payments",
    "/payment.php",
    "GET",
    [],
    200,
    ['status', 'data']
);

runTest(
    "Get Payment by Sale",
    "/payment.php",
    "GET",
    ['id_penjualan' => 1],
    200,
    ['status']
);

// ============================================================================
// 7. REVIEW TESTS
// ============================================================================
printSection("7. REVIEW TESTS");

runTest(
    "Get Product Reviews",
    "/review.php",
    "GET",
    ['id_barang' => 2],
    200,
    ['status', 'data', 'average_rating', 'total_reviews']
);

runTest(
    "Get Reviews by Member",
    "/review.php",
    "GET",
    ['id_member' => 1],
    200,
    ['status', 'data']
);

// ============================================================================
// 8. VOUCHER TESTS
// ============================================================================
printSection("8. VOUCHER TESTS");

runTest(
    "Get All Vouchers",
    "/voucher.php",
    "GET",
    [],
    200,
    ['status', 'data']
);

runTest(
    "Validate Voucher - Valid",
    "/voucher.php",
    "GET",
    ['kode' => 'PROMO10'],
    200,
    ['status', 'valid']
);

runTest(
    "Validate Voucher - Invalid",
    "/voucher.php",
    "GET",
    ['kode' => 'INVALID123'],
    200,
    ['status', 'valid']
);

// ============================================================================
// 9. WISHLIST TESTS
// ============================================================================
printSection("9. WISHLIST TESTS");

runTest(
    "Get Wishlist",
    "/wishlist.php",
    "GET",
    ['id_member' => 1],
    200,
    ['status', 'data', 'total_items']
);

// ============================================================================
// 10. NOTIFICATION TESTS
// ============================================================================
printSection("10. NOTIFICATION TESTS");

runTest(
    "Get Notifications",
    "/notifikasi.php",
    "GET",
    ['id_member' => 1],
    200,
    ['status', 'data', 'unread_count']
);

runTest(
    "Get Unread Notifications",
    "/notifikasi.php",
    "GET",
    ['id_member' => 1, 'sudah_dibaca' => 0],
    200,
    ['status', 'data']
);

// ============================================================================
// 11. SUPPLIER TESTS
// ============================================================================
printSection("11. SUPPLIER TESTS");

runTest(
    "Get All Suppliers",
    "/supplier.php",
    "GET",
    [],
    200,
    ['status', 'data']
);

// ============================================================================
// 12. DISCOUNT TESTS
// ============================================================================
printSection("12. DISCOUNT TESTS");

runTest(
    "Get All Discounts",
    "/discount.php",
    "GET",
    [],
    200,
    ['status', 'data']
);

runTest(
    "Get Active Discounts",
    "/discount.php",
    "GET",
    ['active' => 1],
    200,
    ['status', 'data']
);

// ============================================================================
// 13. SHIPPING ADDRESS TESTS
// ============================================================================
printSection("13. SHIPPING ADDRESS TESTS");

runTest(
    "Get All Addresses",
    "/alamat.php",
    "GET",
    [],
    200,
    ['status', 'data']
);

runTest(
    "Get Addresses by Member",
    "/alamat.php",
    "GET",
    ['id_member' => 1],
    200,
    ['status', 'data']
);

// ============================================================================
// 14. SHIPMENT TESTS
// ============================================================================
printSection("14. SHIPMENT TESTS");

runTest(
    "Get All Shipments",
    "/shipment.php",
    "GET",
    [],
    200,
    ['status', 'data']
);

runTest(
    "Track by Resi",
    "/shipment.php",
    "GET",
    ['resi' => 'JNE123456789'],
    200,
    ['status']
);

// ============================================================================
// PRINT SUMMARY
// ============================================================================
printSummary();

// Exit with appropriate code
exit($tests_failed > 0 ? 1 : 0);

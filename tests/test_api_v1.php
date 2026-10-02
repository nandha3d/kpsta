<?php

$baseUrl = 'http://127.0.0.1:8090';
$passed = 0;
$failed = 0;

function runTest(string $name, callable $fn) {
    global $passed, $failed;
    echo "Testing: {$name}... ";
    try {
        $fn();
        echo "[PASSED]\n";
        $passed++;
    } catch (\Throwable $e) {
        echo "[FAILED]: " . $e->getMessage() . "\n";
        $failed++;
    }
}

function httpGet(string $path, array $headers = []) {
    global $baseUrl;
    $ch = curl_init($baseUrl . $path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge(['Accept: application/json'], $headers));
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $json = json_decode($response, true);
    return ['status' => $status, 'json' => $json, 'raw' => $response];
}

function httpPost(string $path, array $data, array $headers = []) {
    global $baseUrl;
    $ch = curl_init($baseUrl . $path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge(['Content-Type: application/json', 'Accept: application/json'], $headers));
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $json = json_decode($response, true);
    return ['status' => $status, 'json' => $json, 'raw' => $response];
}

echo "=== STARTING KPSTA /api/v1 INTEGRATION TEST SUITE ===\n\n";

// 1. Home
runTest('GET /api/v1/home', function () {
    $res = httpGet('/api/v1/home');
    if ($res['status'] !== 200) throw new Exception("Expected 200, got {$res['status']}. Raw: {$res['raw']}");
    if (!$res['json']['success']) throw new Exception("Expected success: true");
    $data = $res['json']['data'];
    if (!isset($data['sliders'], $data['flash_news'], $data['latest_news'], $data['circulars'], $data['services'])) {
        throw new Exception("Missing expected home keys");
    }
});

// 2. News
runTest('GET /api/v1/news', function () {
    $res = httpGet('/api/v1/news');
    if ($res['status'] !== 200) throw new Exception("Expected 200, got {$res['status']}");
    if (!$res['json']['success']) throw new Exception("Expected success: true");
    if (!isset($res['json']['meta']['total'])) throw new Exception("Missing pagination meta");
});

// 3. Circulars
runTest('GET /api/v1/order-circulars', function () {
    $res = httpGet('/api/v1/order-circulars?type=general');
    if ($res['status'] !== 200) throw new Exception("Expected 200, got {$res['status']}");
    if (!$res['json']['success']) throw new Exception("Expected success: true");
});

// 4. Downloads
runTest('GET /api/v1/downloads', function () {
    $res = httpGet('/api/v1/downloads?type=forms');
    if ($res['status'] !== 200) throw new Exception("Expected 200, got {$res['status']}");
    if (!$res['json']['success']) throw new Exception("Expected success: true");
});

// 5. Galleries
runTest('GET /api/v1/galleries', function () {
    $res = httpGet('/api/v1/galleries');
    if ($res['status'] !== 200) throw new Exception("Expected 200, got {$res['status']}");
    if (!$res['json']['success']) throw new Exception("Expected success: true");
});

// 6. Organization: Office Bearers
runTest('GET /api/v1/office-bearers', function () {
    $res = httpGet('/api/v1/office-bearers?level=State');
    if ($res['status'] !== 200) throw new Exception("Expected 200, got {$res['status']}");
    if (!$res['json']['success']) throw new Exception("Expected success: true");
});

// 7. Organization: Former Leaders
runTest('GET /api/v1/former-leaders', function () {
    $res = httpGet('/api/v1/former-leaders');
    if ($res['status'] !== 200) throw new Exception("Expected 200, got {$res['status']}");
    if (!$res['json']['success']) throw new Exception("Expected success: true");
});

// 8. Districts
runTest('GET /api/v1/districts', function () {
    $res = httpGet('/api/v1/districts');
    if ($res['status'] !== 200) throw new Exception("Expected 200, got {$res['status']}");
    if (!$res['json']['success']) throw new Exception("Expected success: true");
});

// 9. Service Corner
runTest('GET /api/v1/service-corner', function () {
    $res = httpGet('/api/v1/service-corner');
    if ($res['status'] !== 200) throw new Exception("Expected 200, got {$res['status']}");
    if (!$res['json']['success']) throw new Exception("Expected success: true");
});

// 10. Quick Links
runTest('GET /api/v1/quick-links', function () {
    $res = httpGet('/api/v1/quick-links');
    if ($res['status'] !== 200) throw new Exception("Expected 200, got {$res['status']}");
    if (!$res['json']['success']) throw new Exception("Expected success: true");
});

// 11. Contact
runTest('GET /api/v1/contact', function () {
    $res = httpGet('/api/v1/contact');
    if ($res['status'] !== 200) throw new Exception("Expected 200, got {$res['status']}");
    if (!$res['json']['success']) throw new Exception("Expected success: true");
});

// 12. Privacy Policy
runTest('GET /api/v1/privacy-policy', function () {
    $res = httpGet('/api/v1/privacy-policy');
    if ($res['status'] !== 200) throw new Exception("Expected 200, got {$res['status']}");
    if (!$res['json']['success']) throw new Exception("Expected success: true");
});

// 13. Auth Guard test on protected endpoint without token
runTest('Auth Guard: GET /api/v1/admin/dashboard (unauthorized)', function () {
    $res = httpGet('/api/v1/admin/dashboard');
    if ($res['status'] !== 401) throw new Exception("Expected 401 Unauthorized, got {$res['status']}");
    if ($res['json']['code'] !== 'AUTHENTICATION_REQUIRED') throw new Exception("Expected AUTHENTICATION_REQUIRED code");
});

// 14. WhatsApp OTP Request
$testPhone = '919876543210';
runTest('POST /api/v1/auth/whatsapp/request-otp', function () use ($testPhone) {
    // Clear old cooldown for clean test
    $m = new mysqli('localhost', 'root', 'admin', 'kpsta');
    $m->query("DELETE FROM api_otps WHERE phone = '{$testPhone}'");
    $m->close();

    $res = httpPost('/api/v1/auth/whatsapp/request-otp', ['phone' => $testPhone]);
    if ($res['status'] !== 200) throw new Exception("Expected 200, got {$res['status']}. Raw: {$res['raw']}");
    if (!$res['json']['success']) throw new Exception("Expected success: true");
    if (!isset($res['json']['data']['expires_in'])) throw new Exception("Missing expires_in");
});

// 15. WhatsApp OTP Verify & Token Generation
$accessToken = null;
$refreshToken = null;
runTest('POST /api/v1/auth/whatsapp/verify-otp', function () use ($testPhone, &$accessToken, &$refreshToken) {
    $res = httpPost('/api/v1/auth/whatsapp/verify-otp', [
        'phone'       => $testPhone,
        'otp'         => '123456',
        'device_id'   => 'test-device-uuid',
        'device_type' => 'test-runner',
    ]);
    if ($res['status'] !== 200) throw new Exception("Expected 200, got {$res['status']}. Raw: {$res['raw']}");
    if (!$res['json']['success']) throw new Exception("Expected success: true");
    $accessToken = $res['json']['data']['access_token'] ?? null;
    $refreshToken = $res['json']['data']['refresh_token'] ?? null;
    if (!$accessToken) throw new Exception("Access token missing in response");
    if (!$refreshToken) throw new Exception("Refresh token missing in response");
});

// 16. Verify /auth/me with Bearer token
runTest('GET /api/v1/auth/me (authenticated)', function () use (&$accessToken) {
    if (!$accessToken) throw new Exception("Access token not available");
    $res = httpGet('/api/v1/auth/me', ["Authorization: Bearer {$accessToken}"]);
    if ($res['status'] !== 200) throw new Exception("Expected 200, got {$res['status']}. Raw: {$res['raw']}");
    if (!$res['json']['success']) throw new Exception("Expected success: true");
    if (empty($res['json']['data']['id'])) throw new Exception("User ID missing");
});

// 17. Verify Admin Dashboard with token
runTest('GET /api/v1/admin/dashboard (authenticated admin)', function () use (&$accessToken) {
    if (!$accessToken) throw new Exception("Access token not available");
    $res = httpGet('/api/v1/admin/dashboard', ["Authorization: Bearer {$accessToken}"]);
    if ($res['status'] !== 200) throw new Exception("Expected 200, got {$res['status']}. Raw: {$res['raw']}");
    if (!isset($res['json']['data']['total_news'])) throw new Exception("Missing total_news");
});

// 19. Verify Site Visitors
runTest('GET /api/v1/site-visitors', function () {
    $res = httpGet('/api/v1/site-visitors');
    if ($res['status'] !== 200) throw new Exception("Expected 200, got {$res['status']}");
    if (!isset($res['json']['data']['visitors_count'])) throw new Exception("Missing visitors_count");
});

// 20. Admin Flash News
runTest('GET /api/v1/admin/flash-news (authenticated)', function () use (&$accessToken) {
    $res = httpGet('/api/v1/admin/flash-news', ["Authorization: Bearer {$accessToken}"]);
    if ($res['status'] !== 200) throw new Exception("Expected 200, got {$res['status']}");
    if (!is_array($res['json']['data'])) throw new Exception("Expected array data");
});

// 21. Admin Sliders
runTest('GET /api/v1/admin/sliders (authenticated)', function () use (&$accessToken) {
    $res = httpGet('/api/v1/admin/sliders', ["Authorization: Bearer {$accessToken}"]);
    if ($res['status'] !== 200) throw new Exception("Expected 200, got {$res['status']}");
    if (!is_array($res['json']['data'])) throw new Exception("Expected array data");
});

// 22. Admin Galleries
runTest('GET /api/v1/admin/galleries (authenticated)', function () use (&$accessToken) {
    $res = httpGet('/api/v1/admin/galleries', ["Authorization: Bearer {$accessToken}"]);
    if ($res['status'] !== 200) throw new Exception("Expected 200, got {$res['status']}");
    if (!is_array($res['json']['data'])) throw new Exception("Expected array data");
});

// 23. Admin Quick Links
runTest('GET /api/v1/admin/quick-links (authenticated)', function () use (&$accessToken) {
    $res = httpGet('/api/v1/admin/quick-links', ["Authorization: Bearer {$accessToken}"]);
    if ($res['status'] !== 200) throw new Exception("Expected 200, got {$res['status']}");
    if (!is_array($res['json']['data'])) throw new Exception("Expected array data");
});

// 24. Admin Result Links
runTest('GET /api/v1/admin/result-links (authenticated)', function () use (&$accessToken) {
    $res = httpGet('/api/v1/admin/result-links', ["Authorization: Bearer {$accessToken}"]);
    if ($res['status'] !== 200) throw new Exception("Expected 200, got {$res['status']}");
    if (!is_array($res['json']['data'])) throw new Exception("Expected array data");
});

// 25. Membership Counts (SQL Direct Calculation)
runTest('GET /api/v1/membership/counts (authenticated)', function () use (&$accessToken) {
    $res = httpGet('/api/v1/membership/counts?group=1', ["Authorization: Bearer {$accessToken}"]);
    if ($res['status'] !== 200) throw new Exception("Expected 200, got {$res['status']}");
    if (!isset($res['json']['data']['totals']['total'])) throw new Exception("Missing totals.total");
});

// 26. Membership Reports (SQL Aggregation)
runTest('GET /api/v1/membership/reports (authenticated)', function () use (&$accessToken) {
    $res = httpGet('/api/v1/membership/reports?type=district', ["Authorization: Bearer {$accessToken}"]);
    if ($res['status'] !== 200) throw new Exception("Expected 200, got {$res['status']}");
    if (!isset($res['json']['data']['totals']['total_count'])) throw new Exception("Missing totals.total_count");
});

// 27. Membership Metadata (Dropdown Options Direct from Database)
runTest('GET /api/v1/membership/metadata (authenticated)', function () use (&$accessToken) {
    $res = httpGet('/api/v1/membership/metadata', ["Authorization: Bearer {$accessToken}"]);
    if ($res['status'] !== 200) throw new Exception("Expected 200, got {$res['status']}");
    if (empty($res['json']['data']['designations'])) throw new Exception("Missing designations");
    if (empty($res['json']['data']['districts'])) throw new Exception("Missing districts");
});

// 28. Refresh Token
runTest('POST /api/v1/auth/refresh', function () use (&$refreshToken) {
    if (!$refreshToken) throw new Exception("Refresh token not available");
    $res = httpPost('/api/v1/auth/refresh', ['refresh_token' => $refreshToken]);
    if ($res['status'] !== 200) throw new Exception("Expected 200, got {$res['status']}. Raw: {$res['raw']}");
    if (empty($res['json']['data']['access_token'])) throw new Exception("New access token missing");
});

// 20. Logout
runTest('POST /api/v1/auth/logout', function () use (&$accessToken) {
    $res = httpPost('/api/v1/auth/logout', [], ["Authorization: Bearer {$accessToken}"]);
    if ($res['status'] !== 200) throw new Exception("Expected 200, got {$res['status']}");
    // Check that old access token is now rejected
    $check = httpGet('/api/v1/auth/me', ["Authorization: Bearer {$accessToken}"]);
    if ($check['status'] !== 401) throw new Exception("Expected 401 after logout, got {$check['status']}");
});

echo "\n=============================================\n";
echo "TEST RESULTS: {$passed} Passed, {$failed} Failed\n";
echo "=============================================\n";

exit($failed > 0 ? 1 : 0);

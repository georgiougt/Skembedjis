<?php
// soap.php
// Standalone script to pull catalog data from the 1C SOAP web service and generate the items.json file.
// Matches the architecture of the old website.

require_once __DIR__ . '/db.php';

try {
    // 1. Fetch SOAP settings dynamically from SQLite database
    $stmtW = $db->prepare("SELECT value FROM site_settings WHERE key = 'onec_wsdl'");
    $stmtW->execute();
    $wsdl = trim($stmtW->fetchColumn() ?: 'http://213.7.198.218:8080/SKEMBEDJIS/ws/ws1.1cws?wsdl');

    $stmtL = $db->prepare("SELECT value FROM site_settings WHERE key = 'onec_login'");
    $stmtL->execute();
    $login = trim($stmtL->fetchColumn() ?: 'ecommerce');

    $stmtP = $db->prepare("SELECT value FROM site_settings WHERE key = 'onec_password'");
    $stmtP->execute();
    $password = trim($stmtP->fetchColumn() ?: '3c0mm3rc3*');

    echo "Connecting to 1C SOAP service at: $wsdl\n";

    if (!class_exists('SoapClient')) {
        throw new Exception("PHP SOAP extension is not enabled on this server.");
    }

    // Force WSDL bypass cache by appending timestamp query argument for HTTP/HTTPS endpoints
    $nocacheWsdl = $wsdl;
    if (strpos($wsdl, 'http://') === 0 || strpos($wsdl, 'https://') === 0) {
        $nocacheWsdl = $wsdl . (strpos($wsdl, '?') !== false ? '&' : '?') . 'nocache=' . time();
    }

    $soap = new SoapClient($nocacheWsdl, [
        'login'    => $login,
        'password' => $password,
        'trace'    => 1,
        'connection_timeout' => 20,
        'cache_wsdl' => WSDL_CACHE_NONE // Disable local PHP WSDL caching
    ]);

    // 2. Fetch pages loop
    $all = [];
    $page = 1;
    do {
        echo "Fetching page $page...\n";
        $resp  = $soap->GetItems(['Page' => $page]);
        $arr   = json_decode(json_encode($resp), true);
        $items = $arr['return']['Item'] ?? [];
        if (!$items) break;
        if (isset($items['ItemCode'])) $items = [$items];
        $all   = array_merge($all, $items);
        $total = (int)($items[0]['TotalItems'] ?? count($all));
        $page++;
    } while (count($all) < $total && $page < 1000);

    if (empty($all)) {
        throw new Exception("Connected to SOAP, but no items were returned.");
    }

    // 3. Save to items.json in root folder
    $jsonPath = __DIR__ . '/items.json';
    file_put_contents($jsonPath, json_encode($all, JSON_PRETTY_PRINT));

    echo "Successfully generated items.json with " . count($all) . " stock entries!\n";

} catch (Exception $e) {
    echo "SOAP Error: " . $e->getMessage() . "\n";
    exit(1);
}

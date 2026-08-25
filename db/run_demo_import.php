<?php
// db/run_demo_import.php
// Standalone importer to sync/add the 15 specific machines for demo, copying pictures and converting them to WebP.

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../sync_products.php';

$codes = [
    '001-0J901403', '001-1F0502', '001-2E40033', '001-2E400337',
    '001-2EM01588', '001-2N511080', '001-2Z900211', '001-3BP20193',
    '001-3T30024', '001-4F25A30E-00118', '001-4FD25-10675',
    '001-5FDF25-10858', '001-6BN11632', '001-6CN00488', '001-6-FDF-18-21740'
];

$backupJsonPath = 'C:\Users\Georg\Downloads\Skembedjis\public_html\site\soap\items.json';
$backupPicturesPath = 'C:\Users\Georg\Downloads\Skembedjis\public_html\wp-content\uploads\wpallimport\files\Pictures';
$localPicturesPath = __DIR__ . '/../Pictures';

if (!file_exists($backupJsonPath)) {
    die("Error: Backup items.json not found at $backupJsonPath\n");
}

$allItems = json_decode(file_get_contents($backupJsonPath), true) ?: [];
$itemsToSync = [];
$ignoredCount = 0;
$addedCount = 0;

function copy_directory($src, $dst) {
    if (!file_exists($src)) return false;
    @mkdir($dst, 0777, true);
    $dir = opendir($src);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                copy_directory($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
    return true;
}

echo "=== STARTING DEMO IMPORT FOR 15 TARGET MACHINES ===\n";

foreach ($codes as $code) {
    // Check if machine is already in database
    $stmt = $db->prepare("SELECT COUNT(*) FROM products WHERE item_code = :item_code");
    $stmt->execute([':item_code' => $code]);
    $exists = $stmt->fetchColumn() > 0;
    
    if ($exists) {
        echo "- Item $code: ALREADY EXISTS in database. Ignored.\n";
        $ignoredCount++;
        continue;
    }
    
    // Find item data in items.json
    $foundItem = null;
    foreach ($allItems as $item) {
        if (trim($item['ItemCode'] ?? '') === $code) {
            $foundItem = $item;
            break;
        }
    }
    
    // If not found in items.json, construct a realistic mock record
    if (!$foundItem) {
        echo "  * Item $code: Not found in items.json. Constructing realistic mock details...\n";
        $mockItem = [
            'ID' => uniqid(),
            'ItemCode' => $code,
            'Brand' => 'Generic',
            'Model' => 'N/A',
            'Capacity' => '2500kg',
            'Type' => 'Forklifts',
            'Condition' => 'Used',
            'Power' => 'Electric',
            'LiftHeightMast' => '3m Duplex',
            'Prices' => ['Price' => 8500],
            'FullDescription' => 'Reliable material handling machine. Tested and fully operational.',
            'Description' => 'Forklift truck'
        ];
        
        // Custom branding/specifications mapping based on code prefixes
        if ($code === '001-0J901403') {
            $mockItem['Brand'] = 'NISSAN';
            $mockItem['Model'] = 'L01A15W';
            $mockItem['Capacity'] = '1500kg';
            $mockItem['Power'] = 'Electric';
            $mockItem['Type'] = 'Forklifts';
        } elseif ($code === '001-2E40033' || $code === '001-2E400337') {
            $mockItem['Brand'] = 'TCM';
            $mockItem['Model'] = 'FRHB15-8';
            $mockItem['Capacity'] = '1500kg';
            $mockItem['Power'] = 'Electric';
            $mockItem['Type'] = 'Reach Trucks';
        } elseif ($code === '001-2EM01588') {
            $mockItem['Brand'] = 'BT';
            $mockItem['Model'] = 'SPE125';
            $mockItem['Capacity'] = '1200kg';
            $mockItem['Power'] = 'Electric';
            $mockItem['Type'] = 'Stackers';
        } elseif ($code === '001-3BP20193') {
            $mockItem['Brand'] = 'HELI';
            $mockItem['Model'] = 'CBD20';
            $mockItem['Capacity'] = '2000kg';
            $mockItem['Power'] = 'Electric';
            $mockItem['Type'] = 'Pallet Trucks';
        } elseif ($code === '001-4FD25-10675') {
            $mockItem['Brand'] = 'TOYOTA';
            $mockItem['Model'] = '6FD25';
            $mockItem['Capacity'] = '2500kg';
            $mockItem['Power'] = 'Diesel';
            $mockItem['Type'] = 'Forklifts';
        } elseif ($code === '001-6-FDF-18-21740') {
            $mockItem['Brand'] = 'TOYOTA';
            $mockItem['Model'] = '6FDF18';
            $mockItem['Capacity'] = '1800kg';
            $mockItem['Power'] = 'Diesel';
            $mockItem['Type'] = 'Forklifts';
        }
        
        $foundItem = $mockItem;
    }
    
    // Copy images from downloads backup directory if local directory doesn't exist
    $srcDir = $backupPicturesPath . '/' . $code;
    $dstDir = $localPicturesPath . '/' . $code;
    
    if (file_exists($srcDir)) {
        if (!file_exists($dstDir) || count(glob($dstDir . '/*')) === 0) {
            echo "  * Copying images from backup to local folder for $code...\n";
            copy_directory($srcDir, $dstDir);
        }
    } else {
        echo "  * Warning: Backup image folder not found for $code!\n";
    }
    
    // Dynamically scan the copied files and build the 'Photos' collection payload
    $photos = [];
    $files = glob($dstDir . '/*');
    $relDir = "Pictures/$code/";
    
    // Check if there is a PHOTOS subfolder
    if (is_dir($dstDir . '/PHOTOS')) {
        $files = glob($dstDir . '/PHOTOS/*');
        $relDir = "Pictures/$code/PHOTOS/";
    }
    
    $index = 0;
    foreach ($files as $file) {
        if (is_file($file)) {
            $baseName = basename($file);
            $ext = strtolower(pathinfo($baseName, PATHINFO_EXTENSION));
            if ($ext === 'jpg' || $ext === 'jpeg' || $ext === 'png') {
                $photos[] = [
                    'ID' => uniqid(),
                    'Name' => $baseName,
                    'Directory' => $relDir,
                    'isMain' => ($index === 0) ? '1' : ''
                ];
                $index++;
            }
        }
    }
    
    $foundItem['Photos'] = $photos;
    $itemsToSync[] = $foundItem;
    $addedCount++;
}

echo "\nProcessing sync for " . count($itemsToSync) . " new machines...\n";

if (count($itemsToSync) > 0) {
    // Run incremental products sync (which will automatically copy/convert photos and write records to DB)
    $res = run_products_sync($itemsToSync);
    echo "Sync Outcome: " . $res['message'] . "\n";
} else {
    echo "No new machines to add.\n";
}

echo "\nSummary: Added: $addedCount | Ignored: $ignoredCount\n";
echo "=== DEMO IMPORT COMPLETED ===\n";

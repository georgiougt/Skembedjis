<?php
// sync_products.php
// Synchronizes sqlite products table with items.json: inserts new, updates existing, and deletes removed.

ini_set('memory_limit', '512M');
set_time_limit(300);

function run_products_sync($customItems = null, $offset = 0, $limit = null) {
    $dbPath = __DIR__ . '/db/site.db';
    $jsonPath = __DIR__ . '/items.json';
    
    if (!file_exists($dbPath)) {
        return ['success' => false, 'message' => 'Database file not found.'];
    }
    if ($customItems === null && !file_exists($jsonPath)) {
        return ['success' => false, 'message' => 'items.json file not found.'];
    }

    try {
        $db = new PDO('sqlite:' . $dbPath);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->exec("PRAGMA busy_timeout = 5000;");

        if ($customItems !== null) {
            $items = $customItems;
        } else {
            $items = json_decode(file_get_contents($jsonPath), true);
            if (!is_array($items)) {
                return ['success' => false, 'message' => 'Failed to parse items.json.'];
            }
        }

        $totalItemsCount = count($items);
        if ($limit !== null) {
            $items = array_slice($items, $offset, $limit);
        }

        // Helper functions
        $getCategoryId = function($type, $description) {
            $type = strtolower(trim($type ?? ''));
            $desc = strtolower(trim($description ?? ''));
            
            if (strpos($type, 'forklift') !== false || strpos($type, 'ανυψωτικ') !== false) {
                if (strpos($type, 'vna') !== false || strpos($desc, 'vna') !== false) {
                    return 2; // VNA
                }
                if (strpos($type, 'reach') !== false || strpos($desc, 'reach') !== false) {
                    return 12; // Reach Trucks
                }
                return 1; // Forklifts
            }
            if (strpos($type, 'vna') !== false) return 2;
            if (strpos($type, 'stacker') !== false || strpos($type, 'στοιβακτ') !== false) return 3;
            if (strpos($type, 'pallet') !== false || strpos($type, 'παλετοφορ') !== false) return 4;
            if (strpos($type, 'order picker') !== false || strpos($type, 'συλλεκτ') !== false) return 5;
            if (strpos($type, 'cleaning') !== false || strpos($type, 'καθαρισμ') !== false) return 6;
            if (strpos($type, 'ramp') !== false || strpos($type, 'ραμπ') !== false) return 7;
            if (strpos($type, 'batter') !== false || strpos($type, 'charger') !== false || strpos($type, 'μπαταρ') !== false || strpos($type, 'φορτιστ') !== false) return 8;
            if (strpos($type, 'truck mounted') !== false) return 9;
            if (strpos($type, 'handling') !== false) return 10;
            if (strpos($type, 'attachment') !== false || strpos($type, 'εξαρτημ') !== false) return 11;
            if (strpos($type, 'reach') !== false) return 12;
            if (strpos($type, 'tyre') !== false || strpos($type, 'wheel') !== false || strpos($type, 'ελαστικ') !== false) return 13;
            
            if (strpos($desc, 'forklift') !== false) return 1;
            if (strpos($desc, 'stacker') !== false) return 3;
            if (strpos($desc, 'pallet') !== false) return 4;
            if (strpos($desc, 'tyre') !== false || strpos($desc, 'tire') !== false) return 13;
            if (strpos($desc, 'battery') !== false) return 8;
            if (strpos($desc, 'ramp') !== false) return 7;
            
            return 10; // Default
        };

        $createSlug = function($string, $id) {
            $slug = strtolower(trim($string));
            $slug = preg_replace('/[^a-z0-9\-]/', '-', $slug);
            $slug = preg_replace('/-+/', '-', $slug);
            $slug = trim($slug, '-');
            if (empty($slug)) {
                $slug = 'item-' . $id;
            }
            return $slug;
        };

        $formatMachineName = function($item) {
            $cleanItemCode = ltrim(str_replace('001-', '', $item['ItemCode']), ' ');
            $brand = !empty($item['Brand']) ? trim($item['Brand']) : '';
            $model = !empty($item['Model']) ? trim($item['Model']) : '';
            
            $condition = '';
            if (!empty($item['Condition'])) {
                $cond = strtolower(trim($item['Condition']));
                if ($cond === 'new' || $cond === 'brand new' || strpos($cond, 'new') !== false) {
                    $condition = 'Brand New';
                } else {
                    $condition = 'Used';
                }
            }
            
            $type = !empty($item['Type']) ? trim($item['Type']) : '';
            
            if (!empty($brand) && !empty($model)) {
                $parts = [];
                if (!empty($condition)) $parts[] = $condition;
                $parts[] = $brand;
                if (!empty($type)) $parts[] = $type;
                $parts[] = $model;
                
                $name = implode(' ', $parts);
                $name .= ' – ' . $cleanItemCode;
                return $name;
            }
            
            $desc = trim($item['Description']);
            if (preg_match('/Capacity:\s*(.*)$/i', $desc)) {
                $desc = preg_replace('/\s*-\s*Capacity:\s*.*$/i', '', $desc);
                $desc = preg_replace('/Capacity:\s*.*$/i', '', $desc);
                $desc = trim($desc);
            }
            if (!empty($cleanItemCode) && stripos($desc, $cleanItemCode) === false) {
                $desc .= ' – ' . $cleanItemCode;
            }
            
            if (!empty($condition) && stripos($desc, $condition) === false) {
                $desc = $condition . ' ' . $desc;
            }
            return $desc;
        };

        $convertToWebp = function($sourcePath, $destinationPath, $force = false) {
            if (!file_exists($sourcePath)) return false;
            if (!$force && file_exists($destinationPath) && filesize($destinationPath) > 0) return true;
            
            $info = @getimagesize($sourcePath);
            if (!$info) return false;
            
            $mime = strtolower($info['mime']);
            $image = null;
            
            if ($mime === 'image/jpeg' || $mime === 'image/jpg') {
                $image = @imagecreatefromjpeg($sourcePath);
                if ($image && function_exists('exif_read_data')) {
                    $exif = @exif_read_data($sourcePath);
                    if (!empty($exif['Orientation'])) {
                        switch ($exif['Orientation']) {
                            case 3:
                                $image = imagerotate($image, 180, 0);
                                break;
                            case 6:
                                $image = imagerotate($image, -90, 0);
                                break;
                            case 8:
                                $image = imagerotate($image, 90, 0);
                                break;
                        }
                    }
                }
            } elseif ($mime === 'image/png') {
                $image = @imagecreatefrompng($sourcePath);
                if ($image) {
                    imagepalettetotruecolor($image);
                    imagealphablending($image, false);
                    imagesavealpha($image, true);
                }
            } elseif ($mime === 'image/webp') {
                return @copy($sourcePath, $destinationPath);
            }
            
            if ($image) {
                $result = @imagewebp($image, $destinationPath, 85);
                imagedestroy($image);
                return $result;
            }
            return false;
        };

        // Prepare statements
        $stmtCheck = $db->prepare("SELECT id, slug FROM products WHERE item_code = :item_code");
        
        $stmtInsert = $db->prepare("
            INSERT INTO products (
                category_id, name, slug, status, brand, description, 
                lifting_capacity, mast_type, energy, photo_path, warranty, 
                price, model, lift_height, attachment, gallery_images, item_code
            ) VALUES (
                :category_id, :name, :slug, :status, :brand, :description, 
                :lifting_capacity, :mast_type, :energy, :photo_path, :warranty, 
                :price, :model, :lift_height, :attachment, :gallery_images, :item_code
            )
        ");

        $stmtUpdate = $db->prepare("
            UPDATE products SET 
                category_id = :category_id, 
                name = :name, 
                slug = :slug, 
                status = :status, 
                brand = :brand, 
                description = :description, 
                lifting_capacity = :lifting_capacity, 
                mast_type = :mast_type, 
                energy = :energy, 
                photo_path = :photo_path, 
                warranty = :warranty, 
                price = :price, 
                model = :model, 
                lift_height = :lift_height, 
                attachment = :attachment, 
                gallery_images = :gallery_images
            WHERE item_code = :item_code
        ");

        // Fetch flag setting from site_settings if it exists
        $stmtFlag = $db->prepare("SELECT value FROM site_settings WHERE key = 'onec_web_flag'");
        $stmtFlag->execute();
        $flagName = trim($stmtFlag->fetchColumn() ?: '');

        $activeItemCodes = [];
        $added = 0;
        $updated = 0;
        $deleted = 0;
        $slugMap = [];

        $addedItems = [];
        $updatedItems = [];
        $removedItems = [];

        $db->beginTransaction();

        foreach ($items as $item) {
            if (($item['Deletion'] ?? false) === true) continue;
            if (empty($item['Description']) || empty($item['ItemCode'])) continue;

            // Check dynamic publication flag from 1C
            if (!empty($flagName) && isset($item[$flagName])) {
                $isWebVisible = filter_var($item[$flagName], FILTER_VALIDATE_BOOLEAN);
                if (!$isWebVisible) {
                    continue; // Skip this product (will be treated as deleted from website)
                }
            }

            $itemCode = trim($item['ItemCode']);
            $activeItemCodes[] = $itemCode;

            $name = $formatMachineName($item);
            
            // Re-generate slug
            $slug = $createSlug($name, $item['ID']);
            if (isset($slugMap[$slug])) {
                $slugMap[$slug]++;
                $slug .= '-' . $slugMap[$slug];
            } else {
                $slugMap[$slug] = 1;
            }

            $categoryId = $getCategoryId($item['Type'] ?? '', $item['Description'] . ' ' . ($item['FullDescription'] ?? ''));
            if (in_array($categoryId, [6, 7, 8, 10, 11, 13, 14])) {
                continue;
            }
            
            $status = 'Used';
            if (isset($item['Condition'])) {
                $cond = strtolower(trim($item['Condition']));
                if ($cond === 'new' || $cond === 'brand new' || strpos($cond, 'new') !== false) {
                    $status = 'New';
                }
            }

            $brand = !empty($item['Brand']) ? trim($item['Brand']) : 'Generic';
            $description = !empty($item['FullDescription']) ? trim($item['FullDescription']) : 'No additional description provided.';
            $capacity = !empty($item['Capacity']) ? trim($item['Capacity']) : 'N/A';
            $mastType = !empty($item['LiftHeightMast']) ? trim($item['LiftHeightMast']) : 'N/A';
            $energy = !empty($item['Power']) ? trim($item['Power']) : 'N/A';
            $price = isset($item['Prices']['Price']) ? (int)$item['Prices']['Price'] : 0;
            $model = !empty($item['Model']) ? trim($item['Model']) : 'N/A';
            $attachment = !empty($item['Attachment']) ? trim($item['Attachment']) : 'None';
            
            $warranty = '6 Months Warranty';
            if (strpos(strtolower($description), 'one year') !== false || strpos(strtolower($description), '1 year') !== false) {
                $warranty = '1 Year Warranty';
            } elseif (strpos(strtolower($description), 'two year') !== false || strpos(strtolower($description), '2 year') !== false) {
                $warranty = '2 Years Warranty';
            }

            // Map and convert photos
            $photoPath = 'placeholder.php?text=No+Photo';
            $galleryImages = [];
            
            if (!empty($item['Photos']) && is_array($item['Photos'])) {
                foreach ($item['Photos'] as $photo) {
                    if (empty($photo['Name']) || empty($photo['Directory'])) continue;

                    $relDir = str_replace('\\', '/', $photo['Directory']);
                    $relDir = trim($relDir, '/');
                    $photoName = $photo['Name'];
                    
                    // Map directory to the user's custom production path if it starts with "Pictures"
                    $serverRelDir = $relDir;
                    if (stripos($relDir, 'pictures') === 0) {
                        $serverRelDir = preg_replace('/^pictures/i', '../wp-content/uploads/wpallimport/files/Pictures', $relDir);
                    }
                    $sourceFullPath = __DIR__ . '/' . $serverRelDir . '/' . $photoName;
                    
                    // Fallback to local path if custom server path doesn't exist
                    if (!file_exists($sourceFullPath)) {
                        $sourceFullPath = __DIR__ . '/' . $relDir . '/' . $photoName;
                    }
                    
                    // Destination filename ends with .webp, saved inside website's Pictures directory
                    $nameParts = pathinfo($photoName);
                    $destName = $nameParts['filename'] . '.webp';
                    $destFullPath = __DIR__ . '/' . $relDir . '/' . $destName;
                    $destRelPath = $relDir . '/' . $destName;

                    // Ensure target directory exists
                    $destDir = dirname($destFullPath);
                    if (!is_dir($destDir)) {
                        @mkdir($destDir, 0755, true);
                    }

                    // Convert to webp
                    $success = $convertToWebp($sourceFullPath, $destFullPath);
                    if ($success && file_exists($destFullPath)) {
                        if ($photo['isMain'] ?? false) {
                            $photoPath = $destRelPath;
                        }
                        $galleryImages[] = $destRelPath;
                    } else {
                        if (file_exists($sourceFullPath)) {
                            // Copy the original file to target directory so it is accessible on the new site
                            $origDestFullPath = __DIR__ . '/' . $relDir . '/' . $photoName;
                            if (!file_exists($origDestFullPath)) {
                                @copy($sourceFullPath, $origDestFullPath);
                            }
                            
                            if ($photo['isMain'] ?? false) {
                                $photoPath = $relDir . '/' . $photoName;
                            }
                            $galleryImages[] = $relDir . '/' . $photoName;
                        }
                    }
                }
            }

            if (($photoPath === 'placeholder.php?text=No+Photo') && !empty($galleryImages)) {
                $photoPath = $galleryImages[0];
            }

            $galleryCsv = implode(',', $galleryImages);

            // Check if item already exists
            $stmtCheck->execute([':item_code' => $itemCode]);
            $existing = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                $stmtUpdate->execute([
                    ':category_id' => $categoryId,
                    ':name' => $name,
                    ':slug' => $existing['slug'],
                    ':status' => $status,
                    ':brand' => $brand,
                    ':description' => $description,
                    ':lifting_capacity' => $capacity,
                    ':mast_type' => $mastType,
                    ':energy' => $energy,
                    ':photo_path' => $photoPath,
                    ':warranty' => $warranty,
                    ':price' => $price,
                    ':model' => $model,
                    ':lift_height' => $mastType,
                    ':attachment' => $attachment,
                    ':gallery_images' => $galleryCsv,
                    ':item_code' => $itemCode
                ]);
                $updated++;
                $updatedItems[] = ['code' => $itemCode, 'name' => $name];
            } else {
                $stmtInsert->execute([
                    ':category_id' => $categoryId,
                    ':name' => $name,
                    ':slug' => $slug,
                    ':status' => $status,
                    ':brand' => $brand,
                    ':description' => $description,
                    ':lifting_capacity' => $capacity,
                    ':mast_type' => $mastType,
                    ':energy' => $energy,
                    ':photo_path' => $photoPath,
                    ':warranty' => $warranty,
                    ':price' => $price,
                    ':model' => $model,
                    ':lift_height' => $mastType,
                    ':attachment' => $attachment,
                    ':gallery_images' => $galleryCsv,
                    ':item_code' => $itemCode
                ]);
                $added++;
                $addedItems[] = ['code' => $itemCode, 'name' => $name];
            }
        }

        // Delete removed items (and track them first) - ONLY if running full catalog sync and we reached the end
        if ($customItems === null && ($limit === null || $offset + $limit >= $totalItemsCount)) {
            $allJson = json_decode(file_get_contents($jsonPath), true);
            $validCodes = [];
            if (is_array($allJson)) {
                foreach ($allJson as $jItem) {
                    if (!empty($jItem['ItemCode'])) {
                        $validCodes[] = trim($jItem['ItemCode']);
                    }
                }
            }
            
            if (!empty($validCodes)) {
                $inClause = implode(',', array_fill(0, count($validCodes), '?'));
                
                // Get names/codes of products to delete (excluding manually managed categories)
                $stmtGetRemoved = $db->prepare("
                    SELECT item_code as code, name FROM products 
                    WHERE item_code IS NOT NULL 
                      AND item_code != '' 
                      AND category_id NOT IN (6, 7, 8, 10, 11, 13, 14)
                      AND item_code NOT IN ($inClause)
                ");
                $stmtGetRemoved->execute($validCodes);
                $removedItems = $stmtGetRemoved->fetchAll(PDO::FETCH_ASSOC);

                $stmtDelete = $db->prepare("
                    DELETE FROM products 
                    WHERE item_code IS NOT NULL 
                      AND item_code != '' 
                      AND category_id NOT IN (6, 7, 8, 10, 11, 13, 14)
                      AND item_code NOT IN ($inClause)
                ");
                $stmtDelete->execute($validCodes);
                $deleted = $stmtDelete->rowCount();
            }
        }

        $isFinal = ($customItems === null && ($limit === null || $offset + $limit >= $totalItemsCount));

        if ($isFinal) {
            // Update the last sync file timestamp
            file_put_contents(__DIR__ . '/db/last_sync.txt', filemtime($jsonPath));

            $msg = "Synchronization completed. Added: {$added}, Updated: {$updated}, Deleted: {$deleted} products.";
            
            // Log sync action to DB
            $sourceType = (debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'] ?? '') === 'pull_soap_and_sync' ? 'SOAP' : 'JSON';
            
            $stmtLog = $db->prepare("
                INSERT INTO sync_logs (status, source, message, added_items, removed_items, updated_items)
                VALUES (:status, :source, :message, :added, :removed, :updated)
            ");
            $stmtLog->execute([
                ':status' => 'Success',
                ':source' => $sourceType,
                ':message' => $msg,
                ':added' => json_encode($addedItems, JSON_UNESCAPED_UNICODE),
                ':removed' => json_encode($removedItems, JSON_UNESCAPED_UNICODE),
                ':updated' => json_encode($updatedItems, JSON_UNESCAPED_UNICODE)
            ]);
        } else {
            $msg = "Synchronized chunk: Offset {$offset}, processed " . count($items) . " items.";
        }

        $db->commit();

        return [
            'success' => true,
            'message' => $msg,
            'is_final' => $isFinal
        ];

    } catch (PDOException $e) {
        if (isset($db) && $db->inTransaction()) {
            $db->rollBack();
        }
        // Log failed database execution state
        try {
            $stmtLog = $db->prepare("
                INSERT INTO sync_logs (status, source, message, added_items, removed_items, updated_items)
                VALUES (:status, :source, :message, :added, :removed, :updated)
            ");
            $stmtLog->execute([
                ':status' => 'Failed',
                ':source' => 'DB',
                ':message' => 'Database error during execution: ' . $e->getMessage(),
                ':added' => '[]',
                ':removed' => '[]',
                ':updated' => '[]'
            ]);
        } catch (Exception $logEx) {}
        
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}

function pull_soap_and_save_json() {
    $dbPath = __DIR__ . '/db/site.db';
    
    try {
        $db = new PDO('sqlite:' . $dbPath);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->exec("PRAGMA busy_timeout = 5000;");

        // Fetch settings dynamically
        $stmtW = $db->prepare("SELECT value FROM site_settings WHERE key = 'onec_wsdl'");
        $stmtW->execute();
        $wsdl = trim($stmtW->fetchColumn() ?: 'http://213.7.198.218:8080/SKEMBEDJIS/ws/ws1.1cws?wsdl');

        $stmtL = $db->prepare("SELECT value FROM site_settings WHERE key = 'onec_login'");
        $stmtL->execute();
        $login = trim($stmtL->fetchColumn() ?: 'ecommerce');

        $stmtP = $db->prepare("SELECT value FROM site_settings WHERE key = 'onec_password'");
        $stmtP->execute();
        $password = trim($stmtP->fetchColumn() ?: '3c0mm3rc3*');

        if (!class_exists('SoapClient')) {
            return ['success' => false, 'message' => 'PHP SOAP extension is not enabled on this server.'];
        }

        // Force WSDL bypass cache by appending timestamp query argument
        $nocacheWsdl = $wsdl . (strpos($wsdl, '?') !== false ? '&' : '?') . 'nocache=' . time();

        $soap = new SoapClient($nocacheWsdl, [
            'login'    => $login,
            'password' => $password,
            'trace'    => 1,
            'connection_timeout' => 15,
            'cache_wsdl' => WSDL_CACHE_NONE // Disable PHP SoapClient local WSDL cache
        ]);

        $all = [];
        $page = 1;
        do {
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
            return ['success' => false, 'message' => 'Connected to SOAP, but no items were returned.'];
        }

        // Save fetched items locally to items.json as backup/cache
        $jsonPath = __DIR__ . '/items.json';
        file_put_contents($jsonPath, json_encode($all, JSON_PRETTY_PRINT));

        return [
            'success' => true,
            'message' => 'Successfully fetched SOAP catalog and saved to items.json.',
            'total_items' => count($all)
        ];

    } catch (Exception $e) {
        // Log connection failure to database
        try {
            $dbLog = new PDO('sqlite:' . $dbPath);
            $stmtLog = $dbLog->prepare("
                INSERT INTO sync_logs (status, source, message, added_items, removed_items, updated_items)
                VALUES (:status, :source, :message, :added, :removed, :updated)
            ");
            $stmtLog->execute([
                ':status' => 'Failed',
                ':source' => 'SOAP',
                ':message' => 'Failed to connect to 1C SOAP service: ' . $e->getMessage(),
                ':added' => '[]',
                ':removed' => '[]',
                ':updated' => '[]'
            ]);
        } catch (Exception $logEx) {}

        return [
            'success' => false,
            'message' => 'Failed to connect to 1C SOAP service: ' . $e->getMessage()
        ];
    }
}

function pull_soap_and_sync() {
    $res = pull_soap_and_save_json();
    if (!$res['success']) {
        return $res;
    }
    return run_products_sync();
}

// If run from command line directly, execute it
if (php_sapi_name() === 'cli') {
    $args = getopt('', ['source::']);
    $source = $args['source'] ?? 'json';
    
    if ($source === 'soap') {
        echo "Running SOAP pulling and sync...\n";
        $result = pull_soap_and_sync();
    } else {
        echo "Running local JSON sync...\n";
        $result = run_products_sync();
    }
    echo $result['message'] . "\n";
}

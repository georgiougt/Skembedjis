<?php
// sync_products.php
// Synchronizes sqlite products table with items.json: inserts new, updates existing, and deletes removed.

ini_set('memory_limit', '512M');
set_time_limit(300);

function run_products_sync() {
    $dbPath = __DIR__ . '/db/site.db';
    $jsonPath = __DIR__ . '/items.json';
    
    if (!file_exists($dbPath)) {
        return ['success' => false, 'message' => 'Database file not found.'];
    }
    if (!file_exists($jsonPath)) {
        return ['success' => false, 'message' => 'items.json file not found.'];
    }

    try {
        $db = new PDO('sqlite:' . $dbPath);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $items = json_decode(file_get_contents($jsonPath), true);
        if (!is_array($items)) {
            return ['success' => false, 'message' => 'Failed to parse items.json.'];
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
            $capacity = !empty($item['Capacity']) ? trim($item['Capacity']) : '';
            
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
                if (!empty($capacity)) {
                    $name .= ' - Capacity: ' . $capacity;
                }
                return $name;
            }
            
            $desc = trim($item['Description']);
            if (preg_match('/Capacity:\s*(.*)$/i', $desc, $matches)) {
                $replacement = '– ' . $cleanItemCode . ' - Capacity: ' . $matches[1];
                $desc = preg_replace('/Capacity:\s*(.*)$/i', $replacement, $desc);
            } else {
                $desc .= ' – ' . $cleanItemCode;
            }
            
            if (!empty($condition) && stripos($desc, $condition) === false) {
                $desc = $condition . ' ' . $desc;
            }
            return $desc;
        };

        $convertToWebp = function($sourcePath, $destinationPath) {
            if (!file_exists($sourcePath)) return false;
            if (file_exists($destinationPath) && filesize($destinationPath) > 0) return true;
            
            $info = @getimagesize($sourcePath);
            if (!$info) return false;
            
            $mime = strtolower($info['mime']);
            $image = null;
            
            if ($mime === 'image/jpeg' || $mime === 'image/jpg') {
                $image = @imagecreatefromjpeg($sourcePath);
            } elseif ($mime === 'image/png') {
                $image = @imagecreatefrompng($sourcePath);
                if ($image) {
                    imagepalettetotruecolor($image);
                    imagealphavying($image, true);
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

        $activeItemCodes = [];
        $added = 0;
        $updated = 0;
        $deleted = 0;
        $slugMap = [];

        foreach ($items as $item) {
            if (($item['Deletion'] ?? false) === true) continue;
            if (empty($item['Description']) || empty($item['ItemCode'])) continue;

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
                    
                    $sourceFullPath = __DIR__ . '/' . $relDir . '/' . $photoName;
                    
                    // Destination filename ends with .webp
                    $nameParts = pathinfo($photoName);
                    $destName = $nameParts['filename'] . '.webp';
                    $destFullPath = __DIR__ . '/' . $relDir . '/' . $destName;
                    $destRelPath = $relDir . '/' . $destName;

                    // Convert to webp
                    $success = $convertToWebp($sourceFullPath, $destFullPath);
                    if ($success && file_exists($destFullPath)) {
                        if ($photo['isMain'] ?? false) {
                            $photoPath = $destRelPath;
                        }
                        $galleryImages[] = $destRelPath;
                    } else {
                        if (file_exists($sourceFullPath)) {
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
                // Keep the existing slug to prevent breaking SEO/bookmarks unless name changed drastically
                $stmtUpdate->execute([
                    ':category_id' => $categoryId,
                    ':name' => $name,
                    ':slug' => $existing['slug'], // Use existing slug
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
            } else {
                // Insert new
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
            }
        }

        // Delete removed items
        if (!empty($activeItemCodes)) {
            $inClause = implode(',', array_fill(0, count($activeItemCodes), '?'));
            $stmtDelete = $db->prepare("
                DELETE FROM products 
                WHERE item_code IS NOT NULL 
                  AND item_code != '' 
                  AND item_code NOT IN ($inClause)
            ");
            $stmtDelete->execute($activeItemCodes);
            $deleted = $stmtDelete->rowCount();
        }

        // Update the last sync file timestamp
        file_put_contents(__DIR__ . '/db/last_sync.txt', filemtime($jsonPath));

        return [
            'success' => true,
            'message' => "Synchronization completed. Added: {$added}, Updated: {$updated}, Deleted: {$deleted} products."
        ];

    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}

// If run from command line directly, execute it
if (php_sapi_name() === 'cli') {
    $result = run_products_sync();
    echo $result['message'] . "\n";
}

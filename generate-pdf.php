<?php
// generate-pdf.php
// Generates a print-optimized brochure matching the product details layout screenshots exactly.

require_once __DIR__ . '/db.php';

$slug = trim($_GET['slug'] ?? '');
if (empty($slug)) {
    die("Product slug not provided.");
}

// Fetch primary product
$stmt = $db->prepare("
    SELECT p.*, c.name as category_name, c.slug as category_slug 
    FROM products p 
    JOIN product_categories c ON p.category_id = c.id
    WHERE p.slug = :slug
");
$stmt->execute([':slug' => $slug]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Product not found.");
}

// Parse gallery images
$gallery = [];
if (!empty($product['gallery_images'])) {
    $gallery = explode(',', $product['gallery_images']);
}
// Add main image as first item in gallery if not already present
if (!in_array($product['photo_path'], $gallery)) {
    array_unshift($gallery, $product['photo_path']);
}

// Build category labels
$category_labels = [];
if ($product['status'] === 'New') {
    $category_labels[] = 'New';
} else {
    $category_labels[] = 'Used';
}
if (!empty($product['energy']) && $product['energy'] !== 'N/A') {
    $category_labels[] = $product['energy'];
}
if (!empty($product['category_name'])) {
    $category_labels[] = $product['category_name'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brochure - <?php echo htmlspecialchars($product['name']); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-blue: #0284c7;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --border-gray: #e2e8f0;
            --bg-gray: #f8fafc;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Open Sans', sans-serif;
            color: var(--text-dark);
            background: #ffffff;
            line-height: 1.5;
            padding: 2rem;
            max-width: 900px;
            margin: 0 auto;
        }

        /* Print Controls Panel */
        .print-controls {
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 1rem 1.5rem;
            border-radius: 6px;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-print {
            background: var(--primary-blue);
            color: #ffffff;
            border: none;
            padding: 0.5rem 1.5rem;
            font-weight: 700;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.95rem;
            transition: opacity 0.15s;
        }

        .btn-print:hover {
            opacity: 0.9;
        }

        .btn-back {
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .btn-back:hover {
            color: var(--text-dark);
        }

        /* PAGE 1: Product Header & Main Image */
        .page-break {
            page-break-after: always;
            margin-bottom: 3rem;
        }

        .product-title {
            text-align: center;
            font-size: 1.4rem;
            font-weight: 800;
            color: #000000;
            text-transform: uppercase;
            margin-bottom: 2rem;
            line-height: 1.4;
            letter-spacing: 0.5px;
        }

        .main-image-container {
            background: #f1f3f5;
            height: 500px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            margin-bottom: 2rem;
            border: 1px solid var(--border-gray);
        }

        .main-image-container img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .product-price {
            text-align: center;
            font-size: 2.2rem;
            font-weight: 800;
            color: #000000;
            margin-bottom: 1rem;
        }

        .product-meta {
            text-align: center;
            font-size: 0.9rem;
            color: #000000;
            font-weight: 700;
            margin-bottom: 2rem;
        }

        .product-meta a {
            color: #2563eb;
            text-decoration: underline;
        }

        /* PAGE 2: Gallery & Description */
        .section-title {
            text-align: center;
            font-size: 1.25rem;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
            margin-top: 2rem;
            letter-spacing: 0.5px;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .gallery-item {
            border: 1px solid var(--border-gray);
            border-radius: 4px;
            height: 160px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: #ffffff;
        }

        .gallery-item img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .product-description-content {
            text-align: center;
            font-size: 0.95rem;
            line-height: 1.7;
            color: #1e293b;
            font-weight: 500;
            white-space: pre-line;
            max-width: 700px;
            margin: 0 auto 2rem;
        }

        /* PAGE 3: Additional Information */
        .specs-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
            font-size: 0.95rem;
        }

        .specs-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border-gray);
        }

        .specs-table td.label-col {
            font-weight: 700;
            color: #000000;
            width: 40%;
            text-align: left;
        }

        .specs-table td.value-col {
            color: #2563eb;
            font-weight: 600;
            width: 60%;
            text-align: left;
        }

        .specs-table td.value-col a {
            color: #2563eb;
            text-decoration: underline;
        }

        /* Hide elements on Print */
        @media print {
            .print-controls {
                display: none !important;
            }
            body {
                padding: 0;
            }
            .page-break {
                page-break-after: always;
                margin-bottom: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Controls Panel (Hidden in PDF Print) -->
    <div class="print-controls">
        <a href="product-detail.php?slug=<?php echo urlencode($product['slug']); ?>" class="btn-back">
            ← Back to Product Profile
        </a>
        <div>
            <span style="font-size: 0.85rem; color: var(--text-muted); margin-right: 1rem;">
                Click Print to download as PDF
            </span>
            <button onclick="window.print()" class="btn-print">Print PDF</button>
        </div>
    </div>

    <!-- First Page Block -->
    <div class="page-break">
        <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
        
        <div class="main-image-container">
            <img src="<?php echo htmlspecialchars($product['photo_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>

        <div class="product-price">
            <?php echo $product['price'] > 0 ? number_format($product['price'], 0, ',', '.') . ',00 €' : 'Inquire Price'; ?>
        </div>

        <div class="product-meta">
            SKU: <?php echo htmlspecialchars($product['item_code']); ?> | Categories: 
            <?php foreach ($category_labels as $index => $lbl): ?>
                <a href="#"><?php echo htmlspecialchars($lbl); ?></a><?php echo ($index < count($category_labels) - 1) ? ', ' : ' |'; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Second Page Block -->
    <div class="page-break">
        <!-- Gallery thumbnails grid (excluding main photo to show alternative angles) -->
        <?php if (count($gallery) > 1): ?>
            <div class="gallery-grid">
                <?php 
                $shownCount = 0;
                foreach ($gallery as $imgUrl): 
                    if ($imgUrl === $product['photo_path']) continue; // Show other angles
                    if ($shownCount >= 6) break; // Limit grid size to match screenshot
                ?>
                    <div class="gallery-item">
                        <img src="<?php echo htmlspecialchars($imgUrl); ?>" alt="Alternative angle">
                    </div>
                <?php 
                    $shownCount++;
                endforeach; 
                ?>
            </div>
        <?php endif; ?>

        <h2 class="section-title">Product Description</h2>
        
        <div class="product-description-content"><?php echo htmlspecialchars($product['description']); ?></div>
    </div>

    <!-- Third Page Block -->
    <div>
        <h2 class="section-title">Additional Information</h2>
        
        <table class="specs-table">
            <tbody>
                <tr>
                    <td class="label-col">Brand</td>
                    <td class="value-col"><a href="#"><?php echo htmlspecialchars($product['brand']); ?></a></td>
                </tr>
                <tr>
                    <td class="label-col">Power</td>
                    <td class="value-col"><a href="#"><?php echo htmlspecialchars($product['energy']); ?></a></td>
                </tr>
                <tr>
                    <td class="label-col">Capacity</td>
                    <td class="value-col"><a href="#"><?php echo htmlspecialchars($product['lifting_capacity']); ?></a></td>
                </tr>
                <tr>
                    <td class="label-col">Type</td>
                    <td class="value-col"><a href="#"><?php echo htmlspecialchars($product['category_name']); ?></a></td>
                </tr>
                <tr>
                    <td class="label-col">Condition</td>
                    <td class="value-col"><a href="#"><?php echo ($product['status'] === 'New') ? 'Brand New' : 'Used'; ?></a></td>
                </tr>
                <tr>
                    <td class="label-col">Model</td>
                    <td class="value-col"><a href="#"><?php echo htmlspecialchars($product['model']); ?></a></td>
                </tr>
                <?php if (!empty($product['attachment']) && $product['attachment'] !== 'N/A' && $product['attachment'] !== 'None'): ?>
                    <tr>
                        <td class="label-col">Attachment</td>
                        <td class="value-col"><a href="#"><?php echo htmlspecialchars($product['attachment']); ?></a></td>
                    </tr>
                <?php endif; ?>
                <?php if (!empty($product['lift_height']) && $product['lift_height'] !== 'N/A'): ?>
                    <tr>
                        <td class="label-col">Lift Height Capacity</td>
                        <td class="value-col"><a href="#"><?php echo htmlspecialchars($product['lift_height']); ?></a></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Automatically open browser print dialog immediately when page is fully loaded -->
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            // Short timeout to allow image rendering, then trigger print
            setTimeout(() => {
                window.print();
            }, 800);
        });
    </script>
</body>
</html>

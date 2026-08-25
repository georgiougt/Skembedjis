<?php
// catalog.php
// Recreated Premium Products Catalog with Horizontal Filters Bar, Live Search, and Detail Modals

require_once __DIR__ . '/db.php';

// Retrieve filters from GET query or pre-set controller variables
$selected_category = trim($_GET['category'] ?? $preset_category ?? '');
$selected_status = trim($_GET['status'] ?? $preset_status ?? '');
$selected_brand = trim($_GET['brand'] ?? $preset_brand ?? '');
$selected_capacity = trim($_GET['capacity'] ?? '');
$selected_energy = trim($_GET['energy'] ?? '');
$min_price = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (int)$_GET['max_price'] : 62000;
$search_query = trim($_GET['search'] ?? '');

// Fetch products based on filters
$filters = [
    'category' => $selected_category,
    'status' => $selected_status,
    'brand' => $selected_brand,
    'capacity' => $selected_capacity,
    'energy' => $selected_energy,
    'min_price' => $min_price,
    'max_price' => $max_price,
    'search' => $search_query
];

$products = get_products($filters);
$categories = get_product_categories();
$brands = get_product_brands();
$capacities = get_product_capacities();
$energies = get_product_energies();

require_once __DIR__ . '/header.php';
?>

    <!-- Page Title Header Block -->
    <section class="page-header-block">
        <div class="container">
            <div class="divider-line centered"></div>
            <h2>Equipment & Products</h2>
            <p style="text-align: center; color: var(--text-muted); font-size: 0.95rem; margin-top: 0.5rem;">
                Explore our industry-leading range of new and pre-owned material handling machinery.
            </p>
        </div>
    </section>

    <!-- Horizontal Filters Bar Section -->
    <section class="hz-filters-section" style="background-color: #eaeaea; padding: 1.5rem 0; border-top: 1px solid rgba(0,0,0,0.05); border-bottom: 1px solid rgba(0,0,0,0.05);">
        <div class="container">
            <form action="catalog.php" method="GET" class="hz-filter-form">
                <!-- Keep Category and Search values if present -->
                <?php if(!empty($selected_category)): ?>
                    <input type="hidden" name="category" value="<?php echo htmlspecialchars($selected_category); ?>">
                <?php endif; ?>
                <?php if(!empty($search_query)): ?>
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                <?php endif; ?>

                <div class="hz-filter-row">
                    
                    <!-- Brand Select -->
                    <div class="hz-select-group">
                        <select name="brand" class="hz-select">
                            <option value="">Any Brand</option>
                            <?php foreach ($brands as $b): ?>
                                <option value="<?php echo htmlspecialchars($b); ?>" <?php echo $selected_brand === $b ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($b); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="hz-select-caret">▼</div>
                    </div>

                    <!-- Capacity Select -->
                    <div class="hz-select-group">
                        <select name="capacity" class="hz-select">
                            <option value="">Any Capacity</option>
                            <?php foreach ($capacities as $c): ?>
                                <option value="<?php echo htmlspecialchars($c); ?>" <?php echo $selected_capacity === $c ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($c); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="hz-select-caret">▼</div>
                    </div>

                    <!-- Condition Select -->
                    <div class="hz-select-group">
                        <select name="status" class="hz-select">
                            <option value="">Any Condition</option>
                            <option value="New" <?php echo $selected_status === 'New' ? 'selected' : ''; ?>>New</option>
                            <option value="Used" <?php echo $selected_status === 'Used' ? 'selected' : ''; ?>>Used / Pre-Owned</option>
                        </select>
                        <div class="hz-select-caret">▼</div>
                    </div>

                    <!-- Power Select -->
                    <div class="hz-select-group">
                        <select name="energy" class="hz-select">
                            <option value="">Any Power</option>
                            <?php foreach ($energies as $e): ?>
                                <option value="<?php echo htmlspecialchars($e); ?>" <?php echo $selected_energy === $e ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($e); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="hz-select-caret">▼</div>
                    </div>

                    <!-- Filter Submit Button -->
                    <button type="submit" class="hz-filter-btn">Filter</button>

                    <!-- Price Slider Column -->
                    <div class="hz-price-slider-column">
                        <div class="hz-double-slider-wrapper">
                            <div class="slider-track" id="sliderTrack"></div>
                            <input type="range" min="0" max="62000" step="500" value="<?php echo $min_price; ?>" id="sliderMin" class="price-range-slider">
                            <input type="range" min="0" max="62000" step="500" value="<?php echo $max_price; ?>" id="sliderMax" class="price-range-slider">
                        </div>
                        <div class="hz-price-label">
                            Price: <span id="priceMinVal"><?php echo number_format($min_price, 0, ',', '.'); ?></span> € &mdash; <span id="priceMaxVal"><?php echo number_format($max_price, 0, ',', '.'); ?></span> €
                            
                            <!-- Hidden range inputs -->
                            <input type="hidden" name="min_price" id="inputMinPrice" value="<?php echo $min_price; ?>">
                            <input type="hidden" name="max_price" id="inputMaxPrice" value="<?php echo $max_price; ?>">
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </section>

    <!-- Catalog Main Full-Width Layout -->
    <section class="catalog-section" style="padding: 3rem 0 6rem; background-color: var(--body-bg);">
        <div class="container">
            
            <main class="catalog-main-full">
                
                <!-- Filters Active Overview -->
                <div class="catalog-header-meta" style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
                    <span class="results-count"><?php echo count($products); ?> Product(s) Found</span>
                    
                    <!-- Search Tags -->
                    <div class="active-filter-tags">
                        <?php if(!empty($selected_category)): ?>
                            <span class="filter-tag"><?php echo htmlspecialchars(ucfirst(str_replace('-', ' ', $selected_category))); ?> <a href="catalog.php?<?php echo http_build_query(array_filter(['status' => $selected_status, 'brand' => $selected_brand, 'capacity' => $selected_capacity, 'energy' => $selected_energy, 'min_price' => $min_price, 'max_price' => $max_price, 'search' => $search_query])); ?>">&times;</a></span>
                        <?php endif; ?>
                        <?php if(!empty($selected_status)): ?>
                            <span class="filter-tag">Condition: <?php echo htmlspecialchars($selected_status); ?> <a href="catalog.php?<?php echo http_build_query(array_filter(['category' => $selected_category, 'brand' => $selected_brand, 'capacity' => $selected_capacity, 'energy' => $selected_energy, 'min_price' => $min_price, 'max_price' => $max_price, 'search' => $search_query])); ?>">&times;</a></span>
                        <?php endif; ?>
                        <?php if(!empty($selected_brand)): ?>
                            <span class="filter-tag">Brand: <?php echo htmlspecialchars($selected_brand); ?> <a href="catalog.php?<?php echo http_build_query(array_filter(['category' => $selected_category, 'status' => $selected_status, 'capacity' => $selected_capacity, 'energy' => $selected_energy, 'min_price' => $min_price, 'max_price' => $max_price, 'search' => $search_query])); ?>">&times;</a></span>
                        <?php endif; ?>
                        <?php if(!empty($selected_capacity)): ?>
                            <span class="filter-tag">Capacity: <?php echo htmlspecialchars($selected_capacity); ?> <a href="catalog.php?<?php echo http_build_query(array_filter(['category' => $selected_category, 'status' => $selected_status, 'brand' => $selected_brand, 'energy' => $selected_energy, 'min_price' => $min_price, 'max_price' => $max_price, 'search' => $search_query])); ?>">&times;</a></span>
                        <?php endif; ?>
                        <?php if(!empty($selected_energy)): ?>
                            <span class="filter-tag">Power: <?php echo htmlspecialchars($selected_energy); ?> <a href="catalog.php?<?php echo http_build_query(array_filter(['category' => $selected_category, 'status' => $selected_status, 'brand' => $selected_brand, 'capacity' => $selected_capacity, 'min_price' => $min_price, 'max_price' => $max_price, 'search' => $search_query])); ?>">&times;</a></span>
                        <?php endif; ?>
                        <?php if($min_price > 0 || $max_price < 62000): ?>
                            <span class="filter-tag">Price: <?php echo number_format($min_price, 0, ',', '.'); ?>€ - <?php echo number_format($max_price, 0, ',', '.'); ?>€ <a href="catalog.php?<?php echo http_build_query(array_filter(['category' => $selected_category, 'status' => $selected_status, 'brand' => $selected_brand, 'capacity' => $selected_capacity, 'energy' => $selected_energy, 'search' => $search_query])); ?>">&times;</a></span>
                        <?php endif; ?>
                        <?php if(!empty($search_query)): ?>
                            <span class="filter-tag">Search: "<?php echo htmlspecialchars($search_query); ?>" <a href="catalog.php?<?php echo http_build_query(array_filter(['category' => $selected_category, 'status' => $selected_status, 'brand' => $selected_brand, 'capacity' => $selected_capacity, 'energy' => $selected_energy, 'min_price' => $min_price, 'max_price' => $max_price])); ?>">&times;</a></span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Products Grid -->
                <?php if (count($products) === 0): ?>
                    <div class="catalog-no-results" style="padding: 5rem 0; text-align: center; background: #ffffff; border-radius: 8px; border: 1px solid var(--border-gray);">
                        <h3>No Products Found</h3>
                        <p style="color: var(--text-muted); margin-top: 0.5rem;">Try clearing some filters or searching for a different model keyword.</p>
                        <a href="catalog.php" class="btn btn-blue-outline" style="margin-top: 1.5rem;">View All Products</a>
                    </div>
                <?php else: ?>
                    <div class="products-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem;">
                        <?php foreach ($products as $p): ?>
                            <article class="product-card" style="margin: 0; height: 100%; display: flex; flex-direction: column;">
                                
                                <div class="product-card-img-wrap">
                                    <img src="<?php echo htmlspecialchars($p['photo_path']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" class="product-card-img">
                                    <span class="product-badge badge-brand"><?php echo htmlspecialchars($p['brand']); ?></span>
                                    <span class="product-badge badge-status <?php echo strtolower($p['status']); ?>"><?php echo htmlspecialchars(strtoupper($p['status'])); ?></span>
                                </div>

                                <div class="product-card-content" style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                                    <div>
                                        <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 1rem; line-height: 1.3;"><?php echo htmlspecialchars($p['name']); ?></h3>
                                        
                                        <!-- Specs Grid -->
                                        <div class="product-card-specs">
                                            <?php if(!empty($p['lifting_capacity']) && $p['lifting_capacity'] !== 'N/A'): ?>
                                                <div class="spec-item">
                                                    <strong>Capacity:</strong>
                                                    <span><?php echo htmlspecialchars($p['lifting_capacity']); ?></span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if(!empty($p['mast_type']) && $p['mast_type'] !== 'N/A'): ?>
                                                <div class="spec-item">
                                                    <strong>Lift Height Capacity:</strong>
                                                    <span><?php echo htmlspecialchars($p['mast_type']); ?></span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if(!empty($p['energy']) && $p['energy'] !== 'N/A'): ?>
                                                <div class="spec-item">
                                                    <strong>Power:</strong>
                                                    <span><?php echo htmlspecialchars($p['energy']); ?></span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if($p['price'] > 0): ?>
                                                <div class="spec-item" style="border-top: 1px dashed var(--border-gray); padding-top: 0.5rem; margin-top: 0.5rem;">
                                                    <strong>Price:</strong>
                                                    <span style="color: var(--accent-orange); font-weight: 800; font-size: 1.05rem; white-space: nowrap;"><?php echo number_format($p['price'], 0, ',', '.'); ?> €</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="product-card-actions" style="margin-top: 1.5rem; display: block;">
                                        <a href="product-detail.php?slug=<?php echo urlencode($p['slug']); ?>" class="btn btn-primary view-details-premium-btn">
                                            View Details
                                        </a>
                                    </div>
                                </div>

                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </main>
        </div>
    </section>

    <!-- Double price slider script handles handles movement -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Price range handles logic
            const sMin = document.getElementById('sliderMin');
            const sMax = document.getElementById('sliderMax');
            const labelMin = document.getElementById('priceMinVal');
            const labelMax = document.getElementById('priceMaxVal');
            const hiddenMin = document.getElementById('inputMinPrice');
            const hiddenMax = document.getElementById('inputMaxPrice');
            const track = document.getElementById('sliderTrack');

            function updatePriceRange() {
                let min = parseInt(sMin.value);
                let max = parseInt(sMax.value);

                if (min > max) {
                    // swap references visually
                    let temp = min;
                    min = max;
                    max = temp;
                }

                labelMin.innerText = min.toLocaleString('de-DE');
                labelMax.innerText = max.toLocaleString('de-DE');
                hiddenMin.value = min;
                hiddenMax.value = max;

                // Color path layout
                let pctMin = (min / 62000) * 100;
                let pctMax = (max / 62000) * 100;
                track.style.left = pctMin + '%';
                track.style.width = (pctMax - pctMin) + '%';
            }

            if (sMin && sMax) {
                sMin.addEventListener('input', updatePriceRange);
                sMax.addEventListener('input', updatePriceRange);
                updatePriceRange(); // initialize track fill colors
            }
        });
    </script>

<?php
require_once __DIR__ . '/footer.php';
?>

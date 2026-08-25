<?php
// operator-training.php
// Recreated Operator Training Page

require_once __DIR__ . '/db.php';

// Fetch dynamic content sections
$page_title = get_section('training', 'training_page_title', 'Operator Training', '');
$banner_text = get_section('training', 'training_banner_text', '', 'At Y. Skembedjis & Sons Ltd, we put safety first with our comprehensive forklift operator safety training programs.');
$intro_p1 = get_section('training', 'training_intro_p1', '', 'At Y. Skembedjis & Sons Ltd, we put safety first with our comprehensive forklift operator safety training programs. Our engaging instructors provide hands-on learning to give your crew the edge in safe, effective equipment operations.');
$intro_p2 = get_section('training', 'training_intro_p2', '', "Our training center which is located in Limassol is considered by many customers and accrediting agents as being one of the most ideal forklift training facilities of high standards. It is used by both corporate customers and self sponsored delegates (private individuals), who wish to obtain their 'forklift license'.\nThe training center has a suitable area for practical sessions which include all material handling equipment, all suitable loads and has two fully furnished classrooms for running theory classes.");
$intro_p3 = get_section('training', 'training_intro_p3', '', 'The Center can provide safety courses for a variety of material handling equipment such as Counterbalance, Reach Trucks, Pedestrian / Ride on Stackers, Pallet trucks, Manual Handlifts, Very Narrow Aisles (VNA), Order Pickers and Mobile Elevating Platforms. The safety courses also include machines with motive power of Electric, Diesel, LPG and Gasoline.');
$accreditation_text = get_section('training', 'training_accreditation', '', '** Our instructors and programs are accredited by ITSSAR UK & ANAD');
$cta_line1 = get_section('training', 'training_cta_line1', 'Please Inquire For Details.', '');
$cta_line2 = get_section('training', 'training_cta_line2', 'All Courses Can Be Sponsored With ANAD', '');

// Fetch images
$training_hero_img = get_image_path('training_hero_img', 'Operator Training Session');
$itssar_logo = get_image_path('itssar_logo', 'ITSSAR UK Logo');
$anad_logo = get_image_path('anad_logo', 'ANAD Logo');

// Course Aims
$course_aims = [
    'To teach necessary skills and relevant job safety',
    'To acquire knowledge for the safe operation of the machine.',
    'To encourage future good practice in order to maintain and promote these skills.',
    'To create a safe working environment and procedures within the working area of the machine and its operator.',
    'To conform with all relevant statutory procedures (e.g. Health and Safety at Work Act 1974 and approved codes of practice)'
];

// Equipment Categories & Course Data
$categories = [
    [
        'id' => 'forklift-trucks',
        'name' => 'Forklift Trucks',
        'title' => 'Forklift Trucks',
        'image_key' => 'training_forklift',
        'image_alt' => 'Hyster Forklift Truck',
        'tables' => [
            [
                'columns' => ['Forklifts', 'Operator', 'Power', 'Course Type'],
                'rows' => [
                    ['B1', 'Rider', 'Electric/Diesel/LPG/ Gasoline', 'Safe use of Counterbalance Forklift up to 5 Tonnes.'],
                    ['B2', 'Rider', 'Electric/Diesel/LPG/ Gasoline', 'Safe use of Counterbalance Forklift up to 15 Tonnes.'],
                    ['B3', 'Rider', 'Electric/Diesel/LPG/Gasoline', 'Safe use of Counterbalance Forklift up to 45 Tonnes.']
                ]
            ]
        ]
    ],
    [
        'id' => 'reach-trucks',
        'name' => 'Reach Trucks',
        'title' => 'Reach Trucks',
        'image_key' => 'training_reach',
        'image_alt' => 'Hyster Reach Truck',
        'tables' => [
            [
                'columns' => ['Reach Trucks', 'Operator', 'Power', 'Course Type'],
                'rows' => [
                    ['D1', 'Rider', 'Electric', 'Safe use of Counterbalance Reach Truck.']
                ]
            ]
        ]
    ],
    [
        'id' => 'pallet-stackers',
        'name' => 'Pallet Stackers',
        'title' => 'Pallet Stackers',
        'image_key' => 'training_stacker',
        'image_alt' => 'Pallet Stacker',
        'tables' => [
            [
                'columns' => ['Pallet Stackers', 'Operator', 'Power', 'Course Type'],
                'rows' => [
                    ['A5', 'Pedestrian', 'Electric', 'Safe Use of Pallet Stacker by Pedestrian.'],
                    ['D2', 'Rider', 'Semi-Manual', 'Safe Use of Ride-On Semi-Manual Pallet Stacker by Pedestrian.'],
                    ['Z3', 'Pedestrian', 'Semi-Manual', 'Safe use of Semi Manual Pallet Stacker by Pedestrian.'],
                    ['Z4', 'Pedestrian', 'Semi-Manual', 'Safe use of Semi Manual Straddle Truck by Pedestrian.']
                ]
            ]
        ]
    ],
    [
        'id' => 'pallet-trucks',
        'name' => 'Pallet Trucks',
        'title' => 'Powered & Manual Pallet Trucks',
        'image_key' => 'training_pallet',
        'image_alt' => 'Pallet Truck',
        'tables' => [
            [
                'columns' => ['Manual Handlifts', 'Operator', 'Power', 'Course Type'],
                'rows' => [
                    ['Z1', 'Pedestrian', 'Manual & Semi-Manual', 'Safe Use of Semi-Manual Pallet Truck by Pedestrian.']
                ]
            ],
            [
                'columns' => ['Powered Pallet Trucks', 'Operator', 'Power', 'Course Type'],
                'rows' => [
                    ['A1', 'Pedestrian', 'Electric', 'Safe Use of Pallet Truck by Pedestrian.'],
                    ['A2', 'Rider', 'Electric', 'Safe Use of Ride-on Pallet Truck by Pedestrian.']
                ]
            ]
        ]
    ],
    [
        'id' => 'order-pickers',
        'name' => 'Order Pickers',
        'title' => 'Order Pickers',
        'image_key' => 'training_order_picker',
        'image_alt' => 'Order Picker',
        'tables' => [
            [
                'columns' => ['Order Pickers', 'Operator', 'Power', 'Course Type'],
                'rows' => [
                    ['E1', 'Pedestrian', 'Electric', 'Safe use of Order Picker by Pedestrian up to 2.5 Metres.'],
                    ['E2', 'Pedestrian', 'Electric', 'Safe use of Order Picker by Pedestrian from 2.5 Metres and above.']
                ]
            ]
        ]
    ],
    [
        'id' => 'vna',
        'name' => 'Very Narrow Aisle (VNA)',
        'title' => 'Very Narrow Aisle (VNA)',
        'image_key' => 'training_vna',
        'image_alt' => 'VNA Truck',
        'tables' => [
            [
                'columns' => ['Very Narrow Aisles', 'Operator', 'Power', 'Course Type'],
                'rows' => [
                    ['F1', 'Rider Up', 'Electric', 'Safe use of Rider-Up Very Narrow Aisle by Pedestrian.'],
                    ['F2', 'Rider Down', 'Electric', 'Safe use of Rider-Down Very Narrow Aisle by Pedestrian.']
                ]
            ]
        ]
    ],
    [
        'id' => 'mewp',
        'name' => 'Mobile Elevating Working Platforms',
        'title' => 'Mobile Elevating Working Platforms',
        'image_key' => 'training_mewp',
        'image_alt' => 'Mobile Elevating Working Platform',
        'tables' => [
            [
                'columns' => ['Elevating Platforms', 'Operator', 'Power', 'Course Type'],
                'rows' => [
                    ['1A', 'Pedestrian', 'Electric', 'Safe use of MEWP Vertical Elevating Platform (Static)'],
                    ['1B', 'Pedestrian', 'Electric', 'Safe use of MEWP Boom Elevating Platform (Static)'],
                    ['3A', 'Pedestrian', 'Electric', 'Safe use of MEWP Mobile Elevating Platform (Vertical)'],
                    ['3B', 'Pedestrian', 'Electric', 'Safe use of MEWP Mobile Elevating Platform (Boom)']
                ]
            ]
        ]
    ]
];

// Courses info sidebar (shared across all tabs)
$courses_info = [
    'Fork lift Truck Operator 1-day Safety Refresher or Conversion to Counterbalance',
    'Forklift Truck Operator 2 Day Conversion Course/ Refresher (from Counterbalance to Reach Truck)',
    'Forklift Truck Operator 2-3 day* Basic Operating Safety (Experienced Operator)',
    'Fork lift Truck Operator 3 – 5 day Basic Operating Safety (Novice Operator)',
    'Order Picking Truck Operating Safety Course',
    'Pedestrian and Rider Controlled Pallet Truck Operator Safety Course',
    'Pedestrian Controlled Forklift Truck Safety Course',
    'Very Narrow Aisle (VNA) Order Picker Course'
];

require_once __DIR__ . '/header.php';
?>

    <!-- Page Title Header Block -->
    <section class="page-header-block">
        <div class="container">
            <div class="divider-line centered"></div>
            <h2><?php echo htmlspecialchars($page_title['title']); ?></h2>
        </div>
    </section>

    <!-- Split Banner: Image left + Blue panel right -->
    <section class="repairs-split-banner">
        <div class="repairs-split-img" style="background-image: url('<?php echo htmlspecialchars($training_hero_img); ?>');" role="img" aria-label="Operator Training Session"></div>
        <div class="repairs-split-text">
            <p><?php echo nl2br(htmlspecialchars($banner_text['content'])); ?></p>
            <div class="repairs-split-line"></div>
        </div>
    </section>

    <!-- Two Column: Description + Course Aims -->
    <section class="training-content-section">
        <div class="container training-content-grid">
            <!-- Left: Description Paragraphs -->
            <div class="training-desc-col">
                <p><?php echo nl2br(htmlspecialchars($intro_p1['content'])); ?></p>
                <p><?php echo nl2br(htmlspecialchars($intro_p2['content'])); ?></p>
                <p><?php echo nl2br(htmlspecialchars($intro_p3['content'])); ?></p>
            </div>

            <!-- Right: Course Aims + Accreditation -->
            <div class="training-aims-col">
                <h3>Course Aims:</h3>
                <ul class="training-aims-list">
                    <?php foreach ($course_aims as $aim): ?>
                        <li><?php echo htmlspecialchars($aim); ?></li>
                    <?php endforeach; ?>
                </ul>

                <p class="training-accreditation-text">
                    <?php echo htmlspecialchars($accreditation_text['content']); ?>
                </p>

                <div class="training-logos-row">
                    <img src="<?php echo htmlspecialchars($itssar_logo); ?>" alt="ITSSAR UK Accreditation" class="accreditation-logo">
                    <img src="<?php echo htmlspecialchars($anad_logo); ?>" alt="ANAD Accreditation" class="accreditation-logo">
                </div>
            </div>
        </div>
    </section>

    <!-- CTA: Inquire Line -->
    <section class="training-inquire-cta">
        <div class="container" style="text-align: center;">
            <div class="divider-line centered"></div>
            <p class="cta-line-1"><?php echo htmlspecialchars($cta_line1['title']); ?></p>
            <p class="cta-line-2"><?php echo htmlspecialchars($cta_line2['title']); ?></p>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════════════ -->
    <!-- Operator Training Courses – Tabbed Section             -->
    <!-- ═══════════════════════════════════════════════════════ -->
    <section class="training-courses-banner">
        <div class="container">
            <h2>Operator Training Courses</h2>
        </div>
    </section>

    <section class="training-courses-section">
        <div class="container">

            <!-- Equipment Category Tab Navigation -->
            <nav class="training-tab-nav" aria-label="Equipment Categories">
                <?php foreach ($categories as $i => $cat): ?>
                    <?php if ($i > 0): ?><span class="tab-separator">|</span><?php endif; ?>
                    <button class="repairs-tab-btn <?php echo $i === 0 ? 'active' : ''; ?>" data-tab="<?php echo $cat['id']; ?>">
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </button>
                <?php endforeach; ?>
                <span class="tab-separator">|</span>
            </nav>

            <!-- Tab Panels -->
            <?php foreach ($categories as $i => $cat):
                $cat_img = get_image_path($cat['image_key'], $cat['image_alt']);
            ?>
                <div class="repairs-tab-panel <?php echo $i === 0 ? 'active' : ''; ?>" id="panel-<?php echo $cat['id']; ?>">
                    <div class="training-course-layout">
                        <!-- Left: Equipment image + Table -->
                        <div class="training-course-main">
                            <h3 class="training-course-title"><?php echo htmlspecialchars($cat['title']); ?></h3>

                            <div class="training-course-body">
                                <div class="training-equipment-img">
                                    <img src="<?php echo htmlspecialchars($cat_img); ?>" alt="<?php echo htmlspecialchars($cat['image_alt']); ?>">
                                </div>

                                <div class="training-specs-table-wrap">
                                    <?php foreach ($cat['tables'] as $tIdx => $table): ?>
                                        <table class="training-specs-table" style="<?php echo $tIdx > 0 ? 'margin-top: 2rem;' : ''; ?>">
                                            <thead>
                                                <tr>
                                                    <?php foreach ($table['columns'] as $col): ?>
                                                        <th><?php echo htmlspecialchars($col); ?></th>
                                                    <?php endforeach; ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($table['rows'] as $row): ?>
                                                    <tr>
                                                        <?php foreach ($row as $cell): ?>
                                                            <td><?php echo htmlspecialchars($cell); ?></td>
                                                        <?php endforeach; ?>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Right Sidebar: Courses Info -->
                        <aside class="training-courses-sidebar">
                            <h4>Courses info:</h4>
                            <ul class="courses-info-list">
                                <?php foreach ($courses_info as $course): ?>
                                    <li><?php echo htmlspecialchars($course); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </aside>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </section>

    <!-- Tab Switching Script -->
    <script>
        (function() {
            const tabBtns = document.querySelectorAll('.training-courses-section .repairs-tab-btn');
            const tabPanels = document.querySelectorAll('.training-courses-section .repairs-tab-panel');

            function activateTab(tabId) {
                tabBtns.forEach(btn => {
                    btn.classList.toggle('active', btn.dataset.tab === tabId);
                });
                tabPanels.forEach(panel => {
                    panel.classList.toggle('active', panel.id === 'panel-' + tabId);
                });
            }

            tabBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    activateTab(btn.dataset.tab);
                    history.replaceState(null, '', '#' + btn.dataset.tab);
                });
            });

            // Check URL hash on load
            const hash = window.location.hash.replace('#', '');
            if (hash && document.getElementById('panel-' + hash)) {
                activateTab(hash);
            }
        })();
    </script>

<?php
require_once __DIR__ . '/footer.php';
?>

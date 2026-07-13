<?php
// repairs-services.php
// Recreated Repairs & Services Page – Tabbed Content Layout

require_once __DIR__ . '/db.php';

// Fetch dynamic content sections (with sensible defaults matching the original site)
$page_title = get_section('repairs', 'repairs_page_title', 'Repairs & Services', '');

// Tab 1 – Mobile Service Units
$mobile_title = get_section('repairs', 'mobile_title', 'Mobile Service Units', '');
$mobile_intro = get_section('repairs', 'mobile_intro', '', 'We provide on-site repairs with our fully equipped mobile repair vans and highly skilled technicians. Our goal is to help you minimize service and maintenance costs while ensuring your equipment stays in top condition.');
$mobile_banner_text = get_section('repairs', 'mobile_banner_text', '', 'Why waste time waiting around a garage for service when Y. Skembedjis & Sons Ltd will come to you?');

// Tab 2 – Reconditions / Service & Repairs
$recon_title = get_section('repairs', 'recon_title', 'Reconditions/Service & Repairs', '');
$recon_intro = get_section('repairs', 'recon_intro', '', 'At Y. Skembedjis & Sons we took the time and effort to create workshops that are fully equipped as well as well manned with, highly skilled and professional individuals dedicated to forklift and material handling machinery repairs. Additionally, we offer on site repairs. Our mobile repair vans with our qualified staff helping you reduce the costs of service and maintenance whenever possible.');
$recon_body = get_section('repairs', 'recon_body', '', 'Y. Skembedjis & Sons provides used forklifts reconditioning. With more than 50 years of experience in evaluating and equipment\'s refurbishing. We have the expertise to assess the condition of each piece of equipment and identify any needed repairs of refurbishments.');

// Tab 3 – Maintenance Agreement Contract
$maint_title = get_section('repairs', 'maint_title', 'Maintenance Agreement Contract', '');
$maint_intro = get_section('repairs', 'maint_intro', '', 'The machines covered by a maintenance agreement are serviced on a regular basis and not only after breakdown. Full regular Service is the most comprehensive service program ensuring the most up-time from your equipment. This is done by completing all factory recommended services at the proper intervals. It is designed with a budget in mind that allow a fixed cost of repairs for a period of time appointed from your company.');
$maint_benefits_title = get_section('repairs', 'maint_benefits_title', 'Benefits of Maintenance Agreements:', '');
$maint_benefits = get_section('repairs', 'maint_benefits', '', "Repairs not covered by maintenance at a reduced price\nScheduled Preventative Maintenance (PM) Service units\nMachinery Log book\nDiscount Prices on Tires, Spare parts, Training Courses and Thorough Examination\nReplacement machinery at reduced rental cost if your machinery require shop repair (according to availability)");

// Fetch images
$mobile_van_img = get_image_path('repairs_mobile_van', 'Mobile Service Van Interior');
$recon_before_after_img = get_image_path('repairs_before_after', 'Before and After Reconditioning');

require_once __DIR__ . '/header.php';
?>

    <!-- Page Title Header Block -->
    <section class="page-header-block">
        <div class="container">
            <div class="divider-line centered"></div>
            <h2><?php echo htmlspecialchars($page_title['title']); ?></h2>
        </div>
    </section>

    <!-- Tabbed Content Section -->
    <section class="repairs-tabs-section">
        <div class="container">

            <!-- Tab Navigation Bar -->
            <nav class="repairs-tab-nav" aria-label="Service Categories">
                <button class="repairs-tab-btn active" data-tab="mobile-service" id="tab-mobile-service">
                    Mobile Service Units
                </button>
                <span class="tab-separator">|</span>
                <button class="repairs-tab-btn" data-tab="reconditions" id="tab-reconditions">
                    Reconditions/Service &amp; Repairs
                </button>
                <span class="tab-separator">|</span>
                <button class="repairs-tab-btn" data-tab="maintenance" id="tab-maintenance">
                    Maintenance Agreement Contract
                </button>
                <span class="tab-separator">|</span>
            </nav>

            <!-- ═══════════════════════════════════════════════ -->
            <!--  TAB 1 – Mobile Service Units                  -->
            <!-- ═══════════════════════════════════════════════ -->
            <div class="repairs-tab-panel active" id="panel-mobile-service">

                <!-- Intro description text -->
                <div class="repairs-intro-text">
                    <p><?php echo nl2br(htmlspecialchars($mobile_intro['content'])); ?></p>
                </div>

                <!-- Split banner: Image left + Blue text right -->
                <div class="repairs-split-banner">
                    <div class="repairs-split-img" style="background-image: url('<?php echo htmlspecialchars($mobile_van_img); ?>');" role="img" aria-label="Mobile Service Van"></div>
                    <div class="repairs-split-text">
                        <p><?php echo nl2br(htmlspecialchars($mobile_banner_text['content'])); ?></p>
                        <div class="repairs-split-line"></div>
                    </div>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════ -->
            <!--  TAB 2 – Reconditions/Service & Repairs        -->
            <!-- ═══════════════════════════════════════════════ -->
            <div class="repairs-tab-panel" id="panel-reconditions">

                <div class="repairs-intro-text">
                    <p><?php echo nl2br(htmlspecialchars($recon_intro['content'])); ?></p>
                    <p style="margin-top: 1rem;"><?php echo nl2br(htmlspecialchars($recon_body['content'])); ?></p>
                </div>

                <!-- Before / After Image Section -->
                <div class="repairs-before-after-wrapper">
                    <img src="<?php echo htmlspecialchars($recon_before_after_img); ?>" alt="Before and After Reconditioning" class="repairs-ba-image">
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════ -->
            <!--  TAB 3 – Maintenance Agreement Contract        -->
            <!-- ═══════════════════════════════════════════════ -->
            <div class="repairs-tab-panel" id="panel-maintenance">

                <div class="repairs-intro-text">
                    <p><?php echo nl2br(htmlspecialchars($maint_intro['content'])); ?></p>

                    <h4 style="margin-top: 1.5rem; color: var(--primary-blue); font-weight: 600;"><?php echo htmlspecialchars($maint_benefits_title['title']); ?></h4>
                    <ul class="repairs-benefits-list">
                        <?php
                        $benefitLines = explode("\n", $maint_benefits['content']);
                        foreach ($benefitLines as $line):
                            $line = trim($line);
                            if (!empty($line)):
                        ?>
                            <li><?php echo htmlspecialchars($line); ?></li>
                        <?php
                            endif;
                        endforeach;
                        ?>
                    </ul>
                </div>
            </div>

        </div>
    </section>

    <!-- Contact CTA Section -->
    <section class="repairs-cta-section">
        <div class="container" style="text-align: center;">
            <h3>Need Expert Repairs or Maintenance?</h3>
            <p>Get in touch with our certified technicians for immediate service support.</p>
            <a href="contact.php?subject=Repairs+%26+Services+Inquiry" class="btn btn-blue-outline">Contact Our Team</a>
        </div>
    </section>

    <!-- Tab Switching Script -->
    <script>
        (function() {
            const tabBtns = document.querySelectorAll('.repairs-tab-btn');
            const tabPanels = document.querySelectorAll('.repairs-tab-panel');

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
                    // Update URL hash without scrolling
                    history.replaceState(null, '', '#' + btn.dataset.tab);
                });
            });

            // Check for hash on page load to open correct tab
            const hash = window.location.hash.replace('#', '');
            if (hash && document.getElementById('panel-' + hash)) {
                activateTab(hash);
            }
        })();
    </script>

<?php
require_once __DIR__ . '/footer.php';
?>

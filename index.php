<?php
// index.php
// Recreated Skembedjis Home Page

require_once __DIR__ . '/db.php';

// Fetch settings
$siteName = get_setting('site_name', 'Y. Skembedjis & Sons Ltd');
$email = get_setting('contact_email', 'info@skembedjis.com');
$phone = get_setting('contact_phone', '+357 25 712265');
$address = get_setting('address', 'Limassol, Cyprus');
$footerText = get_setting('footer_text', '© ' . date('Y') . ' Y. Skembedjis & Sons Ltd. All rights reserved.');

// Fetch sections (with default contents matching screenshots)
$hero_title = get_section('home', 'hero_caption_title', 'STRONG PARTNERS.', '');
$hero_subtitle = get_section('home', 'hero_caption_subtitle', 'TOUGH TRUCKS.™', '');

$cta_top_1 = get_section('home', 'cta_top_text_1', '', 'Does your business experience peak periods which places extra demands on both equipment and manpower?');
$cta_top_2 = get_section('home', 'cta_top_text_2', '', 'For the best options in Forklift Sales and service, contact us today!');
$cta_top_3 = get_section('home', 'cta_top_text_3', '', 'Explore our comprehensive services for Forklift Sales and rentals.');
$cta_top_btn = get_section('home', 'cta_top_btn_text', '', 'Rent Now');

$solutions_title = get_section('home', 'solutions_title', 'Industry Solutions', '');
$solutions_desc = get_section('home', 'solutions_desc', '', 'We have the solution for every industry!');

$handling_title = get_section('home', 'handling_title', 'Your Complete Material Handling Solution', '');
$handling_sub = get_section('home', 'handling_subtitle', 'SINCE 1971', '');
$handling_desc = get_section('home', 'handling_desc', '', 'Driven by reliability and expertise since 1971, Y. Skembedjis & Sons Ltd has become a reference point in the supply of forklift trucks & warehouse equipment in Cyprus and abroad.');
$handling_btn = get_section('home', 'handling_btn_text', '', 'Explore Our Equipment');
$catalog_btn = get_section('home', 'catalog_btn_text', '', 'Download Our Catalog');

$training_title = get_section('home', 'training_title', 'Operator Training', '');
$training_desc = get_section('home', 'training_desc', '', 'We put safety first with our comprehensive forklift operator safety training programs. Our engaging instructors provide hands-on learning to give your crew the edge in safe, effective equipment operations.');
$training_btn = get_section('home', 'training_btn_text', '', 'Find Out More');

$join_title = get_section('home', 'join_title', 'Be Part of Y. Skembedjis', '');
$join_desc = get_section('home', 'join_desc', '', 'If you want to keep Cyprus lifting, then why not join us?');
$join_btn = get_section('home', 'join_btn_text', '', 'Careers');
$hiring_text = get_section('home', 'hiring_text', '', 'We are hiring!');

$collab_title = get_section('home', 'collaborators_title', 'Our Collaborators', '');
$web_title = get_section('home', 'websites_title', 'Visit Our Websites', '');

$b2b_title = get_section('home', 'b2b_title', 'We make everything easy for you!', '');
$b2b_desc = get_section('home', 'b2b_desc', '', 'Download our B2B app and request an offer or service');

$insta_title = get_section('home', 'instagram_title', 'Instagram Feed', '');

$faq_header = get_section('home', 'faq_title', 'Frequently Asked Questions', '');
$faqs = get_faqs();


// Fetch images
$logoImg = get_image_path('logo', 'Skembedjis Logo');
$heroBg = get_image_path('hero_bg', 'Hero Banner Background');
$hysterLogo = get_image_path('hyster_badge', 'Hyster Badge');
// Industry Solutions Images
$industry_solutions = [
    ['name' => 'Food & Beverage', 'img' => 'assets/industry-solutions/food.webp'],
    ['name' => 'Logistics', 'img' => 'assets/industry-solutions/logistics.webp'],
    ['name' => 'Metal', 'img' => 'assets/industry-solutions/metal-01.webp'],
    ['name' => 'Paper', 'img' => 'assets/industry-solutions/Paper-1.webp'],
    ['name' => 'Pharmacy', 'img' => 'assets/industry-solutions/pharmacy-01.webp'],
    ['name' => 'Ports', 'img' => 'assets/industry-solutions/ports.webp'],
    ['name' => 'Recycling', 'img' => 'assets/industry-solutions/recycling-01.webp'],
    ['name' => 'Retail', 'img' => 'assets/industry-solutions/retail.webp'],
    ['name' => 'Supply Chain', 'img' => 'assets/industry-solutions/supply-01.webp'],
    ['name' => 'Wood', 'img' => 'assets/industry-solutions/wood-01.webp'],
];

// Collaborator Logos
$collaborators_list = [
    ['name' => 'Hyster', 'img' => 'assets/collaborators/hyster.webp'],
    ['name' => 'HC Forklift', 'img' => 'assets/collaborators/hc-320x202.webp'],
    ['name' => 'EP Equipment', 'img' => 'assets/collaborators/epas-1-301x202.webp'],
    ['name' => 'Hako', 'img' => 'assets/collaborators/Hako-Logo.svgmini-1-250x202.webp'],
    ['name' => 'FAAM Batteries', 'img' => 'assets/collaborators/LOGO_FAAM.webp'],
    ['name' => 'Bada', 'img' => 'assets/collaborators/Q_BADA.webp'],
    ['name' => 'Trelleborg', 'img' => 'assets/collaborators/Trelleborg-320x202.webp'],
    ['name' => 'Bolzoni Auramo', 'img' => 'assets/collaborators/bolzoni.webp'],
    ['name' => 'ATIB Elettronica', 'img' => 'assets/collaborators/atib.webp'],
    ['name' => 'Easyramps', 'img' => 'assets/collaborators/easyramps.webp'],
    ['name' => 'Sunbear', 'img' => 'assets/collaborators/Sunbear-Logo.webp'],
];

$catalogCover = 'assets/catalog-cover.webp';
$catalogBg = 'assets/catalogue.webp';
$trainingBg = get_image_path('training_bg', 'Training Background');
$careersImg = get_image_path('careers_img', 'Careers Image');
$webHyster = 'assets/collaborators/HYSTER-LOGO-320x202.webp';
$webHCHouse = 'assets/collaborators/hc-320x202.webp';
$webED = 'assets/collaborators/epas-1-301x202.webp';
$appStoreBtn = get_image_path('app_store_btn', 'App Store');
$googlePlayBtn = get_image_path('google_play_btn', 'Google Play');
$insta1 = get_image_path('insta_1', 'Instagram 1');
$insta2 = get_image_path('insta_2', 'Instagram 2');
$insta3 = get_image_path('insta_3', 'Instagram 3');
require_once __DIR__ . '/header.php';
?>

    <!-- Hero Banner Slider Section -->
    <section class="hero-slider-section">
        <div class="hero-slider">
            <div class="slide active" style="background-image: url('assets/sliders/banner1.webp');"></div>
            <div class="slide" style="background-image: url('assets/sliders/banner2.webp');"></div>
            <div class="slide" style="background-image: url('assets/sliders/banner3.webp');"></div>
        </div>

        <button class="slider-arrow prev" id="sliderPrev" aria-label="Previous Slide">&#10094;</button>
        <button class="slider-arrow next" id="sliderNext" aria-label="Next Slide">&#10095;</button>

        <div class="slider-dots" id="sliderDots">
            <span class="dot active" data-index="0"></span>
            <span class="dot" data-index="1"></span>
            <span class="dot" data-index="2"></span>
        </div>
    </section>

    <!-- Question CTA Section -->
    <section class="cta-top">
        <div class="container">
            <div class="divider-line centered"></div>
            <p class="strong"><?php echo htmlspecialchars($cta_top_1['content']); ?></p>
            <p><?php echo htmlspecialchars($cta_top_2['content']); ?></p>
            <p><?php echo htmlspecialchars($cta_top_3['content']); ?></p>
            <a href="rentals.php" class="btn btn-blue-outline"><?php echo htmlspecialchars($cta_top_btn['content']); ?></a>
        </div>
    </section>

    <!-- Industry Solutions Section -->
    <section class="solutions">
        <div class="container solutions-grid">
            <div class="solutions-info">
                <div class="divider-line"></div>
                <h3><?php echo htmlspecialchars($solutions_title['title']); ?></h3>
                <p><?php echo htmlspecialchars($solutions_desc['content']); ?></p>
            </div>
            
            <div class="solutions-circles">
                <?php foreach ($industry_solutions as $sol): ?>
                    <div class="circle-item">
                        <div class="circle-frame">
                            <img src="<?php echo htmlspecialchars($sol['img']); ?>" alt="<?php echo htmlspecialchars($sol['name']); ?>">
                        </div>
                        <span class="circle-label"><?php echo htmlspecialchars($sol['name']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Material Handling / Catalogue split section -->
    <section class="handling">
        <div class="handling-grid">
            <div class="handling-left">
                <div class="handling-left-content">
                    <div class="divider-line white"></div>
                    <h3><?php echo htmlspecialchars($handling_title['title']); ?></h3>
                    <p class="since"><?php echo htmlspecialchars($handling_sub['title']); ?></p>
                    <p><?php echo htmlspecialchars($handling_desc['content']); ?></p>
                    <a href="products.php" class="btn btn-white-outline"><?php echo htmlspecialchars($handling_btn['content']); ?></a>
                </div>
            </div>
            
            <div class="handling-right" style="background-image: url('<?php echo htmlspecialchars($catalogBg); ?>');">
                <div class="catalog-btn-container">
                    <a href="catalog.pdf" download class="btn btn-gray-box">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        <?php echo htmlspecialchars($catalog_btn['content']); ?>
                    </a>
                </div>
                <div class="catalog-img-container">
                    <a href="catalog.pdf" download title="Download Products & Services Catalogue">
                        <img src="<?php echo htmlspecialchars($catalogCover); ?>" alt="Products & Services Catalogue Booklet">
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Operator Training Section -->
    <section class="training" style="background-image: url('<?php echo htmlspecialchars($trainingBg); ?>');">
        <div class="container">
            <div class="training-content">
                <div class="divider-line"></div>
                <h3><?php echo htmlspecialchars($training_title['title']); ?></h3>
                <p><?php echo htmlspecialchars($training_desc['content']); ?></p>
                <a href="training.php" class="btn btn-blue-outline"><?php echo htmlspecialchars($training_btn['content']); ?></a>
            </div>
        </div>
    </section>

    <!-- Careers Banner Split Section -->
    <section class="careers-split">
        <div class="careers-left">
            <div class="divider-line"></div>
            <h3><?php echo htmlspecialchars($join_title['title']); ?></h3>
            <p><?php echo htmlspecialchars($join_desc['content']); ?></p>
            <a href="careers" class="btn btn-blue-outline"><?php echo htmlspecialchars($join_btn['content']); ?></a>
        </div>
        
        <div class="careers-middle">
            <div class="divider-line white" style="margin: 0 auto 1.5rem;"></div>
            <h3><?php echo htmlspecialchars($hiring_text['content']); ?></h3>
            <div class="divider-line white" style="margin: 1.5rem auto 0;"></div>
        </div>
        
        <div class="careers-right" style="background-image: url('<?php echo htmlspecialchars($careersImg); ?>');"></div>
    </section>

    <!-- Collaborators Section -->
    <section class="collaborators">
        <div class="container">
            <div class="divider-line centered"></div>
            <h3 class="collab-title"><?php echo htmlspecialchars($collab_title['title']); ?></h3>
        </div>
        
        <div class="collab-slider">
            <div class="collab-track">
                <?php foreach (array_merge($collaborators_list, $collaborators_list) as $collab): ?>
                    <div class="collab-item">
                        <img src="<?php echo htmlspecialchars($collab['img']); ?>" alt="<?php echo htmlspecialchars($collab['name']); ?>" title="<?php echo htmlspecialchars($collab['name']); ?>">
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Visit Our Websites Grid Section -->
    <section class="websites-section">
        <div class="container">
            <div class="divider-line centered"></div>
            <h3 class="collab-title"><?php echo htmlspecialchars($web_title['title']); ?></h3>
            
            <div class="web-grid">
                <div class="web-card">
                    <img src="<?php echo htmlspecialchars($webHyster); ?>" alt="Hyster Website">
                </div>
                <div class="web-card">
                    <img src="<?php echo htmlspecialchars($webHCHouse); ?>" alt="HC Forklift House Website">
                </div>
                <div class="web-card">
                    <img src="<?php echo htmlspecialchars($webED); ?>" alt="ED Website">
                </div>
            </div>
        </div>
    </section>

    <!-- B2B App Promo Section -->
    <section class="b2b-promo">
        <div class="container b2b-container">
            <div class="b2b-text">
                <h3><?php echo htmlspecialchars($b2b_title['title']); ?></h3>
                <p><?php echo htmlspecialchars($b2b_desc['content']); ?></p>
            </div>
            
            <div class="b2b-badges">
                <img src="<?php echo htmlspecialchars($appStoreBtn); ?>" alt="Download on App Store">
                <img src="<?php echo htmlspecialchars($googlePlayBtn); ?>" alt="Get it on Google Play">
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <div class="divider-line centered"></div>
            <h3><?php echo htmlspecialchars($faq_header['title']); ?></h3>
            <p class="faq-subtitle">Find answers to common questions about forklift sales, rental options, repair services, safety certifications, and battery warranties in Cyprus.</p>
            
            <div class="faq-container">
                <?php foreach ($faqs as $faq): ?>
                    <div class="faq-group">
                        <button class="faq-question">
                            <h4><?php echo htmlspecialchars($faq['question']); ?></h4>
                            <span class="faq-toggle-icon">+</span>
                        </button>
                        <div class="faq-answer">
                            <div class="faq-answer-content">
                                <?php echo $faq['answer']; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Instagram Feed Section -->
    <section class="instagram">
        <div class="container">
            <div class="divider-line centered"></div>
            <h3><?php echo htmlspecialchars($insta_title['title']); ?></h3>
        </div>
        
        <div class="insta-grid">
            <div class="insta-item">
                <img src="<?php echo htmlspecialchars($insta1); ?>" alt="Instagram Feed Image 1">
            </div>
            <div class="insta-item">
                <img src="<?php echo htmlspecialchars($insta2); ?>" alt="Instagram Feed Image 2">
            </div>
            <div class="insta-item">
                <img src="<?php echo htmlspecialchars($insta3); ?>" alt="Instagram Feed Image 3">
            </div>
        </div>
    </section>

    <script>
        // Hero Slider Logic
        (function() {
            const slides = document.querySelectorAll('.hero-slider .slide');
            const dots = document.querySelectorAll('.slider-dots .dot');
            const prevBtn = document.getElementById('sliderPrev');
            const nextBtn = document.getElementById('sliderNext');
            let currentIndex = 0;
            let slideInterval;

            function goToSlide(index) {
                slides.forEach((slide, i) => {
                    slide.classList.toggle('active', i === index);
                });
                dots.forEach((dot, i) => {
                    dot.classList.toggle('active', i === index);
                });
                currentIndex = index;
            }

            function nextSlide() {
                let nextIndex = (currentIndex + 1) % slides.length;
                goToSlide(nextIndex);
            }

            function prevSlide() {
                let prevIndex = (currentIndex - 1 + slides.length) % slides.length;
                goToSlide(prevIndex);
            }

            function startTimer() {
                stopTimer();
                slideInterval = setInterval(nextSlide, 5000);
            }

            function stopTimer() {
                if (slideInterval) clearInterval(slideInterval);
            }

            if (prevBtn && nextBtn) {
                nextBtn.addEventListener('click', () => { nextSlide(); startTimer(); });
                prevBtn.addEventListener('click', () => { prevSlide(); startTimer(); });
            }

            dots.forEach(dot => {
                dot.addEventListener('click', (e) => {
                    const idx = parseInt(e.target.getAttribute('data-index'));
                    goToSlide(idx);
                    startTimer();
                });
            });

            const heroSection = document.querySelector('.hero-slider-section');
            if (heroSection) {
                heroSection.addEventListener('mouseenter', stopTimer);
                heroSection.addEventListener('mouseleave', startTimer);
            }

            startTimer();
        })();

        // FAQ Accordion Toggle
        const faqQuestions = document.querySelectorAll('.faq-question');
        faqQuestions.forEach(btn => {
            btn.addEventListener('click', () => {
                const group = btn.parentElement;
                const answer = group.querySelector('.faq-answer');
                
                // Toggle active class
                const isActive = group.classList.contains('active');
                
                // Close other open questions
                document.querySelectorAll('.faq-group').forEach(g => {
                    g.classList.remove('active');
                    g.querySelector('.faq-answer').style.maxHeight = null;
                });
                
                if (!isActive) {
                    group.classList.add('active');
                    answer.style.maxHeight = answer.scrollHeight + 'px';
                }
            });
        });
    </script>
<?php
require_once __DIR__ . '/footer.php';
?>

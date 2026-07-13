<?php
// index.php
// Recreated Skembedjis Home Page

require_once __DIR__ . '/db.php';

// Fetch settings
$siteName = get_setting('site_name', 'Y. Skembedjis & Sons Ltd');
$email = get_setting('contact_email', 'info@skembedjis.com');
$phone = get_setting('contact_phone', '+357 25 878700');
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
$sol1 = get_image_path('sol_1', 'Solution 1');
$sol2 = get_image_path('sol_2', 'Solution 2');
$sol3 = get_image_path('sol_3', 'Solution 3');
$sol4 = get_image_path('sol_4', 'Solution 4');
$catalogCover = get_image_path('catalog_cover', 'Catalog Cover');
$trainingBg = get_image_path('training_bg', 'Training Background');
$careersImg = get_image_path('careers_img', 'Careers Image');
$collab1 = get_image_path('collab_1', 'ATIB');
$collab2 = get_image_path('collab_2', 'Easyramps');
$collab3 = get_image_path('collab_3', 'Hyster');
$collab4 = get_image_path('collab_4', 'AA');
$webHyster = get_image_path('web_hyster', 'Hyster Site');
$webHCHouse = get_image_path('web_hchouse', 'HC Forklift House');
$webED = get_image_path('web_ed', 'ED Site');
$appStoreBtn = get_image_path('app_store_btn', 'App Store');
$googlePlayBtn = get_image_path('google_play_btn', 'Google Play');
$insta1 = get_image_path('insta_1', 'Instagram 1');
$insta2 = get_image_path('insta_2', 'Instagram 2');
$insta3 = get_image_path('insta_3', 'Instagram 3');
require_once __DIR__ . '/header.php';
?>

    <!-- Hero Banner Section -->
    <section class="hero" style="background-image: url('<?php echo htmlspecialchars($heroBg); ?>');">
        <div class="hyster-badge">
            <img src="<?php echo htmlspecialchars($hysterLogo); ?>" alt="Hyster Logo">
            <h2><?php echo htmlspecialchars($hero_title['title']); ?></h2>
            <h2><?php echo htmlspecialchars($hero_subtitle['title']); ?></h2>
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
                <div class="circle-item">
                    <div class="circle-frame">
                        <img src="<?php echo htmlspecialchars($sol1); ?>" alt="Solution 1">
                    </div>
                </div>
                <div class="circle-item">
                    <div class="circle-frame">
                        <img src="<?php echo htmlspecialchars($sol2); ?>" alt="Solution 2">
                    </div>
                </div>
                <div class="circle-item">
                    <div class="circle-frame">
                        <img src="<?php echo htmlspecialchars($sol3); ?>" alt="Solution 3">
                    </div>
                </div>
                <div class="circle-item">
                    <div class="circle-frame">
                        <img src="<?php echo htmlspecialchars($sol4); ?>" alt="Solution 4">
                    </div>
                </div>
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
            
            <div class="handling-right">
                <div class="catalog-img-container">
                    <img src="<?php echo htmlspecialchars($catalogCover); ?>" alt="Catalog Booklet Cover">
                </div>
                <div class="catalog-btn-container">
                    <a href="catalog.pdf" download class="btn btn-gray-box"><?php echo htmlspecialchars($catalog_btn['content']); ?></a>
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
            <a href="careers.php" class="btn btn-blue-outline"><?php echo htmlspecialchars($join_btn['content']); ?></a>
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
            
            <div class="collab-row">
                <img src="<?php echo htmlspecialchars($collab1); ?>" alt="ATIB Elettronica">
                <img src="<?php echo htmlspecialchars($collab2); ?>" alt="Easyramps">
                <img src="<?php echo htmlspecialchars($collab3); ?>" alt="Hyster">
                <img src="<?php echo htmlspecialchars($collab4); ?>" alt="AA partner">
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
                    // Set height based on inner scrollHeight for smooth transition
                    answer.style.maxHeight = answer.scrollHeight + 'px';
                }
            });
        });
    </script>
<?php
require_once __DIR__ . '/footer.php';
?>

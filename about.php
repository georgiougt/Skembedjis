<?php
// about.php
// Recreated About Us Page

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/header.php';

// Fetch sections
$hero_title = get_section('about', 'about_hero_title', '50+ Proud Years', '');
$intro_title = get_section('about', 'about_intro_title', 'About Us', '');
$intro_body = get_section('about', 'about_intro_body', '', 'Y. Skembedjis & Sons Ltd has been serving customers since 1971. Through experienced leadership, steady growth and award winning success each of our two locations provides world-class, customer-first support, service and equipment.');
$history_body = get_section('about', 'about_history_body', '', 'Driven by reliability and expertise, Y. Skembedjis & Sons Ltd has become a reference point in the supply of lift trucks and logistic equipment in Cyprus and abroad.');

// Directory Cards
$card1_title = get_section('about', 'about_card_1_title', 'About The Company', '');
$card1_content = get_section('about', 'about_card_1_content', '', "*Branches\n*Corporate Videos");

$card2_title = get_section('about', 'about_card_2_title', 'Engagement & Values', '');
$card2_content = get_section('about', 'about_card_2_content', '', "*Quality\n*Vision, Mission & Values");

$card3_title = get_section('about', 'about_card_3_title', 'Board Of Directors', '');
$card3_content = get_section('about', 'about_card_3_content', '', "1. COSTAS SKEMBEDJIS\nChairman of the Board of Directors\n\n2. ANDROULLA SKEMBEDJI\nExecutive Director/Founder\n\n3. STALO SKEMBEDJI\nExecutive Director\n\n4. NIKOLAS SKEMBEDJIS\nExecutive Director");

$card4_title = get_section('about', 'about_card_4_title', 'Ownership Structure', '');
$card4_content = get_section('about', 'about_card_4_content', '', "100% YIANNAKIS SKEMBEDJIS LTD");

// Fetch image paths
$heroImg = get_image_path('about_hero', 'Historic Famagusta Shop');
$founderImg = get_image_path('about_founder', 'Founder Yiannakis Skembedjis');
?>

    <!-- Banner Title Section -->
    <section class="about-banner-title-sec">
        <div class="container">
            <h1 class="about-banner-text-line">
                <?php echo htmlspecialchars($hero_title['title']); ?>
            </h1>
        </div>
    </section>

    <!-- Hero Video Section -->
    <section class="about-hero-img-sec">
        <div class="about-hero-img-container">
            <video autoplay muted loop playsinline style="width: 100%; height: auto; display: block;">
                <source src="assets/Skembedjis-trimmed.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        </div>
    </section>

    <!-- Intro Section -->
    <section class="about-intro-sec">
        <div class="container">
            <div class="divider-line centered"></div>
            <h2><?php echo htmlspecialchars($intro_title['title']); ?></h2>
            <p><?php echo htmlspecialchars($intro_body['content']); ?></p>
        </div>
    </section>

    <!-- Founder Biography & History Section -->
    <section class="about-history-sec">
        <div class="container about-history-grid">
            <div class="founder-portrait-wrapper">
                <img src="<?php echo htmlspecialchars($founderImg); ?>" alt="Founder Yiannakis Skembedjis">
            </div>
            
            <div class="about-history-content">
                <?php echo nl2br($history_body['content']); ?>
            </div>
        </div>
    </section>

    <!-- Cards Subpage Directory Section -->
    <section class="about-directory-sec">
        <div class="container">
            <div class="about-directory-grid">
                
                <!-- Card 1 -->
                <div class="about-directory-card">
                    <div class="card-title-separator"></div>
                    <h3><?php echo htmlspecialchars($card1_title['title']); ?></h3>
                    <ul>
                        <?php
                        $lines = explode("\n", trim($card1_content['content']));
                        foreach ($lines as $line):
                            $line = trim($line);
                            if (empty($line)) continue;
                            if (strpos($line, '*') === 0):
                                $txt = trim(substr($line, 1));
                                $url = '#';
                                if (strtolower($txt) === 'branches') {
                                    $url = 'branches.php';
                                } elseif (strtolower($txt) === 'corporate videos') {
                                    $url = 'corporate-videos.php';
                                }
                        ?>
                                <li>* <a href="<?php echo htmlspecialchars($url); ?>"><?php echo htmlspecialchars($txt); ?></a></li>
                            <?php else: ?>
                                <li><?php echo htmlspecialchars($line); ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Card 2 -->
                <div class="about-directory-card">
                    <div class="card-title-separator"></div>
                    <h3><?php echo htmlspecialchars($card2_title['title']); ?></h3>
                    <ul>
                        <?php
                        $lines = explode("\n", trim($card2_content['content']));
                        foreach ($lines as $line):
                            $line = trim($line);
                            if (empty($line)) continue;
                            if (strpos($line, '*') === 0):
                                $txt = trim(substr($line, 1));
                                $url = '#';
                                if (strtolower($txt) === 'branches') {
                                    $url = 'branches.php';
                                }
                        ?>
                                <li>* <a href="<?php echo htmlspecialchars($url); ?>"><?php echo htmlspecialchars($txt); ?></a></li>
                            <?php else: ?>
                                <li><?php echo htmlspecialchars($line); ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Card 3 -->
                <div class="about-directory-card">
                    <div class="card-title-separator"></div>
                    <h3><?php echo htmlspecialchars($card3_title['title']); ?></h3>
                    <ul>
                        <?php
                        $lines = explode("\n", trim($card3_content['content']));
                        foreach ($lines as $line):
                            $line = trim($line);
                            if (empty($line)) continue;
                        ?>
                            <li><?php echo htmlspecialchars($line); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Card 4 -->
                <div class="about-directory-card">
                    <div class="card-title-separator"></div>
                    <h3><?php echo htmlspecialchars($card4_title['title']); ?></h3>
                    <ul>
                        <?php
                        $lines = explode("\n", trim($card4_content['content']));
                        foreach ($lines as $line):
                            $line = trim($line);
                            if (empty($line)) continue;
                        ?>
                            <li><strong><?php echo htmlspecialchars($line); ?></strong></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

            </div>
        </div>
    </section>

<?php
require_once __DIR__ . '/footer.php';
?>

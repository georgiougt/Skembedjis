<?php
// db.php
// Database connection and initialization

$dbPath = __DIR__ . '/db/site.db';

// Ensure the db directory exists
if (!is_dir(__DIR__ . '/db')) {
    mkdir(__DIR__ . '/db', 0755, true);
    // Create htaccess to protect database file
    file_put_contents(__DIR__ . '/db/.htaccess', "Require all denied\n");
}

try {
    $db = new PDO("sqlite:" . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Initialize tables
$db->exec("
    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS site_settings (
        key TEXT PRIMARY KEY,
        value TEXT NOT NULL
    );

    CREATE TABLE IF NOT EXISTS content_sections (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        page TEXT NOT NULL,
        section_key TEXT NOT NULL,
        title TEXT,
        content TEXT,
        UNIQUE(page, section_key)
    );

    CREATE TABLE IF NOT EXISTS site_images (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        image_key TEXT UNIQUE NOT NULL,
        image_path TEXT NOT NULL,
        alt_text TEXT
    );

    CREATE TABLE IF NOT EXISTS faqs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        question TEXT NOT NULL,
        answer TEXT NOT NULL,
        sort_order INTEGER DEFAULT 0
    );

    CREATE TABLE IF NOT EXISTS blog_posts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        slug TEXT UNIQUE NOT NULL,
        excerpt TEXT,
        content TEXT,
        image_path TEXT,
        badge_text TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS branches (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        address TEXT NOT NULL,
        postal_address TEXT,
        phone TEXT,
        email TEXT,
        image_path TEXT,
        map_url TEXT
    );

    CREATE TABLE IF NOT EXISTS corporate_videos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        video_url TEXT NOT NULL,
        thumbnail_url TEXT
    );

    CREATE TABLE IF NOT EXISTS services (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        slug TEXT UNIQUE NOT NULL,
        description TEXT,
        image_path TEXT,
        sort_order INTEGER DEFAULT 0
    );

    CREATE TABLE IF NOT EXISTS rental_requests (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        full_name TEXT NOT NULL,
        phone TEXT NOT NULL,
        entity TEXT,
        condition TEXT NOT NULL,
        from_date TEXT NOT NULL,
        till_date TEXT NOT NULL,
        full_address TEXT NOT NULL,
        location TEXT NOT NULL,
        max_weight TEXT,
        max_height TEXT,
        forks_length TEXT,
        application_work TEXT,
        specifications TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS sell_machine_requests (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        full_name TEXT NOT NULL,
        phone TEXT NOT NULL,
        email TEXT NOT NULL,
        model TEXT NOT NULL,
        power TEXT,
        serial_number TEXT,
        photo_path TEXT,
        condition_details TEXT,
        location TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS part_requests (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        full_name TEXT NOT NULL,
        phone TEXT NOT NULL,
        entity TEXT NOT NULL,
        part_description TEXT NOT NULL,
        quantity INTEGER NOT NULL,
        brand TEXT NOT NULL,
        model TEXT NOT NULL,
        serial_number TEXT,
        location TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );
");

// Seed default admin user if none exists
$userCount = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
if ($userCount == 0) {
    // Seed default admin with password 'admin123'
    $defaultPass = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
    $stmt->execute([
        ':username' => 'admin',
        ':password' => $defaultPass
    ]);
}

// Seed default site settings if none exists
$settingsCount = $db->query("SELECT COUNT(*) FROM site_settings")->fetchColumn();
if ($settingsCount == 0) {
    $defaultSettings = [
        'site_name' => 'Y. Skembedjis & Sons Ltd',
        'contact_email' => 'info@skembedjis.com',
        'contact_phone' => '+357 25 712265',
        'address' => 'Limassol, Cyprus',
        'footer_text' => '© ' . date('Y') . ' Y. Skembedjis & Sons Ltd. All rights reserved.'
    ];

    $stmt = $db->prepare("INSERT OR IGNORE INTO site_settings (key, value) VALUES (:key, :value)");
    foreach ($defaultSettings as $key => $val) {
        $stmt->execute([':key' => $key, ':value' => $val]);
    }
}

// Seed default content sections if empty
$sectionsCount = $db->query("SELECT COUNT(*) FROM content_sections")->fetchColumn();
if ($sectionsCount == 0) {
    $defaultSections = [
        ['home', 'hero_caption_title', 'STRONG PARTNERS.', ''],
        ['home', 'hero_caption_subtitle', 'TOUGH TRUCKS.™', ''],
        ['home', 'cta_top_text_1', '', 'Does your business experience peak periods which places extra demands on both equipment and manpower?'],
        ['home', 'cta_top_text_2', '', 'For the best options in Forklift Sales and service, contact us today!'],
        ['home', 'cta_top_text_3', '', 'Explore our comprehensive services for Forklift Sales and rentals.'],
        ['home', 'cta_top_btn_text', '', 'Rent Now'],
        ['home', 'solutions_title', 'Industry Solutions', ''],
        ['home', 'solutions_desc', '', 'We have the solution for every industry!'],
        ['home', 'handling_title', 'Your Complete Material Handling Solution', ''],
        ['home', 'handling_subtitle', 'SINCE 1971', ''],
        ['home', 'handling_desc', '', 'Driven by reliability and expertise since 1971, Y. Skembedjis & Sons Ltd has become a reference point in the supply of forklift trucks & warehouse equipment in Cyprus and abroad.'],
        ['home', 'handling_btn_text', '', 'Explore Our Equipment'],
        ['home', 'catalog_btn_text', '', 'Download Our Catalog'],
        ['home', 'training_title', 'Operator Training', ''],
        ['home', 'training_desc', '', 'We put safety first with our comprehensive forklift operator safety training programs. Our engaging instructors provide hands-on learning to give your crew the edge in safe, effective equipment operations.'],
        ['home', 'training_btn_text', '', 'Find Out More'],
        ['home', 'join_title', 'Be Part of Y. Skembedjis', ''],
        ['home', 'join_desc', '', 'If you want to keep Cyprus lifting, then why not join us?'],
        ['home', 'join_btn_text', '', 'Careers'],
        ['home', 'hiring_text', '', 'We are hiring!'],
        ['home', 'collaborators_title', 'Our Collaborators', ''],
        ['home', 'websites_title', 'Visit Our Websites', ''],
        ['home', 'b2b_title', 'We make everything easy for you!', ''],
        ['home', 'b2b_desc', '', 'Download our B2B app and request an offer or service'],
        ['home', 'instagram_title', 'Instagram Feed', ''],
        ['spare_parts', 'parts_title', 'Spare Parts', ''],
        ['spare_parts', 'parts_banner_title', 'Not in Stock?', ''],
        ['spare_parts', 'parts_banner_subtitle', 'No Problem!', ''],
        ['spare_parts', 'parts_cta_title', 'Does your forklift require a part ?', ''],
        ['spare_parts', 'parts_cta_body', '', 'Then we probably have all you need in stock at our warehouses in Limassol or Nicosia. At Y. Skembedjis and Sons we do our best to keep the parts for all trucks and equipment we sell in stock, in our efforts to minimize the downtime for our client trucks. We have the largest selection of New and Used Forklift Parts warehouse in Cyprus ensuring that we can accommodate your needs for a repair in the most economical way without jeopardizing the quality of the products.'],
        ['spare_parts', 'parts_cta_btn_text', '', 'Request A Part'],
        ['spare_parts', 'parts_store_title', 'Forklift Spare Parts Store', ''],
        ['spare_parts', 'parts_store_details', '', "1 Agoras Street, Ypsonas Industrial Area\n3056 Limassol – Cyprus\n\nPostal Address: P.O.Box 53312,\n3302 Limassol – Cyprus\n\n+357 25 712 265 +357 25 710 413\nforkliftparts@skembedjis.com"],
        ['about', 'about_hero_title', '50+ Proud Years', ''],
        ['about', 'about_intro_title', 'About Us', ''],
        ['about', 'about_intro_body', '', 'Y. Skembedjis & Sons Ltd has been serving customers since 1971. Through experienced leadership, steady growth and award winning success each of our two locations provides world-class, customer-first support, service and equipment.'],
        ['about', 'about_history_body', '', "Driven by reliability and expertise, Y. Skembedjis & Sons Ltd has become a reference point in the supply of lift trucks and logistic equipment in Cyprus and abroad. Y. Skembedjis & Sons Ltd was established in Famagusta, by Yiannakis Skembedjis who was initially providing only forklift hiring services.\n\nAt the moment the company is managed by Costa, Stalo, Nicola & George Skembedjis who are committed to the same values embedded within the company since its establishment.\n\nThe company is currently the sole agent of HYSTER, HC Forklifts & EP Warehouse Equipment – leading manufacturers of supply-chain machinery – as well as EASYRAMPS UK lifting ramps, Trelleborg tyres and wheels, FAAM Italian batteries, and ATIB ELETTRONICA Italian chargers. In addition, the company offers Hako cleaning solutions, including the Scrubmaster and Sweepmaster ranges. Furthermore, the company is the service provider for Yale and Combilift trucks.\n\nThe company’s objective is to offer its partners the most suitable machinery adapted to their needs, at the highest quality and the most competitive rates.\n\nThe company’s privately owned premises, covering an area of 10000m2 in both Limassol and Nicosia..."],
        ['about', 'about_card_1_title', 'About The Company', ''],
        ['about', 'about_card_1_content', '', "*Branches\n*Corporate Videos"],
        ['about', 'about_card_2_title', 'Engagement & Values', ''],
        ['about', 'about_card_2_content', '', "*Quality\n*Vision, Mission & Values"],
        ['about', 'about_card_3_title', 'Board Of Directors', ''],
        ['about', 'about_card_3_content', '', "1. COSTAS SKEMBEDJIS\nChairman of the Board of Directors\n\n2. ANDROULLA SKEMBEDJI\nExecutive Director/Founder\n\n3. STALO SKEMBEDJI\nExecutive Director\n\n4. NIKOLAS SKEMBEDJIS\nExecutive Director"],
        ['about', 'about_card_4_title', 'Ownership Structure', ''],
        ['about', 'about_card_4_content', '', "100% YIANNAKIS SKEMBEDJIS LTD"],
        ['rentals', 'rentals_title', 'Rentals', ''],
        ['rentals', 'rentals_banner_text', 'Does your business experience peak periods which places extra demands on both equipment and manpower?', ''],
        ['rentals', 'rentals_col1_heading', 'Remember – you don’t have to own a truck to use it!', ''],
        ['rentals', 'rentals_col1_body', '', 'Y. Skembedjis & Sons Ltd can satisfy your rental needs in Cyprus by offering an expansive hire and leasing fleet, with a wide range of forklift trucks. This includes both NEW and Used Equipment, for Short term rental, Long term rental, Seasonal rental, Emergencies and Special Projects rental.'],
        ['rentals', 'rentals_col1_commit', '', 'We commit to: A rapid response to ensure that your operations keep running. Guaranteed performance levels of all forklift trucks and machinery. Total support from the Y.Skembedjis & Sons Ltd service team, with rapid and guaranteed response times.'],
        ['rentals', 'rentals_col2_body1', '', 'We can provide you with the right truck for your specific needs whether that is a truck of Diesel, LPG, Gasoline or Electric Motive Power. Every application requires particular truck specifications both in capacity and fuel type. We would like to ensure you that we will assist you with the selection of a truck to meet those needs.'],
        ['rentals', 'rentals_col2_body2', '', 'Keep in mind that we have the ability for a tailor plan to suit your specific time requirements for rentals. The plans are ranging from a day to a week, to months or years; all these delivered fast – with support through our service system ensuring you achieve maximum productivity from the selected machinery.'],
        ['rentals', 'rentals_col2_body3', '', 'Please don’t hesitate to ask for the full range of equipment specifications we have available.'],
        ['rentals', 'rentals_cta_title', 'Rent your machine online', ''],
        ['sell_machine', 'sell_machine_title', 'Sell Your Machine', ''],
        ['sell_machine', 'sell_machine_subtitle', 'Submit your material handling equipment details and upload a photo to receive a custom market valuation from our sales team.', '']
    ];

    $stmt = $db->prepare("INSERT OR IGNORE INTO content_sections (page, section_key, title, content) VALUES (:page, :section_key, :title, :content)");
    foreach ($defaultSections as $sect) {
        $stmt->execute([
            ':page' => $sect[0],
            ':section_key' => $sect[1],
            ':title' => $sect[2],
            ':content' => $sect[3]
        ]);
    }
}

// Seed default image configurations if empty
$imagesCount = $db->query("SELECT COUNT(*) FROM site_images")->fetchColumn();
if ($imagesCount == 0) {
    $defaultImages = [
        ['logo', 'assets/logo-skembedjis.webp', 'Y. Skembedjis & Sons Ltd Logo'],
        ['footer_logo', 'assets/footer-logo.webp', 'Y. Skembedjis & Sons Ltd Footer Logo'],
        ['hero_bg', 'placeholder.php?text=Hyster+Forklifts+Background&w=1920&h=900', 'Forklift banner background image'],
        ['hyster_badge', 'placeholder.php?text=Hyster+Yellow+Badge&w=200&h=200', 'Hyster Strong Partners Yellow Badge'],
        ['sol_1', 'placeholder.php?text=Industry+Sol+1&w=300&h=300', 'Industry Solution Image 1'],
        ['sol_2', 'placeholder.php?text=Industry+Sol+2&w=300&h=300', 'Industry Solution Image 2'],
        ['sol_3', 'placeholder.php?text=Industry+Sol+3&w=300&h=300', 'Industry Solution Image 3'],
        ['sol_4', 'placeholder.php?text=Industry+Sol+4&w=300&h=300', 'Industry Solution Image 4'],
        ['catalog_cover', 'placeholder.php?text=Products+and+Services+Catalog&w=600&h=800', 'Catalog Cover Sheet'],
        ['catalog_small_icon', 'placeholder.php?text=Small+Icon&w=80&h=40', 'Catalogue Download icon'],
        ['training_bg', 'placeholder.php?text=Operator+Training+Background&w=1920&h=700', 'Training background in warehouse'],
        ['careers_img', 'placeholder.php?text=Careers+Showcase&w=800&h=500', 'Team members working with technology'],
        ['collab_1', 'placeholder.php?text=ATIB+Elettronica&w=200&h=80', 'ATIB Elettronica Logo'],
        ['collab_2', 'placeholder.php?text=Easyramps&w=200&h=80', 'Easyramps Logo'],
        ['collab_3', 'placeholder.php?text=Hyster&w=200&h=80', 'Hyster Partner Logo'],
        ['collab_4', 'placeholder.php?text=AA&w=200&h=80', 'AA Partner Logo'],
        ['web_hyster', 'placeholder.php?text=Hyster+Web&w=200&h=200', 'Hyster Website Link Logo'],
        ['web_hchouse', 'placeholder.php?text=HC+Forklift+House&w=250&h=150', 'HC Forklift House Logo'],
        ['web_ed', 'placeholder.php?text=ED+Web&w=200&h=200', 'ED Logo Link'],
        ['app_store_btn', 'placeholder.php?text=Download+on+App+Store&w=200&h=60', 'App Store Download Badge'],
        ['google_play_btn', 'placeholder.php?text=Get+it+on+Google+Play&w=200&h=60', 'Google Play Download Badge'],
        ['insta_1', 'placeholder.php?text=Instagram+Feed+1&w=400&h=400', 'Instagram Feed 1'],
        ['insta_2', 'placeholder.php?text=Instagram+Feed+2&w=400&h=400', 'Instagram Feed 2'],
        ['insta_3', 'placeholder.php?text=Instagram+Feed+3&w=400&h=400', 'Instagram Feed 3'],
        ['parts_collage', 'placeholder.php?text=Forklift+Spare+Parts+Collection&w=800&h=500', 'Spare parts display collage'],
        ['about_hero', 'placeholder.php?text=Historic+Famagusta+Store+1971&w=1200&h=600', 'Historic Famagusta Shop - Established in 1971'],
        ['about_founder', 'assets/YIANNAKIS-SKEMBEDJIS-copy.jpeg', 'Founder Yiannakis Skembedjis Portrait'],
        ['rentals_banner_bg', 'placeholder.php?text=Forklift+Rentals+Hero&w=900&h=500', 'Rentals Banner Background Image'],
        ['sell_machine_banner', 'placeholder.php?text=Sell+Your+Machine+Banner&w=1200&h=500', 'Sell Your Machine page top banner'],
        ['repairs_mobile_van', 'placeholder.php?text=Mobile+Service+Van+Interior&w=800&h=500', 'Mobile service van equipped with tools'],
        ['repairs_before_after', 'placeholder.php?text=Before+After+Reconditioning&w=800&h=400', 'Forklift before and after reconditioning'],
        ['training_hero_img', 'placeholder.php?text=Operator+Training+Session&w=800&h=500', 'Forklift operator training in warehouse'],
        ['itssar_logo', 'placeholder.php?text=ITSSAR+UK&w=120&h=100', 'ITSSAR UK Accreditation Logo'],
        ['anad_logo', 'placeholder.php?text=ANAD&w=120&h=100', 'ANAD Accreditation Logo'],
        ['training_forklift', 'placeholder.php?text=Hyster+Forklift&w=250&h=300', 'Hyster Counterbalance Forklift'],
        ['training_reach', 'placeholder.php?text=Reach+Truck&w=250&h=300', 'Hyster Reach Truck'],
        ['training_stacker', 'placeholder.php?text=Pallet+Stacker&w=250&h=300', 'Pallet Stacker Equipment'],
        ['training_pallet', 'placeholder.php?text=Pallet+Truck&w=250&h=300', 'Pallet Truck'],
        ['training_order_picker', 'placeholder.php?text=Order+Picker&w=250&h=300', 'Order Picker Machine'],
        ['training_vna', 'placeholder.php?text=VNA+Truck&w=250&h=300', 'Very Narrow Aisle Truck'],
        ['training_mewp', 'placeholder.php?text=MEWP+Platform&w=250&h=300', 'Mobile Elevating Working Platform'],
        ['cat_forklifts', 'placeholder.php?text=Forklifts+Category&w=300&h=300', 'Forklifts Category Image'],
        ['cat_reach_trucks', 'placeholder.php?text=Reach+Trucks+Category&w=300&h=300', 'Reach Trucks Category Image'],
        ['cat_stackers', 'placeholder.php?text=Stackers+Category&w=300&h=300', 'Stackers Category Image'],
        ['cat_pallet_trucks', 'placeholder.php?text=Pallet+Trucks+Category&w=300&h=300', 'Pallet Trucks Category Image'],
        ['cat_order_pickers', 'placeholder.php?text=Order+Pickers+Category&w=300&h=300', 'Order Pickers Category Image'],
        ['cat_vna', 'placeholder.php?text=VNA+Category&w=300&h=300', 'VNA Category Image'],
        ['cat_truck_mounted', 'placeholder.php?text=Truck+Mounted+Category&w=300&h=300', 'Truck Mounted Category Image'],
        ['cat_tyres', 'placeholder.php?text=Tyres+Category&w=300&h=300', 'Tyres Category Image'],
        ['cat_handling', 'placeholder.php?text=Handling+Equipment+Category&w=300&h=300', 'Handling Equipment Category Image'],
        ['cat_attachments', 'placeholder.php?text=Attachments+Category&w=300&h=300', 'Attachments Category Image'],
        ['cat_batteries', 'placeholder.php?text=Batteries+Chargers+Category&w=300&h=300', 'Batteries & Chargers Category Image'],
        ['cat_ramps', 'placeholder.php?text=Ramps+Category&w=300&h=300', 'Ramps Category Image'],
        ['badge_iso', 'placeholder.php?text=ISO+9001+Badge&w=150&h=150', 'ISO 9001 Certification Badge'],
        ['badge_iqnet', 'placeholder.php?text=IQNET+Badge&w=150&h=150', 'IQNET Certification Badge'],
        ['badge_itssar_accredited', 'placeholder.php?text=ITSSAR+Accredited+Badge&w=150&h=150', 'ITSSAR Accredited Training Provider Badge'],
        ['hyster_banner_forklift', 'placeholder.php?text=Hyster+Forklift+Product+Banner&w=600&h=500', 'Hyster Forklift Split Banner Left Image']
    ];

    $stmt = $db->prepare("INSERT OR IGNORE INTO site_images (image_key, image_path, alt_text) VALUES (:key, :path, :alt)");
    foreach ($defaultImages as $img) {
        $stmt->execute([
            ':key' => $img[0],
            ':path' => $img[1],
            ':alt' => $img[2]
        ]);
    }
}

// Seed default FAQs if empty
$faqsCount = $db->query("SELECT COUNT(*) FROM faqs")->fetchColumn();
if ($faqsCount == 0) {
    $defaultFaqs = [
        [
            'What types of forklifts do you supply?',
            'We offer a wide range of brand-new and pre-owned forklifts, including diesel, LPG, and electric models, primarily specializing in Hyster machinery.',
            1
        ],
        [
            'Do you provide forklift rentals?',
            'Yes, we offer flexible short-term and long-term rental plans tailored to meet your business peak demands and logistics requirements.',
            2
        ],
        [
            'Are your training programs certified?',
            'Yes, our Operator Training Center is ITSSAR UK-certified, providing hands-on certification for forklift and warehouse equipment safety.',
            3
        ],
        [
            'Do you offer maintenance and spare parts?',
            'Absolutely. We provide comprehensive repair services across Cyprus and stock a vast catalog of genuine spare parts to minimize downtime.',
            4
        ]
    ];

    $stmt = $db->prepare("INSERT INTO faqs (question, answer, sort_order) VALUES (:q, :a, :order)");
    foreach ($defaultFaqs as $f) {
        $stmt->execute([
            ':q' => $f[0],
            ':a' => $f[1],
            ':order' => $f[2]
        ]);
    }
}

// Seed default blog posts if empty
$postsCount = $db->query("SELECT COUNT(*) FROM blog_posts")->fetchColumn();
if ($postsCount == 0) {
    $defaultPosts = [
        [
            'The Importance of Watering a Forklift Battery',
            'importance-watering-forklift-battery',
            'Forklifts are an essential piece of machinery for various sectors, including manufacturing and warehousing. In order to keep running and functioning properly, it\'s important they have routine maintenance. An example of this is ensuring things like the battery are hydrated appropriately. This blog will take a deep dive into the specifics of what is needed in order to keep your forklift battery hydrated including a step-by-step tutorial of how [...]',
            'July 13, 2026',
            'placeholder.php?text=Forklift+Battery+Watering&w=600&h=400',
            ''
        ],
        [
            'The Rise of Autonomous Forklifts: How AI Is Transforming Warehouse Operations in 2026',
            'rise-autonomous-forklifts-ai-2026',
            'The forklift industry is undergoing a major transformation. As businesses face increasing pressure to improve efficiency, reduce costs, and address labour shortages, autonomous forklifts are quickly becoming a key part of modern warehouse operations. What Are Autonomous Forklifts? 1. Labour Shortages in Logistics One of the biggest challenges facing the logistics sector is the shortage of skilled forklift operators. Autonomous forklifts help bridge this gap by reducing reliance on manual labour [...]',
            'July 8, 2026',
            'placeholder.php?text=Autonomous+Forklift&w=600&h=400',
            ''
        ],
        [
            'The Future of Forklifts: Trends in Automation and Electric Technology',
            'future-forklifts-trends-automation-electric',
            'The material handling industry is undergoing a major transformation, and forklifts are at the center of it. With rapid advancements in automation, electric power, and smart technologies, the future of forklifts looks not only more efficient—but also safer, greener, and more intelligent. In this blog, we\'ll explore the top forklift trends shaping the future, including automation, electric forklift adoption, telemetry systems, and sustainability initiatives. Whether you\'re a warehouse manager, fleet operator, or equipment dealer, [...]',
            'June 17, 2026',
            'placeholder.php?text=Future+Electric+Forklifts&w=600&h=400',
            ''
        ],
        [
            'Ενοικίαση',
            'rental-benefits-leasing',
            'Η Εταιρεία Y. Skembedjis & Sons LTD προσφέρει επιλογές ενοικίασης μηχανημάτων! Διαβάστε Περισσότερα για τα πλεονεκτήματα της ενοικίασης.Τα Οφέλη της Ενοικίασης ενός Περονοφόρου για την Επιχείρησή ΣαςΌταν πρόκειται για διαχείριση υλικών και λειτουργίες αποθήκης, τα περονοφόρα ανυψωτικά είναι ένα απαραίτητο εργαλείο για την αποδοτικότητα και την παραγωγικότητα. Ωστόσο, η απόφαση μεταξύ αγοράς και ενοικίασης ενός περονοφόρου μπορεί να είναι δύσκολη για τις επιχειρήσεις. Η ενοικίαση ενός περονοφόρου προσφέρει αρκετά πλεονεκτήματα [...]',
            'March 4, 2025',
            'placeholder.php?text=Forklift+Leasing&w=600&h=400',
            'Benefits of Leasing'
        ],
        [
            'Hyster Training',
            'hyster-training-education',
            'Επένδυση στην εξέλιξη του προσωπικού μας! Ολοκληρώθηκε μια ακόμη εκπαίδευση από εξειδικευμένο προσωπικό της εταιρείας Hyster.',
            'September 18, 2024',
            'placeholder.php?text=Hyster+Training&w=600&h=400',
            ''
        ],
        [
            'Join Our Team',
            'join-our-team-job-opening',
            'Η εταιρεία Γ. Σκεμπετζής & Υιοί ΛΤΔ εισαγωγή μηχανημάτων εφοδιαστικής αλυσίδας, επιθυμεί να εργοδοτήσει άμεσα Μηχανικό Βαρέων Οχημάτων...',
            'July 5, 2023',
            'placeholder.php?text=Join+Our+Team&w=600&h=400',
            ''
        ]
    ];

    $stmt = $db->prepare("INSERT INTO blog_posts (title, slug, excerpt, content, image_path, badge_text, created_at) VALUES (:title, :slug, :excerpt, :content, :img, :badge, :date)");
    foreach ($defaultPosts as $p) {
        $stmt->execute([
            ':title' => $p[0],
            ':slug' => $p[1],
            ':excerpt' => $p[2],
            ':content' => $p[2],
            ':img' => $p[4],
            ':badge' => $p[5],
            ':date' => date('Y-m-d H:i:s', strtotime($p[3]))
        ]);
    }
}

// Seed default branches if empty
$branchesCount = $db->query("SELECT COUNT(*) FROM branches")->fetchColumn();
if ($branchesCount == 0) {
    $defaultBranches = [
        [
            'Limassol (Headquarters & Spare Parts Store)',
            '1 Agoras Street, Ypsonas Industrial Area, 3056 Limassol, Cyprus',
            'P.O.Box 53312, 3302 Limassol, Cyprus',
            '+357 25 712 265',
            'forkliftparts@skembedjis.com',
            'assets/Limassol-Headquarters-outside.jpg',
            'https://www.google.com/maps/search/?api=1&query=Y.+Skembedjis+%26+Sons+Ltd+Limassol'
        ],
        [
            'Nicosia Branch',
            'Peiraios 6, Block A, Storage no. 6, Latsia Area, Nicosia, Cyprus',
            '',
            '+357 22 488 400',
            'nicosia@skembedjis.com',
            'assets/Showroom-Nicosia-outside.jpg',
            'https://www.google.com/maps/search/?api=1&query=Y.+Skembedjis+%26+Sons+Ltd+Nicosia'
        ],
        [
            'Training Centre',
            '119 Franklin Roosevelt, 3011 Limassol, Cyprus',
            '',
            '+357 25 878 700',
            'training@skembedjis.com',
            'assets/Training.jpg',
            'https://www.google.com/maps/search/?api=1&query=Y.+Skembedjis+%26+Sons+Ltd+Training+Centre+Limassol'
        ]
    ];

    $stmt = $db->prepare("INSERT INTO branches (name, address, postal_address, phone, email, image_path, map_url) VALUES (:name, :address, :postal, :phone, :email, :img, :map)");
    foreach ($defaultBranches as $b) {
        $stmt->execute([
            ':name' => $b[0],
            ':address' => $b[1],
            ':postal' => $b[2],
            ':phone' => $b[3],
            ':email' => $b[4],
            ':img' => $b[5],
            ':map' => $b[6]
        ]);
    }
}

// Seed default corporate videos if empty
$videosCount = $db->query("SELECT COUNT(*) FROM corporate_videos")->fetchColumn();
if ($videosCount == 0) {
    $defaultVideos = [
        [
            'Nicosia Showroom Tour',
            'assets/XRYSES-ETAIRIES-SKEMBEDJIS-SONS-NICOSIA-SHOWROOM.mp4',
            ''
        ],
        [
            'Nicosia Service Center Facility Video',
            'assets/XRYSES-ETAIRIES-SKEMBEDJIS-SONS-NICOSIA-SERVICE-CENTER.mp4',
            ''
        ],
        [
            'Limassol Service Center Facility Video',
            'assets/XRYSES-ETAIRIES-SKEMBEDJIS-SONS-LIMASSOL-SERVICE-CENTER.mp4',
            ''
        ],
        [
            'Material Handling Equipment & Safety Training Center Video',
            'assets/XRYSES-ETAIRIES-SKEMBEDJIS-SONS-HANDLING-EQUIPMENT-SAFETY-TRAINING-CENTER.mp4',
            ''
        ],
        [
            'VNA Project Showcase Video',
            'assets/FINAL-Video-VNA-Project-skembedjis-Logo-only.mp4',
            ''
        ]
    ];

    $stmt = $db->prepare("INSERT INTO corporate_videos (title, video_url, thumbnail_url) VALUES (:title, :url, :thumb)");
    foreach ($defaultVideos as $v) {
        $stmt->execute([
            ':title' => $v[0],
            ':url' => $v[1],
            ':thumb' => $v[2]
        ]);
    }
}

// Seed default services if empty
$servicesCount = $db->query("SELECT COUNT(*) FROM services")->fetchColumn();
if ($servicesCount == 0) {
    $defaultServices = [
        [
            'Rentals',
            'rentals',
            'We offer flexible rental programs customized to meet your company demands and peak workloads.',
            'placeholder.php?text=Forklift+Rentals&w=600&h=400',
            1
        ],
        [
            'Sell Your Machine',
            'sell-machine',
            'Let us inspect and help you sell your used material handling equipment at premium market rates.',
            'placeholder.php?text=Sell+Your+Machine&w=600&h=400',
            2
        ],
        [
            'Repairs & Services',
            'repairs-services',
            'Our certified mechanics carry out repair and maintenance services on all forklift truck models.',
            'placeholder.php?text=Repairs+and+Services&w=600&h=400',
            3
        ],
        [
            'Operator Training',
            'operator-training',
            'Get your crew safety certified with our ITSSAR UK accredited forklift driver safety courses.',
            'placeholder.php?text=Operator+Training&w=600&h=400',
            4
        ]
    ];

    $stmt = $db->prepare("INSERT INTO services (title, slug, description, image_path, sort_order) VALUES (:title, :slug, :desc, :img, :order)");
    foreach ($defaultServices as $s) {
        $stmt->execute([
            ':title' => $s[0],
            ':slug' => $s[1],
            ':desc' => $s[2],
            ':img' => $s[3],
            ':order' => $s[4]
        ]);
    }
}

// Helper functions for content retrieval and updating

function get_setting($key, $default = '') {
    global $db;
    $stmt = $db->prepare("SELECT value FROM site_settings WHERE key = :key");
    $stmt->execute([':key' => $key]);
    $val = $stmt->fetchColumn();
    return $val !== false ? $val : $default;
}

function set_setting($key, $value) {
    global $db;
    $stmt = $db->prepare("INSERT OR REPLACE INTO site_settings (key, value) VALUES (:key, :value)");
    $stmt->execute([':key' => $key, ':value' => $value]);
}

function get_section($page, $section_key, $default_title = '', $default_content = '') {
    global $db;
    $stmt = $db->prepare("SELECT title, content FROM content_sections WHERE page = :page AND section_key = :section_key");
    $stmt->execute([':page' => $page, ':section_key' => $section_key]);
    $res = $stmt->fetch();
    if ($res) {
        return $res;
    }
    // If not found, insert default and return it
    $stmtInsert = $db->prepare("INSERT OR IGNORE INTO content_sections (page, section_key, title, content) VALUES (:page, :section_key, :title, :content)");
    $stmtInsert->execute([
        ':page' => $page,
        ':section_key' => $section_key,
        ':title' => $default_title,
        ':content' => $default_content
    ]);
    return ['title' => $default_title, 'content' => $default_content];
}

function update_section($page, $section_key, $title, $content) {
    global $db;
    $stmt = $db->prepare("INSERT OR REPLACE INTO content_sections (page, section_key, title, content) VALUES (:page, :section_key, :title, :content)");
    $stmt->execute([
        ':page' => $page,
        ':section_key' => $section_key,
        ':title' => $title,
        ':content' => $content
    ]);
}

function get_image_path($key, $default_alt = 'Placeholder') {
    global $db;
    $stmt = $db->prepare("SELECT image_path, alt_text FROM site_images WHERE image_key = :key");
    $stmt->execute([':key' => $key]);
    $res = $stmt->fetch();
    if ($res) {
        return $res['image_path'];
    }
    // If not found, return a placeholder path (we'll generate placeholders dynamically)
    return "placeholder.php?text=" . urlencode($default_alt);
}

function get_faqs() {
    global $db;
    return $db->query("SELECT * FROM faqs ORDER BY sort_order, id")->fetchAll();
}

function get_blog_posts() {
    global $db;
    return $db->query("SELECT * FROM blog_posts ORDER BY created_at DESC")->fetchAll();
}

function get_branches() {
    global $db;
    return $db->query("SELECT * FROM branches ORDER BY id ASC")->fetchAll();
}

function get_corporate_videos() {
    global $db;
    return $db->query("SELECT * FROM corporate_videos ORDER BY id ASC")->fetchAll();
}

function get_services() {
    global $db;
    return $db->query("SELECT * FROM services ORDER BY sort_order, id ASC")->fetchAll();
}

function get_product_categories() {
    global $db;
    return $db->query("SELECT * FROM product_categories ORDER BY sort_order, id ASC")->fetchAll();
}

function get_products($filters = []) {
    global $db;
    
    $query = "SELECT p.*, c.name as category_name, c.slug as category_slug 
              FROM products p 
              JOIN product_categories c ON p.category_id = c.id";
    
    $where = [];
    $params = [];
    
    if (!empty($filters['category'])) {
        $where[] = "c.slug = :category";
        $params[':category'] = $filters['category'];
    }
    
    if (!empty($filters['status'])) {
        $where[] = "p.status = :status";
        $params[':status'] = $filters['status'];
    }
    
    if (!empty($filters['brand'])) {
        $where[] = "p.brand = :brand";
        $params[':brand'] = $filters['brand'];
    }

    if (!empty($filters['capacity'])) {
        $where[] = "p.lifting_capacity = :capacity";
        $params[':capacity'] = $filters['capacity'];
    }

    if (!empty($filters['energy'])) {
        $where[] = "p.energy = :energy";
        $params[':energy'] = $filters['energy'];
    }

    if (isset($filters['min_price']) && is_numeric($filters['min_price'])) {
        $where[] = "p.price >= :min_price";
        $params[':min_price'] = (int)$filters['min_price'];
    }

    if (isset($filters['max_price']) && is_numeric($filters['max_price'])) {
        $where[] = "p.price <= :max_price";
        $params[':max_price'] = (int)$filters['max_price'];
    }
    
    if (!empty($filters['search'])) {
        $where[] = "(p.name LIKE :search OR p.description LIKE :search OR p.brand LIKE :search)";
        $params[':search'] = '%' . $filters['search'] . '%';
    }
    
    if (count($where) > 0) {
        $query .= " WHERE " . implode(" AND ", $where);
    }
    
    $query .= " ORDER BY p.id DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function get_product_brands() {
    global $db;
    return $db->query("SELECT DISTINCT brand FROM products WHERE brand IS NOT NULL AND brand != '' ORDER BY brand ASC")->fetchAll(PDO::FETCH_COLUMN);
}

function get_product_capacities() {
    global $db;
    return $db->query("SELECT DISTINCT lifting_capacity FROM products WHERE lifting_capacity IS NOT NULL AND lifting_capacity != 'N/A' AND lifting_capacity != '' ORDER BY lifting_capacity ASC")->fetchAll(PDO::FETCH_COLUMN);
}

function get_product_energies() {
    global $db;
    return $db->query("SELECT DISTINCT energy FROM products WHERE energy IS NOT NULL AND energy != 'N/A' AND energy != '' ORDER BY energy ASC")->fetchAll(PDO::FETCH_COLUMN);
}






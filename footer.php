<?php
// footer.php
// Recreated Premium 4-Column Footer include

require_once __DIR__ . '/db.php';
$siteName = get_setting('site_name', 'Y. Skembedjis & Sons Ltd');
$email = get_setting('contact_email', 'info@skembedjis.com');
$phone = get_setting('contact_phone', '+357 25 878700');
$address = get_setting('address', 'Limassol, Cyprus');
$footerText = get_setting('footer_text', '© ' . date('Y') . ' Y. Skembedjis & Sons Ltd. All rights reserved.');

$logoImg = get_image_path('logo', 'Skembedjis Logo');
?>
    <!-- Premium 4-Column Footer -->
    <footer class="footer-premium">
        <div class="container footer-premium-grid">
            <!-- Col 1: Logo & Facilities -->
            <div class="footer-col col-brand">
                <img src="<?php echo htmlspecialchars($logoImg); ?>" alt="<?php echo htmlspecialchars($siteName); ?> Logo" class="footer-logo">
                <a href="facilities.php" class="btn btn-blue-outline btn-sm" style="margin-top: 1rem;">Our Facilities</a>
            </div>
            
            <!-- Col 2: Material Handling Equipment (Two sub-lists) -->
            <div class="footer-col col-equipment">
                <h4>Material Handling Equipment</h4>
                <div class="sub-columns">
                    <ul class="footer-links-list">
                        <li><a href="new-equipment.php">New Equipment</a></li>
                        <li><a href="forklifts.php">Forklifts</a></li>
                        <li><a href="vna.php">Very Narrow Aisles</a></li>
                        <li><a href="truck-mounted.php">Truck Mounted Forklifts</a></li>
                        <li><a href="stackers.php">Stackers</a></li>
                    </ul>
                    <ul class="footer-links-list">
                        <li><a href="pallet-trucks.php">Pallet Trucks</a></li>
                        <li><a href="order-pickers.php">Order Pickers</a></li>
                        <li><a href="handling.php">Handling Equipment</a></li>
                        <li><a href="attachments.php">Attachments</a></li>
                        <li><a href="batteries-chargers.php">Batteries & Chargers</a></li>
                        <li><a href="ramps.php">Ramps</a></li>
                    </ul>
                </div>
            </div>
            
            <!-- Col 3: Service & Repair -->
            <div class="footer-col col-service">
                <h4>Service & Repair</h4>
                <ul class="footer-links-list">
                    <li><a href="spare-parts.php">Spare Parts</a></li>
                    <li><a href="rentals.php">Rentals</a></li>
                    <li><a href="operator-training.php">Operator Training</a></li>
                    <li><a href="sell-machine.php">Sell Your Machine</a></li>
                    <li><a href="repairs-services.php">Mobile Service Units</a></li>
                </ul>
            </div>
            
            <!-- Col 4: Company & Details -->
            <div class="footer-col col-company">
                <h4>Company</h4>
                <ul class="footer-links-list">
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="careers.php">Careers</a></li>
                    <li><a href="blog.php">Blog</a></li>
                    <li><a href="products.php">Equipment</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
                
                <div class="footer-accreditation">
                    <span class="phone-number">70004440</span>
                    <img src="placeholder.php?text=Safety+Accreditation&w=120&h=60" alt="Accreditation Safety Badge" class="accredit-badge">
                </div>
            </div>
        </div>
        
        <div class="footer-copyright">
            <div class="container copyright-container">
                <p><?php echo htmlspecialchars($footerText); ?></p>
                <p style="font-size: 0.8rem; opacity: 0.7; margin-top: 0.25rem;">
                    Contact: <?php echo htmlspecialchars($email); ?> | <?php echo htmlspecialchars($phone); ?> | <?php echo htmlspecialchars($address); ?>
                </p>
            </div>
        </div>
    </footer>

    <!-- Global Responsive JS Scripts -->
    <script>
        // Mobile Toggle Navigation Menu
        const menuToggle = document.getElementById('menuToggle');
        const navLinks = document.getElementById('navLinks');
        
        if (menuToggle && navLinks) {
            menuToggle.addEventListener('click', () => {
                navLinks.classList.toggle('active');
            });
        }

        // Mobile Dropdown Submenu Toggle
        const dropdownItems = document.querySelectorAll('.nav-item.dropdown');
        dropdownItems.forEach(item => {
            const link = item.querySelector('.nav-link');
            if (link) {
                link.addEventListener('click', (e) => {
                    if (window.innerWidth <= 768) {
                        e.preventDefault();
                        item.classList.toggle('active');
                    }
                });
            }
        });
    </script>
</body>
</html>

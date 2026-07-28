<?php
// footer.php
// Recreated Premium 4-Column Footer include

require_once __DIR__ . '/db.php';
$siteName = get_setting('site_name', 'Y. Skembedjis & Sons Ltd');
$email = get_setting('contact_email', 'info@skembedjis.com');
$phone = get_setting('contact_phone', '+357 25 712265');
$address = get_setting('address', 'Limassol, Cyprus');
$footerText = get_setting('footer_text', '© ' . date('Y') . ' Y. Skembedjis & Sons Ltd. All rights reserved.');

$footerLogoImg = get_image_path('footer_logo', 'assets/footer-logo.webp');
?>
    <!-- Premium 4-Column Footer -->
    <footer class="footer-premium">
        <div class="container footer-premium-grid">
            <!-- Col 1: Logo & Facilities -->
            <div class="footer-col col-brand">
                <img src="<?php echo htmlspecialchars($footerLogoImg); ?>" alt="<?php echo htmlspecialchars($siteName); ?> Logo" class="footer-logo">
                <a href="branches.php" class="btn btn-blue-outline btn-sm" style="margin-top: 1rem;">Our Facilities</a>
            </div>
            
            <!-- Col 2: Material Handling Equipment (Two sub-lists) -->
            <div class="footer-col col-equipment">
                <h4>Material Handling Equipment</h4>
                <div class="sub-columns">
                    <ul class="footer-links-list">
                        <li><a href="new-equipment">New Equipment</a></li>
                        <li><a href="product-category/forklifts">Forklifts</a></li>
                        <li><a href="product-category/vna">Very Narrow Aisles</a></li>
                        <li><a href="product-category/truck-mounted-forklifts">Truck Mounted Forklifts</a></li>
                        <li><a href="product-category/stackers">Stackers</a></li>
                    </ul>
                    <ul class="footer-links-list">
                        <li><a href="product-category/pallet-trucks">Pallet Trucks</a></li>
                        <li><a href="product-category/order-pickers">Order Pickers</a></li>
                        <li><a href="product-category/handling-equipment">Handling Equipment</a></li>
                        <li><a href="product-category/attachments">Attachments</a></li>
                        <li><a href="product-category/batteries-chargers">Batteries & Chargers</a></li>
                        <li><a href="product-category/ramps">Ramps</a></li>
                    </ul>
                </div>
            </div>
            
            <!-- Col 3: Service & Repair -->
            <div class="footer-col col-service">
                <h4>Service & Repair</h4>
                <ul class="footer-links-list">
                    <li><a href="spare-parts">Spare Parts</a></li>
                    <li><a href="rentals">Rentals</a></li>
                    <li><a href="operator-training">Operator Training</a></li>
                    <li><a href="sell-your-machine">Sell Your Machine</a></li>
                    <li><a href="mobile-service-unit">Mobile Service Units</a></li>
                </ul>
            </div>
            
            <!-- Col 4: Company & Details -->
            <div class="footer-col col-company">
                <h4>Company</h4>
                <ul class="footer-links-list">
                    <li><a href="about-us">About Us</a></li>
                    <li><a href="careers">Careers</a></li>
                    <li><a href="news">Blog</a></li>
                    <li><a href="products">Equipment</a></li>
                    <li><a href="contact-us">Contact Us</a></li>
                </ul>
                
                <div class="footer-social-wrapper">
                    <a href="tel:70004440" class="phone-number">70004440</a>
                    <div class="footer-social-icons">
                        <a href="https://www.facebook.com/skembedjis" target="_blank" rel="noopener noreferrer" class="social-icon-btn social-fb" aria-label="Facebook" title="Facebook">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="https://www.instagram.com/skembedjis/" target="_blank" rel="noopener noreferrer" class="social-icon-btn social-ig" aria-label="Instagram" title="Instagram">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                        <a href="https://www.linkedin.com/company/skembedjis" target="_blank" rel="noopener noreferrer" class="social-icon-btn social-li" aria-label="LinkedIn" title="LinkedIn">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="footer-copyright">
            <div class="container copyright-container">
                <p><?php echo htmlspecialchars($footerText); ?></p>
                <p style="font-size: 0.8rem; opacity: 0.7; margin-top: 0.25rem;">
                    Contact: <a href="mailto:<?php echo htmlspecialchars($email); ?>"><?php echo htmlspecialchars($email); ?></a> | <a href="tel:<?php echo htmlspecialchars(str_replace(' ', '', $phone)); ?>"><?php echo htmlspecialchars($phone); ?></a> | <?php echo htmlspecialchars($address); ?>
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

        // Global Search Overlay Toggle
        const searchBtn = document.getElementById('globalSearchBtn');
        const searchOverlay = document.getElementById('searchOverlay');
        const searchCloseBtn = document.getElementById('searchCloseBtn');
        const searchInput = document.getElementById('globalSearchInput');

        if (searchBtn && searchOverlay) {
            searchBtn.addEventListener('click', (e) => {
                e.preventDefault();
                searchOverlay.classList.add('active');
                if (searchInput) {
                    setTimeout(() => searchInput.focus(), 100);
                }
            });
        }

        if (searchCloseBtn && searchOverlay) {
            searchCloseBtn.addEventListener('click', () => {
                searchOverlay.classList.remove('active');
            });
        }

        if (searchOverlay) {
            searchOverlay.addEventListener('click', (e) => {
                if (e.target === searchOverlay) {
                    searchOverlay.classList.remove('active');
                }
            });

            // Close on ESC key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && searchOverlay.classList.contains('active')) {
                    searchOverlay.classList.remove('active');
                }
            });
        }
    </script>
</body>
</html>

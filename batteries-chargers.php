<?php
// batteries-chargers.php
// Beautiful custom information page for Batteries & Chargers matching live site brand and styles

require_once __DIR__ . '/db.php';

$current_page = 'batteries-chargers.php';
require_once __DIR__ . '/header.php';
?>

    <!-- Hero Section -->
    <section class="batteries-hero-sec" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 5rem 0; color: #ffffff; text-align: center; border-bottom: 3px solid var(--accent-orange);">
        <div class="container">
            <h1 style="font-family: var(--font-heading); font-size: 3rem; margin-bottom: 1rem; color: #ffffff; text-transform: uppercase; letter-spacing: 1px;">
                Batteries & Chargers
            </h1>
            <p style="font-size: 1.2rem; color: #94a3b8; max-width: 800px; margin: 0 auto; line-height: 1.6;">
                High-performance industrial traction batteries, modern charging solutions, and professional battery regeneration services in Cyprus.
            </p>
        </div>
    </section>

    <!-- Content Section -->
    <section class="batteries-content-sec" style="padding: 4rem 0; background-color: #f8fafc;">
        <div class="container">
            
            <!-- Overview Row -->
            <div style="background-color: #ffffff; border-radius: 4px; border: 1px solid var(--border-gray); padding: 3rem; box-shadow: 0 4px 20px rgba(0,0,0,0.02); margin-bottom: 4rem; text-align: left;">
                <h2 style="font-size: 1.8rem; color: #1e293b; font-weight: 800; margin-bottom: 1.5rem; font-family: var(--font-heading);">
                    Industrial Power Solutions
                </h2>
                <p style="color: #475569; font-size: 1.1rem; line-height: 1.8; margin-bottom: 0;">
                    At <strong>Y. Skembedjis & Sons</strong>, we provide complete, modern power supply and charging solutions for electric forklifts and warehouse equipment. We partner with leading international manufacturers to supply heavy-duty traction batteries and highly efficient electronic chargers designed to optimize your fleet's runtime and reduce energy costs.
                </p>
            </div>

            <!-- Batteries Grid -->
            <h2 style="font-size: 2rem; color: #1e293b; font-weight: 800; margin-bottom: 2.5rem; text-align: center; font-family: var(--font-heading); text-transform: uppercase;">
                Products & Services We Offer
            </h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; margin-bottom: 5rem;">
                
                <!-- Card 1 -->
                <div style="background: #ffffff; border: 1px solid var(--border-gray); border-radius: 4px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between;">
                    <div style="height: 200px; display: flex; align-items: center; justify-content: center; background-color: #ffffff; border-bottom: 1px solid #f1f5f9; padding: 1rem;">
                        <img src="assets/batteries/battery.png" alt="FAAM Traction Battery" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                    </div>
                    <div style="padding: 2rem; text-align: left; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <h3 style="font-size: 1.25rem; color: #1e293b; font-weight: 700; margin-bottom: 1rem;">Traction Batteries</h3>
                            <p style="color: #64748b; font-size: 0.95rem; line-height: 1.6; margin-bottom: 1.5rem;">
                                Our lead-acid traction batteries provide high efficiency, power, and durability for all electric material handling trucks. Includes a 3-year warranty for guaranteed operations.
                            </p>
                        </div>
                        <ul style="list-style-type: disc; padding-left: 1.25rem; color: #475569; font-size: 0.9rem; line-height: 1.6;">
                            <li>Advanced positive plates</li>
                            <li>Optimized cell sizing</li>
                            <li>3 Years Guarantee</li>
                            <li>Lithium options available</li>
                        </ul>
                    </div>
                </div>

                <!-- Card 2 -->
                <div style="background: #ffffff; border: 1px solid var(--border-gray); border-radius: 4px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between;">
                    <div style="height: 200px; display: flex; align-items: center; justify-content: center; background-color: #ffffff; border-bottom: 1px solid #f1f5f9; padding: 1rem;">
                        <img src="assets/batteries/charger.png" alt="ATIB Battery Charger" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                    </div>
                    <div style="padding: 2rem; text-align: left; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <h3 style="font-size: 1.25rem; color: #1e293b; font-weight: 700; margin-bottom: 1rem;">ATIB Charging Solutions</h3>
                            <p style="color: #64748b; font-size: 0.95rem; line-height: 1.6; margin-bottom: 1.5rem;">
                                Modern high-quality and cost-effective battery chargers from A.T.I.B. Elettronica. Offering both conventional 50Hz and high-frequency (HF) charger lines.
                            </p>
                        </div>
                        <ul style="list-style-type: disc; padding-left: 1.25rem; color: #475569; font-size: 0.9rem; line-height: 1.6;">
                            <li>50Hz Wa/Wsa Conventional line</li>
                            <li>HF W/IUIUa High Frequency line</li>
                            <li>Onboard DC/DC Converters</li>
                            <li>IP65 Isolated versions</li>
                        </ul>
                    </div>
                </div>

                <!-- Card 3 -->
                <div style="background: #ffffff; border: 1px solid var(--border-gray); border-radius: 4px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between;">
                    <div style="height: 200px; display: flex; align-items: center; justify-content: center; background-color: #ffffff; border-bottom: 1px solid #f1f5f9; padding: 1rem;">
                        <img src="assets/batteries/regeneration.png" alt="Battery Regeneration Station" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                    </div>
                    <div style="padding: 2rem; text-align: left; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <h3 style="font-size: 1.25rem; color: #1e293b; font-weight: 700; margin-bottom: 1rem;">Battery Regeneration</h3>
                            <p style="color: #64748b; font-size: 0.95rem; line-height: 1.6; margin-bottom: 1.5rem;">
                                Is your forklift battery lifecycle bad? We offer advanced battery regeneration services to recover sulfated cells and extend capacity by up to 30%.
                            </p>
                        </div>
                        <ul style="list-style-type: disc; padding-left: 1.25rem; color: #475569; font-size: 0.9rem; line-height: 1.6;">
                            <li>DDL-150 Battery Discharger test</li>
                            <li>HRC-3 Regenerating Station</li>
                            <li>Detailed capacity reports</li>
                            <li>Saves up to 30% battery life</li>
                        </ul>
                    </div>
                </div>

            </div>

            <!-- Brand Partnership Row -->
            <div style="background-color: #ffffff; border-radius: 4px; border: 1px solid var(--border-gray); padding: 3rem; box-shadow: 0 4px 20px rgba(0,0,0,0.02); margin-bottom: 4rem; text-align: center;">
                <h3 style="font-size: 1.6rem; color: #1e293b; font-weight: 800; margin-bottom: 2rem; font-family: var(--font-heading); text-transform: uppercase;">
                    Our Premium Partner Brands
                </h3>
                <div style="display: flex; justify-content: center; align-items: center; gap: 4rem; flex-wrap: wrap;">
                    <div style="background: #ffffff; padding: 0.5rem 1rem; border: 1px solid #f1f5f9; border-radius: 4px; width: 220px; height: 110px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.01);">
                        <img src="assets/collaborators/LOGO_FAAM.webp" alt="FAAM Traction Batteries Logo" style="max-width: 90%; max-height: 80%; object-fit: contain;">
                    </div>
                    <div style="background: #ffffff; padding: 0.5rem 1rem; border: 1px solid #f1f5f9; border-radius: 4px; width: 220px; height: 110px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.01);">
                        <img src="assets/collaborators/atib_logo.png" alt="ATIB Elettronica Chargers Logo" style="max-width: 90%; max-height: 80%; object-fit: contain;">
                    </div>
                </div>
            </div>

        </div>
    </section>

<?php
require_once __DIR__ . '/footer.php';
?>

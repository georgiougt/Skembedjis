<?php
// tyres.php
// Beautiful custom information page for Tyres & Wheels matching live site brand and styles

require_once __DIR__ . '/db.php';

$current_page = 'tyres.php';
require_once __DIR__ . '/header.php';
?>

    <!-- Hero Section -->
    <section class="tyres-hero-sec" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 5rem 0; color: #ffffff; text-align: center; border-bottom: 3px solid var(--accent-orange);">
        <div class="container">
            <h1 style="font-family: var(--font-heading); font-size: 3rem; margin-bottom: 1rem; color: #ffffff; text-transform: uppercase; letter-spacing: 1px;">
                Material Handling Tyres
            </h1>
            <p style="font-size: 1.2rem; color: #94a3b8; max-width: 800px; margin: 0 auto; line-height: 1.6;">
                Durable and cost-effective tyre solutions to keep your material handling operations running smoothly across Cyprus.
            </p>
        </div>
    </section>

    <!-- Content Section -->
    <section class="tyres-content-sec" style="padding: 4rem 0; background-color: #f8fafc;">
        <div class="container">
            
            <!-- Overview Row -->
            <div style="background-color: #ffffff; border-radius: 4px; border: 1px solid var(--border-gray); padding: 3rem; box-shadow: 0 4px 20px rgba(0,0,0,0.02); margin-bottom: 4rem; text-align: left;">
                <h2 style="font-size: 1.8rem; color: #1e293b; font-weight: 800; margin-bottom: 1.5rem; font-family: var(--font-heading);">
                    Forklift Tyres in Cyprus
                </h2>
                <p style="color: #475569; font-size: 1.1rem; line-height: 1.8; margin-bottom: 0;">
                    Does your forklift need new material handling tires? We’ve got you covered! At <strong>Y. Skembedjis & Sons</strong>, we stock a wide range of high-quality forklift tires at our warehouses in Limassol and Nicosia. Our goal is to minimize your truck’s downtime by providing durable and cost-effective tire solutions. As the largest supplier of new and used forklift parts in Cyprus, we ensure that you get the best products without compromising on quality. Contact us today for the right tires to keep your operations running smoothly!
                </p>
            </div>

            <!-- Tyres Grid -->
            <h2 style="font-size: 2rem; color: #1e293b; font-weight: 800; margin-bottom: 2.5rem; text-align: center; font-family: var(--font-heading); text-transform: uppercase;">
                Forklift Truck Tyres We Stock
            </h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 2rem; margin-bottom: 5rem;">
                
                <!-- Card 1 -->
                <div style="background: #ffffff; border: 1px solid var(--border-gray); border-radius: 4px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between;">
                    <div style="height: 200px; display: flex; align-items: center; justify-content: center; background-color: #ffffff; border-bottom: 1px solid #f1f5f9; padding: 1rem;">
                        <img src="assets/tyres/pneumatic.png" alt="Industrial Pneumatic Tyre" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                    </div>
                    <div style="padding: 2rem; text-align: left; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <h3 style="font-size: 1.25rem; color: #1e293b; font-weight: 700; margin-bottom: 1rem;">1. Industrial Pneumatic</h3>
                            <p style="color: #64748b; font-size: 0.95rem; line-height: 1.6; margin-bottom: 1.5rem;">
                                Designed for heavy-duty material handling operations on rough and uneven outdoor terrains. Features very high load capacity, high wear resistance, and low rolling resistance.
                            </p>
                        </div>
                        <ul style="list-style-type: disc; padding-left: 1.25rem; color: #475569; font-size: 0.9rem; line-height: 1.6;">
                            <li>High Load Capacity</li>
                            <li>Rough Outdoor Use</li>
                            <li>Low Rolling Resistance</li>
                        </ul>
                    </div>
                </div>

                <!-- Card 2 -->
                <div style="background: #ffffff; border: 1px solid var(--border-gray); border-radius: 4px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between;">
                    <div style="height: 200px; display: flex; align-items: center; justify-content: center; background-color: #ffffff; border-bottom: 1px solid #f1f5f9; padding: 1rem;">
                        <img src="assets/tyres/presson.png" alt="Press on Band Cushion Tyre" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                    </div>
                    <div style="padding: 2rem; text-align: left; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <h3 style="font-size: 1.25rem; color: #1e293b; font-weight: 700; margin-bottom: 1rem;">2. Press On Cushion</h3>
                            <p style="color: #64748b; font-size: 0.95rem; line-height: 1.6; margin-bottom: 1.5rem;">
                                Designed for indoor warehouse operations and smooth concrete floors. Built with a steel band molded to a thick rubber cushion for maximum stability and load carrying.
                            </p>
                        </div>
                        <ul style="list-style-type: disc; padding-left: 1.25rem; color: #475569; font-size: 0.9rem; line-height: 1.6;">
                            <li>Maximum Stability</li>
                            <li>Indoor Warehouse Use</li>
                            <li>Floor Protecting Compounds</li>
                        </ul>
                    </div>
                </div>

                <!-- Card 3 -->
                <div style="background: #ffffff; border: 1px solid var(--border-gray); border-radius: 4px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between;">
                    <div style="height: 200px; display: flex; align-items: center; justify-content: center; background-color: #ffffff; border-bottom: 1px solid #f1f5f9; padding: 1rem;">
                        <img src="assets/tyres/solid.png" alt="Solid Resilient Tyre" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                    </div>
                    <div style="padding: 2rem; text-align: left; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <h3 style="font-size: 1.25rem; color: #1e293b; font-weight: 700; margin-bottom: 1rem;">3. Solid Resilient</h3>
                            <p style="color: #64748b; font-size: 0.95rem; line-height: 1.6; margin-bottom: 1.5rem;">
                                100% puncture-resistant resilient tyres designed for demanding and high-frequency forklift operations. Available in both Clip (easy-fit) and No Clip (standard) rim configurations.
                            </p>
                        </div>
                        <ul style="list-style-type: disc; padding-left: 1.25rem; color: #475569; font-size: 0.9rem; line-height: 1.6;">
                            <li>Puncture-free Operation</li>
                            <li>Clip / No Clip Options</li>
                            <li>High Wear Resistance</li>
                        </ul>
                    </div>
                </div>

                <!-- Card 4 -->
                <div style="background: #ffffff; border: 1px solid var(--border-gray); border-radius: 4px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between;">
                    <div style="height: 200px; display: flex; align-items: center; justify-content: center; background-color: #ffffff; border-bottom: 1px solid #f1f5f9; padding: 1rem;">
                        <img src="assets/tyres/solidwhite.png" alt="Non-Marking White Resilient Tyre" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                    </div>
                    <div style="padding: 2rem; text-align: left; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <h3 style="font-size: 1.25rem; color: #1e293b; font-weight: 700; margin-bottom: 1rem;">4. Non-Marking White</h3>
                            <p style="color: #64748b; font-size: 0.95rem; line-height: 1.6; margin-bottom: 1.5rem;">
                                Engineered for clean-room facilities like pharmaceuticals, paper, and food processing. Leaves no black tire marks on floors while preserving standard tire capacity.
                            </p>
                        </div>
                        <ul style="list-style-type: disc; padding-left: 1.25rem; color: #475569; font-size: 0.9rem; line-height: 1.6;">
                            <li>Clean Floor Preservation</li>
                            <li>Food & Pharma Grade</li>
                            <li>Maintenance Free Solid</li>
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
                    <div style="background: #ffffff; padding: 1.5rem; border: 1px solid #f1f5f9; border-radius: 4px; width: 220px; height: 110px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.01);">
                        <img src="assets/collaborators/Trelleborg-320x202.webp" alt="Trelleborg Logo" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                    </div>
                    <div style="background: #ffffff; padding: 1.5rem; border: 1px solid #f1f5f9; border-radius: 4px; width: 220px; height: 110px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.01);">
                        <img src="assets/collaborators/Sunbear-Logo.webp" alt="Sunbear Logo" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                    </div>
                </div>
            </div>

        </div>
    </section>

<?php
require_once __DIR__ . '/footer.php';
?>

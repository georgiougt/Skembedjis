<?php
// placeholder.php
// Generates SVG placeholder images dynamically

header("Content-Type: image/svg+xml");

$text = isset($_GET['text']) ? htmlspecialchars($_GET['text']) : 'Insert Image Here';
$width = isset($_GET['w']) ? (int)$_GET['w'] : 600;
$height = isset($_GET['h']) ? (int)$_GET['h'] : 400;

echo '<?xml version="1.0" standalone="no"?>';
?>
<svg xmlns="http://www.w3.org/2000/svg" width="<?php echo $width; ?>" height="<?php echo $height; ?>" viewBox="0 0 <?php echo $width; ?> <?php echo $height; ?>">
  <rect width="100%" height="100%" fill="#f1f5f9"/>
  <!-- Decorative grid lines to look like a placeholder -->
  <line x1="0" y1="0" x2="<?php echo $width; ?>" y2="<?php echo $height; ?>" stroke="#e2e8f0" stroke-width="2"/>
  <line x1="<?php echo $width; ?>" y1="0" x2="0" y2="<?php echo $height; ?>" stroke="#e2e8f0" stroke-width="2"/>
  
  <!-- Content Card Background -->
  <rect x="10%" y="25%" width="80%" height="50%" rx="8" fill="#ffffff" filter="drop-shadow(0 4px 6px rgba(0, 0, 0, 0.05))" stroke="#cbd5e1" stroke-width="1"/>
  
  <!-- Text -->
  <text x="50%" y="46%" font-family="system-ui, -apple-system, sans-serif" font-size="20" font-weight="bold" fill="#475569" dominant-baseline="middle" text-anchor="middle">Insert Image Here</text>
  <text x="50%" y="54%" font-family="system-ui, -apple-system, sans-serif" font-size="14" fill="#64748b" dominant-baseline="middle" text-anchor="middle"><?php echo $text; ?></text>
  <text x="50%" y="62%" font-family="system-ui, -apple-system, sans-serif" font-size="11" fill="#94a3b8" dominant-baseline="middle" text-anchor="middle"><?php echo $width . 'x' . $height; ?></text>
</svg>

<?php
/**
 * Generate Placeholder Images for Restaurant Landing Page
 * This script creates placeholder images so the website displays properly
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if GD extension is available
if (!extension_loaded('gd')) {
    die('<h1>❌ GD Extension Required</h1><p>Please enable the GD extension in PHP to generate placeholder images.</p>');
}

echo "<h1>🖼️ Generating Placeholder Images</h1>";

// Define images to create
$images = [
    // Hero Section
    'hero-bg.jpg' => [1920, 1080, '#2c1810', 'Restaurant Hero Background', '#d4af37'],
    
    // About Section  
    'chef-cooking.jpg' => [800, 600, '#8B4513', 'Chef Cooking', '#FFE4B5'],
    
    // Menu Items
    'pasta-carbonara.jpg' => [400, 300, '#F4A460', 'Pasta Carbonara', '#8B0000'],
    'margherita-pizza.jpg' => [400, 300, '#DC143C', 'Margherita Pizza', '#FFFF00'],
    'osso-buco.jpg' => [400, 300, '#A0522D', 'Osso Buco', '#F5DEB3'],
    'tiramisu.jpg' => [400, 300, '#DEB887', 'Tiramisu', '#8B4513'],
    
    // Gallery
    'restaurant-interior-1.jpg' => [600, 400, '#654321', 'Restaurant Interior', '#D2691E'],
    'food-plating.jpg' => [600, 400, '#FF6347', 'Food Plating', '#FFFFE0'],
    'wine-selection.jpg' => [600, 400, '#800080', 'Wine Selection', '#FFB6C1'],
    'outdoor-seating.jpg' => [600, 400, '#228B22', 'Outdoor Seating', '#F0E68C'],
    'kitchen-action.jpg' => [600, 400, '#B22222', 'Kitchen Action', '#FFA500'],
    'private-dining.jpg' => [600, 400, '#4B0082', 'Private Dining', '#E6E6FA'],
    
    // Testimonials
    'customer-1.jpg' => [100, 100, '#4682B4', 'Sarah J.', '#FFFFFF'],
    'customer-2.jpg' => [100, 100, '#32CD32', 'Mike C.', '#FFFFFF'],
    'customer-3.jpg' => [100, 100, '#FF1493', 'Emily R.', '#FFFFFF'],
];

$images_dir = '../assets/images/';

// Create images directory if it doesn't exist
if (!is_dir($images_dir)) {
    mkdir($images_dir, 0755, true);
    echo "<p>📁 Created images directory</p>";
}

$created_count = 0;
$skipped_count = 0;

foreach ($images as $filename => $config) {
    $filepath = $images_dir . $filename;
    
    // Skip if image already exists
    if (file_exists($filepath)) {
        echo "<p>⏭️ Skipped: {$filename} (already exists)</p>";
        $skipped_count++;
        continue;
    }
    
    list($width, $height, $bg_color, $text, $text_color) = $config;
    
    // Create image
    $image = imagecreatetruecolor($width, $height);
    
    // Set background color
    $bg = imagecolorallocate($image, 
        hexdec(substr($bg_color, 1, 2)), 
        hexdec(substr($bg_color, 3, 2)), 
        hexdec(substr($bg_color, 5, 2))
    );
    imagefill($image, 0, 0, $bg);
    
    // Set text color
    $text_col = imagecolorallocate($image, 
        hexdec(substr($text_color, 1, 2)), 
        hexdec(substr($text_color, 3, 2)), 
        hexdec(substr($text_color, 5, 2))
    );
    
    // Add text
    $font_size = min($width, $height) / 20;
    $font_file = null; // Use default font
    
    // Calculate text position (center)
    $text_box = imagettfbbox($font_size, 0, $font_file, $text);
    $text_width = $text_box[4] - $text_box[0];
    $text_height = $text_box[1] - $text_box[7];
    $x = ($width - $text_width) / 2;
    $y = ($height - $text_height) / 2 + $text_height;
    
    // Add main text
    if (function_exists('imagettftext')) {
        imagettftext($image, $font_size, 0, $x, $y, $text_col, $font_file, $text);
    } else {
        // Fallback to imagestring if TTF not available
        $x = ($width - strlen($text) * 10) / 2;
        $y = ($height - 15) / 2;
        imagestring($image, 5, $x, $y, $text, $text_col);
    }
    
    // Add dimensions text
    $dim_text = $width . 'x' . $height;
    $dim_y = $y + 30;
    if (function_exists('imagettftext')) {
        imagettftext($image, $font_size * 0.6, 0, $x, $dim_y, $text_col, $font_file, $dim_text);
    } else {
        $dim_x = ($width - strlen($dim_text) * 8) / 2;
        imagestring($image, 3, $dim_x, $dim_y, $dim_text, $text_col);
    }
    
    // Save image
    if (imagejpeg($image, $filepath, 85)) {
        echo "<p style='color: green;'>✅ Created: {$filename} ({$width}x{$height})</p>";
        $created_count++;
    } else {
        echo "<p style='color: red;'>❌ Failed: {$filename}</p>";
    }
    
    // Clean up memory
    imagedestroy($image);
}

// Create a simple favicon
$favicon_path = $images_dir . 'favicon.ico';
if (!file_exists($favicon_path)) {
    $favicon = imagecreatetruecolor(32, 32);
    $bg = imagecolorallocate($favicon, 212, 175, 55); // Gold color
    $text_col = imagecolorallocate($favicon, 44, 24, 16); // Dark brown
    imagefill($favicon, 0, 0, $bg);
    imagestring($favicon, 5, 8, 8, 'BV', $text_col); // Bella Vista initials
    
    // Convert to ICO format (simplified)
    if (imagepng($favicon, str_replace('.ico', '.png', $favicon_path))) {
        echo "<p style='color: green;'>✅ Created: favicon.png (use as favicon)</p>";
        $created_count++;
    }
    imagedestroy($favicon);
}

echo "<div style='background: #d4edda; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h2 style='color: #155724;'>🎉 Placeholder Images Generated!</h2>";
echo "<p><strong>Created:</strong> {$created_count} images</p>";
echo "<p><strong>Skipped:</strong> {$skipped_count} images (already existed)</p>";
echo "<p><strong>Status:</strong> Your website should now display properly! 🚀</p>";
echo "</div>";

echo "<h3>🧪 Next Steps:</h3>";
echo "<ol>";
echo "<li><a href='../index.html' target='_blank'>View your website</a> - Images should now display</li>";
echo "<li>Replace placeholder images with your actual restaurant photos</li>";
echo "<li>Optimize images for web (compress to under 200KB each)</li>";
echo "</ol>";

echo "<h3>📁 Generated Images:</h3>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; margin: 20px 0;'>";

foreach ($images as $filename => $config) {
    $filepath = $images_dir . $filename;
    if (file_exists($filepath)) {
        echo "<div style='border: 1px solid #ddd; padding: 10px; text-align: center;'>";
        echo "<img src='{$filepath}' style='max-width: 100%; height: 100px; object-fit: cover;' alt='{$filename}'>";
        echo "<p style='margin: 5px 0; font-size: 12px;'>{$filename}</p>";
        echo "</div>";
    }
}
echo "</div>";

?>

<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
    background: #f5f5f5;
}

h1 {
    color: #2c1810;
    text-align: center;
    margin-bottom: 30px;
}

p {
    margin: 5px 0;
}

a {
    color: #d4af37;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}
</style>

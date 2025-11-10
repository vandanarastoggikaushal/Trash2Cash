<?php
/**
 * Dynamic Sitemap Generator
 * Generates sitemap.xml with actual file modification dates
 * Run this script to update sitemap with current file dates
 */

$baseUrl = 'https://trash2cash.co.nz';
$rootDir = __DIR__;

// Define pages with their priorities and change frequencies
$pages = [
    'index.php' => [
        'url' => '/',
        'priority' => '1.0',
        'changefreq' => 'weekly'
    ],
    'how-it-works.php' => [
        'url' => '/how-it-works',
        'priority' => '0.9',
        'changefreq' => 'monthly'
    ],
    'rewards.php' => [
        'url' => '/rewards',
        'priority' => '0.9',
        'changefreq' => 'monthly'
    ],
    'schedule-pickup.php' => [
        'url' => '/schedule-pickup',
        'priority' => '0.9',
        'changefreq' => 'monthly'
    ],
    'faq.php' => [
        'url' => '/faq',
        'priority' => '0.8',
        'changefreq' => 'monthly'
    ],
    'contact.php' => [
        'url' => '/contact',
        'priority' => '0.8',
        'changefreq' => 'monthly'
    ],
    'partners.php' => [
        'url' => '/partners',
        'priority' => '0.7',
        'changefreq' => 'monthly'
    ],
    'recycling-wellington.php' => [
        'url' => '/recycling-wellington',
        'priority' => '0.9',
        'changefreq' => 'monthly'
    ],
    'resources.php' => [
        'url' => '/resources',
        'priority' => '0.7',
        'changefreq' => 'weekly'
    ],
    'resources/wellington-recycling-guide.php' => [
        'url' => '/resources/wellington-recycling-guide',
        'priority' => '0.6',
        'changefreq' => 'monthly'
    ],
    'privacy.php' => [
        'url' => '/privacy',
        'priority' => '0.5',
        'changefreq' => 'yearly'
    ],
    'terms.php' => [
        'url' => '/terms',
        'priority' => '0.5',
        'changefreq' => 'yearly'
    ]
];

// Generate sitemap XML
$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
$xml .= '        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . "\n";
$xml .= '        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9' . "\n";
$xml .= '        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\n";

foreach ($pages as $file => $config) {
    $filePath = $rootDir . '/' . $file;
    
    // Get file modification date
    if (file_exists($filePath)) {
        $lastmod = date('Y-m-d', filemtime($filePath));
    } else {
        // Fallback to current date if file doesn't exist
        $lastmod = date('Y-m-d');
    }
    
    $xml .= "  <url>\n";
    $xml .= "    <loc>" . htmlspecialchars($baseUrl . $config['url']) . "</loc>\n";
    $xml .= "    <lastmod>" . $lastmod . "</lastmod>\n";
    $xml .= "    <changefreq>" . $config['changefreq'] . "</changefreq>\n";
    $xml .= "    <priority>" . $config['priority'] . "</priority>\n";
    $xml .= "  </url>\n";
}

$xml .= "</urlset>\n";

// Write to sitemap.xml
$sitemapPath = $rootDir . '/sitemap.xml';
if (file_put_contents($sitemapPath, $xml)) {
    echo "✅ Sitemap generated successfully!\n";
    echo "📄 File: sitemap.xml\n";
    echo "🔗 Base URL: $baseUrl\n";
    echo "📊 Pages: " . count($pages) . "\n";
} else {
    echo "❌ Error: Could not write sitemap.xml\n";
    exit(1);
}


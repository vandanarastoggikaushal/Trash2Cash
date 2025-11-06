<?php
/**
 * Sync Version Script
 * Updates version display and optionally updates sitemap
 * Run this after syncing files to update version and sitemap dates
 */

$rootDir = __DIR__;
$versionFile = $rootDir . '/VERSION';

// Check if VERSION file exists
if (!file_exists($versionFile)) {
    echo "⚠️  VERSION file not found. Creating default version...\n";
    file_put_contents($versionFile, "1.0.0\n");
}

// Read current version
$currentVersion = trim(file_get_contents($versionFile));

echo "📦 Current version: $currentVersion\n";
echo "✅ Version is already dynamic - it reads from VERSION file\n";
echo "💡 To update version, edit the VERSION file\n\n";

// Optionally update sitemap
if (file_exists($rootDir . '/generate-sitemap.php')) {
    echo "🔄 Updating sitemap with current file dates...\n\n";
    // Include the sitemap generator (it will output its own messages)
    include $rootDir . '/generate-sitemap.php';
} else {
    echo "ℹ️  Run generate-sitemap.php to update sitemap dates\n";
}


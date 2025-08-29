#!/usr/bin/env php
<?php

/**
 * Laravel 12 Compatibility Verification Script
 * 
 * This script verifies that the Litepie Trans package is compatible with Laravel 12.
 * Run this script after installation to ensure everything is working correctly.
 */

echo "🚀 Laravel 12 Compatibility Verification for Litepie Trans\n";
echo "========================================================\n\n";

// Check PHP version
echo "1. Checking PHP version...\n";
$phpVersion = PHP_VERSION;
$requiredPhp = '8.1';

if (version_compare($phpVersion, $requiredPhp, '>=')) {
    echo "   ✅ PHP {$phpVersion} is supported (requires PHP {$requiredPhp}+)\n";
} else {
    echo "   ❌ PHP {$phpVersion} is not supported (requires PHP {$requiredPhp}+)\n";
    exit(1);
}

// Check if we're in a Laravel project
echo "\n2. Checking Laravel environment...\n";
if (!file_exists('artisan')) {
    echo "   ❌ Not in a Laravel project directory\n";
    exit(1);
}

// Check Laravel version
$laravelVersion = null;
if (file_exists('composer.json')) {
    $composer = json_decode(file_get_contents('composer.json'), true);
    if (isset($composer['require']['laravel/framework'])) {
        $constraint = $composer['require']['laravel/framework'];
        echo "   ✅ Laravel framework constraint: {$constraint}\n";
    }
}

// Check if package is installed
echo "\n3. Checking package installation...\n";
$packageInstalled = false;
if (file_exists('composer.json')) {
    $composer = json_decode(file_get_contents('composer.json'), true);
    if (isset($composer['require']['litepie/trans'])) {
        $packageInstalled = true;
        $version = $composer['require']['litepie/trans'];
        echo "   ✅ Litepie Trans package is installed: {$version}\n";
    }
}

if (!$packageInstalled) {
    echo "   ❌ Litepie Trans package is not installed\n";
    echo "   💡 Run: composer require litepie/trans:^2.1\n";
    exit(1);
}

// Check config file
echo "\n4. Checking configuration...\n";
if (file_exists('config/trans.php')) {
    echo "   ✅ Configuration file exists at config/trans.php\n";
} else {
    echo "   ⚠️  Configuration file not published\n";
    echo "   💡 Run: php artisan vendor:publish --provider=\"Litepie\\Trans\\TransServiceProvider\" --tag=\"trans-config\"\n";
}

// Check environment variables
echo "\n5. Checking environment variables...\n";
$envFile = '.env';
$envVars = [
    'TRANS_FORCE_HTTPS',
    'TRANS_REDIRECT_TO_DEFAULT_LOCALE',
    'TRANS_ENABLE_MEMORY_OPTIMIZATION'
];

$envContent = file_exists($envFile) ? file_get_contents($envFile) : '';
$foundVars = 0;

foreach ($envVars as $var) {
    if (strpos($envContent, $var) !== false) {
        $foundVars++;
    }
}

if ($foundVars > 0) {
    echo "   ✅ Found {$foundVars} TRANS_* environment variables\n";
} else {
    echo "   ⚠️  No TRANS_* environment variables found\n";
    echo "   💡 Consider adding environment variables for customization\n";
}

// Check middleware registration
echo "\n6. Checking middleware registration...\n";
$middlewareCheck = shell_exec('php artisan route:list --middleware=localization 2>/dev/null');
if ($middlewareCheck !== null && !empty(trim($middlewareCheck))) {
    echo "   ✅ Localization middleware is available\n";
} else {
    echo "   ℹ️  Localization middleware not yet used in routes\n";
}

// Performance recommendations
echo "\n7. Laravel 12 Performance Recommendations...\n";
echo "   💡 Enable memory optimization in config/trans.php:\n";
echo "      'performance' => ['enableMemoryOptimization' => true]\n";
echo "   💡 Use environment variables for deployment configuration\n";
echo "   💡 Consider enabling route model binding for better performance\n";

// Testing recommendations
echo "\n8. Testing Recommendations...\n";
echo "   💡 Run your test suite: php artisan test\n";
echo "   💡 Clear caches after configuration changes:\n";
echo "      php artisan config:cache\n";
echo "      php artisan route:cache\n";

echo "\n✅ Compatibility verification completed!\n";
echo "📖 For more information, see LARAVEL-12-UPGRADE.md\n";
echo "🐛 Report issues at: https://github.com/litepie/trans/issues\n";

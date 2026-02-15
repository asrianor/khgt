<?php
// lang.php - Language Loader
// Dynamically loads all language files from the lang/ directory

$translations = [];
$langDir = __DIR__ . '/lang/';

if (is_dir($langDir)) {
    $files = scandir($langDir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            $langCode = pathinfo($file, PATHINFO_FILENAME);
            include $langDir . $file;
            if (isset($lang) && is_array($lang)) {
                $translations[$langCode] = $lang;
                unset($lang); // Clear for next iteration
            }
        }
    }
}

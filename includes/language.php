<?php
// Language handler for bilingual website
require_once __DIR__ . '/session.php';

// Initialize secure session
initSecureSession();

// Set default language
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'vi';
}

// Handle language switching
if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'vi'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

// Load translation file
$lang = $_SESSION['lang'];
$translations = [];

$lang_file = __DIR__ . '/../lang/' . $lang . '.php';
if (file_exists($lang_file)) {
    $translations = include $lang_file;
}

// Translation helper function
function t($key) {
    global $translations;

    $keys = explode('.', $key);
    $value = $translations;

    foreach ($keys as $k) {
        if (isset($value[$k])) {
            $value = $value[$k];
        } else {
            return $key; // Return key if translation not found
        }
    }

    return $value;
}

// Get current language
function getCurrentLang() {
    return $_SESSION['lang'];
}
?>

<?php
/**
 * Structured Data (JSON-LD) for SEO
 * This file contains all Schema.org markup for the website
 */

// Get current page info
$currentUrl = 'https://www.latrungprint.vn' . $_SERVER['REQUEST_URI'];
$siteName = 'La TRUNG Printing & Packaging';
$siteUrl = 'https://www.latrungprint.vn';
$logoUrl = 'https://www.latrungprint.vn/assets/logo.png';

// Organization Schema
$organizationSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => $siteName,
    'url' => $siteUrl,
    'logo' => [
        '@type' => 'ImageObject',
        'url' => $logoUrl,
        'width' => 484,
        'height' => 484
    ],
    'description' => 'Leading offset printing and packaging company since 2004. Specializing in mass production of premium printed materials for global markets.',
    'foundingDate' => '2004',
    'address' => [
        '@type' => 'PostalAddress',
        'addressCountry' => 'VN',
        'addressLocality' => 'Ho Chi Minh City'
    ],
    'contactPoint' => [
        '@type' => 'ContactPoint',
        'telephone' => '+84-28-3960-0128',
        'contactType' => 'Customer Service',
        'availableLanguage' => ['Vietnamese', 'English']
    ],
    'sameAs' => [
        $siteUrl
    ]
];

// WebSite Schema
$websiteSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'name' => $siteName,
    'url' => $siteUrl,
    'description' => 'Leading offset printing and packaging company since 2004. Specializing in mass production of premium printed materials for global markets.',
    'publisher' => [
        '@type' => 'Organization',
        'name' => $siteName,
        'logo' => [
            '@type' => 'ImageObject',
            'url' => $logoUrl
        ]
    ],
    'inLanguage' => ['vi', 'en']
];

// Site Navigation Schema
$navigationSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'SiteNavigationElement',
    'name' => 'Main Navigation',
    'hasPart' => [
        [
            '@type' => 'WebPage',
            'name' => 'Home',
            'url' => $siteUrl . '/'
        ],
        [
            '@type' => 'WebPage',
            'name' => 'About Us',
            'url' => $siteUrl . '/about'
        ],
        [
            '@type' => 'WebPage',
            'name' => 'Contact',
            'url' => $siteUrl . '/contact'
        ]
    ]
];

// Combine all schemas
$structuredData = [
    $organizationSchema,
    $websiteSchema,
    $navigationSchema
];

// Output JSON-LD
foreach ($structuredData as $schema) {
    echo '<script type="application/ld+json">' . "\n";
    echo json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "\n";
    echo '</script>' . "\n";
}
?>

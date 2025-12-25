<?php
$page = 'home';
require_once __DIR__ . '/includes/language.php';
$pageTitle = 'Home | La TRUNG Printing & Packaging';
?>
<!DOCTYPE html>
<html lang="<?php echo getCurrentLang(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <meta name="description" content="La TRUNG - Leading offset printing and packaging company since 2004. Specializing in mass production of premium printed materials for global markets.">
    <link rel="icon" type="image/png" href="assets/logo.png">
    <link rel="stylesheet" href="css/styles.css?v=<?php echo filemtime('css/styles.css'); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title"><?php echo t('home.hero.title'); ?></h1>
                <p class="hero-subtitle"><?php echo t('home.hero.subtitle'); ?></p>
                <div class="hero-cta">
                    <a href="/contact" class="btn btn-primary"><?php echo t('home.hero.btn_discuss'); ?></a>
                    <a href="/about" class="btn btn-secondary"><?php echo t('home.hero.btn_about'); ?></a>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Overview -->
    <section class="services-overview">
        <div class="container">
            <div class="section-header">
                <h2><?php echo t('home.services.title'); ?></h2>
                <p><?php echo t('home.services.subtitle'); ?></p>
            </div>
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-image">
                        <img src="assets/bag.png" alt="<?php echo t('home.services.paper_bags'); ?>">
                    </div>
                    <h3><?php echo t('home.services.paper_bags'); ?></h3>
                </div>
                <div class="service-card">
                    <div class="service-image">
                        <img src="assets/big-box.png" alt="<?php echo t('home.services.gift_boxes'); ?>">
                    </div>
                    <h3><?php echo t('home.services.gift_boxes'); ?></h3>
                </div>
                <div class="service-card">
                    <div class="service-image">
                        <img src="assets/small-box.png" alt="<?php echo t('home.services.paper_boxes'); ?>">
                    </div>
                    <h3><?php echo t('home.services.paper_boxes'); ?></h3>
                </div>
                <div class="service-card">
                    <div class="service-image">
                        <img src="assets/brochure.png" alt="<?php echo t('home.services.brochures'); ?>">
                    </div>
                    <h3><?php echo t('home.services.brochures'); ?></h3>
                </div>
                <div class="service-card">
                    <div class="service-image">
                        <img src="assets/flyer.png" alt="<?php echo t('home.services.flyers'); ?>">
                    </div>
                    <h3><?php echo t('home.services.flyers'); ?></h3>
                </div>
                <div class="service-card">
                    <div class="service-image">
                        <img src="assets/sticker.png" alt="<?php echo t('home.services.labels'); ?>">
                    </div>
                    <h3><?php echo t('home.services.labels'); ?></h3>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="why-choose">
        <div class="container">
            <div class="section-header">
                <h2><?php echo t('home.why_choose.title'); ?></h2>
            </div>
            <div class="features-grid">
                <div class="feature">
                    <div class="feature-icon">
                        <svg width="48" height="48" viewBox="0 0 48 48" fill="none">
                            <circle cx="24" cy="24" r="20" stroke="#2DBAA7" stroke-width="2"/>
                            <circle cx="24" cy="24" r="14" stroke="#2DBAA7" stroke-width="2"/>
                            <circle cx="24" cy="24" r="8" stroke="#2DBAA7" stroke-width="2"/>
                            <circle cx="24" cy="24" r="3" fill="#2DBAA7"/>
                        </svg>
                    </div>
                    <h3><?php echo t('home.why_choose.feature1'); ?></h3>
                </div>
                <div class="feature">
                    <div class="feature-icon">
                        <svg width="48" height="48" viewBox="0 0 48 48" fill="none">
                            <circle cx="24" cy="24" r="20" stroke="#2DBAA7" stroke-width="2"/>
                            <path d="M16 24L22 30L32 18" stroke="#2DBAA7" stroke-width="3" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <h3><?php echo t('home.why_choose.feature2'); ?></h3>
                </div>
                <div class="feature">
                    <div class="feature-icon">
                        <svg width="48" height="48" viewBox="0 0 48 48" fill="none">
                            <circle cx="24" cy="24" r="20" stroke="#2DBAA7" stroke-width="2"/>
                            <path d="M24 8V24L32 32" stroke="#2DBAA7" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <h3><?php echo t('home.why_choose.feature3'); ?></h3>
                </div>
                <div class="feature">
                    <div class="feature-icon">
                        <svg width="48" height="48" viewBox="0 0 48 48" fill="none">
                            <path d="M24 4L28 16L40 18L32 26L34 38L24 32L14 38L16 26L8 18L20 16L24 4Z" stroke="#2DBAA7" stroke-width="2"/>
                        </svg>
                    </div>
                    <h3><?php echo t('home.why_choose.feature4'); ?></h3>
                </div>
                <div class="feature">
                    <div class="feature-icon">
                        <svg width="48" height="48" viewBox="0 0 48 48" fill="none">
                            <rect x="10" y="14" width="28" height="24" rx="2" stroke="#2DBAA7" stroke-width="2"/>
                            <path d="M18 14V10C18 8.9 18.9 8 20 8H28C29.1 8 30 8.9 30 10V14" stroke="#2DBAA7" stroke-width="2"/>
                            <circle cx="24" cy="26" r="4" stroke="#2DBAA7" stroke-width="2"/>
                        </svg>
                    </div>
                    <h3><?php echo t('home.why_choose.feature5'); ?></h3>
                </div>
                <div class="feature">
                    <div class="feature-icon">
                        <svg width="48" height="48" viewBox="0 0 48 48" fill="none">
                            <path d="M12 20C12 14 16 8 24 8C32 8 36 14 36 20C36 26 36 32 36 38C36 40 34 42 32 42H16C14 42 12 40 12 38C12 32 12 26 12 20Z" stroke="#2DBAA7" stroke-width="2"/>
                            <path d="M20 24L24 28L28 24M24 28V20" stroke="#2DBAA7" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <h3><?php echo t('home.why_choose.feature6'); ?></h3>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Clients -->
    <section class="industries">
        <div class="container">
            <div class="section-header">
                <h2><?php echo t('home.industries.title'); ?></h2>
                <p><?php echo t('home.industries.subtitle'); ?></p>
            </div>
            <div class="industries-grid">
                <div class="industry-card">
                    <div class="industry-icon">
                        <img src="assets/pharmaceutical.png" alt="<?php echo t('home.industries.pharmaceutical'); ?>">
                    </div>
                    <h3><?php echo t('home.industries.pharmaceutical'); ?></h3>
                </div>
                <div class="industry-card">
                    <div class="industry-icon">
                        <img src="assets/fmcg.png" alt="<?php echo t('home.industries.fmcg'); ?>">
                    </div>
                    <h3><?php echo t('home.industries.fmcg'); ?></h3>
                </div>
                <div class="industry-card">
                    <div class="industry-icon">
                        <img src="assets/cosmetics.png" alt="<?php echo t('home.industries.beauty'); ?>">
                    </div>
                    <h3><?php echo t('home.industries.beauty'); ?></h3>
                </div>
                <div class="industry-card">
                    <div class="industry-icon">
                        <img src="assets/healthcare.png" alt="<?php echo t('home.industries.healthcare'); ?>">
                    </div>
                    <h3><?php echo t('home.industries.healthcare'); ?></h3>
                </div>
                <div class="industry-card">
                    <div class="industry-icon">
                        <img src="assets/fnb.png" alt="<?php echo t('home.industries.fnb'); ?>">
                    </div>
                    <h3><?php echo t('home.industries.fnb'); ?></h3>
                </div>
                <div class="industry-card">
                    <div class="industry-icon">
                        <svg width="48" height="48" viewBox="0 0 48 48" fill="none">
                            <circle cx="24" cy="24" r="3" fill="white"/>
                            <circle cx="12" cy="24" r="2" fill="white"/>
                            <circle cx="36" cy="24" r="2" fill="white"/>
                            <circle cx="18" cy="14" r="2" fill="white"/>
                            <circle cx="30" cy="14" r="2" fill="white"/>
                            <circle cx="18" cy="34" r="2" fill="white"/>
                            <circle cx="30" cy="34" r="2" fill="white"/>
                        </svg>
                    </div>
                    <h3><?php echo t('home.industries.more'); ?></h3>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>

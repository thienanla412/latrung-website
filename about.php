<?php
$page = 'about';
require_once __DIR__ . '/includes/language.php';
$pageTitle = 'About Us | La TRUNG Printing & Packaging';
?>
<!DOCTYPE html>
<html lang="<?php echo getCurrentLang(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <meta name="description" content="Reliable offset printing built on consistency and discipline. La TRUNG specializes in stable, repeatable production for packaging and commercial applications.">

    <!-- Favicons -->
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="48x48" href="/assets/favicon-48x48.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/apple-touch-icon.png">

    <!-- Structured Data for SEO -->
    <?php include 'includes/structured-data.php'; ?>

    <link rel="stylesheet" href="css/styles.css?v=<?php echo filemtime('css/styles.css'); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Page Hero -->
    <section class="page-hero">
        <div class="container">
            <h1><?php echo t('about.hero.title'); ?></h1>
        </div>
    </section>

    <!-- Introduction -->
    <section class="about-intro">
        <div class="container">
            <div class="intro-content">
                <h2 class="intro-title"><?php echo t('about.intro.title'); ?></h2>
                <p class="intro-large"><?php echo t('about.intro.p1'); ?></p>
                <p><?php echo t('about.intro.p2'); ?></p>
            </div>
        </div>
    </section>

    <!-- What We Do -->
    <section class="about-section">
        <div class="container">
            <div class="section-header">
                <h2><?php echo t('about.what_we_do.title'); ?></h2>
            </div>
            <div class="about-content">
                <p class="section-intro"><?php echo t('about.what_we_do.intro'); ?></p>
                <div class="requirements-grid">
                    <div class="requirement-item">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="10" stroke="#2DBAA7" stroke-width="2"/>
                            <path d="M8 12L11 15L16 9" stroke="#2DBAA7" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <span><?php echo t('about.what_we_do.req1'); ?></span>
                    </div>
                    <div class="requirement-item">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="10" stroke="#2DBAA7" stroke-width="2"/>
                            <path d="M8 12L11 15L16 9" stroke="#2DBAA7" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <span><?php echo t('about.what_we_do.req2'); ?></span>
                    </div>
                    <div class="requirement-item">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="10" stroke="#2DBAA7" stroke-width="2"/>
                            <path d="M8 12L11 15L16 9" stroke="#2DBAA7" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <span><?php echo t('about.what_we_do.req3'); ?></span>
                    </div>
                    <div class="requirement-item">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="10" stroke="#2DBAA7" stroke-width="2"/>
                            <path d="M8 12L11 15L16 9" stroke="#2DBAA7" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <span><?php echo t('about.what_we_do.req4'); ?></span>
                    </div>
                </div>
                <p class="section-closing"><?php echo t('about.what_we_do.closing'); ?></p>
            </div>
        </div>
    </section>

    <!-- How We Work -->
    <section class="about-section bg-gray">
        <div class="container">
            <div class="section-header">
                <h2><?php echo t('about.how_we_work.title'); ?></h2>
                <p class="section-subtitle"><?php echo t('about.how_we_work.subtitle'); ?></p>
            </div>
            <div class="process-grid">
                <div class="process-card">
                    <div class="process-number">01</div>
                    <h3><?php echo t('about.how_we_work.process1'); ?></h3>
                </div>
                <div class="process-card">
                    <div class="process-number">02</div>
                    <h3><?php echo t('about.how_we_work.process2'); ?></h3>
                </div>
                <div class="process-card">
                    <div class="process-number">03</div>
                    <h3><?php echo t('about.how_we_work.process3'); ?></h3>
                </div>
                <div class="process-card">
                    <div class="process-number">04</div>
                    <h3><?php echo t('about.how_we_work.process4'); ?></h3>
                </div>
            </div>
            <p class="process-conclusion"><?php echo t('about.how_we_work.conclusion'); ?></p>
        </div>
    </section>

    <!-- Confidentiality -->
    <section class="about-section">
        <div class="container">
            <div class="confidentiality-content">
                <div class="section-header">
                    <h2><?php echo t('about.confidentiality.title'); ?></h2>
                </div>
                <p class="emphasis-text"><?php echo t('about.confidentiality.p1'); ?></p>
                <p><?php echo t('about.confidentiality.p2'); ?></p>
                <p class="emphasis-text"><?php echo t('about.confidentiality.p3'); ?></p>
            </div>
        </div>
    </section>

    <!-- Long-Term Partnership -->
    <section class="about-section bg-gray">
        <div class="container">
            <div class="partnership-content">
                <div class="section-header">
                    <h2><?php echo t('about.partnership.title'); ?></h2>
                </div>
                <p class="emphasis-text"><?php echo t('about.partnership.p1'); ?></p>
                <p><?php echo t('about.partnership.p2'); ?></p>
                <div class="goal-statement">
                    <p><?php echo t('about.partnership.goal'); ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Positioning -->
    <section class="about-section">
        <div class="container">
            <div class="positioning-content">
                <div class="section-header">
                    <h2><?php echo t('about.positioning.title'); ?></h2>
                </div>
                <p><?php echo t('about.positioning.intro'); ?></p>
                <div class="positioning-grid">
                    <div class="positioning-item">
                        <h3><?php echo t('about.positioning.item1_title'); ?></h3>
                        <p><?php echo t('about.positioning.item1_desc'); ?></p>
                    </div>
                    <div class="positioning-item">
                        <h3><?php echo t('about.positioning.item2_title'); ?></h3>
                        <p><?php echo t('about.positioning.item2_desc'); ?></p>
                    </div>
                    <div class="positioning-item">
                        <h3><?php echo t('about.positioning.item3_title'); ?></h3>
                        <p><?php echo t('about.positioning.item3_desc'); ?></p>
                    </div>
                </div>
                <p class="section-closing"><?php echo t('about.positioning.closing'); ?></p>
            </div>
        </div>
    </section>

    <!-- Location -->
    <section class="about-section bg-gray">
        <div class="container">
            <div class="location-content">
                <div class="section-header">
                    <h2><?php echo t('about.location.title'); ?></h2>
                </div>
                <p><?php echo t('about.location.content'); ?></p>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>

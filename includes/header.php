<?php include_once __DIR__ . '/language.php'; ?>
<header class="main-header">
    <nav class="navbar">
        <div class="container nav-container">
            <a href="/" class="logo">
                <img src="assets/logo-latrung.png" alt="La TRUNG Printing & Packaging" class="logo-img">
            </a>
            <button class="mobile-menu-toggle" id="mobileMenuToggle">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <ul class="nav-menu" id="navMenu">
                <li><a href="/" class="nav-link <?php echo ($page == 'home') ? 'active' : ''; ?>"><?php echo t('nav.home'); ?></a></li>
                <li><a href="/about" class="nav-link <?php echo ($page == 'about') ? 'active' : ''; ?>"><?php echo t('nav.about'); ?></a></li>
                <li><a href="/contact" class="nav-link <?php echo ($page == 'contact') ? 'active' : ''; ?>"><?php echo t('nav.contact'); ?></a></li>
            </ul>
            <div class="lang-switcher">
                <a href="?lang=en" class="lang-btn <?php echo (getCurrentLang() == 'en') ? 'active' : ''; ?>">EN</a>
                <a href="?lang=vi" class="lang-btn <?php echo (getCurrentLang() == 'vi') ? 'active' : ''; ?>">VI</a>
            </div>
        </div>
    </nav>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const navMenu = document.getElementById('navMenu');

    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            mobileMenuToggle.classList.toggle('active');
        });
    }
});
</script>

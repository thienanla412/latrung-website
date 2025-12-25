<?php
$page = 'contact';

// Include required files
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/language.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/ratelimit.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/mailer.php';

$pageTitle = t('contact.hero.title') . ' | La TRUNG Printing & Packaging';

// Handle form submission
$formSubmitted = false;
$formError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Verify CSRF token
        verifyCSRF();

        // Get client IP for rate limiting
        $clientIP = getClientIP();

        // Check rate limit
        if (!checkRateLimit($clientIP, 'contact_form')) {
            $remainingTime = RateLimit::getRemainingTime($clientIP, 'contact_form');
            $minutes = ceil($remainingTime / 60);
            $formError = t('contact.errors.rate_limit') ?: "Too many submissions. Please try again in {$minutes} minutes.";
            throw new Exception($formError);
        }

        // Collect and sanitize form data
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $company = trim($_POST['company'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $service = trim($_POST['service'] ?? '');
        $quantity = trim($_POST['quantity'] ?? '');
        $message = trim($_POST['message'] ?? '');

        // Validation - only validate if provided
        // Validate email only if provided
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $formError = t('contact.errors.invalid_email');
            throw new Exception($formError);
        }

        // Validate phone only if provided - allow numbers, spaces, +, -, and parentheses
        if (!empty($phone) && !preg_match('/^[0-9+\-\s()]+$/', $phone)) {
            $formError = t('contact.errors.invalid_phone') ?: 'Please enter a valid phone number.';
            throw new Exception($formError);
        }

        // Additional spam protection - honeypot check (if you add one to the form)
        if (!empty($_POST['website'])) {
            // This is a honeypot field that should be empty
            logSecurityEvent('Honeypot triggered', ['ip' => $clientIP, 'email' => $email]);
            $formError = t('contact.errors.spam_detected') ?: 'Spam detected.';
            throw new Exception($formError);
        }

        // Prepare data for database
        $submissionData = [
            'name' => $name,
            'email' => $email,
            'company' => $company,
            'phone' => $phone,
            'service' => $service,
            'other_service' => null,
            'quantity' => $quantity,
            'message' => $message,
            'language' => getCurrentLang(),
            'ip_address' => $clientIP,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'status' => 'new',
            'priority' => 'normal'
        ];

        // Save to database
        try {
            $db = db();
            $submissionId = $db->insert('contact_submissions', $submissionData);

            // Log the submission
            if (LOG_ENABLED) {
                $logFile = LOG_PATH . 'submissions.log';
                $timestamp = date('Y-m-d H:i:s');
                $logMessage = "[{$timestamp}] New submission #{$submissionId} from {$email} ({$company})\n";

                if (!is_dir(LOG_PATH)) {
                    mkdir(LOG_PATH, 0755, true);
                }

                error_log($logMessage, 3, $logFile);
            }

        } catch (Exception $e) {
            // Log database error
            $formError = t('contact.errors.database_error') ?: 'Failed to save your submission. Please try again.';
            error_log("Database error in contact form: " . $e->getMessage());
            throw new Exception($formError);
        }

        // Send email notifications
        try {
            $mailer = mailer();

            // Send notification to admin
            $emailData = array_merge($submissionData, ['submission_id' => $submissionId]);
            $mailer->sendContactFormNotification($emailData);

            // Send auto-reply to customer
            $mailer->sendContactFormAutoReply($submissionData);

        } catch (Exception $e) {
            // Log email error but don't fail the submission
            error_log("Email error in contact form: " . $e->getMessage());
            // Continue - submission was saved to database
        }

        // Record rate limit attempt
        recordRateLimit($clientIP, 'contact_form');

        // Success!
        $formSubmitted = true;

        // Clear form data
        $_POST = [];

    } catch (Exception $e) {
        // Error handling
        if (empty($formError)) {
            $formError = $e->getMessage();
        }

        // Log the error
        if (LOG_ENABLED) {
            $logFile = LOG_PATH . 'form-errors.log';
            $timestamp = date('Y-m-d H:i:s');
            $logMessage = "[{$timestamp}] Contact form error: {$formError}\n";

            if (!is_dir(LOG_PATH)) {
                mkdir(LOG_PATH, 0755, true);
            }

            error_log($logMessage, 3, $logFile);
        }
    }
}

// Generate CSRF token for the form
$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="<?php echo getCurrentLang(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <meta name="description" content="Contact La TRUNG for your printing and packaging needs. Request a quote for high-volume production projects.">
    <link rel="icon" type="image/png" href="assets/logo.png">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <!-- Page Hero -->
    <section class="page-hero">
        <div class="container">
            <h1><?php echo t('contact.hero.title'); ?></h1>
            <p><?php echo t('contact.hero.subtitle'); ?></p>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <div class="contact-grid">
                <!-- Contact Information -->
                <div class="contact-info">
                    <h2><?php echo t('contact.info.title'); ?></h2>

                    <div class="contact-details">
                        <div class="contact-item">
                            <div class="contact-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M3 8L12 13L21 8M3 8V18C3 18.5 3.5 19 4 19H20C20.5 19 21 18.5 21 18V8M3 8L12 3L21 8" stroke="#2DBAA7" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <div>
                                <h4><?php echo t('contact.info.email'); ?></h4>
                                <a href="mailto:info@latrungprint.vn">info@latrungprint.vn</a>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M3 5H8L10 11L7 13C8.5 15.5 10.5 17.5 13 19L15 16L21 18V23C21 23.5 20.5 24 20 24C10.6 24 3 16.4 3 7C3 6.5 3.5 6 4 6H3V5Z" stroke="#2DBAA7" stroke-width="2"/>
                                </svg>
                            </div>
                            <div>
                                <h4><?php echo t('contact.info.phone'); ?></h4>
                                <p style="margin: 0;"><?php echo t('contact.info.phone_office'); ?>: <a href="tel:+842838632759"><?php echo t('contact.info.phone_office_number'); ?></a></p>
                                <p style="margin: 0;"><?php echo t('contact.info.phone_partnership'); ?>: <a href="tel:+84866988260"><?php echo t('contact.info.phone_partnership_number'); ?></a> <?php echo t('contact.info.phone_partnership_name'); ?></p>
                                <p style="margin: 0;"><?php echo t('contact.info.phone_technical'); ?>: <a href="tel:+84903672094"><?php echo t('contact.info.phone_technical_number'); ?></a> <?php echo t('contact.info.phone_technical_name'); ?></p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M5 9C5 6.5 6.5 3 12 3C17.5 3 19 6.5 19 9C19 12.5 16 15 12 21C8 15 5 12.5 5 9Z" stroke="#2DBAA7" stroke-width="2"/>
                                    <circle cx="12" cy="9" r="2" stroke="#2DBAA7" stroke-width="2"/>
                                </svg>
                            </div>
                            <div>
                                <h4><?php echo t('contact.info.location'); ?></h4>
                                <p><?php echo t('contact.info.address'); ?></p>
                            </div>
                        </div>

                        <div class="contact-item">
                            <div class="contact-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="12" r="9" stroke="#2DBAA7" stroke-width="2"/>
                                    <path d="M12 7V12L15 15" stroke="#2DBAA7" stroke-width="2" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <div>
                                <h4><?php echo t('contact.info.business_hours'); ?></h4>
                                <p><?php echo t('contact.info.hours_weekday'); ?></p>
                                <p><?php echo t('contact.info.hours_saturday'); ?></p>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Contact Form -->
                <div class="contact-form-container">
                    <?php if ($formSubmitted): ?>
                        <div class="form-success">
                            <div class="success-icon">
                                <svg width="64" height="64" viewBox="0 0 64 64" fill="none">
                                    <circle cx="32" cy="32" r="28" stroke="#2DBAA7" stroke-width="3"/>
                                    <path d="M20 32L28 40L44 24" stroke="#2DBAA7" stroke-width="4" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <h3><?php echo t('contact.success.title'); ?></h3>
                            <p><?php echo t('contact.success.message'); ?></p>
                            <a href="/contact" class="btn btn-primary"><?php echo t('contact.success.btn'); ?></a>
                        </div>
                    <?php else: ?>
                        <h2><?php echo t('contact.form.title'); ?></h2>
                        <p class="form-intro"><?php echo t('contact.form.intro'); ?></p>

                        <?php if ($formError): ?>
                            <div class="form-error">
                                <?php echo htmlspecialchars($formError, ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="" class="contact-form" id="contactForm">
                            <?php echo csrfField(); ?>

                            <!-- Honeypot field for spam protection (hidden with CSS) -->
                            <div style="position: absolute; left: -9999px;">
                                <label for="website">Website</label>
                                <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="name"><?php echo t('contact.form.name'); ?></label>
                                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="email"><?php echo t('contact.form.email'); ?></label>
                                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="company"><?php echo t('contact.form.company'); ?></label>
                                    <input type="text" id="company" name="company" value="<?php echo htmlspecialchars($_POST['company'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="phone"><?php echo t('contact.form.phone'); ?></label>
                                    <input type="tel" id="phone" name="phone" pattern="[0-9+\-\s()]*" value="<?php echo htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="service"><?php echo t('contact.form.service'); ?></label>
                                    <input type="text" id="service" name="service" placeholder="<?php echo t('contact.form.service_placeholder'); ?>" value="<?php echo htmlspecialchars($_POST['service'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                </div>

                                <div class="form-group">
                                    <label for="quantity"><?php echo t('contact.form.quantity'); ?></label>
                                    <input type="text" id="quantity" name="quantity" placeholder="<?php echo t('contact.form.quantity_placeholder'); ?>" value="<?php echo htmlspecialchars($_POST['quantity'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="message"><?php echo t('contact.form.message'); ?></label>
                                <textarea id="message" name="message" rows="6" placeholder="<?php echo t('contact.form.message_placeholder'); ?>"><?php echo htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary btn-submit"><?php echo t('contact.form.submit'); ?></button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>

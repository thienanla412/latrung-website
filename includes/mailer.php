<?php
/**
 * Email Helper
 * Sends emails using PHP mail() or SMTP
 */

require_once __DIR__ . '/../config.php';

class Mailer {
    private $lastError = null;

    /**
     * Send email
     *
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $message Email message (plain text or HTML)
     * @param array $options Additional options (cc, bcc, replyTo, isHTML)
     * @return bool Success status
     */
    public function send($to, $subject, $message, $options = []) {
        // Validate email
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $this->lastError = "Invalid recipient email address";
            $this->logError("Invalid email: {$to}");
            return false;
        }

        // Sanitize inputs to prevent email header injection
        $to = $this->sanitizeEmail($to);
        $subject = $this->sanitizeSubject($subject);

        // Use SMTP if configured, otherwise use PHP mail()
        if (!empty(SMTP_HOST)) {
            return $this->sendSMTP($to, $subject, $message, $options);
        } else {
            return $this->sendPHPMail($to, $subject, $message, $options);
        }
    }

    /**
     * Send email using PHP mail()
     */
    private function sendPHPMail($to, $subject, $message, $options = []) {
        $isHTML = $options['isHTML'] ?? false;
        $replyTo = $options['replyTo'] ?? null;
        $cc = $options['cc'] ?? null;
        $bcc = $options['bcc'] ?? null;

        // Build headers
        $headers = [];
        $headers[] = "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM_EMAIL . ">";
        $headers[] = "MIME-Version: 1.0";

        if ($isHTML) {
            $headers[] = "Content-Type: text/html; charset=UTF-8";
        } else {
            $headers[] = "Content-Type: text/plain; charset=UTF-8";
        }

        if ($replyTo && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
            $headers[] = "Reply-To: " . $this->sanitizeEmail($replyTo);
        }

        if ($cc && filter_var($cc, FILTER_VALIDATE_EMAIL)) {
            $headers[] = "Cc: " . $this->sanitizeEmail($cc);
        }

        if ($bcc && filter_var($bcc, FILTER_VALIDATE_EMAIL)) {
            $headers[] = "Bcc: " . $this->sanitizeEmail($bcc);
        }

        $headers[] = "X-Mailer: PHP/" . phpversion();

        // Send email
        $headerString = implode("\r\n", $headers);

        try {
            $result = mail($to, $subject, $message, $headerString);

            if ($result) {
                $this->logEmail($to, $subject, 'success');
                return true;
            } else {
                $this->lastError = "Failed to send email";
                $this->logEmail($to, $subject, 'failed');
                return false;
            }
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            $this->logError("Mail error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send email using SMTP (basic implementation)
     * For production, consider using PHPMailer library
     */
    private function sendSMTP($to, $subject, $message, $options = []) {
        // This is a placeholder for SMTP implementation
        // In production, use PHPMailer or similar library for robust SMTP support
        $this->lastError = "SMTP not fully implemented. Please use PHPMailer library for SMTP support.";
        $this->logError("SMTP send attempted but not fully implemented");

        // Fall back to PHP mail
        return $this->sendPHPMail($to, $subject, $message, $options);
    }

    /**
     * Send contact form notification
     */
    public function sendContactFormNotification($formData) {
        $subject = "New Contact Form Submission from " . ($formData['company'] ?? 'Unknown');

        $message = $this->buildContactFormEmail($formData);

        $options = [
            'isHTML' => true,
            'replyTo' => $formData['email'] ?? null
        ];

        return $this->send(MAIL_TO_EMAIL, $subject, $message, $options);
    }

    /**
     * Send auto-reply to contact form submitter
     */
    public function sendContactFormAutoReply($formData) {
        $subject = "Thank you for contacting " . SITE_NAME;

        $message = $this->buildAutoReplyEmail($formData);

        $options = [
            'isHTML' => true
        ];

        return $this->send($formData['email'], $subject, $message, $options);
    }

    /**
     * Build contact form email HTML
     */
    private function buildContactFormEmail($data) {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2DBAA7; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; }
        .field { margin-bottom: 15px; }
        .label { font-weight: bold; color: #2DBAA7; }
        .value { margin-top: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>New Contact Form Submission</h2>
        </div>
        <div class="content">
            <div class="field">
                <div class="label">Name:</div>
                <div class="value">' . htmlspecialchars($data['name'] ?? '') . '</div>
            </div>
            <div class="field">
                <div class="label">Email:</div>
                <div class="value">' . htmlspecialchars($data['email'] ?? '') . '</div>
            </div>
            <div class="field">
                <div class="label">Company:</div>
                <div class="value">' . htmlspecialchars($data['company'] ?? '') . '</div>
            </div>
            <div class="field">
                <div class="label">Phone:</div>
                <div class="value">' . htmlspecialchars($data['phone'] ?? '') . '</div>
            </div>
            <div class="field">
                <div class="label">Service:</div>
                <div class="value">' . htmlspecialchars($data['service'] ?? '') . '</div>
            </div>';

        if (!empty($data['other_service'])) {
            $html .= '<div class="field">
                <div class="label">Other Service:</div>
                <div class="value">' . htmlspecialchars($data['other_service']) . '</div>
            </div>';
        }

        $html .= '<div class="field">
                <div class="label">Quantity:</div>
                <div class="value">' . htmlspecialchars($data['quantity'] ?? '') . '</div>
            </div>
            <div class="field">
                <div class="label">Message:</div>
                <div class="value">' . nl2br(htmlspecialchars($data['message'] ?? '')) . '</div>
            </div>
            <div class="field">
                <div class="label">Submitted:</div>
                <div class="value">' . date('Y-m-d H:i:s') . '</div>
            </div>
        </div>
    </div>
</body>
</html>';

        return $html;
    }

    /**
     * Build auto-reply email HTML
     */
    private function buildAutoReplyEmail($data) {
        $name = htmlspecialchars($data['name'] ?? 'Valued Customer');

        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2DBAA7; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; }
        .footer { background: #f9f9f9; padding: 20px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>' . SITE_NAME . '</h2>
        </div>
        <div class="content">
            <p>Dear ' . $name . ',</p>
            <p>Thank you for contacting ' . SITE_NAME . '. We have received your inquiry and our team will review it shortly.</p>
            <p>We typically respond to all inquiries within 24 business hours. If your request is urgent, please feel free to call us at +84 (028) 38-632-759.</p>
            <p>We look forward to working with you.</p>
            <p>Best regards,<br>' . SITE_NAME . ' Team</p>
        </div>
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>' . SITE_NAME . ' | www.latrungprint.vn</p>
        </div>
    </div>
</body>
</html>';

        return $html;
    }

    /**
     * Sanitize email address to prevent header injection
     */
    private function sanitizeEmail($email) {
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }

    /**
     * Sanitize subject to prevent header injection
     */
    private function sanitizeSubject($subject) {
        return str_replace(["\r", "\n", "%0a", "%0d"], '', $subject);
    }

    /**
     * Get last error
     */
    public function getLastError() {
        return $this->lastError;
    }

    /**
     * Log email activity
     */
    private function logEmail($to, $subject, $status) {
        if (!LOG_ENABLED) {
            return;
        }

        $logFile = LOG_PATH . 'email.log';
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] To: {$to} | Subject: {$subject} | Status: {$status}\n";

        if (!is_dir(LOG_PATH)) {
            mkdir(LOG_PATH, 0755, true);
        }

        error_log($logMessage, 3, $logFile);
    }

    /**
     * Log errors
     */
    private function logError($message) {
        if (!LOG_ENABLED) {
            return;
        }

        $logFile = LOG_PATH . 'email-errors.log';
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] {$message}\n";

        if (!is_dir(LOG_PATH)) {
            mkdir(LOG_PATH, 0755, true);
        }

        error_log($logMessage, 3, $logFile);
    }
}

/**
 * Helper function to get mailer instance
 */
function mailer() {
    return new Mailer();
}

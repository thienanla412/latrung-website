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
     * Send email using SMTP
     */
    private function sendSMTP($to, $subject, $message, $options = []) {
        $isHTML = $options['isHTML'] ?? false;
        $replyTo = $options['replyTo'] ?? null;

        try {
            // Determine encryption type and context
            $encryption = strtolower(SMTP_ENCRYPTION);
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => true,
                    'verify_peer_name' => true,
                    'allow_self_signed' => false
                ]
            ]);

            // Connect to SMTP server
            if ($encryption === 'ssl') {
                // SSL connection (port 465)
                $smtp = stream_socket_client(
                    'ssl://' . SMTP_HOST . ':' . SMTP_PORT,
                    $errno,
                    $errstr,
                    30,
                    STREAM_CLIENT_CONNECT,
                    $context
                );
            } else {
                // Plain connection, will upgrade to TLS (port 587)
                $smtp = stream_socket_client(
                    'tcp://' . SMTP_HOST . ':' . SMTP_PORT,
                    $errno,
                    $errstr,
                    30,
                    STREAM_CLIENT_CONNECT
                );
            }

            if (!$smtp) {
                $this->lastError = "Failed to connect to SMTP server: {$errstr} ({$errno})";
                $this->logError($this->lastError);
                return false;
            }

            // Read server greeting (may be multi-line)
            $greeting = '';
            while ($line = fgets($smtp, 515)) {
                $greeting .= $line;
                // Check if this is the last line (has space after code instead of dash)
                if (strlen($line) >= 4 && $line[3] === ' ') {
                    break;
                }
            }

            if (substr($greeting, 0, 3) !== '220') {
                $this->lastError = "SMTP server error: {$greeting}";
                fclose($smtp);
                return false;
            }

            // Send EHLO
            fwrite($smtp, "EHLO " . SMTP_HOST . "\r\n");
            $ehloResponse = $this->readSMTPResponse($smtp);

            // Check if EHLO was successful
            if (substr($ehloResponse, 0, 3) !== '250') {
                $this->lastError = "EHLO failed: {$ehloResponse}";
                fclose($smtp);
                return false;
            }

            // Start TLS if using STARTTLS (port 587)
            if ($encryption === 'tls') {
                fwrite($smtp, "STARTTLS\r\n");
                $response = fgets($smtp, 515);
                if (substr($response, 0, 3) !== '220') {
                    $this->lastError = "STARTTLS failed: {$response}";
                    fclose($smtp);
                    return false;
                }

                stream_socket_enable_crypto($smtp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);

                // Send EHLO again after STARTTLS
                fwrite($smtp, "EHLO " . SMTP_HOST . "\r\n");
                $response = $this->readSMTPResponse($smtp);
            }

            // Authenticate
            if (!empty(SMTP_USER) && !empty(SMTP_PASS)) {
                // Try AUTH LOGIN first
                fwrite($smtp, "AUTH LOGIN\r\n");
                $response = fgets($smtp, 515);

                // If AUTH LOGIN not supported (504), try AUTH PLAIN
                if (substr($response, 0, 3) === '504' || substr($response, 0, 3) === '502') {
                    // AUTH LOGIN not supported, try AUTH PLAIN
                    $authString = base64_encode("\0" . SMTP_USER . "\0" . SMTP_PASS);
                    fwrite($smtp, "AUTH PLAIN {$authString}\r\n");
                    $response = fgets($smtp, 515);

                    if (substr($response, 0, 3) !== '235') {
                        $this->lastError = "AUTH PLAIN failed. Check credentials: {$response}";
                        fclose($smtp);
                        return false;
                    }
                } elseif (substr($response, 0, 3) === '334') {
                    // AUTH LOGIN supported, continue
                    fwrite($smtp, base64_encode(SMTP_USER) . "\r\n");
                    $response = fgets($smtp, 515);

                    if (substr($response, 0, 3) !== '334') {
                        $this->lastError = "Username authentication failed: {$response}";
                        fclose($smtp);
                        return false;
                    }

                    fwrite($smtp, base64_encode(SMTP_PASS) . "\r\n");
                    $response = fgets($smtp, 515);

                    if (substr($response, 0, 3) !== '235') {
                        $this->lastError = "Password authentication failed. Check your credentials: {$response}";
                        fclose($smtp);
                        return false;
                    }
                } else {
                    $this->lastError = "Unexpected AUTH response: {$response}";
                    fclose($smtp);
                    return false;
                }
            }

            // Send MAIL FROM
            fwrite($smtp, "MAIL FROM: <" . MAIL_FROM_EMAIL . ">\r\n");
            $response = fgets($smtp, 515);
            if (substr($response, 0, 3) !== '250') {
                $this->lastError = "MAIL FROM failed: {$response}";
                fclose($smtp);
                return false;
            }

            // Send RCPT TO
            fwrite($smtp, "RCPT TO: <{$to}>\r\n");
            $response = fgets($smtp, 515);
            if (substr($response, 0, 3) !== '250') {
                $this->lastError = "RCPT TO failed: {$response}";
                fclose($smtp);
                return false;
            }

            // Send DATA
            fwrite($smtp, "DATA\r\n");
            $response = fgets($smtp, 515);
            if (substr($response, 0, 3) !== '354') {
                $this->lastError = "DATA command failed: {$response}";
                fclose($smtp);
                return false;
            }

            // Build email headers and body
            $headers = [];
            $headers[] = "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM_EMAIL . ">";
            $headers[] = "To: <{$to}>";
            $headers[] = "Subject: {$subject}";
            $headers[] = "Date: " . date('r');
            $headers[] = "MIME-Version: 1.0";

            if ($isHTML) {
                $headers[] = "Content-Type: text/html; charset=UTF-8";
            } else {
                $headers[] = "Content-Type: text/plain; charset=UTF-8";
            }

            if ($replyTo && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
                $headers[] = "Reply-To: <{$replyTo}>";
            }

            $headers[] = "X-Mailer: PHP/" . phpversion();

            // Send headers and message
            $emailContent = implode("\r\n", $headers) . "\r\n\r\n" . $message . "\r\n.";
            fwrite($smtp, $emailContent . "\r\n");
            $response = fgets($smtp, 515);

            if (substr($response, 0, 3) !== '250') {
                $this->lastError = "Failed to send message: {$response}";
                fclose($smtp);
                return false;
            }

            // Send QUIT
            fwrite($smtp, "QUIT\r\n");
            fclose($smtp);

            $this->logEmail($to, $subject, 'success (SMTP)');
            return true;

        } catch (Exception $e) {
            $this->lastError = "SMTP error: " . $e->getMessage();
            $this->logError($this->lastError);
            return false;
        }
    }

    /**
     * Read SMTP multi-line response
     */
    private function readSMTPResponse($smtp) {
        $response = '';
        while ($line = fgets($smtp, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) === ' ') {
                break;
            }
        }
        return $response;
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
        // Get language from form data, default to Vietnamese
        $language = $formData['language'] ?? 'vi';

        // Set subject based on language
        if ($language === 'en') {
            $subject = "Thank you for contacting " . SITE_NAME;
        } else {
            $subject = "Cảm ơn bạn đã liên hệ " . SITE_NAME;
        }

        $message = $this->buildAutoReplyEmail($formData, $language);

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
    private function buildAutoReplyEmail($data, $language = 'vi') {
        $name = htmlspecialchars($data['name'] ?? ($language === 'en' ? 'Valued Customer' : 'Quý khách'));

        // Set content based on language
        if ($language === 'en') {
            $greeting = 'Dear';
            $thankYou = 'Thank you for contacting ' . SITE_NAME . '. We have received your inquiry and our team will review it shortly.';
            $responseTime = 'We typically respond to all inquiries within 24 business hours. If your request is urgent, please feel free to call us at +84 (028) 38-632-759.';
            $lookForward = 'We look forward to working with you.';
            $regards = 'Best regards,';
            $team = SITE_NAME . ' Team';
            $automated = 'This is an automated message. Please do not reply to this email.';
        } else {
            $greeting = 'Kính gửi';
            $thankYou = 'Cảm ơn quý khách đã liên hệ tới ' . SITE_NAME . '. Chúng tôi đã nhận được yêu cầu của quý khách và đội ngũ của chúng tôi sẽ phản hồi trong thời gian sớm nhất.';
            $responseTime = 'Chúng tôi thường phản hồi tất cả các yêu cầu trong vòng 24 giờ làm việc. Nếu yêu cầu của quý khách khẩn cấp, vui lòng gọi cho chúng tôi theo số +84 (028) 38-632-759.';
            $lookForward = 'Chúng tôi mong được hợp tác cùng quý khách.';
            $regards = 'Trân trọng,';
            $team = 'Đội ngũ ' . SITE_NAME;
            $automated = 'Đây là thông báo tự động. Vui lòng không trả lời email này.';
        }

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
            <p>' . $greeting . ' ' . $name . ',</p>
            <p>' . $thankYou . '</p>
            <p>' . $responseTime . '</p>
            <p>' . $lookForward . '</p>
            <p>' . $regards . '<br>' . $team . '</p>
        </div>
        <div class="footer">
            <p>' . $automated . '</p>
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

<?php
$page = '500';
require_once __DIR__ . '/includes/language.php';
$pageTitle = '500 - Server Error | La TRUNG Printing & Packaging';

http_response_code(500);
?>
<!DOCTYPE html>
<html lang="<?php echo getCurrentLang(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/png" href="assets/logo.png">
    <link rel="stylesheet" href="css/styles.css?v=<?php echo filemtime('css/styles.css'); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .error-page {
            min-height: 70vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 60px 20px;
        }
        .error-content {
            max-width: 600px;
            margin: 0 auto;
        }
        .error-code {
            font-size: 120px;
            font-weight: 700;
            color: #2DBAA7;
            line-height: 1;
            margin-bottom: 20px;
        }
        .error-title {
            font-size: 32px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 16px;
        }
        .error-description {
            font-size: 18px;
            color: #666;
            margin-bottom: 40px;
            line-height: 1.6;
        }
        .error-actions {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }
        @media (max-width: 768px) {
            .error-code {
                font-size: 80px;
            }
            .error-title {
                font-size: 24px;
            }
            .error-description {
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <section class="error-page">
        <div class="container">
            <div class="error-content">
                <div class="error-code">500</div>
                <h1 class="error-title">Server Error</h1>
                <p class="error-description">
                    <?php if (getCurrentLang() === 'vi'): ?>
                        Xin lỗi, đã xảy ra lỗi máy chủ. Chúng tôi đang khắc phục sự cố. Vui lòng thử lại sau.
                    <?php else: ?>
                        Sorry, something went wrong on our server. We're working to fix it. Please try again later.
                    <?php endif; ?>
                </p>
                <div class="error-actions">
                    <a href="javascript:location.reload()" class="btn btn-primary">
                        <?php echo getCurrentLang() === 'vi' ? 'Thử Lại' : 'Try Again'; ?>
                    </a>
                    <a href="/" class="btn btn-secondary">
                        <?php echo getCurrentLang() === 'vi' ? 'Về Trang Chủ' : 'Go to Homepage'; ?>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>

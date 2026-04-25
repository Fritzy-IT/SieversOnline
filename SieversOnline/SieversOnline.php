<?php
session_start();

/** * REAL BACKEND LOGIC: Database Connection 
 * Gamit ang PDO para iwas SQL Injection.
 */
$host = 'localhost'; 
$db   = 'sievers_tech_db';
$user = 'root'; 
$pass = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Sa real logic, hindi natin pinapakita ang error sa user for security
    $error = "System is currently undergoing maintenance.";
}

/** * REAL BACKEND LOGIC: Authentication System
 */
$login_error = "";
if (isset($_POST['login'])) {
    $username = trim($_POST['user']);
    $password = $_POST['pass'];
    $role     = $_POST['role'];

    // Prepared Statement para sa security
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND role = ? LIMIT 1");
    $stmt->execute([$username, $role]);
    $user_data = $stmt->fetch();

    if ($user_data && password_verify($password, $user_data['password'])) {
        // Secure Session Handling
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user_data['id'];
        $_SESSION['role']    = $user_data['role'];
        $_SESSION['name']    = $user_data['fullname'];
        
        header("Location: dashboard.php");
        exit();
    } else {
        $login_error = "Invalid $role credentials.";
    }
}

$view = isset($_GET['portal']) ? $_GET['portal'] : 'intro';
$platform = isset($_GET['platform']) ? $_GET['platform'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STA | Professional Distribution Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { 
            --glass: rgba(255, 255, 255, 0.6);
            --primary: #6c5ce7; 
            --secondary: #00b894; 
            --text: #2d3436; 
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: linear-gradient(-45deg, #f3f4f7, #e3e6ed);
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
        }
        .glass-shell { 
            background: var(--glass); backdrop-filter: blur(25px); 
            border-radius: 30px; border: 1px solid rgba(255,255,255,0.7);
            padding: 40px; width: 95%; max-width: 850px; text-align: center;
            box-shadow: 0 20px 50px rgba(0,0,0,0.05);
        }
        .btn-portal { 
            background: white; padding: 25px; border-radius: 20px; text-decoration: none;
            color: var(--text); border: 1px solid rgba(0,0,0,0.05); display: inline-block;
            width: 220px; margin: 10px; transition: 0.3s; font-weight: 700;
        }
        .btn-portal:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        .progress-bar { width: 100%; background: #eee; height: 10px; border-radius: 5px; overflow: hidden; margin: 20px 0; display: none; }
        .progress-fill { width: 0%; background: var(--secondary); height: 100%; transition: width 0.2s; }
        input { width: 100%; padding: 15px; margin: 10px 0; border-radius: 12px; border: 1px solid #ddd; outline: none; }
        .btn-main { background: var(--primary); color: white; padding: 15px 40px; border-radius: 12px; border: none; font-weight: 700; cursor: pointer; }
        /* Admin App UI Specifics */
.app-frame {
    max-width: 380px;
    margin: 0 auto;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 40px;
    border: 8px solid #2d3436; /* Parang bezel ng phone */
    overflow: hidden;
    box-shadow: 0 50px 100px rgba(0,0,0,0.2);
    position: relative;
}

.status-bar {
    display: flex;
    justify-content: space-between;
    padding: 10px 25px;
    font-size: 0.7rem;
    color: #636e72;
    font-weight: 700;
}

.app-logo-small {
    font-family: 'Cinzel', serif;
    font-size: 1.2rem;
    margin: 20px 0;
}

.glass-login-card {
    padding: 30px;
    text-align: left;
}

.glass-login-card h2 { font-size: 1.5rem; color: var(--text); margin-bottom: 5px; }
.glass-login-card p { font-size: 0.8rem; color: #b2bec3; margin-bottom: 30px; }

.input-group { margin-bottom: 20px; }
.input-group label { 
    display: block; 
    font-size: 0.65rem; 
    font-weight: 800; 
    color: var(--primary); 
    margin-bottom: 8px;
    letter-spacing: 1px;
}

.glass-login-card input {
    background: rgba(255, 255, 255, 0.5);
    border: 1px solid rgba(0,0,0,0.05);
    padding: 15px;
    border-radius: 12px;
    width: 100%;
    color: var(--text);
}

.btn-admin-login {
    background: #2d3436; /* Dark professional button for Admin */
    color: white;
    width: 100%;
    padding: 18px;
    border-radius: 12px;
    border: none;
    font-weight: 700;
    font-size: 0.8rem;
    cursor: pointer;
    transition: 0.3s;
    margin-top: 10px;
    letter-spacing: 1px;
}

.btn-admin-login:hover {
    background: var(--primary);
    box-shadow: 0 10px 20px rgba(108, 92, 231, 0.3);
}

.app-footer-links {
    text-align: center;
    margin-top: 25px;
    font-size: 0.75rem;
}

.app-footer-links a { color: #b2bec3; text-decoration: none; }
.error-msg { color: #ff7675; font-size: 0.8rem; margin-bottom: 15px; text-align: center; font-weight: 600; }
    </style>
</head>
<body>

<div class="glass-shell">
    <h1 style="margin-bottom: 30px; letter-spacing: 2px;">SIEVERS<span>TECH</span></h1>

    <?php if ($view == 'intro'): ?>
        <p style="margin-bottom: 30px; color: #636e72;">Secure Distribution & Portal Access</p>
        <div class="grid">
            <a href="?portal=Admin" class="btn-portal">⚙️ Admin</a>
            <a href="?portal=Teacher" class="btn-portal">👨‍🏫 Teacher</a>
            <a href="?portal=Student" class="btn-portal">🎓 Student</a>
        </div>

    <?php elseif ($view && !$platform): ?>
        <h3>Select Device Platform</h3>
        <p style="color: #636e72; margin: 20px 0;">Download the STA executable for <?php echo $view; ?> portal.</p>
        <a href="?portal=<?php echo $view; ?>&platform=iOS" class="btn-portal"> iOS</a>
        <a href="?portal=<?php echo $view; ?>&platform=Android" class="btn-portal">🤖 Android</a>
        <br><a href="index.php" style="margin-top:20px; display:inline-block; color:#aaa; text-decoration:none;">← Back</a>

    <?php elseif ($platform): ?>
        <div id="dl-section">
            <h3>Ready to Install: STA_<?php echo $view; ?></h3>
            <div class="progress-bar" id="p-bar"><div class="progress-fill" id="p-fill"></div></div>
            <p id="dl-status" style="font-size: 0.9rem; color: #636e72; margin-bottom: 20px;">Platform: <?php echo $platform; ?></p>
            <button class="btn-main" onclick="startRealDownload()">Download & Install</button>
        </div>

        <div id="login-section" style="display:none;">
            <h3>Authentication Required</h3>
            <?php if($login_error) echo "<p style='color:red;'>$login_error</p>"; ?>
            <form method="POST" style="max-width: 350px; margin: 20px auto;">
                <input type="hidden" name="role" value="<?php echo $view; ?>">
                <input type="text" name="user" placeholder="Username" required>
                <input type="password" name="pass" placeholder="Password" required>
                <button type="submit" name="login" class="btn-main" style="width:100%;">Open Portal</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<script>
function startRealDownload() {
    const bar = document.getElementById('p-bar');
    const fill = document.getElementById('p-fill');
    const status = document.getElementById('dl-status');
    const loginSec = document.getElementById('login-section');
    const dlSec = document.getElementById('dl-section');

    bar.style.display = 'block';
    let width = 0;
    
    const interval = setInterval(() => {
        if (width >= 100) {
            clearInterval(interval);
            
            // REAL LOGIC: Trigger download via PHP handler
            const urlParams = new URLSearchParams(window.location.search);
            const pf = urlParams.get('platform').toLowerCase();
            window.location.href = 'download_handler.php?file=' + pf;

            setTimeout(() => {
                dlSec.style.display = 'none';
                loginSec.style.display = 'block';
            }, 2000);
        } else {
            width += Math.random() * 10;
            fill.style.width = (width > 100 ? 100 : width) + '%';
            status.innerText = "Downloading encrypted package... " + Math.floor(width > 100 ? 100 : width) + "%";
        }
    }, 200);
}
</script>
</body>
</html>
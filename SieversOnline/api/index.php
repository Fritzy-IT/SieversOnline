<?php
session_start();
// PAALALA: Sa Vercel, kailangan mo ng external database (tulad ng PlanetScale o Supabase) 
// dahil hindi gagana ang 'localhost' na database sa cloud.
$host = 'iyong_remote_db_host'; 
$db   = 'sievers_tech_db';
$user = 'iyong_db_user'; 
$pass = 'iyong_db_password'; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $login_error = "Database Connection Error.";
}

// ... (Login Logic remains the same as previous)

$view = $_GET['portal'] ?? 'intro';
$platform = $_GET['platform'] ?? '';
?>

<script>
function startRealDownload() {
    // ... animation logic ...
    const urlParams = new URLSearchParams(window.location.search);
    const pf = urlParams.get('platform').toLowerCase();
    
    // Redirect sa download route na nasa vercel.json
    window.location.href = '/download/' + pf;
}
</script>
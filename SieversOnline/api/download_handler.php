<?php
if (isset($_GET['file'])) {
    $file_type = $_GET['file'];
    $filename = ($file_type == 'android') ? "STA_App.apk" : "STA_App.ipa";
    
    // Sa Vercel, ang static files ay nasa root directory relative sa execution
    $path = __DIR__ . "/../public/app_files/" . $filename;

    if (file_exists($path)) {
        header('Content-Type: application/vnd.android.package-archive');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    } else {
        header("HTTP/1.0 404 Not Found");
        echo "Error: File not found. Path: " . $path;
    }
}
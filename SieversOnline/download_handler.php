<?php
// download_handler.php
if (isset($_GET['file'])) {
    $file_type = $_GET['file'];
    
    // Folder kung saan nakatago ang APK/IPA mo
    $dir = "app_files/"; 
    $filename = ($file_type == 'android') ? "STA_App.apk" : "STA_App.ipa";
    $path = $dir . $filename;

    if (file_exists($path)) {
        // Professional Headers para sa Mobile Download
        header('Content-Type: application/vnd.android.package-archive');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($path));
        header('Cache-Control: no-cache');
        
        readfile($path);
        exit;
    } else {
        // Kung wala pang file, gagawa muna tayo ng dummy file para ma-test mo
        if(!is_dir($dir)) mkdir($dir);
        file_put_contents($path, "Fake APK Content for Testing");
        header("Location: " . $_SERVER['PHP_SELF'] . "?file=" . $file_type);
    }
}
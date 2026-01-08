<?php
session_start(); 
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("請先登入後再上傳檔案。");
}
// 檢查是否有檔案上傳
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['upload_file'])) {
    
    $user_id = $_SESSION['user_id'];; // 預設使用者 (admin)
    $title = mysqli_real_escape_string($link, $_POST['title']);
    $description = mysqli_real_escape_string($link, $_POST['description']);
    $type = 'image'; // 目前只處理圖片

    $file = $_FILES['upload_file'];
    
    // 簡單錯誤檢查
    if ($file['error'] !== UPLOAD_ERR_OK) {
        die("上傳失敗，錯誤代碼：" . $file['error']);
    }

    // 檢查檔案類型
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowed_types)) {
        die("只允許上傳圖片 (JPG, PNG, GIF, WEBP)");
    }

    // 準備儲存路徑
    $upload_dir = 'uploads/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true); // 如果資料夾不存在就建立
    }

    // 產生唯一檔名避免覆蓋
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $target_path = $upload_dir . $filename;

    // 移動檔案
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        // 寫入資料庫
        $sql = "INSERT INTO media (user_id, type, title, description, path) 
                VALUES ('$user_id', '$type', '$title', '$description', '$target_path')";
        
        if (mysqli_query($link, $sql)) {
            echo "<script>alert('上傳成功！'); location.href='index.php?page=home';</script>";
        } else {
            echo "資料庫寫入失敗：" . mysqli_error($link);
        }
    } else {
        echo "移動檔案失敗，請檢查資料夾權限。";
    }
} else {
    echo "無效的請求";
}
?>
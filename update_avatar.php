<?php
session_start();
require_once 'db.php';

// 檢查登入
if (!isset($_SESSION['user_id'])) {
    die("<script>alert('請先登入'); location.href='index.php?page=login';</script>");
}

// 檢查是否有檔案上傳
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['avatar_file'])) {
    
    $user_id = $_SESSION['user_id'];
    $file = $_FILES['avatar_file'];

    // 簡單錯誤檢查
    if ($file['error'] !== UPLOAD_ERR_OK) {
        die("<script>alert('上傳失敗 (Error Code: {$file['error']})'); history.back();</script>");
    }

    // 檢查檔案類型
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowed_types)) {
        die("<script>alert('只允許上傳圖片 (JPG, PNG, GIF, WEBP)'); history.back();</script>");
    }

    // 準備路徑
    $upload_dir = 'uploads/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // 產生檔名：avatar_使用者ID_亂數.副檔名 (避免快取問題)
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'avatar_' . $user_id . '_' . uniqid() . '.' . $extension;
    $target_path = $upload_dir . $filename;

    // 移動檔案
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        
        // 更新資料庫
        $sql = "UPDATE user SET avatar = '$target_path' WHERE id = '$user_id'";
        
        if (mysqli_query($link, $sql)) {
            // 更新 Session 中的大頭貼，這樣 Header 的頭貼才會同步更新
            $_SESSION['avatar'] = $target_path;
            
            echo "<script>alert('大頭貼更新成功！'); location.href='index.php?page=profile';</script>";
        } else {
            echo "資料庫更新失敗：" . mysqli_error($link);
        }
    } else {
        echo "<script>alert('移動檔案失敗'); history.back();</script>";
    }
} else {
    echo "<script>location.href='index.php?page=profile';</script>";
}
?>
<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    die("<script>alert('請先登入'); location.href='index.php?page=login';</script>");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $title = mysqli_real_escape_string($link, $_POST['title']);
    $description = mysqli_real_escape_string($link, $_POST['description']);
    
    // 接收資料
    $media_ids = isset($_POST['media_ids']) ? $_POST['media_ids'] : [];
    $cover_id = isset($_POST['cover_id']) ? intval($_POST['cover_id']) : 0;

    if (empty($title)) {
        die("<script>alert('標題不能為空'); history.back();</script>");
    }

    if (empty($media_ids)) {
        die("<script>alert('請至少選擇一張圖片'); history.back();</script>");
    }

    // 如果沒選封面，預設用第一張圖當封面
    if ($cover_id == 0 && count($media_ids) > 0) {
        $cover_id = intval($media_ids[0]);
    }

    // 如果選了封面但沒勾選，後端幫忙補進去
    if ($cover_id > 0 && !in_array($cover_id, $media_ids)) {
        $media_ids[] = $cover_id;
    }

    // 1. 建立 Album
    $sql_album = "INSERT INTO album (user_id, title, description, cover_media_id) 
                  VALUES ('$user_id', '$title', '$description', '$cover_id')";
    
    if (mysqli_query($link, $sql_album)) {
        $album_id = mysqli_insert_id($link);

        // 2. 建立關聯
        foreach ($media_ids as $index => $mid) {
            $mid = intval($mid);
            $sql_relation = "INSERT INTO album_media (album_id, media_id, sequence) 
                             VALUES ('$album_id', '$mid', '$index')";
            mysqli_query($link, $sql_relation);
        }

        echo "<script>alert('相簿建立成功！'); location.href='index.php?page=home';</script>";
    } else {
        echo "建立失敗：" . mysqli_error($link);
    }
} else {
    echo "無效的請求";
}
?>
<?php
session_start();
require_once 'db.php';
header('Content-Type: application/json');

// 錯誤處理：如果沒登入
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => '請先登入']);
    exit();
}

$user_id = $_SESSION['user_id'];

// 接收前端傳來的 JSON
$input = json_decode(file_get_contents('php://input'), true);
$target_type = isset($input['type']) ? $input['type'] : '';
$target_id = isset($input['id']) ? intval($input['id']) : 0;

if (empty($target_type) || empty($target_id)) {
    echo json_encode(['success' => false, 'message' => '參數錯誤']);
    exit();
}

// 檢查資料庫狀態
$check_sql = "SELECT id FROM likes WHERE user_id = '$user_id' AND target_type = '$target_type' AND target_id = '$target_id'";
$result = mysqli_query($link, $check_sql);

if (mysqli_num_rows($result) > 0) {
    //  已按讚 -> 取消
    $sql = "DELETE FROM likes WHERE user_id = '$user_id' AND target_type = '$target_type' AND target_id = '$target_id'";
    mysqli_query($link, $sql);
    $action = 'unliked';
} else {
    //  未按讚 -> 新增
    $sql = "INSERT INTO likes (user_id, target_type, target_id) VALUES ('$user_id', '$target_type', '$target_id')";
    mysqli_query($link, $sql);
    $action = 'liked';
}

// 計算最新總數
$count_sql = "SELECT COUNT(*) as total FROM likes WHERE target_type = '$target_type' AND target_id = '$target_id'";
$count_result = mysqli_query($link, $count_sql);
$count_row = mysqli_fetch_assoc($count_result);

// 回傳結果
echo json_encode([
    'success' => true,
    'action' => $action,
    'count' => $count_row['total']
]);
?>
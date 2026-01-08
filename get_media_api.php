<?php
header('Content-Type: application/json');
require_once 'db.php';

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'No ID provided']);
    exit();
}

$id = mysqli_real_escape_string($link, $_GET['id']);

// 獲取 Media 基本資料 (包含路徑、時間、作者ID)
// JOIN user 資料表來取得作者名字
$query = "SELECT media.*, user.username as author_name 
          FROM media 
          LEFT JOIN user ON media.user_id = user.id 
          WHERE media.id = '$id'";

$result = mysqli_query($link, $query);
$media = mysqli_fetch_array($result);

if (!$media) {
    echo json_encode(['error' => 'Media not found']);
    exit();
}

// 獲取留言
$comments = [];
$query_comments = "SELECT c.content, u.username 
                   FROM comment c
                   LEFT JOIN user u ON c.user_id = u.id
                   WHERE c.target_type = 'media' AND c.target_id = '$id'
                   ORDER BY c.created_at ASC";
$result_comments = mysqli_query($link, $query_comments);

while($row_c = mysqli_fetch_array($result_comments)){
    $comments[] = [
        'user' => $row_c['username'],
        'content' => $row_c['content']
    ];
}

// 組合回傳資料
$response = [
    'id' => $media['id'],
    'title' => $media['title'],
    'description' => $media['description'],
    'author' => $media['author_name'] ? $media['author_name'] : 'Unknown',
    'created_at' => substr($media['created_at'], 0, 10), // 只取日期部分
    'location' => $media['path'], 
    'comments' => $comments
];

echo json_encode($response);
?>
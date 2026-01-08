<?php
session_start(); 
include('db.php') ;

//處理註冊提交
if (isset($_GET['action']) && $_GET['action'] == 'register_submit') {
    $username = mysqli_real_escape_string($link, $_POST['username']);
    $email = mysqli_real_escape_string($link, $_POST['email']);
    $password = $_POST['password'];

    // 檢查帳號是否重複
    $check = mysqli_query($link, "SELECT id FROM user WHERE username = '$username' OR email = '$email'");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('帳號或 Email 已存在！'); history.back();</script>";
        exit();
    }

    // 密碼加密 
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // 插入資料庫 (預設給個假圖當大頭貼)
    $sql = "INSERT INTO user (username, email, password, avatar) VALUES ('$username', '$email', '$password_hash', 'img/default_avatar.png')";
    
    if (mysqli_query($link, $sql)) {
        echo "<script>alert('註冊成功！請登入'); location.href='index.php?page=login';</script>";
    } else {
        echo "<script>alert('註冊失敗，請稍後再試'); history.back();</script>";
    }
    exit();
}

// 處理登入提交
if (isset($_GET['action']) && $_GET['action'] == 'login_submit') {
    $username = mysqli_real_escape_string($link, $_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM user WHERE username = '$username'";
    $result = mysqli_query($link, $sql);
    $user = mysqli_fetch_array($result);

    // 驗證帳號存在 且 密碼正確
    if ($user && password_verify($password, $user['password'])) {
        //登入成功，設定 Session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['avatar'] = $user['avatar']; // 存一下頭像路徑方便顯示
        
        echo "<script>alert('歡迎回來，" . $user['username'] . "！'); location.href='index.php?page=home';</script>";
    } else {
        echo "<script>alert('帳號或密碼錯誤！'); history.back();</script>";
    }
    exit();
}

// 處理登出
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    echo "<script>alert('已登出'); location.href='index.php?page=login';</script>";
    exit();
}

// 處理留言
if (isset($_GET['comment-contents']) && !empty($_GET['comment-contents'])) {
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('請先登入才能留言！'); location.href='index.php?page=login';</script>";
        exit();
    }

    $content = mysqli_real_escape_string($link, $_GET['comment-contents']);
    $user_id = $_SESSION['user_id']; // ★ 改用 Session ID
    $target_type = isset($_GET['type']) ? $_GET['type'] : 'album';
    $target_id = isset($_GET['id']) ? intval($_GET['id']) : 1;
    
    $sql = "INSERT INTO comment (user_id, target_type, target_id, content) 
            VALUES ('$user_id', '$target_type', '$target_id', '$content')";
    mysqli_query($link, $sql);
    
    $page = isset($_GET['page']) ? $_GET['page'] : 'home';
    header("Location: index.php?page=$page&id=$target_id");
    exit();
}

// 5. 處理新增/刪除聯絡資訊 (需驗證權限)
if (isset($_GET['action']) && ($_GET['action'] == 'add_contact' || $_GET['action'] == 'del_contact')) {
    if (!isset($_SESSION['user_id'])) {
        die("請先登入");
    }
    $current_user_id = $_SESSION['user_id'];

    if ($_GET['action'] == 'add_contact') {
        $type = mysqli_real_escape_string($link, $_GET['add-type']);
        $value = mysqli_real_escape_string($link, $_GET['add-value']);
        if(!empty($value)){
            $sql = "INSERT INTO user_contact (user_id, type, value) VALUES ('$current_user_id', '$type', '$value')";
            mysqli_query($link, $sql);
        }
    } elseif ($_GET['action'] == 'del_contact') {
        $cid = intval($_GET['cid']);
        // 確保只能刪除自己的資料
        $sql = "DELETE FROM user_contact WHERE id = '$cid' AND user_id = '$current_user_id'";
        mysqli_query($link, $sql);
    }
    header("Location: index.php?page=profile");
    exit();
}

// 6. 處理更新自我介紹 (Profile)
if (isset($_GET['action']) && $_GET['action'] == 'update_intro') {
    if (!isset($_SESSION['user_id'])) {
        die("<script>alert('請先登入'); location.href='index.php?page=login';</script>");
    }
    
    $current_user_id = $_SESSION['user_id'];
    // 接收 POST 過來的描述
    $description = mysqli_real_escape_string($link, $_POST['description']);
    
    $sql = "UPDATE user SET description = '$description' WHERE id = '$current_user_id'";
    
    if(mysqli_query($link, $sql)) {
        // 更新成功，跳回 Profile 頁面
        header("Location: index.php?page=profile");
    } else {
        echo "<script>alert('更新失敗'); history.back();</script>";
    }
    exit();
}   

// --- 路由邏輯 ---
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unnamed Blog</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <?php include 'header.php'; ?>

    <main id="app">
        <?php 
            switch ($page) {
                case 'home':
                    include 'view_home.php';
                    break;
                case 'albumdetail':
                    include 'view_album_detail.php';
                    break;
                case 'mediadetail':
                    include 'view_media_detail.php';
                    break;
                case 'create':
                    // 只有登入者能看到上傳頁
                    if(!isset($_SESSION['user_id'])) {
                        echo "<script>alert('請先登入'); location.href='index.php?page=login';</script>";
                    } else {
                        include 'view_create.php';
                    }
                    break;
                case 'about-us':
                    include 'view_about.php';
                    break;
                case 'profile':
                    include 'view_profile.php';
                    break;
                case 'login':
                    include 'view_login.php';
                    break;
                case 'register':
                    include 'view_register.php';
                    break;
                default:
                    include 'view_home.php';
                    break;
            }
        ?>
    </main>

    <script src="script.js"></script>

</body>
</html>
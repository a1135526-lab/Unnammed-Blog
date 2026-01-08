<?php
    if (!isset($_GET['id'])) {
        echo "<script>alert('無效的 ID'); location.href='index.php';</script>";
        exit();
    }
    
    $media_id = mysqli_real_escape_string($link, $_GET['id']);

    // 查詢 Media 資料與作者
    $sql = "SELECT media.*, user.username 
            FROM media 
            LEFT JOIN user ON media.user_id = user.id 
            WHERE media.id = '$media_id'";
    $result = mysqli_query($link, $sql);
    $media = mysqli_fetch_array($result);

    if (!$media) {
        echo "<div style='padding:50px; text-align:center;'>找不到此媒體資料</div>";
        exit(); // 停止執行後續 HTML
    }

    $is_liked = false;
    $total_likes = 0;

    // 1. 查總數
    $like_count_sql = "SELECT COUNT(*) as total FROM likes WHERE target_type = 'media' AND target_id = '$media_id'";
    $lc_result = mysqli_query($link, $like_count_sql);
    $total_likes = mysqli_fetch_assoc($lc_result)['total'];

    // 2. 查自己有無按過 (如果有登入)
    if (isset($_SESSION['user_id'])) {
        $my_uid = $_SESSION['user_id'];
        $check_like = "SELECT id FROM likes WHERE user_id = '$my_uid' AND target_type = 'media' AND target_id = '$media_id'";
        if (mysqli_num_rows(mysqli_query($link, $check_like)) > 0) {
            $is_liked = true;
        }
    }
?>

<section id="mediadetail" class="page active" style="display:block;">
    <div class="detail-layout">
        <div class="detail-image">
            <img src="<?php echo htmlspecialchars($media['path']); ?>" style="width: 100%; height: 100%; object-fit: contain; display: block;">
        </div>

        <div class="detail-info">
            <div class="user-header">
                <div class="avatar-small"></div>
                <h3><?php echo htmlspecialchars($media['username'] ?? 'Unknown'); ?></h3>
            </div>
    
            <h4><?php echo htmlspecialchars($media['title']); ?></h4>
            <br>
            <p class="content-text">
                <?php echo nl2br(htmlspecialchars($media['description'])); ?>
            </p>
            <div class="date-stamp"><?php echo substr($media['created_at'], 0, 10); ?></div>

            <div style="margin-top: 30px; display: flex; flex-direction: column; gap: 15px; align-items: flex-end;">
                <button 
                    onclick="toggleLike('media', <?php echo $media['id']; ?>, this)" 
                    class="comment-button" 
                    style="font-size: 2rem; border: none; background: none; cursor: pointer; color: <?php echo $is_liked ? '#ff5252' : '#ccc'; ?>;">
                    
                    ❤ 
                    
                    <span class="like-count" style="font-size: 1.2rem; vertical-align: middle; color: #333;">
                        <?php echo $total_likes; ?>
                    </span>
                
                </button>
            </div>
        </div>

        <div class="detail-comments">
            <h3 style="margin-bottom: 15px; text-align: center;">comments</h3>
            <div class="comment-list">
                <?php
                    // 查詢留言
                    $comment_sql = "SELECT c.*, u.username 
                                    FROM comment c 
                                    LEFT JOIN user u ON c.user_id = u.id
                                    WHERE c.target_type = 'media' AND c.target_id = '$media_id'
                                    ORDER BY c.created_at ASC";
                    $comment_result = mysqli_query($link, $comment_sql);
                    
                    if (mysqli_num_rows($comment_result) > 0) {
                        while($c_row = mysqli_fetch_array($comment_result)){
                            echo '<div class="comment-item" style="padding:10px; border-bottom:1px solid #eee;">';
                            echo '<strong>' . htmlspecialchars($c_row['username']) . ':</strong> ';
                            echo htmlspecialchars($c_row['content']);
                            echo '</div>';
                        }
                    } else {
                        echo '<p style="text-align:center; color:#999;">目前沒有留言</p>';
                    }
                ?>
            </div>
            <div class="comment-input-area">
                <form action="index.php" method="get">
                    <input type="text" class="comment-input" name="comment-contents" placeholder="留下你的足跡吧!">
                    <input type="hidden" name="page" value="mediadetail">
                    <input type="hidden" name="type" value="media"> <input type="hidden" name="id" value="<?php echo $media['id']; ?>">
                    <button type="submit" style="background:none; border:none; cursor:pointer;">➤</button>
                </form>
            </div>
        </div>
    </div>
    <!-- <button onclick="location.href='index.php?page=home'" style="position:fixed; top:80px; left:20px; z-index:1001;">返回列表</button> -->
</section>
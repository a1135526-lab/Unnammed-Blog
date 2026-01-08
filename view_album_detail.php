<?php
    $album_id = isset($_GET['id']) ? intval($_GET['id']) : 1;
    
    // 1. 查詢 Album 基本資料 
    // ★關鍵：JOIN media 以取得封面的路徑 (cover_path)
    $sql = "SELECT album.*, user.username, media.path AS cover_path 
            FROM album 
            LEFT JOIN user ON album.user_id = user.id 
            LEFT JOIN media ON album.cover_media_id = media.id
            WHERE album.id = '$album_id'";
            
    $result = mysqli_query($link, $sql);
    $album = mysqli_fetch_array($result);

    if (!$album) {
        echo "<div style='padding:50px; text-align:center;'>找不到此相簿</div>";
        exit();
    }

    // 2. 查詢相簿內的所有照片
    $photos_sql = "SELECT m.* FROM media m 
                   JOIN album_media am ON m.id = am.media_id 
                   WHERE am.album_id = '$album_id' 
                   ORDER BY am.sequence ASC";
    $photos_result = mysqli_query($link, $photos_sql);
?>

<section id="albumdetail" class="page active" style="display:block;">
    
    <div class="detail-layout" style="height: auto; min-height: 400px; margin-bottom: 30px;">
        
        <div class="detail-image">
            <?php if (!empty($album['cover_path'])): ?>
                <img src="<?php echo htmlspecialchars($album['cover_path']); ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px; max-height:400px;">
            <?php else: ?>
                <div class="image-placeholder" style="display:flex; justify-content:center; align-items:center;">
                    No Cover
                </div>
            <?php endif; ?>
        </div>

        <div class="detail-info">
            <div class="user-header">
                <div class="avatar-small"></div>
                <h3><?php echo htmlspecialchars($album['username'] ?? 'Unknown'); ?></h3>
            </div>
            
            <h2 style="margin-bottom:10px;"><?php echo htmlspecialchars($album['title'] ?? '未命名相簿'); ?></h2>
            <p class="content-text"><?php echo nl2br(htmlspecialchars($album['description'] ?? '沒有描述')); ?></p>
            <div class="date-stamp" style="margin-top:auto;">
                建立於: <?php echo substr($album['created_at'] ?? '', 0, 10); ?><br>
                共 <?php echo mysqli_num_rows($photos_result); ?> 張照片
            </div>
        </div>

        <div class="detail-comments" style="height: 500px;">
            <h3 style="margin-bottom: 15px; text-align: center;">Comments</h3>
            <div class="comment-list">
                <?php
                $comment_query = "SELECT c.*, u.username FROM comment c LEFT JOIN user u ON c.user_id = u.id WHERE target_type='album' AND target_id='$album_id' ORDER BY c.created_at ASC";
                $result_content = mysqli_query($link, $comment_query);
                if (mysqli_num_rows($result_content) > 0) {
                    while ($row = mysqli_fetch_array($result_content)) {
                        echo '<div class="comment-item"><strong>'.htmlspecialchars($row['username']).':</strong> '.htmlspecialchars($row['content']).'</div>';
                    }
                } else { echo '<p style="text-align:center; color:#999;">目前沒有留言</p>'; }
                ?>                    
            </div>
            <div class="comment-input-area">
                <form action="index.php" method="get">
                    <input type="text" class="comment-input" name="comment-contents" placeholder="回應這個相簿...">
                    <input type="hidden" name="page" value="albumdetail"><input type="hidden" name="type" value="album"><input type="hidden" name="id" value="<?php echo $album_id; ?>">
                    <button type="submit" style="background:none; border:none; cursor:pointer;">➤</button>
                </form>
            </div>
        </div>
    </div>

    <div style="padding: 20px; background: var(--bg-color);">
        <h3 style="margin-bottom: 20px; padding-left: 10px; border-left: 5px solid var(--accent-color);">
            相簿內容 (<?php echo mysqli_num_rows($photos_result); ?>)
        </h3>
        <div class="card-grid">
            <?php 
            if (mysqli_num_rows($photos_result) > 0) {
                while ($media_row = mysqli_fetch_array($photos_result)) {
                    ?>
                    <div class="card" onclick="location.href='index.php?page=mediadetail&id=<?php echo $media_row['id']; ?>'">
                        <div class="card-img">
                            <?php if(!empty($media_row['path'])): ?>
                                <img src="<?php echo htmlspecialchars($media_row['path']); ?>" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                            <?php endif; ?>
                        </div>
                        <div class="card-info">
                            <div class="card-title"><?php echo htmlspecialchars($media_row['title']); ?></div>
                            <div class="card-date"><?php echo substr($media_row['created_at'], 0, 10); ?></div>
                        </div>
                    </div>
                    <?php
                }
            } else { echo "<p style='color:#777; grid-column: 1 / -1; text-align: center;'>此相簿內沒有照片。</p>"; }
            ?>
        </div>
    </div>
</section>
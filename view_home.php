<section id="home" class="page active" style="display:block;">
    <div class="grid-container">
        <?php
            //  Album 區塊       
            //  同時撈出封面圖路徑 (cover_path)
            $sql_query = "SELECT album.*, media.path AS cover_path 
                          FROM album 
                          LEFT JOIN media ON album.cover_media_id = media.id 
                          ORDER BY album.created_at DESC";
            $result = mysqli_query($link, $sql_query);
            
            echo '<div class="left-half">';
            echo '<h2 class="section-title">ALBUM</h2>';
            echo '<div class="card-grid">';
                while($row = mysqli_fetch_array($result)){
                    ?>
                    <div class="card" onclick="location.href='index.php?page=albumdetail&id=<?php echo $row['id']; ?>'">
                        <div class="card-img">
                            <?php if(!empty($row['cover_path'])): ?>
                                <img src="<?php echo htmlspecialchars($row['cover_path']); ?>" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                            <?php else: ?>
                                <div style="width:100%; height:100%; display:flex; justify-content:center; align-items:center; color:#fff; background:#999;">Album</div>
                            <?php endif; ?>
                        </div>
                        <div class="card-info">
                            <div class="card-title"><?php echo htmlspecialchars($row['title']); ?></div>
                            <div class="card-date"><?php echo substr($row['created_at'], 0, 10); ?></div>
                        </div>
                    </div>
                    <?php
                }
            echo '</div>';
            echo '</div>';
        ?>

        <?php
            // Media 區塊
            $sql_query = "SELECT * FROM media ORDER BY created_at DESC";
            $result = mysqli_query($link, $sql_query);

            echo '<div class="right-half">';
            echo '<h2 class="section-title">MEDIA</h2>';
            echo '<div class="card-grid">';
                while($row = mysqli_fetch_array($result)){
                    ?>
                    <div class="card" onclick="location.href='index.php?page=mediadetail&id=<?php echo $row['id']; ?>'">
                        <div class="card-img">
                            <?php if(!empty($row['path'])) { echo '<img src="' . htmlspecialchars($row['path']) . '" style="width: 100%; height: 100%; object-fit: cover; display: block;">'; } ?>
                        </div>
                        <div class="card-info">
                            <div class="card-title"><?php echo htmlspecialchars($row['title']); ?></div>
                            <div class="card-date"><?php echo substr($row['created_at'], 0, 10); ?></div>
                        </div>
                    </div>
                    <?php
                }
            echo '</div>';
            echo '</div>';
        ?>
    </div>
    
    <div class="fab-post" onclick="location.href='index.php?page=create'">
        <span>+</span>
        <div style="font-size: 0.8rem;">POST</div>
    </div>
</section>
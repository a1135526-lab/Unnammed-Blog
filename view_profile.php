<?php
    // 檢查權限與讀取資料
    if (isset($_GET['uid'])) {
        $user_id = intval($_GET['uid']);
        $is_me = (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id);
    } elseif (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $is_me = true;
    } else {
        echo "<script>location.href='index.php?page=login';</script>"; exit();
    }
    
    // 撈取使用者資料
    $user_sql = "SELECT * FROM user WHERE id = '$user_id'";
    $user_result = mysqli_query($link, $user_sql);
    $user_data = mysqli_fetch_array($user_result);

    // 撈取聯絡資訊
    $contact_result = mysqli_query($link, "SELECT * FROM user_contact WHERE user_id = '$user_id'");

    // 統計資料 (模擬)
    $fans_count = 99; 
    $likes_count = 0;
?>

<section id="profile" class="page active" style="display:block;">
    <div style="background: #d3d3d3; padding: 50px; border-radius: 10px;">
        <div class="profile-layout">
            
            <div class="profile-intro">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                    <h2>自我介紹</h2>
                    <?php if($is_me): ?>
                        <button onclick="toggleIntroEdit(true)" class="edit-icon-btn" title="編輯介紹">✎</button>
                    <?php endif; ?>
                </div>
                
                <div id="intro-display">
                    <p style="line-height: 1.6; white-space: pre-wrap;"><?php echo htmlspecialchars($user_data['description'] ?? '這個人很懶，什麼都沒寫。'); ?></p>
                </div>

                <?php if($is_me): ?>
                <form id="intro-edit-form" action="index.php?action=update_intro" method="post" style="display:none;">
                    <textarea name="description" class="form-control" rows="5" style="margin-bottom:10px;"><?php echo htmlspecialchars($user_data['description'] ?? ''); ?></textarea>
                    <div style="text-align:right;">
                        <button type="button" onclick="toggleIntroEdit(false)" class="btn-cancel" style="font-size:0.9rem; padding:5px 15px;">取消</button>
                        <button type="submit" class="btn-primary" style="font-size:0.9rem; padding:5px 15px;">儲存</button>
                    </div>
                </form>
                <?php endif; ?>
            </div>

            <div style="flex: 1; min-width: 300px;">
                <div style="display: flex; align-items: center; gap: 20px;">
                    
                    <div class="avatar-wrapper">
                        <?php if(!empty($user_data['avatar'])): ?>
                            <img src="<?php echo $user_data['avatar']; ?>" class="profile-avatar">
                        <?php else: ?>
                            <div class="profile-avatar default-avatar"></div>
                        <?php endif; ?>

                        <?php if($is_me): ?>
                            <div class="avatar-overlay" onclick="document.getElementById('avatarInput').click();">
                                <span>📷 更換</span>
                            </div>
                            <form id="avatarForm" action="update_avatar.php" method="post" enctype="multipart/form-data" style="display:none;">
                                <input type="file" id="avatarInput" name="avatar_file" accept="image/*" onchange="document.getElementById('avatarForm').submit();">
                            </form>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h1 style="font-size: 3rem; font-weight: bold;"><?php echo htmlspecialchars($user_data['username']); ?></h1>
                        <div class="profile-stats">
                            <span id="fans-number">粉絲: <?php echo $fans_count; ?></span>
                            <span id="like-number">Like: <?php echo $likes_count; ?></span>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 20px; font-size: 1.1rem; background: rgba(255,255,255,0.5); padding: 15px; border-radius: 10px;">
                    <?php 
                        if(mysqli_num_rows($contact_result) > 0){
                            while($contact = mysqli_fetch_array($contact_result)){
                                $icon = "📌";
                                if(strpos($contact['type'], 'email') !== false) $icon = "✉";
                                if(strpos($contact['type'], 'phone') !== false) $icon = "📞";
                                echo "<div style='margin: 5px 0;'>$icon " . htmlspecialchars($contact['value']);
                                if($is_me) echo " <a href='index.php?page=profile&action=del_contact&cid=".$contact['id']."' style='font-size:0.8rem; color:red; text-decoration:none;'>[x]</a>";
                                echo "</div>";
                            }
                        } else { echo "<div>尚無聯絡資訊</div>"; }
                    ?>
                </div>

                <?php if($is_me): ?>
                <button id="add-btn" onclick="addInformation(true)" style="border-radius: 5px; margin-top: 15px; padding: 5px 10px; cursor: pointer; border:1px solid #555;">+ 新增資訊</button>
                <form id="addContactForm" class="d-none" action="index.php" method="get" style="margin-top: 15px; padding-top: 10px; border-top: 1px solid #ccc; display: flex; gap: 5px;">
                    <input type="hidden" name="page" value="profile"><input type="hidden" name="action" value="add_contact">
                    <select class="addItem" name="add-type" style="padding: 5px;"><option value="phone">📞 電話</option><option value="email">📧 E-mail</option><option value="instagram">📷 IG</option></select>
                    <input type="text" name="add-value" placeholder="輸入資訊..." required style="padding: 5px; flex: 1;">
                    <button type="submit" style="cursor: pointer;">儲存</button><button type="button" onclick="addInformation(false)" style="cursor: pointer;">取消</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
    .profile-layout { display: flex; align-items: flex-start; gap: 50px; flex-wrap: wrap; }
    .profile-intro { background: white; padding: 20px; border-radius: 15px; width: 300px; margin-right: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
    
    .edit-icon-btn {
        background: none; border: none; cursor: pointer; font-size: 1.2rem; color: #888;
        padding: 5px; border-radius: 50%; transition: background 0.2s;
    }
    .edit-icon-btn:hover { background: #eee; color: #4a90e2; }

    /* --- 大頭貼更換樣式 --- */
    .avatar-wrapper {
        position: relative;
        width: 150px; height: 150px;
        border-radius: 50%;
        border: 4px solid white; 
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        overflow: hidden; /* 確保遮罩是圓的 */
        flex-shrink: 0;
    }
    
    .profile-avatar {
        width: 100%; height: 100%;
        object-fit: cover;
        display: block;
    }
    .default-avatar { background: #795548; }

    /* 遮罩層：預設透明，滑鼠移入變黑 */
    .avatar-overlay {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5);
        display: flex; justify-content: center; align-items: center;
        color: white; font-weight: bold; font-size: 0.9rem;
        opacity: 0; transition: opacity 0.3s;
        cursor: pointer;
    }
    
    .avatar-wrapper:hover .avatar-overlay {
        opacity: 1;
    }
    /* ------------------- */

    @media (max-width: 768px) {
        .profile-layout { flex-direction: column; }
        .profile-intro { width: 100%; margin: 0 0 20px 0; }
    }
</style>

<script>
function toggleIntroEdit(editMode) {
    const displayDiv = document.getElementById('intro-display');
    const form = document.getElementById('intro-edit-form');
    if(editMode) { displayDiv.style.display = 'none'; form.style.display = 'block'; } 
    else { displayDiv.style.display = 'block'; form.style.display = 'none'; }
}
</script>
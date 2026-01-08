<?php
    // 確保有登入
    if(!isset($_SESSION['user_id'])) {
        echo "<script>location.href='index.php?page=login';</script>";
        exit;
    }

    $my_uid = $_SESSION['user_id'];
    // 撈取圖片
    $media_sql = "SELECT * FROM media WHERE user_id = '$my_uid' ORDER BY created_at DESC";
    $media_result = mysqli_query($link, $media_sql);
?>

<section id="create" class="page active" style="display:block;">
    <h2 style="margin-bottom: 20px;">Create New Post</h2>
    
    <div class="custom-tabs">
        <button type="button" class="tab-btn active" onclick="switchTab('tab-upload')">上傳單張圖片</button>
        <button type="button" class="tab-btn" onclick="switchTab('tab-album')">建立相簿</button>
    </div>

    <div id="tab-upload" class="tab-content active">
        <form action="mediaupload.php" method="post" enctype="multipart/form-data">
            <div class="create-layout">
                <div class="upload-box" onclick="document.getElementById('fileInput').click();">
                    
                    <div id="upload-placeholder">
                        <div style="font-size: 3rem;">📁</div>
                        <p>+新增媒體 (點擊選擇)</p>
                    </div>

                    <img id="image-preview" class="preview-img" style="display:none;">

                    <input type="file" id="fileInput" name="upload_file" accept="image/*" style="display:none" onchange="previewImage(this)">
                </div>

                <div class="form-box">
                    <div class="form-group"><label>標題</label><input type="text" name="title" class="form-control" placeholder="輸入標題..." required></div>
                    <div class="form-group"><label>圖片敘述</label><textarea name="description" class="form-control" placeholder="這張照片的故事..."></textarea></div>
                    <div style="display:flex; justify-content:space-between; margin-top:20px;">
                        <button type="button" onclick="location.href='index.php?page=home'" class="btn-cancel">取消</button>
                        <button type="submit" class="btn-primary">上傳發布</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div id="tab-album" class="tab-content">
        <form action="create_album.php" method="post">
            <div class="form-group">
                <label>相簿標題</label>
                <input type="text" name="title" class="form-control" placeholder="相簿名稱..." required>
            </div>
            <div class="form-group">
                <label>相簿描述</label>
                <textarea name="description" class="form-control" style="height:80px;" placeholder="關於這個相簿..."></textarea>
            </div>

            <div class="selection-guide">
                <p>1. 點擊圖片勾選要加入的照片 (藍框)</p>
                <p>2. 點擊下方文字設定一張封面 (★)</p>
            </div>
            
            <?php if(mysqli_num_rows($media_result) > 0): ?>
                <div class="photo-grid">
                    <?php while($m = mysqli_fetch_array($media_result)): ?>
                        <div class="photo-item" id="item_<?php echo $m['id']; ?>" onclick="toggleCheckbox(<?php echo $m['id']; ?>, event)">
                            <img src="<?php echo $m['path']; ?>">
                            <label class="check-box" onclick="event.stopPropagation()">
                                <input type="checkbox" name="media_ids[]" value="<?php echo $m['id']; ?>" id="chk_<?php echo $m['id']; ?>" onchange="updateSelection(<?php echo $m['id']; ?>)">
                                <span class="checkmark">✔</span>
                            </label>
                            <label class="cover-box" onclick="event.stopPropagation()">
                                <input type="radio" name="cover_id" value="<?php echo $m['id']; ?>" onclick="selectCover(<?php echo $m['id']; ?>)" required>
                                <span class="cover-text">設為封面</span>
                            </label>
                        </div>
                    <?php endwhile; ?>
                </div>
                <div style="margin-top: 20px; text-align: right;">
                    <button type="submit" class="btn-primary">建立相簿</button>
                </div>
            <?php else: ?>
                <div class="empty-state">無照片可選，請先上傳單張圖片。</div>
            <?php endif; ?>
        </form>
    </div>
</section>

<style>
    /* 頁籤 */
    .custom-tabs { display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 2px solid #ddd; overflow-x: auto; }
    .tab-btn { padding: 10px 15px; background: none; border: none; font-size: 1rem; cursor: pointer; color: #888; border-bottom: 3px solid transparent; white-space: nowrap; }
    .tab-btn.active { color: #333; border-bottom: 3px solid #4a90e2; font-weight: bold; }
    
    .tab-content { display: none; }
    .tab-content.active { display: block; animation: fadeIn 0.3s; }

    .create-layout { display: flex; gap: 30px; }
    
    .upload-box { 
        flex: 1; border: 2px dashed #888; border-radius: 10px; height: 300px; 
        display: flex; flex-direction: column; justify-content: center; align-items: center; 
        cursor: pointer; background: #f9f9f9; position: relative; overflow: hidden;
    }
    .upload-box:hover { background: #eee; border-color: #4a90e2; }
    
    /* 預覽圖片 */
    .preview-img { width: 100%; height: 100%; object-fit: contain; display: block; }

    .form-box { flex: 1; display: flex; flex-direction: column; gap: 15px; }

    /* 照片grid */
    .photo-grid {
        display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 10px; max-height: 500px; overflow-y: auto; padding: 10px;
        border: 1px solid #ddd; border-radius: 10px; background: #fff;
    }

    .photo-item {
        position: relative; aspect-ratio: 1/1; border-radius: 8px; overflow: hidden;
        border: 4px solid transparent; cursor: pointer; transition: all 0.2s; background: #eee;
    }
    .photo-item img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .photo-item.selected { border-color: #4a90e2; box-shadow: 0 0 10px rgba(74, 144, 226, 0.5); }

    /* 勾選框 */
    .check-box { position: absolute; top: 5px; right: 5px; width: 24px; height: 24px; z-index: 5; }
    .check-box input { display: none; }
    .checkmark {
        display: flex; justify-content: center; align-items: center;
        width: 100%; height: 100%; background: rgba(0,0,0,0.3); color: rgba(255,255,255,0.3);
        border-radius: 50%; border: 2px solid white; font-size: 14px;
    }
    .check-box input:checked + .checkmark { background: #4a90e2; color: white; border-color: #4a90e2; }

    /* 封面設定 */
    .cover-box {
        position: absolute; bottom: 0; left: 0; right: 0;
        background: rgba(0,0,0,0.6); color: #ddd; font-size: 0.8rem;
        text-align: center; padding: 5px 0; cursor: pointer;
    }
    .cover-box:hover { background: rgba(0,0,0,0.8); }
    .cover-box input { display: none; }
    .cover-box input:checked + .cover-text { color: #ffd700; font-weight: bold; text-shadow: 0 1px 2px black; }
    .cover-box input:checked + .cover-text::before { content: "★ "; }

    .selection-guide { background: #eef; padding: 10px; border-radius: 5px; margin: 10px 0; font-size: 0.9rem; color: #555; }
    .empty-state { text-align: center; padding: 30px; background: #eee; border-radius: 10px; color: #777; }

    /*RWD*/
    @media (max-width: 768px) {
        .create-layout { flex-direction: column; gap: 20px; }
        .upload-box { height: 220px; }
    }
</style>

<script>
//圖片預覽功能
function previewImage(input) {
    const placeholder = document.getElementById('upload-placeholder');
    const preview = document.getElementById('image-preview');
    
    // 檢查是否有選取檔案
    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            // 將讀取到的圖片數據設為 img 的 src
            preview.src = e.target.result;
            // 顯示圖片，隱藏文字
            preview.style.display = 'block';
            placeholder.style.display = 'none';
        }

        reader.readAsDataURL(input.files[0]);
    } else {
        // 如果取消選取，恢復原狀
        preview.src = '';
        preview.style.display = 'none';
        placeholder.style.display = 'flex'; // 因為原本是有用了 flex:center
    }
}

// 頁籤切換
function switchTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
    
    const btns = document.querySelectorAll('.tab-btn');
    if(tabId === 'tab-upload') btns[0].classList.add('active'); 
    else btns[1].classList.add('active');
}

// 相簿勾選
function toggleCheckbox(id, event) {
    if(event.target.tagName === 'INPUT' || event.target.tagName === 'LABEL') return;
    const checkbox = document.getElementById('chk_' + id);
    checkbox.checked = !checkbox.checked;
    updateSelection(id);
}

function updateSelection(id) {
    const checkbox = document.getElementById('chk_' + id);
    const item = document.getElementById('item_' + id);
    if (checkbox.checked) {
        item.classList.add('selected');
    } else {
        item.classList.remove('selected');
    }
}

function selectCover(id) {
    const checkbox = document.getElementById('chk_' + id);
    if (!checkbox.checked) {
        checkbox.checked = true;
        updateSelection(id);
    }
}
</script>
<section id="register" class="page active" style="display:block;">
    <div style="display: flex; justify-content: center; align-items: center; min-height: 80vh;">
        <div class="card" style="width: 100%; max-width: 400px; padding: 30px; cursor: default;">
            <h2 style="text-align: center; margin-bottom: 20px;">註冊新帳號</h2>
            
            <form action="index.php?action=register_submit" method="post">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>帳號 (Username)</label>
                    <input type="text" name="username" class="form-control" required>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                    <label>電子郵件 (Email)</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label>密碼 (Password)</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%;">註冊</button>
            </form>

            <div style="margin-top: 20px; text-align: center; font-size: 0.9rem;">
                已經有帳號了？ <a href="index.php?page=login" style="color: var(--accent-color);">馬上登入</a>
            </div>
        </div>
    </div>
</section>  
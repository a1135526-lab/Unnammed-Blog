<section id="login" class="page active" style="display:block;">
    <div style="display: flex; justify-content: center; align-items: center; min-height: 80vh;">
        <div class="card" style="width: 100%; max-width: 400px; padding: 30px; cursor: default;">
            <h2 style="text-align: center; margin-bottom: 20px;">會員登入</h2>
            
            <form action="index.php?action=login_submit" method="post">
                <div class="form-group" style="margin-bottom: 15px;">
                    <label>帳號 (Username)</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label>密碼 (Password)</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button type="submit" class="btn-primary" style="width: 100%;">登入</button>
            </form>

            <div style="margin-top: 20px; text-align: center; font-size: 0.9rem;">
                還沒有帳號嗎？ <a href="index.php?page=register" style="color: var(--accent-color);">立即註冊</a>
            </div>
        </div>
    </div>
</section>
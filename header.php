<header>
    <div class="nav-icon" onclick="location.href='index.php?page=home'">🏠</div>
    <div class="brand">Unnamed Blog</div>
    <div class="nav-controls">
        <a href="index.php?page=about-us" style="color:inherit; text-decoration:none;">about us</a>
        <button class="mode-toggle" onclick="toggleTheme()">
            ☀️ 淺色模式
        </button>
        
        <?php if(isset($_SESSION['user_id'])): ?>
            <div class="nav-icon" onclick="location.href='index.php?page=profile'" title="個人檔案">👤</div>
            <a href="index.php?action=logout" style="color:inherit; text-decoration:none; font-size:0.9rem; border:1px solid #fff; padding:5px 10px; border-radius:15px;">Logout</a>
        <?php else: ?>
            <a href="index.php?page=login" style="color:inherit; text-decoration:none; font-weight:bold;">Login</a>
        <?php endif; ?>
    </div>
</header>
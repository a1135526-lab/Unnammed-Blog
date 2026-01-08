// 深色模式切換
function toggleTheme() {
    const body = document.body;
    const btn = document.querySelector('.mode-toggle');

    body.classList.toggle('dark-mode');

    if (body.classList.contains('dark-mode')) {
        btn.innerHTML = '🌙 深色模式';
    } else {
        btn.innerHTML = '☀️ 淺色模式';
    }
}

// 個人檔案新增資訊開關
function addInformation(show){
    const btn = document.getElementById('add-btn');
    const form = document.getElementById('addContactForm');

    if (show) {
        btn.classList.add('d-none');
        form.classList.remove('d-none');
    } else {
        btn.classList.remove('d-none');
        form.classList.add('d-none');
    }
}

// 按讚功能 (AJAX)
function toggleLike(type, id, btnElement) {
    // 防止重複點擊 (可選)
    btnElement.style.pointerEvents = 'none';

    fetch('api_like.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ type: type, id: id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // ★ 修改重點：只切換 class，不手動改顏色
            if (data.action === 'liked') {
                btnElement.classList.add('liked');
            } else {
                btnElement.classList.remove('liked');
            }
            
            // 更新數字
            const countSpan = btnElement.querySelector('.like-count');
            if (countSpan) {
                countSpan.innerText = data.count;
            }
        } else {
            alert(data.message);
            if(data.message === '請先登入') {
                location.href = 'index.php?page=login';
            }
        }
    })
    .catch(error => console.error('Error:', error))
    .finally(() => {
        // 恢復點擊
        btnElement.style.pointerEvents = 'auto';
    });
}
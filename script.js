/* script.js */

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

/* script.js */

// 按讚功能 (AJAX)
function toggleLike(type, id, btnElement) {
    // 1. Debug 用：確認函式有被呼叫
    console.log("正在按讚:", type, id);

    // 2. 發送請求給後端
    fetch('api_like.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ type: type, id: id })
    })
    .then(response => {
        // 檢查 API 是否有回傳 200 OK
        if (!response.ok) {
            throw new Error("HTTP error " + response.status);
        }
        return response.json();
    })
    .then(data => {
        console.log("後端回傳:", data); // Debug 用：看後端回傳什麼

        if (data.success) {
            // 3. 更新愛心顏色與樣式
            if (data.action === 'liked') {
                btnElement.style.color = '#ff5252'; // 紅色
                btnElement.classList.add('liked');
            } else {
                btnElement.style.color = '#ccc'; // 灰色
                btnElement.classList.remove('liked');
            }
            
            // 4. 更新數字
            const countSpan = btnElement.querySelector('.like-count');
            if (countSpan) {
                countSpan.innerText = data.count;
            }
        } else {
            // 如果後端回傳失敗 (例如沒登入)
            alert(data.message);
            if(data.message === '請先登入') {
                location.href = 'index.php?page=login';
            }
        }
    })
    .catch(error => {
        console.error('發生錯誤:', error);
        alert("系統發生錯誤，請檢查 Console");
    });
}
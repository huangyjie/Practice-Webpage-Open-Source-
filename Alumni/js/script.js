function submitForm(event) {
    event.preventDefault();
    const form = document.getElementById('classmateForm');
    const formData = new FormData(form);
    
    fetch('insert.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        const messageDiv = document.getElementById('message');
        messageDiv.innerHTML = data;
        messageDiv.className = 'message';
        form.reset(); // Reset the form
        showPopup(); // Show the popup notification
    })
    .catch(error => {
        const messageDiv = document.getElementById('message');
        messageDiv.innerHTML = '提交失败: ' + error;
        messageDiv.className = 'error';
    });
}

function queryMessage(event) {
    event.preventDefault();
    const queryForm = document.getElementById('queryForm');
    const formData = new FormData(queryForm);

    fetch('query.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const resultDiv = document.getElementById('queryResult');
        if (data.success) {
            resultDiv.innerHTML = `<p>姓名: ${data.name}</p>
                                   <p>生日: ${data.birthdate}</p>
                                   <p>性别: ${data.gender}</p>
                                   <p>年龄: ${data.age}</p>
                                   <p>星座: ${data.zodiac}</p>
                                   <p>血型: ${data.blood_type}</p>
                                   <p>QQ: ${data.qq}</p>
                                   <p>电话: ${data.phone}</p>
                                   <p>好友留言: ${data.message}</p>`;
            resultDiv.className = 'message';
        } else {
            resultDiv.innerHTML = data.message;
            resultDiv.className = 'error';
        }
    })
    .catch(error => {
        const resultDiv = document.getElementById('queryResult');
        resultDiv.innerHTML = '查询失败: ' + error;
        resultDiv.className = 'error';
    });
}

// Function to show the popup
function showPopup() {
    var popupContainer = document.getElementById('popupContainer');
    popupContainer.style.display = 'block';
    setTimeout(function() {
        popupContainer.style.display = 'none';
    }, 2000); // Adjust the time (in milliseconds) for how long the popup should be visible
}

document.getElementById('classmateForm').addEventListener('submit', submitForm);
document.getElementById('queryForm').addEventListener('submit', queryMessage);

// 获取图片元素
const photo = document.getElementById('photo');

// 添加鼠标右键事件监听器
photo.addEventListener('mousedown', handleMouseDown);

// 添加点击事件监听器
photo.addEventListener('click', toggleEnlarge);

// 长按时间间隔，单位为毫秒
const longPressDuration = 1000; // 1秒

let longPressTimer;

function handleMouseDown(event) {
    // 检查是否是鼠标右键点击
    if (event.button === 2) {
        // 阻止默认右键菜单
        event.preventDefault();

        // 开始计时长按
        longPressTimer = setTimeout(downloadPhoto, longPressDuration);
    }
}

function downloadPhoto() {
    // 创建一个 <a> 元素
    const link = document.createElement('a');
    link.href = photo.src;
    link.download = 'photo.jpg';

    // 模拟点击下载链接
    link.click();
}

// 取消长按计时器
photo.addEventListener('mouseup', () => clearTimeout(longPressTimer));
photo.addEventListener('mouseleave', () => clearTimeout(longPressTimer));

// 切换图片放大状态的函数
function toggleEnlarge() {
    // 切换类名以触发 CSS 中的放大效果
    photo.classList.toggle('enlarged');
}

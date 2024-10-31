<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>账户已锁定 - H's blog circle</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background: url('https://picsum.photos/1920/1080') no-repeat center center/cover;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            padding: 0;
        }
        .lock-container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        .lock-icon {
            font-size: 48px;
            color: #e74c3c;
            margin-bottom: 20px;
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
        }
        p {
            margin-bottom: 20px;
        }
        .timer {
            font-size: 24px;
            font-weight: bold;
            color: #e74c3c;
            margin-bottom: 20px;
        }
        .back-link {
            color: #3498db;
            text-decoration: none;
            font-weight: bold;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .beian {
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }
        .ip-address {
            font-weight: bold;
            color: #e74c3c;
        }
    </style>
</head>
<body>
    <div class="lock-container">
        <i class="fas fa-lock lock-icon"></i>
        <h1>账户已锁定</h1>
        <p><?php echo $lock_message; ?></p>
        <p>您的IP <span class="ip-address"><?php echo $_SERVER['REMOTE_ADDR']; ?></span> 已经被记录，网络不是法外之地。</p>
        <div class="timer" id="countdown"></div>
        <a href="../index.html" class="back-link">返回主页</a>
        <div class="beian">
            <p>备案号: <a href="https://beian.miit.gov.cn/" target="_blank">鄂ICP备2024077565号</a></p>
        </div>
    </div>

    <script>
        // 倒计时功能
        var timeLeft = <?php echo $lock_time; ?>;
        var countdownElement = document.getElementById('countdown');

        function updateCountdown() {
            var minutes = Math.floor(timeLeft / 60);
            var seconds = timeLeft % 60;
            countdownElement.innerHTML = minutes + "分 " + seconds + "秒";
            if (timeLeft > 0) {
                timeLeft--;
                setTimeout(updateCountdown, 1000);
            } else {
                countdownElement.innerHTML = "锁定已解除，请刷新页面";
            }
        }

        updateCountdown();
    </script>
</body>
</html>

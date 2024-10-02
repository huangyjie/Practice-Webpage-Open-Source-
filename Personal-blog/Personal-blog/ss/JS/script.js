async function queryDatabase() {
    const userInput = document.getElementById("userInput").value.trim();
    const errorMessage = document.getElementById("errorMessage");

    if (!userInput) {
        errorMessage.innerText = "请输入查询条件再点击。";
        return;
    }

    errorMessage.innerText = "";

    try {
        const response = await fetch("PHP/query.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: new URLSearchParams({ user_input: userInput }).toString()
        });

        if (!response.ok) {
            throw new Error("网络请求失败。");
        }

        const result = await response.text();
        document.getElementById("result").innerHTML = result;
        await displayQuestionCount();
    } catch (error) {
        console.error("查询数据库时发生错误:", error);
    }
}

async function displayQuestionCount() {
    try {
        const response = await fetch("PHP/get_question_count.php");

        if (!response.ok) {
            throw new Error("网络请求失败。");
        }

        const questionCount = await response.text();
        document.getElementById("questionCount").innerHTML = questionCount;
    } catch (error) {
        console.error("获取题目数量时发生错误:", error);
    }
}

function addData() {
    const password = prompt("请输入密码：");
    const correctPassword = "root";

    if (password !== correctPassword) {
        alert("密码错误，添加失败。");
        return;
    }

    const question = document.getElementById("question").value.trim();
    const optionA = document.getElementById("optionA").value.trim();
    const optionB = document.getElementById("optionB").value.trim();
    const optionC = document.getElementById("optionC").value.trim();
    const optionD = document.getElementById("optionD").value.trim();
    const correctAnswer = document.getElementById("correctAnswer").value.trim();

    const addMessage = document.getElementById("addMessage");

    if (!question || !optionA || !optionB || !optionC || !optionD || !correctAnswer) {
        addMessage.innerText = "请填写所有输入框再添加题目。";
        return;
    }

    addMessage.innerText = "";

    const data = new URLSearchParams({
        question,
        optionA,
        optionB,
        optionC,
        optionD,
        correctAnswer
    });

    fetch("PHP/insert_data.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: data.toString()
    })
    .then(response => {
        if (!response.ok) {
            throw new Error("添加失败。");
        }
        addMessage.innerText = "添加成功。";
    })
    .catch(error => {
        console.error("添加数据时发生错误:", error);
        addMessage.innerText = "添加失败。";
    });
}

document.addEventListener("DOMContentLoaded", function() {
    const userInput = document.getElementById("userInput");
    const queryButton = document.querySelector("#queryForm button");

    userInput.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            queryButton.click(); // 模拟点击查找按钮的操作
        }
    });

    // 自动弹出提示弹窗，并设置定时关闭
    const popup = document.getElementById('popup');
    popup.style.display = 'block'; // 显示弹窗

    // 定时关闭弹窗，10秒后自动隐藏
    setTimeout(function() {
        popup.style.display = 'none'; // 关闭弹窗
    }, 10000); // 10秒
});

// 关闭弹窗的函数
function closePopup() {
    const popup = document.getElementById('popup');
    popup.style.display = 'none'; // 关闭弹窗
}

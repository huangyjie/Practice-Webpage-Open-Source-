<?php
// 数据库配置
$servername = "localhost"; // 数据库服务器名称
$username = "root"; // 数据库用户名
$password = "123456"; // 数据库密码
$dbname = "wy"; // 数据库名称

// 创建连接
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error); // 如果连接失败，则输出错误信息并终止脚本执行
}

// 设置连接字符集为utf8mb4
$conn->set_charset("utf8mb4");

// 获取提交的数据
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
$qq = filter_input(INPUT_POST, 'qq', FILTER_SANITIZE_STRING);
$age = filter_input(INPUT_POST, 'age', FILTER_SANITIZE_NUMBER_INT);
$gender = filter_input(INPUT_POST, 'gender', FILTER_SANITIZE_STRING);
$country = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_STRING);
$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
$school = filter_input(INPUT_POST, 'school', FILTER_SANITIZE_STRING);
$programming_languages = isset($_POST['programming_languages']) ? implode(",", array_map('htmlspecialchars', $_POST['programming_languages'])) : '';
$comments = filter_input(INPUT_POST, 'comments', FILTER_SANITIZE_STRING);
$terms = filter_input(INPUT_POST, 'terms', FILTER_SANITIZE_STRING) ? '同意' : '不同意';

// 验证必填字段
if (empty($name) || empty($email) || empty($phone) || empty($age) || empty($gender) || empty($country) || empty($address) || empty($school) || !$terms) {
    $response = array("message" => "请查看是否全部填写完成"); // 如果有必填字段为空或者条款和条件未接受，则返回错误信息
} else {
    // 插入数据到数据库
    $stmt = $conn->prepare("INSERT INTO survey_responses (name, email, phone, qq, age, gender, country, address, school, programming_languages, comments, terms) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssss", $name, $email, $phone, $qq, $age, $gender, $country, $address, $school, $programming_languages, $comments, $terms);

    $response = array();

    if ($stmt->execute()) {
        $response["message"] = "数据已存储";
    } else {
        $response["message"] = "错误: " . $stmt->error;
    }
}

$conn->close(); // 关闭数据库连接

// 返回JSON响应
header('Content-Type: application/json'); // 设置响应头部为JSON格式
echo json_encode($response); // 输出JSON格式的响应
?>

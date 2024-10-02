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
$name = isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : ''; // 获取姓名字段，并进行数据库转义
$email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : ''; // 获取电子邮件字段，并进行数据库转义
$phone = isset($_POST['phone']) ? $conn->real_escape_string($_POST['phone']) : ''; // 获取手机号字段，并进行数据库转义
$qq = isset($_POST['qq']) ? $conn->real_escape_string($_POST['qq']) : ''; // 获取QQ号字段，并进行数据库转义
$age = isset($_POST['age']) ? (int)$_POST['age'] : 0; // 获取年龄字段，并转换为整数类型
$gender = isset($_POST['gender']) ? $conn->real_escape_string($_POST['gender']) : ''; // 获取性别字段，并进行数据库转义
$country = isset($_POST['country']) ? $conn->real_escape_string($_POST['country']) : ''; // 获取国家字段，并进行数据库转义
$address = isset($_POST['address']) ? $conn->real_escape_string($_POST['address']) : ''; // 获取家庭住址字段，并进行数据库转义
$school = isset($_POST['school']) ? $conn->real_escape_string($_POST['school']) : ''; // 获取学校字段，并进行数据库转义
$programming_languages = isset($_POST['programming_languages']) ? $conn->real_escape_string(implode(",", $_POST['programming_languages'])) : ''; // 获取编程语言字段，并进行数据库转义
$comments = isset($_POST['comments']) ? $conn->real_escape_string($_POST['comments']) : ''; // 获取评论字段，并进行数据库转义
$terms = isset($_POST['terms']) ? '同意' : '不同意'; // 获取是否接受条款和条件字段，并转换为整数类型（1表示接受，0表示不接受）

// 验证必填字段
if (empty($name) || empty($email) || empty($phone) || empty($age) || empty($gender) || empty($country) || empty($address) || empty($school) || !$terms) {
    $response = array("message" => "请查看是否全部填写完成"); // 如果有必填字段为空或者条款和条件未接受，则返回错误信息
} else {
    // 插入数据到数据库
    $sql = "INSERT INTO survey_responses (name, email, phone, qq, age, gender, country, address, school, programming_languages, comments, terms) 
    VALUES ('$name', '$email', '$phone', '$qq', '$age', '$gender', '$country', '$address', '$school', '$programming_languages', '$comments', '$terms')";

    $response = array();

    if ($conn->query($sql) === TRUE) {
        $response["message"] = "数据已存储"; // 如果插入成功，则返回成功信息
    } else {
        $response["message"] = "错误: " . $sql . "<br>" . $conn->error; // 如果插入失败，则返回错误信息
    }
}

$conn->close(); // 关闭数据库连接

// 返回JSON响应
header('Content-Type: application/json'); // 设置响应头部为JSON格式
echo json_encode($response); // 输出JSON格式的响应
?>

<?php
// Retrieve form data
$name = $_POST['s_xingming'];
$idNumber = $_POST['s_shenfenzhenghao'];

// Validate the input (you may want to add more validation)
if (!empty($name) && !empty($idNumber)) {
    // Connect to the database (replace with your database credentials)
    $servername = "localhost";
    $username = "root";
    $password = "123456";
    $dbname = "grade_system_db"; // Replace with your database name

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Set character set to utf8mb4
    $conn->set_charset("utf8mb4");

    // Prepare SQL statement to insert data into the database
    $sql = "INSERT INTO user_data (name, id_number) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);

    // Check if prepare() succeeded
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameters and execute the SQL statement
    $stmt->bind_param("ss", $name, $idNumber);
    if ($stmt->execute() === TRUE) {
        // Redirect to another HTML page after successful insertion
        header("Location: index1.html");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "Name and ID number are required.";
}
?>

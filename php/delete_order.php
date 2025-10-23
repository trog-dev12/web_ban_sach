<?php
// Kết nối cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "caitiembansach";

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Kiểm tra và nhận tham số ID
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Chuyển ID thành số nguyên để tránh SQL Injection

    // Câu lệnh xóa
    $sql = "DELETE FROM orders WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Xóa đơn hàng thành công!'); window.location.href='QLDH.php';</script>";
    } else {
        echo "Lỗi khi xóa đơn hàng: " . $conn->error;
    }
} else {
    echo "ID đơn hàng không được cung cấp!";
}

$conn->close();

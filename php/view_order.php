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

// Kiểm tra và lấy tham số ID
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM orders WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();

    if (!$order) {
        die("Đơn hàng không tồn tại.");
    }
} else {
    die("Không tìm thấy ID đơn hàng.");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đơn hàng</title>
    <style>
        table {
            width: 50%;
            margin: auto;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid black;
            padding: 10px;
            text-align: left;
        }
    </style>
</head>

<body>
    <h1 style="text-align: center;">Chi tiết đơn hàng</h1>
    <table>
        <tr>
            <th>ID</th>
            <td><?= $order['id'] ?></td>
        </tr>
        <tr>
            <th>Tên người mua</th>
            <td><?= htmlspecialchars($order['name']) ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?= htmlspecialchars($order['email']) ?></td>
        </tr>
        <tr>
            <th>Số điện thoại</th>
            <td><?= htmlspecialchars($order['phone']) ?></td>
        </tr>
        <tr>
            <th>Địa chỉ</th>
            <td><?= htmlspecialchars($order['address']) ?></td>
        </tr>
        <tr>
            <th>Tổng tiền</th>
            <td><?= number_format($order['total_amount'], 2) ?> VND</td>
        </tr>
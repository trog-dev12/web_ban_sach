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

// Lấy tất cả đơn hàng từ bảng 'orders'
$sql = "SELECT * FROM orders ORDER BY created_at DESC"; // Lấy dữ liệu theo thứ tự ngày tạo
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <title>Quản lý đơn hàng</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 10px;
            text-align: center;
        }

        button {
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
    <link rel="stylesheet" href="../css/QLDH.css?=v1">
</head>

<body>
    <header>
        <div class="box">
            <div class="logo">
                <a href="index.php"><img src="../img/OIP(1).webp" width="50" height="auto"></a>
                BOOK STORE
            </div>
            <div class="menu">
                <ul>
                    <li class="tc"><a href="acount_management.php">QUẢN LÝ TÀI KHOẢN</a></li>
                    <li class="tc"><a href="QLHH.php">QUẢN LÝ HÀNG HÓA</a></li>
                    <li class="tc"><a href="QLDH.php">QUẢN LÝ ĐƠN HÀNG</a></li>
                    <li class="tc"><a href="/caitiembansach.final/php/index.php">THOÁT</a></li>
                </ul>
            </div>
        </div>
    </header>
    <h1 style="text-align: center; margin-top: 40px;">Quản lý đơn hàng</h1>

    <!-- Danh sách đơn hàng -->
    <table>
        <thead>
            <tr>
                <th>ID Đơn Hàng</th>
                <th>Tên Người Mua</th>
                <th>Email</th>
                <th>Số Điện Thoại</th>
                <th>Địa Chỉ</th>
                <th>Tổng Tiền</th>
                <th>Phương Thức Thanh Toán</th>
                <th>Ngày Đặt</th>
                <th>Ghi Chú</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td><?= htmlspecialchars($row['address']) ?></td>
                    <td><?= number_format($row['total_amount'], 2) ?> VND</td>
                    <td><?= htmlspecialchars($row['payment_method']) ?></td>
                    <td><?= $row['created_at'] ?></td>
                    <td><?= htmlspecialchars($row['note']) ?></td>
                    <td>
                        <a href="view_order.php?id=<?= $row['id'] ?>">Xem</a> |
                        <a href="change_status.php?id=<?= $row['id'] ?>&status=shipped" onclick="return confirm('Đã giao hàng thành công');">Giao hàng</a> |
                        <a href="delete_order.php?id=<?= $row['id'] ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa đơn hàng này?');">Xóa</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <footer>
        <div class="title">
            CÁI TIỆM BÁN SÁCH
        </div>
        <ul class="menuBottom">
            <li><i class="fa-brands fa-facebook fa-2xl" style="color: #225dc3;"></i>&nbsp;FACEBOOK</li>
            <li><i class="fa-brands fa-youtube fa-2xl" style="color: #f50a0a;"></i></i>&nbsp;YOUTUBE</li>
            <li><i class="fa-brands fa-twitter fa-2xl" style="color: #1da1f2;"></i></i>&nbsp;TWITTER</li>
        </ul>
    </footer>
</body>

</html>

<?php
$conn->close();
?>
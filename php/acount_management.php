<?php
// Kết nối cơ sở dữ liệu
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "caitiembansach";

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Xử lý Xóa tài khoản
if (isset($_POST['id'])) {
    // Lấy id từ tham số URL
    $delete_id = intval($_POST['id']);

    // Kiểm tra xem id có hợp lệ không
    if ($delete_id > 0) {
        // Chuẩn bị câu lệnh xóa
        $sql = "DELETE FROM register WHERE id = ?";
        $stmt = $conn->prepare($sql);

        // Kiểm tra xem câu lệnh chuẩn bị có thành công không
        if ($stmt === false) {
            die("Lỗi khi chuẩn bị câu lệnh: " . $conn->error);
        }

        // Liên kết tham số và thực thi câu lệnh
        $stmt->bind_param("i", $delete_id);

        // Thực thi câu lệnh
        if ($stmt->execute()) {
            // Nếu xóa thành công, chuyển hướng về trang quản lý tài khoản
            header("Location: acount_management.php");
            exit();
        } else {
            echo "Lỗi khi xóa tài khoản: " . $stmt->error;
        }

        // Đóng câu lệnh
        $stmt->close();
    } else {
        echo "ID không hợp lệ!";
    }
}

// Lấy tất cả tài khoản người dùng
$sql = "SELECT * FROM register ORDER BY created_at DESC";
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
    <title>Quản lý tài khoản</title>
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
        }
    </style>
    <link rel="stylesheet" href="../css/acount_management.css">

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
    </header>
    <h1>Quản lý tài khoản</h1>

    <!-- Danh sách tài khoản người dùng -->
    <h2>Danh sách tài khoản</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên người dùng</th>
                <th>Email</th>
                <th>Ngày tạo</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= $row['created_at'] ?></td>
                    <td>
                        <form action="acount_management.php" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?');">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <button type="submit">Xóa</button>
                        </form>
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
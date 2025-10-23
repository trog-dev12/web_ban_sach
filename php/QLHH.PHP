<?php
// Kết nối cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "caitiembansach";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Biến lưu trạng thái sửa
$edit_mode = false;
$current_shoe = null;

// Xử lý Thêm/Sửa
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $image_url = null;

    // Kiểm tra và xử lý upload ảnh
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "../img/"; // Thư mục lưu ảnh
        $image_url = $target_dir . basename($_FILES["image"]["name"]);

        // Di chuyển file vào thư mục
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $image_url)) {
            die("Lỗi khi upload ảnh.");
        }
    }

    // Kiểm tra có phải là chế độ sửa không
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Chế độ sửa
        $id = intval($_POST['id']);
        $sql = "UPDATE book SET name = ?, price = ?, description = ?, image_url = IFNULL(?, image_url) WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdssi", $name, $price, $description, $image_url, $id);
    } else {
        // Chế độ thêm mới
        $sql = "INSERT INTO book (name, price, description, image_url) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdss", $name, $price, $description, $image_url);
    }

    if ($stmt->execute()) {
        echo "Sản phẩm đã được thêm/sửa thành công!";
        header("Location: QLHH.php"); // Chuyển hướng về trang quản lý sau khi thêm/sửa
        exit();
    } else {
        echo "Lỗi: " . $stmt->error; // Nếu có lỗi trong quá trình thêm/sửa
    }
}

// Xử lý Xóa sản phẩm
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $sql = "DELETE FROM book WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        header("Location: QLHH.php"); // Chuyển hướng về trang quản lý sau khi xóa
        exit();
    } else {
        echo "Lỗi khi xóa: " . $stmt->error;
    }
}

// Xử lý Sửa sản phẩm (lấy dữ liệu sản phẩm để sửa)
if (isset($_GET['edit_id'])) {
    $edit_mode = true;
    $edit_id = intval($_GET['edit_id']);
    $sql = "SELECT * FROM book WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $current_shoe = $result->fetch_assoc();
}

// Lấy danh sách sản phẩm để hiển thị
$sql = "SELECT * FROM book ORDER BY created_at DESC";
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
    <title>Quản lý sản phẩm</title>
    <link rel="stylesheet" href="../css/QLHH.css">
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

        form {
            margin: 20px 0;
        }

        img {
            width: 50px;
            height: auto;
        }

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
    <h1>Quản lý sản phẩm</h1>

    <!-- Form Thêm/Sửa Sản Phẩm -->
    <h2><?= $edit_mode ? "Sửa sản phẩm" : "Thêm sản phẩm mới" ?></h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <?php if ($edit_mode): ?>
            <input type="hidden" name="id" value="<?= $current_shoe['id'] ?>">
        <?php endif; ?>
        <label for="name">Tên sách:</label><br>
        <input type="text" id="name" name="name" value="<?= $edit_mode ? htmlspecialchars($current_shoe['name']) : "" ?>" required><br><br>

        <label for="price">Giá:</label><br>
        <input type="number" id="price" name="price" step="0.01" value="<?= $edit_mode ? $current_shoe['price'] : "" ?>" required><br><br>

        <label for="description">Mô tả:</label><br>
        <textarea id="description" name="description" rows="4"><?= $edit_mode ? htmlspecialchars($current_shoe['description']) : "" ?></textarea><br><br>

        <label for="image">Hình ảnh:</label><br>
        <input type="file" id="image" name="image" accept="image/*"><br><br>

        <button type="submit"><?= $edit_mode ? "Lưu thay đổi" : "Thêm sản phẩm" ?></button>
    </form>

    <!-- Danh sách sản phẩm -->
    <h2>Danh sách sản phẩm</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên sách</th>
                <th>Giá</th>
                <th>Mô tả</th>
                <th>Hình ảnh</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= number_format($row['price'], 2) ?> VND</td>
                    <td><?= htmlspecialchars($row['description']) ?></td>
                    <td>
                        <?php if ($row['image_url']): ?>
                            <img src="<?= htmlspecialchars($row['image_url']) ?>" alt="Shoe Image">
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="QLHH.php?edit_id=<?= $row['id'] ?>">Sửa</a> |
                        <a href="QLHH.php?delete_id=<?= $row['id'] ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa?');">Xóa</a>
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

<?php $conn->close(); ?>